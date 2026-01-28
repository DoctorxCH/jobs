<?php

namespace App\Filament\Resources\Billing;

use App\Filament\Resources\Billing\CouponResource\Pages;
use App\Filament\Resources\Billing\CouponResource\RelationManagers\CouponRedemptionsRelationManager;
use App\Filament\Resources\Billing\CouponResource\RelationManagers\CouponScopesRelationManager;
use App\Models\Billing\Coupon;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class CouponResource extends Resource
{
    protected static ?string $model = Coupon::class;

    protected static ?string $navigationGroup = 'Finance';
    protected static ?string $navigationLabel = 'Coupons';
    protected static ?string $navigationIcon = 'heroicon-o-tag';

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\TextInput::make('code')
                ->required()
                ->maxLength(255)
                ->unique(ignoreRecord: true),
            Forms\Components\TextInput::make('name')
                ->required()
                ->maxLength(255),
            Forms\Components\Textarea::make('description')
                ->rows(3)
                ->nullable(),
            Forms\Components\Select::make('discount_type')
                ->options(['percent' => 'Percent', 'fixed' => 'Fixed'])
                ->required(),
            Forms\Components\TextInput::make('discount_value')
                ->numeric()
                ->required(),
            Forms\Components\TextInput::make('currency')
                ->maxLength(3)
                ->nullable(),
            Forms\Components\DateTimePicker::make('valid_from')->nullable(),
            Forms\Components\DateTimePicker::make('valid_to')->nullable(),
            Forms\Components\TextInput::make('min_cart_amount_minor')->numeric()->nullable(),
            Forms\Components\TextInput::make('max_discount_amount_minor')->numeric()->nullable(),
            Forms\Components\TextInput::make('usage_limit_total')->numeric()->nullable(),
            Forms\Components\TextInput::make('usage_limit_per_company')->numeric()->nullable(),
            Forms\Components\TextInput::make('usage_limit_per_user')->numeric()->nullable(),
            Forms\Components\Toggle::make('stackable')->default(false),
            Forms\Components\Toggle::make('active')->default(true),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('code')->searchable(),
                Tables\Columns\TextColumn::make('name')->searchable(),
                Tables\Columns\TextColumn::make('discount_type'),
                Tables\Columns\TextColumn::make('discount_value'),
                Tables\Columns\IconColumn::make('active')->boolean(),
                Tables\Columns\TextColumn::make('valid_from')->dateTime(),
                Tables\Columns\TextColumn::make('valid_to')->dateTime(),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\DeleteBulkAction::make(),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            CouponScopesRelationManager::class,
            CouponRedemptionsRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListCoupons::route('/'),
            'create' => Pages\CreateCoupon::route('/create'),
            'edit' => Pages\EditCoupon::route('/{record}/edit'),
        ];
    }
}
