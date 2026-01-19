<?php

namespace App\Filament\Resources\PlatformUser\Schemas;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Illuminate\Support\Facades\Hash;

class PlatformUserForm
{
    public static function schema(Schema $schema): Schema
    {
        return $schema->components([
            Section::make('User Details')
                ->schema([
                    Grid::make(2)
                        ->schema([
                            TextInput::make('name')
                                ->required()
                                ->maxLength(255),
                            TextInput::make('email')
                                ->email()
                                ->required()
                                ->maxLength(255),
                            TextInput::make('password')
                                ->password()
                                ->dehydrateStateUsing(fn (?string $state) => filled($state) ? Hash::make($state) : null)
                                ->dehydrated(fn (?string $state) => filled($state))
                                ->required(fn (string $context): bool => $context === 'create')
                                ->maxLength(255),
                            Select::make('company_id')
                                ->label('Active Company')
                                ->relationship('company', 'name')
                                ->searchable()
                                ->preload()
                                ->nullable(),
                        ]),
                ]),
        ]);
    }
}
