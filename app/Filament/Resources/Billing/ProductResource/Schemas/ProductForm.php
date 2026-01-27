<?php

namespace App\Filament\Resources\Billing\ProductResource\Schemas;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class ProductForm
{
    public static function schema(Schema $schema): Schema
    {
        return $schema->components([
            Section::make('Product Details')
                ->schema([
                    TextInput::make('key')
                        ->required()
                        ->maxLength(150)
                        ->unique(ignoreRecord: true),
                    TextInput::make('name')
                        ->required()
                        ->maxLength(255),
                    Select::make('product_type')
                        ->options([
                            'job_post' => 'Job Post',
                            'credit_pack' => 'Credit Pack',
                            'service' => 'Service',
                        ])
                        ->required(),
                    Toggle::make('active')
                        ->default(true),
                    Textarea::make('description')
                        ->columnSpanFull(),
                ])
                ->columns(2),
        ]);
    }
}
