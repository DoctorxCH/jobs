<?php

namespace App\Filament\Resources\Billing\EntitlementResource\Schemas;

use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class EntitlementForm
{
    public static function schema(Schema $schema): Schema
    {
        return $schema->components([
            Section::make('Entitlement')
                ->schema([
                    TextInput::make('company_id')
                        ->disabled(),
                    TextInput::make('type')
                        ->disabled(),
                    TextInput::make('quantity_total')
                        ->disabled(),
                    TextInput::make('quantity_remaining')
                        ->disabled(),
                    TextInput::make('starts_at')
                        ->disabled(),
                    TextInput::make('ends_at')
                        ->disabled(),
                ])
                ->columns(2),
        ]);
    }
}
