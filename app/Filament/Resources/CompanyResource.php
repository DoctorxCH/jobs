<?php

namespace App\Filament\Resources;

use App\Filament\Resources\CompanyResource\Pages;
use App\Models\Company;
use App\Models\CompanyCategory;
use App\Models\User;
use App\Services\PermissionService;
use Filament\Forms;
use Filament\Forms\Get;
use Filament\Forms\Set;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Str;

class CompanyResource extends Resource
{
    protected static ?string $model = Company::class;

    protected static ?string $navigationIcon = 'heroicon-o-building-office';
    protected static ?string $navigationGroup = 'Companies';
    protected static ?string $navigationLabel = 'Companies';

    public static function getPermissionKey(): string
    {
        return 'companies';
    }

    public static function canViewAny(): bool
    {
        return PermissionService::can(static::getPermissionKey(), 'view');
    }

    public static function canCreate(): bool
    {
        return PermissionService::can(static::getPermissionKey(), 'create');
    }

    public static function canEdit($record): bool
    {
        return PermissionService::can(static::getPermissionKey(), 'edit');
    }

    public static function canDelete($record): bool
    {
        return PermissionService::can(static::getPermissionKey(), 'delete');
    }

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\Tabs::make('CompanyTabs')
                ->columnSpanFull()
                ->tabs([

                    Forms\Components\Tabs\Tab::make('Basics')
                        ->schema([
                            Forms\Components\Section::make('Company')
                                ->schema([
                                    Forms\Components\TextInput::make('legal_name')
                                        ->label('Legal name')
                                        ->required()
                                        ->maxLength(255)
                                        ->live(onBlur: true)
                                        ->afterStateUpdated(function ($state, Set $set, Get $get) {
                                            $slug = Str::slug((string) $state);
                                            if ($get('slug') === null || $get('slug') === '') {
                                                $set('slug', $slug);
                                            }
                                        })
                                        ->validationMessages([
                                            'required' => 'Legal name is required.',
                                            'max' => 'Legal name is too long.',
                                        ]),

                                    Forms\Components\TextInput::make('slug')
                                        ->label('Slug')
                                        ->required()
                                        ->maxLength(255)
                                        ->unique(ignoreRecord: true)
                                        ->helperText('URL-friendly identifier. Auto-filled from legal name.')
                                        ->validationMessages([
                                            'required' => 'Slug is required.',
                                            'unique' => 'This slug is already taken.',
                                        ]),

                                    Forms\Components\Select::make('category_id')
                                        ->label('Category')
                                        ->relationship('category', 'name')
                                        ->searchable()
                                        ->preload()
                                        ->nullable()
                                        ->helperText('Optional. Used for grouping/filters.'),

                                    Forms\Components\Select::make('owner_user_id')
                                        ->label('Owner user')
                                        ->relationship('owner', 'email')
                                        ->searchable()
                                        ->preload()
                                        ->required()
                                        ->getOptionLabelFromRecordUsing(function (User $record): string {
                                            $name = trim((string) ($record->name ?? ''));
                                            return $name !== '' ? "{$name} ({$record->email})" : $record->email;
                                        })
                                        ->disabled(fn () => ! static::isPlatformAdmin())
                                        ->helperText('Only platform admins can change the owner.')
                                        ->validationMessages([
                                            'required' => 'Owner user is required.',
                                        ]),

                                    Forms\Components\Grid::make(3)
                                        ->schema([
                                            Forms\Components\TextInput::make('ico')
                                                ->label('ICO')
                                                ->required()
                                                ->maxLength(8)
                                                ->regex('/^\d{8}$/')
                                                ->unique(ignoreRecord: true)
                                                ->helperText('8 digits (Slovakia).')
                                                ->validationMessages([
                                                    'required' => 'ICO is required.',
                                                    'regex' => 'ICO must be exactly 8 digits.',
                                                    'unique' => 'This ICO is already used by another company.',
                                                ]),

                                            Forms\Components\TextInput::make('dic')
                                                ->label('DIC')
                                                ->nullable()
                                                ->maxLength(10)
                                                ->regex('/^\d{10}$/')
                                                ->unique(ignoreRecord: true)
                                                ->helperText('Optional. 10 digits.')
                                                ->validationMessages([
                                                    'regex' => 'DIC must be exactly 10 digits.',
                                                    'unique' => 'This DIC is already used by another company.',
                                                ]),

                                            Forms\Components\TextInput::make('ic_dph')
                                                ->label('IC DPH')
                                                ->nullable()
                                                ->maxLength(12)
                                                ->regex('/^SK\d{10}$/')
                                                ->unique(ignoreRecord: true)
                                                ->helperText('Optional. Format: SK + 10 digits.')
                                                ->validationMessages([
                                                    'regex' => 'IC DPH must be in format SK + 10 digits.',
                                                    'unique' => 'This IC DPH is already used by another company.',
                                                ]),
                                        ]),
                                ])
                                ->columns(2),

                            Forms\Components\Section::make('Seats & status')
                                ->schema([
                                    Forms\Components\TextInput::make('seats_purchased')
                                        ->label('Seats purchased')
                                        ->numeric()
                                        ->minValue(1)
                                        ->default(1)
                                        ->required()
                                        ->helperText('How many active members are allowed.')
                                        ->validationMessages([
                                            'required' => 'Seats purchased is required.',
                                            'numeric' => 'Seats purchased must be a number.',
                                            'min' => 'Seats purchased must be at least 1.',
                                        ]),

                                    Forms\Components\TextInput::make('seats_locked')
                                        ->label('Seats locked')
                                        ->numeric()
                                        ->minValue(0)
                                        ->default(0)
                                        ->helperText('Reserved seats (optional).')
                                        ->maxValue(fn (Get $get) => (int) ($get('seats_purchased') ?? 1))
                                        ->validationMessages([
                                            'numeric' => 'Seats locked must be a number.',
                                            'min' => 'Seats locked cannot be negative.',
                                            'max' => 'Seats locked cannot exceed seats purchased.',
                                        ]),

                                    Forms\Components\Select::make('status')
                                        ->label('Status')
                                        ->options([
                                            'pending' => 'pending',
                                            'active' => 'active',
                                            'suspended' => 'suspended',
                                        ])
                                        ->required()
                                        ->default('pending')
                                        ->validationMessages([
                                            'required' => 'Status is required.',
                                        ]),

                                    Forms\Components\Toggle::make('active')
                                        ->label('Active')
                                        ->default(true)
                                        ->helperText('Soft on/off switch for visibility.')
                                        ->inline(false),

                                    Forms\Components\DateTimePicker::make('verified_at')
                                        ->label('Verified at')
                                        ->seconds(false)
                                        ->nullable()
                                        ->helperText('Set when company verification is completed.')
                                        ->disabled(fn () => ! static::isPlatformAdmin()),
                                ])
                                ->columns(2),
                        ]),

                    Forms\Components\Tabs\Tab::make('Public profile')
                        ->schema([
                            Forms\Components\Section::make('Public / profile')
                                ->schema([
                                    Forms\Components\TextInput::make('website_url')
                                        ->label('Website')
                                        ->nullable()
                                        ->maxLength(255)
                                        ->url()
                                        ->validationMessages([
                                            'url' => 'Website must be a valid URL.',
                                        ]),

                                    Forms\Components\TextInput::make('general_email')
                                        ->label('General email')
                                        ->nullable()
                                        ->maxLength(255)
                                        ->email()
                                        ->validationMessages([
                                            'email' => 'General email must be a valid email address.',
                                        ]),

                                    Forms\Components\TextInput::make('phone')
                                        ->label('Phone')
                                        ->nullable()
                                        ->maxLength(50),

                                    Forms\Components\TextInput::make('logo_path')
                                        ->label('Logo path')
                                        ->nullable()
                                        ->maxLength(255)
                                        ->helperText('If you later switch to FileUpload, keep this as storage path.'),

                                    Forms\Components\Textarea::make('description_short')
                                        ->label('Short description')
                                        ->nullable()
                                        ->maxLength(280)
                                        ->rows(3)
                                        ->helperText('Max 280 chars.'),

                                    Forms\Components\RichEditor::make('bio')
                                        ->label('Bio')
                                        ->nullable()
                                        ->columnSpanFull(),

                                    Forms\Components\KeyValue::make('social_links')
                                        ->label('Social links')
                                        ->nullable()
                                        ->keyLabel('Platform')
                                        ->valueLabel('URL')
                                        ->helperText('Keys like: linkedin, facebook, instagram, x, youtube, github')
                                        ->columnSpanFull(),
                                ])
                                ->columns(2),

                            Forms\Components\Section::make('Facts')
                                ->schema([
                                    Forms\Components\TextInput::make('team_size')
                                        ->label('Team size')
                                        ->numeric()
                                        ->minValue(1)
                                        ->nullable()
                                        ->validationMessages([
                                            'numeric' => 'Team size must be a number.',
                                            'min' => 'Team size must be at least 1.',
                                        ]),

                                    Forms\Components\TextInput::make('founded_year')
                                        ->label('Founded year')
                                        ->numeric()
                                        ->minValue(1800)
                                        ->maxValue((int) date('Y'))
                                        ->nullable()
                                        ->validationMessages([
                                            'numeric' => 'Founded year must be a number.',
                                            'min' => 'Founded year seems too old.',
                                            'max' => 'Founded year cannot be in the future.',
                                        ]),
                                ])
                                ->columns(2),
                        ]),

                    Forms\Components\Tabs\Tab::make('Address & contact')
                        ->schema([
                            Forms\Components\Section::make('Headquarters address')
                                ->schema([
                                    Forms\Components\TextInput::make('country_code')
                                        ->label('Country code')
                                        ->required()
                                        ->maxLength(2)
                                        ->default('SK')
                                        ->helperText('ISO 3166-1 alpha-2 (e.g. SK, CH).')
                                        ->validationMessages([
                                            'required' => 'Country code is required.',
                                            'max' => 'Country code must be 2 characters.',
                                        ]),

                                    Forms\Components\TextInput::make('region')
                                        ->label('Region')
                                        ->nullable()
                                        ->maxLength(255),

                                    Forms\Components\TextInput::make('city')
                                        ->label('City')
                                        ->nullable()
                                        ->maxLength(255),

                                    Forms\Components\TextInput::make('postal_code')
                                        ->label('Postal code')
                                        ->nullable()
                                        ->maxLength(30),

                                    Forms\Components\TextInput::make('street')
                                        ->label('Street')
                                        ->nullable()
                                        ->maxLength(255),
                                ])
                                ->columns(2),

                            Forms\Components\Section::make('Contact person')
                                ->schema([
                                    Forms\Components\TextInput::make('contact_first_name')
                                        ->label('First name')
                                        ->nullable()
                                        ->maxLength(255),

                                    Forms\Components\TextInput::make('contact_last_name')
                                        ->label('Last name')
                                        ->nullable()
                                        ->maxLength(255),

                                    Forms\Components\TextInput::make('contact_email')
                                        ->label('Email')
                                        ->nullable()
                                        ->maxLength(255)
                                        ->email()
                                        ->validationMessages([
                                            'email' => 'Contact email must be a valid email address.',
                                        ]),

                                    Forms\Components\TextInput::make('contact_phone')
                                        ->label('Phone')
                                        ->nullable()
                                        ->maxLength(50),
                                ])
                                ->columns(2),
                        ]),

                    Forms\Components\Tabs\Tab::make('Internal')
                        ->schema([
                            Forms\Components\Section::make('Internal notes')
                                ->schema([
                                    Forms\Components\Textarea::make('notes_internal')
                                        ->label('Notes (internal)')
                                        ->nullable()
                                        ->rows(8)
                                        ->columnSpanFull(),
                                ]),
                        ]),
                ]),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->modifyQueryUsing(function (Builder $query) {
                // Option: later restrict to own company for non-platform admins
                return $query;
            })
            ->columns([
                Tables\Columns\TextColumn::make('legal_name')
                    ->label('Company')
                    ->searchable()
                    ->sortable()
                    ->wrap(),

                Tables\Columns\TextColumn::make('category.name')
                    ->label('Category')
                    ->toggleable()
                    ->sortable()
                    ->searchable(),

                Tables\Columns\TextColumn::make('ico')
                    ->label('ICO')
                    ->sortable()
                    ->searchable(),

                Tables\Columns\TextColumn::make('city')
                    ->label('City')
                    ->toggleable()
                    ->sortable()
                    ->searchable(),

                Tables\Columns\TextColumn::make('country_code')
                    ->label('CC')
                    ->toggleable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('status')
                    ->badge()
                    ->sortable(),

                Tables\Columns\IconColumn::make('active')
                    ->label('Active')
                    ->boolean()
                    ->sortable(),

                Tables\Columns\TextColumn::make('seats_purchased')
                    ->label('Seats')
                    ->sortable()
                    ->toggleable(),

                Tables\Columns\TextColumn::make('seats_locked')
                    ->label('Locked')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),

                Tables\Columns\TextColumn::make('general_email')
                    ->label('Email')
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->searchable(),

                Tables\Columns\TextColumn::make('created_at')
                    ->label('Created')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),

