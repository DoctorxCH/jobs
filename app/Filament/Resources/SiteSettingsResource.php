<?php

namespace App\Filament\Resources;

use App\Filament\Resources\SiteSettingsResource\Pages;
use App\Models\SiteSetting;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Cache;

class SiteSettingsResource extends Resource
{
    protected static ?string $model = SiteSetting::class;
    protected static ?string $navigationIcon = 'heroicon-o-cog-6-tooth';
    protected static ?string $navigationGroup = 'Settings';
    protected static ?string $navigationLabel = 'Site Settings';
    protected static ?string $modelLabel = 'Site Settings';
    protected static ?string $pluralModelLabel = 'Site Settings';
    protected static ?int $navigationSort = 10;

    public static function canCreate(): bool
    {
        return false;
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()->whereKey(1);
    }

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\Section::make('Core')
                ->schema([
                    Forms\Components\Select::make('default_locale')
                        ->label('Default language')
                        ->options([
                            'en' => 'English',
                            'sk' => 'Slovak',
                        ])
                        ->required(),
                ])
                ->columns(2),

            Forms\Components\Section::make('Security')
                ->schema([
                    Forms\Components\TextInput::make('session_rev')
                        ->label('Session rev')
                        ->disabled()
                        ->dehydrated(false),
                    Forms\Components\TextInput::make('force_logout_message')
                        ->label('Force logout message')
                        ->maxLength(255)
                        ->nullable(),
                    Forms\Components\TextInput::make('idle_timeout_minutes')
                        ->label('Idle timeout (minutes)')
                        ->numeric()
                        ->minValue(1)
                        ->nullable(),
                    Forms\Components\TextInput::make('reauth_for_sensitive_minutes')
                        ->label('Reauth for sensitive (minutes)')
                        ->numeric()
                        ->minValue(1)
                        ->nullable(),
                    Forms\Components\TextInput::make('max_login_attempts')
                        ->label('Max login attempts')
                        ->numeric()
                        ->minValue(1)
                        ->nullable(),
                    Forms\Components\TextInput::make('lockout_minutes')
                        ->label('Lockout (minutes)')
                        ->numeric()
                        ->minValue(1)
                        ->nullable(),
                ])
                ->columns(3),

            Forms\Components\Section::make('Ops')
                ->schema([
                    Forms\Components\Toggle::make('maintenance_banner_enabled')
                        ->label('Maintenance banner enabled'),
                    Forms\Components\Textarea::make('maintenance_banner_text')
                        ->label('Maintenance banner text')
                        ->rows(3)
                        ->nullable(),
                ])
                ->columns(2),

            Forms\Components\Section::make('Integrations')
                ->schema([
                    Forms\Components\Toggle::make('superfaktura_enabled')
                        ->label('SuperFaktura enabled'),
                    Forms\Components\TextInput::make('superfaktura_timeout_seconds')
                        ->label('SuperFaktura timeout (seconds)')
                        ->numeric()
                        ->minValue(1)
                        ->nullable(),
                    Forms\Components\TextInput::make('webhook_signing_secret')
                        ->label('Webhook signing secret')
                        ->password()
                        ->revealable()
                        ->afterStateHydrated(fn (Forms\Components\TextInput $component) => $component->state(''))
                        ->dehydrated(fn ($state) => filled($state))
                        ->nullable(),
                ])
                ->columns(3),

            Forms\Components\Section::make('Uploads')
                ->schema([
                    Forms\Components\TextInput::make('max_logo_kb')
                        ->label('Max logo size (KB)')
                        ->numeric()
                        ->minValue(1)
                        ->nullable(),
                ])
                ->columns(2),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('default_locale')->label('Default language'),
                Tables\Columns\TextColumn::make('session_rev')->label('Session rev'),
                Tables\Columns\TextColumn::make('updated_at')->dateTime()->sortable(),
            ])
            ->actions([
                Tables\Actions\Action::make('forceLogout')
                    ->label('Alle abmelden')
                    ->color('danger')
                    ->requiresConfirmation()
                    ->action(function (SiteSetting $record) {
                        $record->increment('session_rev');
                        Cache::forget('site_settings.current');
                        Cache::forget('site_settings.default_locale');
                    })
                    ->successNotificationTitle('Abmeldung ausgelöst'),
                Tables\Actions\EditAction::make(),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListSiteSettings::route('/'),
            'edit' => Pages\EditSiteSettings::route('/{record}/edit'),
        ];
    }
}
