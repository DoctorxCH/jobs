<?php

namespace App\Filament\Resources;

use App\Filament\Resources\CompanyInvitationResource\Pages;
use App\Models\CompanyInvitation;
use App\Services\PermissionService;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Actions\Action;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class CompanyInvitationResource extends Resource
{
    protected static ?string $model = CompanyInvitation::class;

    protected static ?string $navigationIcon = 'heroicon-o-paper-airplane';
    protected static ?string $navigationLabel = 'Invitations';
    protected static ?string $modelLabel = 'Invitation';
    protected static ?string $pluralModelLabel = 'Invitations';
    protected static ?string $navigationGroup = 'Companies';
    protected static ?int $navigationSort = 40;

    public static function getPermissionKey(): string
    {
        return 'company_invitations';
    }

    public static function canViewAny(): bool
    {
        return PermissionService::can(static::getPermissionKey(), 'view') || static::isCompanyOwner();
    }

    public static function canCreate(): bool
    {
        return PermissionService::can(static::getPermissionKey(), 'create') || static::isCompanyOwner();
    }

    public static function canEdit(Model $record): bool
    {
        return PermissionService::can(static::getPermissionKey(), 'edit') || static::isCompanyOwner();
    }

    public static function canDelete(Model $record): bool
    {
        return PermissionService::can(static::getPermissionKey(), 'delete') || static::isCompanyOwner();
    }

    public static function form(Form $form): Form
    {
        return $form->schema([
            Select::make('company_id')
                ->label('Company')
                ->relationship('company', 'legal_name')
                ->searchable()
                ->preload()
                ->required()
                ->default(fn () => auth()->user()?->company_id)
                ->disabled(fn () => ! static::isPlatformAdmin())
                ->visible(fn () => static::isPlatformAdmin()),

            TextInput::make('email')
                ->label('Email')
                ->email()
                ->required()
                ->maxLength(255),

            Select::make('role')
                ->options([
                    'owner' => 'Owner',
                    'member' => 'Member',
                    'recruiter' => 'Recruiter',
                    'viewer' => 'Viewer',
                ])
                ->required()
                ->default('member'),

            DateTimePicker::make('expires_at')
                ->label('Expires at')
                ->seconds(false)
                ->nullable()
                ->helperText('Leave empty for no expiration.'),
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
                TextColumn::make('company.legal_name')
                    ->label('Company')
                    ->visible(fn () => static::isPlatformAdmin())
                    ->searchable()
                    ->sortable(),

                TextColumn::make('email')
                    ->label('Email')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('role')
                    ->badge()
                    ->sortable(),

                TextColumn::make('status')
                    ->badge()
                    ->state(function (CompanyInvitation $record): string {
                        if ($record->accepted_at !== null) {
                            return 'accepted';
                        }

                        if ($record->expires_at !== null && $record->expires_at->isPast()) {
                            return 'expired';
                        }

                        return 'pending';
                    }),

                TextColumn::make('expires_at')
                    ->label('Expires')
                    ->dateTime()
                    ->sortable(),

                TextColumn::make('created_at')
                    ->label('Created')
                    ->dateTime()
                    ->sortable(),
            ])
            ->actions([
                Tables\Actions\EditAction::make()
                    ->visible(fn (CompanyInvitation $record) => static::canEdit($record)),

                Action::make('withdraw')
                    ->label('Withdraw')
                    ->requiresConfirmation()
                    ->color('danger')
                    ->action(fn (CompanyInvitation $record) => $record->delete())
                    ->visible(fn (CompanyInvitation $record) => $record->accepted_at === null),
            ])
            ->defaultSort('created_at', 'desc');
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListCompanyInvitations::route('/'),
            'create' => Pages\CreateCompanyInvitation::route('/create'),
            'edit' => Pages\EditCompanyInvitation::route('/{record}/edit'),
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

    private static function isCompanyOwner(): bool
    {
        $user = auth()->user();
        if (! $user) {
            return false;
        }

        return (bool) $user->is_company_owner;
    }
}
