<?php

namespace App\Filament\Resources;

use App\Filament\Resources\PlatformUserResource\Pages;
use App\Models\User;
use App\Services\PermissionService;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Forms\Get;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Support\Str;

class PlatformUserResource extends Resource
{
    protected static ?string $model = User::class;

    protected static ?string $navigationIcon = 'heroicon-o-users';
    protected static ?string $navigationLabel = 'Platform Users';
    protected static ?string $modelLabel = 'Platform User';
    protected static ?string $pluralModelLabel = 'Platform Users';
    protected static ?string $navigationGroup = 'System';

    public static function getPermissionKey(): string
    {
        return 'platform_users';
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
            Forms\Components\Section::make('User')
                ->schema([
                    Forms\Components\TextInput::make('name')
                        ->required()
                        ->maxLength(255),

                    Forms\Components\TextInput::make('email')
                        ->email()
                        ->required()
                        ->maxLength(255)
                        ->unique(ignoreRecord: true),

                    Forms\Components\TextInput::make('password')
                        ->password()
                        ->revealable()
                        ->dehydrated(fn (Get $get) => filled($get('password')))
                        ->helperText('Leer lassen, um Passwort beim Bearbeiten nicht zu ändern.')
                        ->suffixAction(
                            Forms\Components\Actions\Action::make('generatePassword')
                                ->label('Generate')
                                ->action(function (callable $set) {
                                    $pw = Str::password(20, true, true, false, false);
                                    $set('password', $pw);
                                })
                        ),
                ])
                ->columns(2),

            Forms\Components\Section::make('Roles')
                ->schema([
                    Forms\Components\Select::make('roles')
                        ->label('Platform Roles')
                        ->relationship(
                            name: 'roles',
                            titleAttribute: 'name',
                            modifyQueryUsing: fn ($query) => $query
                                ->where('name', 'like', 'platform.%')
                                ->orderBy('name')
                        )
                        ->preload()
                        ->searchable()
                        ->multiple()
                        ->helperText('Nur platform.* Rollen werden hier angezeigt. Company-Rollen laufen ueber Company Team (Pivot).'),
                ]),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')->searchable()->sortable(),
                Tables\Columns\TextColumn::make('email')->searchable()->sortable(),
                Tables\Columns\TextColumn::make('roles.name')
                    ->label('Roles')
                    ->badge()
                    ->separator(', ')
                    ->sortable(),
                Tables\Columns\TextColumn::make('created_at')->dateTime()->sortable(),
            ])
            ->defaultSort('created_at', 'desc')
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
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListPlatformUsers::route('/'),
            'create' => Pages\CreatePlatformUser::route('/create'),
            'edit' => Pages\EditPlatformUser::route('/{record}/edit'),
        ];
    }
}
