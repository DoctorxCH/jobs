<?php

namespace App\Filament\Resources\Billing\PaymentResource\Schemas;

use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class PaymentForm
{
    public static function schema(Schema $schema): Schema
    {
        return $schema->components([
            Section::make('Payment')
                ->schema([
                    TextInput::make('invoice_id')
                        ->disabled(),
                    TextInput::make('method')
                        ->disabled(),
                    TextInput::make('status')
                        ->disabled(),
                    TextInput::make('amount_minor')
                        ->disabled(),
                    TextInput::make('currency')
                        ->disabled(),
                    TextInput::make('bank_reference')
                        ->disabled(),
                ])
                ->columns(2),
        ]);
    }
}
