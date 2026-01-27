<?php

namespace App\Filament\Resources\Billing\ProductPriceResource\Schemas;

use App\Models\Billing\Product;
use App\Models\Billing\TaxClass;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class ProductPriceForm
{
    public static function schema(Schema $schema): Schema
    {
        return $schema->components([
            Section::make('Pricing')
                ->schema([
                    Select::make('product_id')
                        ->label('Product')
                        ->options(Product::query()->orderBy('name')->pluck('name', 'id'))
                        ->searchable()
                        ->required(),
                    Select::make('tax_class_id')
                        ->label('Tax Class')
                        ->options(TaxClass::query()->orderBy('name')->pluck('name', 'id'))
                        ->searchable()
                        ->required(),
                    TextInput::make('currency')
                        ->required()
                        ->maxLength(3),
                    TextInput::make('unit_net_amount_minor')
                        ->numeric()
                        ->required(),
                    DateTimePicker::make('valid_from'),
                    DateTimePicker::make('valid_to'),
                    Toggle::make('active')
                        ->default(true),
                ])
                ->columns(2),
        ]);
    }
}
