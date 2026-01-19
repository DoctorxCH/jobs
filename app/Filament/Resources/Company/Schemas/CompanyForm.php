<?php

namespace App\Filament\Resources\Company\Schemas;

use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class CompanyForm
{
    public static function schema(Schema $schema): Schema
    {
        return $schema->components([
            Section::make('Company Details')
                ->schema([
                    Grid::make(2)
                        ->schema([
                            TextInput::make('name')
                                ->label('Company Name')
                                ->required()
                                ->maxLength(255),
                            TextInput::make('seats_purchased')
                                ->label('Seats Purchased')
                                ->numeric()
                                ->minValue(0)
                                ->required(),
                        ]),
                ]),
        ]);
    }
}
