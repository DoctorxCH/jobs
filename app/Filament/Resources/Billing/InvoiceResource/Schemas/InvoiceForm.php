<?php

namespace App\Filament\Resources\Billing\InvoiceResource\Schemas;

use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class InvoiceForm
{
    public static function schema(Schema $schema): Schema
    {
        return $schema->components([
            Section::make('Invoice Snapshot')
                ->schema([
                    TextInput::make('company_id')
                        ->label('Company')
                        ->disabled(),
                    TextInput::make('order_id')
                        ->label('Order')
                        ->disabled(),
                    TextInput::make('status')
                        ->disabled(),
                    TextInput::make('currency')
                        ->disabled(),
                    TextInput::make('total_gross_minor_snapshot')
                        ->label('Total (minor)')
                        ->disabled(),
                    TextInput::make('payment_reference')
                        ->disabled(),
                    Toggle::make('reverse_charge_snapshot')
                        ->disabled(),
                ])
                ->columns(2),
        ]);
    }
}
