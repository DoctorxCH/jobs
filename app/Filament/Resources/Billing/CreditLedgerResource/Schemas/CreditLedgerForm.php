<?php

namespace App\Filament\Resources\Billing\CreditLedgerResource\Schemas;

use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class CreditLedgerForm
{
    public static function schema(Schema $schema): Schema
    {
        return $schema->components([
            Section::make('Credit Ledger Entry')
                ->schema([
                    TextInput::make('company_id')
                        ->disabled(),
                    TextInput::make('change')
                        ->disabled(),
                    TextInput::make('reason')
                        ->disabled(),
                    TextInput::make('reference_type')
                        ->disabled(),
                    TextInput::make('reference_id')
                        ->disabled(),
                    TextInput::make('created_at')
                        ->disabled(),
                ])
                ->columns(2),
        ]);
    }
}
