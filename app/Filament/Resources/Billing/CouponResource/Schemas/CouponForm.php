<?php

namespace App\Filament\Resources\Billing\CouponResource\Schemas;

use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class CouponForm
{
    public static function schema(Schema $schema): Schema
    {
        return $schema->components([
            Section::make('Coupon')
                ->schema([
                    TextInput::make('code')
                        ->required()
                        ->maxLength(100)
                        ->unique(ignoreRecord: true),
                    TextInput::make('name')
                        ->required()
                        ->maxLength(255),
                    Textarea::make('description')
                        ->columnSpanFull(),
                    Select::make('discount_type')
                        ->options([
                            'percent' => 'Percent',
                            'fixed' => 'Fixed',
                        ])
                        ->required(),
                    TextInput::make('discount_value')
                        ->numeric()
                        ->required(),
                    TextInput::make('currency')
                        ->maxLength(3),
                    DateTimePicker::make('valid_from'),
                    DateTimePicker::make('valid_to'),
                    TextInput::make('min_cart_amount_minor')
                        ->numeric(),
                    TextInput::make('max_discount_amount_minor')
                        ->numeric(),
                    TextInput::make('usage_limit_total')
                        ->numeric(),
                    TextInput::make('usage_limit_per_company')
                        ->numeric(),
                    Toggle::make('stackable')
                        ->default(false),
                    Toggle::make('active')
                        ->default(true),
                ])
                ->columns(2),
            Section::make('Scopes')
                ->schema([
                    Repeater::make('scopes')
                        ->relationship()
                        ->schema([
                            Select::make('scope_type')
                                ->options([
                                    'global' => 'Global',
                                    'company' => 'Company',
                                    'product' => 'Product',
                                    'category' => 'Category',
                                ])
                                ->required(),
                            TextInput::make('scope_id')
                                ->numeric()
                                ->nullable(),
                        ])
                        ->columns(2),
                ]),
        ]);
    }
}
