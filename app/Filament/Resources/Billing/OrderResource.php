<?php

namespace App\Filament\Resources\Billing;

use App\Filament\Resources\Billing\OrderResource\Pages;
use App\Models\Billing\Order;
use App\Services\Billing\OrderService;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class OrderResource extends Resource
{
    protected static ?string $model = Order::class;

    protected static ?string $navigationGroup = 'Finance';
    protected static ?string $navigationLabel = 'Orders';
    protected static ?string $navigationIcon = 'heroicon-o-shopping-cart';

    public static function canCreate(): bool
    {
        return false;
    }

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\TextInput::make('status')->disabled(),
            Forms\Components\TextInput::make('currency')->disabled(),
            Forms\Components\TextInput::make('subtotal_net_minor')->disabled(),
            Forms\Components\TextInput::make('tax_minor')->disabled(),
            Forms\Components\TextInput::make('total_gross_minor')->disabled(),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('id')->sortable(),
                Tables\Columns\TextColumn::make('company.legal_name')->label('Company')->searchable(),
                Tables\Columns\TextColumn::make('user.email')->label('User')->searchable(),
                Tables\Columns\TextColumn::make('status')->badge(),
                Tables\Columns\TextColumn::make('total_gross_minor')->label('Total'),
                Tables\Columns\TextColumn::make('created_at')->dateTime(),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\Action::make('createInvoice')
                    ->label('Create Invoice')
                    ->visible(fn (Order $record) => $record->status === 'awaiting_invoice')
                    ->action(function (Order $record) {
                        app(OrderService::class)->createInvoiceForOrder($record);
                    }),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListOrders::route('/'),
            'view' => Pages\ViewOrder::route('/{record}'),
        ];
    }
}
