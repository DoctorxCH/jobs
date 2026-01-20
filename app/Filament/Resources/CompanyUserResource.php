<?php

namespace App\Filament\Resources;

use App\Filament\Resources\CompanyUserResource\Pages;
use App\Models\Company;
use App\Models\CompanyUser;
use App\Models\User;
use App\Services\PermissionService;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;

class CompanyUserResource extends Resource
{
    protected static ?string $model = CompanyUser::class;

    protected static ?string $navigationIcon = 'heroicon-o-user-group';
    protected static ?string $navigationLabel = 'Company Team';
    protected static ?string $modelLabel = 'Company Member';
    protected static ?string $pluralModelLabel = 'Company Members';
    protected static ?string $navigationGroup = 'Companies';

    public static function getPermissionKey(): string
    {
        return 'company_users';
    }

    public static function canViewAny(): bool
    {
        return PermissionService::can(static::getPermissionKey(), 'view');
    }

    public static function canCreate(): bool
    {
        return PermissionService::can(static::getPermissionKey(), 'create');
    }

    public static function canEdit(Model $record): bool
    {
        return PermissionService::can(static::getPermissionKey(), 'edit');
    }

    public static function canDelete(Model $record): bool
    {
        return PermissionService::can(static::getPermissionKey(), 'delete');
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make('Membership')
                    ->schema([
                        Select::make('company_id')
                            ->label('Company')
                            ->relationship('company', 'legal_name')
                            ->searchable()
                            ->preload()
                            ->required()
                            ->disabled(fn () => ! static::isPlatformAdmin())
                            ->default(fn () => auth()->user()?->company_id)
                            ->rules([
                                function (string $attribute, $value, \Closure $fail) {
                                    $company = Company::find($value);
                                    if (! $company) {
                                        return;
                                    }

                                    if (! $company->hasFreeSeats()) {
                                        $fail('No free seats available for this company.');
                                    }
                                },
                            ])
                            ->helperText('Wenn kein Platform Admin: wird automatisch auf deine Company gesetzt.'),

                        Select::make('user_id')
                            ->label('User')
                            ->relationship('user', 'email')
                            ->searchable()
                            ->preload()
                            ->required()
                            ->getOptionLabelFromRecordUsing(function (User $record): string {
                                $name = trim((string) ($record->name ?? ''));
                                return $name !== '' ? "{$name} ({$record->email})" : $record->email;
                            })
                            ->unique(
                                table: CompanyUser::class,
                                column: 'user_id',
                                ignoreRecord: true
                            )
                            ->helperText('Es muss ein existierender User sein (Team Member hat eigenes Login).'),

                        Select::make('role')
                            ->options([
                                'owner' => 'Owner',
                                'member' => 'Member',
                                'recruiter' => 'Recruiter',
                                'viewer' => 'Viewer',
                            ])
                            ->required()
                            ->default('member')
                            ->disabled(fn ($record) => $record?->role === 'owner' && ! static::isPlatformAdmin())
                            ->helperText('Owner kann Company/Seats verwalten; Member kann posten.'),

                        Select::make('status')
                            ->options([
                                'active' => 'Active',
                                'invited' => 'Invited',
                                'disabled' => 'Disabled',
                            ])
                            ->required()
                            ->default('active'),

                        Toggle::make('set_primary_company')
                            ->label('Als Primary Company setzen')
                            ->default(true)
                            ->helperText('Wenn aktiv: user.company_id wird auf diese Company gesetzt (damit er immer im Namen der Company postet).')
                            ->dehydrated(false),
                    ])
                    ->columns(2),

                Section::make('Invite / Meta')
                    ->schema([
                        TextInput::make('invited_at')
                            ->label('Invited at')
                            ->disabled()
                            ->dehydrated(false)
                            ->visible(fn ($record) => filled($record?->invited_at)),

                        TextInput::make('accepted_at')
                            ->label('Accepted at')
                            ->disabled()
                            ->dehydrated(false)
                            ->visible(fn ($record) => filled($record?->accepted_at)),
                    ])
                    ->columns(2)
                    ->collapsed(),

                Section::make('Actions')
                    ->schema([
                        Placeholder::make('hint')
                            ->content('Hinweis: Seats/Einladungen werden im echten Invite-Flow ueber CompanyInvitations gemacht. Dieses CRUD ist fuer Admin/Debug & manuelles Management.')
                            ->columnSpanFull(),
                    ])
                    ->collapsed(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->modifyQueryUsing(function (Builder $query) {
                $user = auth()->user();
                if (! $user) {
                    return $query->whereRaw('1=0');
                }

                if (static::isPlatformAdmin()) {
                    return $query;
                }

                if (! empty($user->company_id)) {
                    return $query->where('company_id', $user->company_id);
                }

                return $query->whereRaw('1=0');
            })
            ->columns([
                Tables\Columns\TextColumn::make('company.legal_name')
                    ->label('Company')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('user.name')
                    ->label('Name')
                    ->toggleable()
                    ->searchable(),

                Tables\Columns\TextColumn::make('user.email')
                    ->label('Email')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('role')
                    ->badge()
                    ->sortable(),

                Tables\Columns\TextColumn::make('status')
                    ->badge()
                    ->sortable(),

                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),

                Tables\Columns\TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                SelectFilter::make('company_id')
                    ->label('Company')
                    ->relationship('company', 'legal_name')
                    ->searchable()
                    ->preload()
                    ->visible(fn () => static::isPlatformAdmin()),

                SelectFilter::make('role')
                    ->options([
                        'owner' => 'Owner',
                        'member' => 'Member',
                        'recruiter' => 'Recruiter',
                        'viewer' => 'Viewer',
                    ]),

                SelectFilter::make('status')
                    ->options([
                        'active' => 'Active',
                        'invited' => 'Invited',
                        'disabled' => 'Disabled',
                    ]),
            ])
            ->actions([
                Tables\Actions\EditAction::make()
                    ->visible(fn () => static::canEdit(new CompanyUser())),

                Tables\Actions\DeleteAction::make()
                    ->visible(fn (CompanyUser $record) => static::isPlatformAdmin() || ($record->role !== 'owner' && static::canDelete($record))),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make()
                        ->visible(fn () => static::isPlatformAdmin()),
                ]),
            ])
            ->defaultSort('created_at', 'desc');
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListCompanyUsers::route('/'),
            'create' => Pages\CreateCompanyUser::route('/create'),
            'edit' => Pages\EditCompanyUser::route('/{record}/edit'),
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
