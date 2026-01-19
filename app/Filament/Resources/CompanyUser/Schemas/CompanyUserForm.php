<?php

namespace App\Filament\Resources\CompanyUser\Schemas;

use Filament\Forms\Components\Select;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class CompanyUserForm
{
    public static function schema(Schema $schema): Schema
    {
        return $schema->components([
            Section::make('Team Member')
                ->schema([
                    Grid::make(2)
                        ->schema([
                            Select::make('user_id')
                                ->relationship('user', 'name')
                                ->searchable()
                                ->preload()
                                ->required(),
                            Select::make('role')
                                ->options([
                                    'owner' => 'Owner',
                                    'member' => 'Member',
                                ])
                                ->required(),
                            Select::make('status')
                                ->options([
                                    'active' => 'Active',
                                    'invited' => 'Invited',
                                    'disabled' => 'Disabled',
                                ])
                                ->required(),
                        ]),
                ]),
        ]);
    }
}
