<?php

namespace App\Filament\Resources;

use App\Filament\Resources\UserResource\Pages;
use App\Models\User;
use App\Services\PermissionService;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;

class UserResource extends Resource
{
    protected static ?string $model = User::class;

    protected static ?string $navigationIcon = 'heroicon-o-users';
    protected static ?string $navigationLabel = 'Users';
    protected static ?string $modelLabel = 'User';
    protected static ?string $pluralModelLabel = 'Users';
    protected static ?string $navigationGroup = 'Admin';
    protected static ?int $navigationSort = 10;

    public static function getPermissionKey(): string
    {
        return 'users';
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

    /** NUR Kunden-Users (kein platform.*) */
    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->whereDoesntHave('roles', fn (Builder $q) => $q->where('name', 'like', 'platform.%'));
    }

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\Section::make('Account')
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
                        ->label('Password')
                        ->required(fn (string $operation, $get): bool => $operation === 'create' || filled($get('password')))
                        ->minLength(8)
                        ->same('passwordConfirmation')
                        ->dehydrateStateUsing(fn (?string $state): ?string => filled($state) ? Hash::make($state) : null)
                        ->dehydrated(fn (?string $state): bool => filled($state)),

                    Forms\Components\TextInput::make('passwordConfirmation')
                        ->password()
                        ->label('Password (confirm)')
                        ->required(fn (string $operation, $get): bool => $operation === 'create' || filled($get('password')))
                        ->dehydrated(false),

                    Forms\Components\Select::make('roles')
                        ->label('Roles (non-platform)')
                        ->multiple()
                        ->preload()
                        ->searchable()
                        ->options(fn () => Role::query()
                            ->where('name', 'not like', 'platform.%')
                            ->orderBy('name')
                            ->pluck('name', 'name')
                            ->all()
                        )
                        ->saveRelationshipsUsing(function (?User $record, ?array $state): void {
                            if (! $record) return;
                            $record->syncRoles($state ?? []);
                        })
                        ->dehydrateStateUsing(function (?User $record): array {
                            return $record?->getRoleNames()
                                ->filter(fn ($r) => ! str_starts_with($r, 'platform.'))
                                ->values()
                                ->all() ?? [];
                        }),
                ])
                ->columns(2),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')->searchable()->sortable(),
                Tables\Columns\TextColumn::make('email')->searchable()->sortable(),
                Tables\Columns\TextColumn::make('roles.name')->label('Roles')->badge()->separator(', '),
                Tables\Columns\TextColumn::make('created_at')->dateTime()->sortable()->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('role')
                    ->label('Role')
                    ->options(fn () => Role::query()
                        ->where('name', 'not like', 'platform.%')
                        ->orderBy('name')
                        ->pluck('name', 'name')
                        ->all()
                    )
                    ->query(function ($query, array $data) {
                        if (empty($data['value'])) return $query;
                        return $query->whereHas('roles', fn ($q) => $q->where('name', $data['value']));
                    }),
            ])
            ->actions([
                Tables\Actions\EditAction::make()
                    ->visible(fn (User $record) => static::canEdit($record)),
                Tables\Actions\DeleteAction::make()
                    ->visible(fn (User $record) => static::canDelete($record)),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make()
                        ->visible(fn () => static::canDelete(new User())),
                ]),
            ])
            ->defaultSort('id', 'desc');
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListUsers::route('/'),
            'create' => Pages\CreateUser::route('/create'),
            'edit' => Pages\EditUser::route('/{record}/edit'),
        ];
    }
}
