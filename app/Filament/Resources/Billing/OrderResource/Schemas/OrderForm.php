<?php

namespace App\Filament\Resources\Billing\OrderResource\Schemas;

use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class OrderForm
{
    public static function schema(Schema $schema): Schema
    {
        return $schema->components([
            Section::make('Order Snapshot')
                ->schema([
                    TextInput::make('company_id')
                        ->label('Company')
                        ->disabled(),
                    TextInput::make('user_id')
                        ->label('User')
                        ->disabled(),
                    TextInput::make('currency')
                        ->disabled(),
                    TextInput::make('status')
                        ->disabled(),
                    TextInput::make('subtotal_net_minor')
                        ->disabled(),
                    TextInput::make('discount_minor')
                        ->disabled(),
                    TextInput::make('tax_minor')
                        ->disabled(),
                    TextInput::make('total_gross_minor')
                        ->disabled(),
                    TextInput::make('tax_rule_applied')
                        ->disabled(),
                    Toggle::make('reverse_charge')
                        ->disabled(),
                ])
                ->columns(2),
        ]);
    }
}