                Tables\Columns\TextColumn::make('updated_at')
                    ->label('Updated')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                SelectFilter::make('category_id')
                    ->label('Category')
                    ->relationship('category', 'name')
                    ->searchable()
                    ->preload(),

                SelectFilter::make('status')
                    ->options([
                        'pending' => 'pending',
                        'active' => 'active',
                        'suspended' => 'suspended',
                    ]),

                TernaryFilter::make('active')
                    ->label('Active')
                    ->boolean(),

                SelectFilter::make('country_code')
                    ->label('Country')
                    ->options([
                        'SK' => 'SK',
                        'CZ' => 'CZ',
                        'AT' => 'AT',
                        'DE' => 'DE',
                        'CH' => 'CH',
                        'PL' => 'PL',
                        'HU' => 'HU',
                    ]),
            ])
            ->actions([
                Tables\Actions\EditAction::make()
                    ->visible(fn () => static::canEdit(null)),

                Tables\Actions\DeleteAction::make()
                    ->visible(fn () => static::canDelete(null)),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make()
                        ->visible(fn () => static::canDelete(null)),
                ]),
            ])
            ->defaultSort('created_at', 'desc');
    }

    public static function getRelations(): array
    {
        return [
            // keep your existing relation manager if present in your project
            // \App\Filament\Resources\CompanyResource\RelationManagers\CompanyUsersRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListCompanies::route('/'),
            'create' => Pages\CreateCompany::route('/create'),
            'edit' => Pages\EditCompany::route('/{record}/edit'),
        ];
    }

    private static function isPlatformAdmin(): bool
    {
        $user = auth()->user();
        if (! $user) {
            return false;
        }

        return method_exists($user, 'hasAnyRole') && $user->hasAnyRole(['platform.super_admin', 'platform.admin']);
    }
}
