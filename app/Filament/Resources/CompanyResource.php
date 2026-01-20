<?php

namespace App\Filament\Resources;

use App\Filament\Resources\CompanyResource\Pages;
use App\Models\Company;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Support\Str;
use App\Filament\Resources\CompanyResource\RelationManagers\CompanyUsersRelationManager;
use App\Services\PermissionService;

class CompanyResource extends Resource
{
    protected static ?string $model = Company::class;

    protected static ?string $navigationIcon = 'heroicon-o-building-office';
    protected static ?string $navigationGroup = 'Companies';
    protected static ?string $navigationLabel = 'Companies';

    public static function canViewAny(): bool
    {
        return auth()->check();
    }

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\Section::make('Company')
                ->schema([
                    Forms\Components\TextInput::make('legal_name')
                        ->required()
                        ->live(onBlur: true)
                        ->afterStateUpdated(fn ($state, callable $set) =>
                            $set('slug', Str::slug($state))
                        ),

                    Forms\Components\TextInput::make('slug')
                        ->required()
                        ->unique(ignoreRecord: true),

                    Forms\Components\TextInput::make('ico')
                        ->required()
                        ->regex('/^\d{8}$/')
                        ->unique(ignoreRecord: true),

                    Forms\Components\TextInput::make('dic')
                        ->nullable()
                        ->regex('/^\d{10}$/')
                        ->unique(ignoreRecord: true),

                    Forms\Components\TextInput::make('ic_dph')
                        ->nullable()
                        ->regex('/^SK\d{10}$/')
                        ->unique(ignoreRecord: true),

                    Forms\Components\TextInput::make('founded_year')
                        ->numeric()
                        ->minValue(1800)
                        ->maxValue((int) date('Y'))
                        ->nullable(),

                    Forms\Components\TextInput::make('team_size')
                        ->numeric()
                        ->nullable(),

                    Forms\Components\Textarea::make('notes_internal')
                        ->columnSpanFull(),
                ])->columns(2),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('legal_name')->searchable(),
                Tables\Columns\TextColumn::make('ico'),
                Tables\Columns\TextColumn::make('status'),
                Tables\Columns\TextColumn::make('created_at')->dateTime(),
            ])
            ->actions([
                Tables\Actions\EditAction::make()
                    ->visible(fn () => PermissionService::can(static::getPermissionKey(), 'edit')),
            ]);
    }
    public static function getRelations(): array
    {
        return [
            CompanyUsersRelationManager::class,
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

    public static function getPermissionKey(): string
    {
        return 'companies';
    }
}
