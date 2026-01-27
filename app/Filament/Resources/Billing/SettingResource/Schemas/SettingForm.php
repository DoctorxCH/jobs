<?php

namespace App\Filament\Resources\Billing\SettingResource\Schemas;

use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class SettingForm
{
    public static function schema(Schema $schema): Schema
    {
        return $schema->components([
            Section::make('Setting')
                ->schema([
                    TextInput::make('key')
                        ->required()
                        ->maxLength(150)
                        ->unique(ignoreRecord: true),
                    Textarea::make('value')
                        ->label('Value (JSON)')
                        ->rows(6)
                        ->required(),
                    Textarea::make('description')
                        ->rows(3),
                ])
                ->columns(1),
        ]);
    }
}
