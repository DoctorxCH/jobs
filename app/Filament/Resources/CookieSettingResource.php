<?php

namespace App\Filament\Resources;

use App\Filament\Resources\CookieSettingResource\Pages;
use App\Models\CookieSetting;
use Filament\Forms;
use Filament\Resources\Resource;
use Filament\Tables;

class CookieSettingResource extends Resource
{
    protected static ?string $model = CookieSetting::class;

    protected static ?string $navigationIcon = 'heroicon-o-cog-6-tooth';
    protected static ?string $navigationLabel = 'Cookies';
    protected static ?string $navigationGroup = 'Settings';
    protected static ?int $navigationSort = 10;
    public static function form(Forms\Form $form): Forms\Form
    {
        return $form->schema([
            Forms\Components\Section::make('Banner')
                ->schema([
                    Forms\Components\TextInput::make('title')->required(),
                    Forms\Components\Textarea::make('message')->rows(4)->required(),

                    Forms\Components\TextInput::make('btn_essential')->required(),
                    Forms\Components\TextInput::make('btn_stats')->required(),

                    Forms\Components\Select::make('position')->options([
                        'bottom' => 'Bottom',
                        'top' => 'Top',
                    ])->required(),

                    Forms\Components\Select::make('align')->options([
                        'left' => 'Left',
                        'center' => 'Center',
                        'right' => 'Right',
                    ])->required(),

                    Forms\Components\Select::make('theme')->options([
                        'dark' => 'Dark',
                        'light' => 'Light',
                    ])->required(),
                ]),

            Forms\Components\Section::make('Consent')
                ->schema([
                    Forms\Components\TextInput::make('consent_days')
                        ->numeric()
                        ->minValue(1)
                        ->required(),

                    Forms\Components\TextInput::make('consent_version')
                        ->numeric()
                        ->disabled()
                        ->dehydrated(false),
                ]),

            Forms\Components\Section::make('Google Analytics')
                ->schema([
                    Forms\Components\Toggle::make('ga_enabled'),
                    Forms\Components\TextInput::make('ga_measurement_id')
                        ->placeholder('G-XXXXXXXXXX')
                        ->visible(fn ($get) => (bool) $get('ga_enabled')),
                ]),
        ]);
    }

    public static function table(Tables\Table $table): Tables\Table
    {
        return $table->columns([
            Tables\Columns\TextColumn::make('title'),
            Tables\Columns\IconColumn::make('ga_enabled')->boolean(),
            Tables\Columns\TextColumn::make('consent_days')->label('Days'),
            Tables\Columns\TextColumn::make('consent_version')->label('Version'),
            Tables\Columns\TextColumn::make('updated_at')->dateTime(),
        ])->actions([
            Tables\Actions\EditAction::make(),
        ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListCookieSettings::route('/'),
            'edit' => Pages\EditCookieSetting::route('/{record}/edit'),
        ];
    }
}
