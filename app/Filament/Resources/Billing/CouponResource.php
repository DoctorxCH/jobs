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

    protected static ?string $navigationIcon = 'heroicon-o-ticket';
    protected static ?string $navigationGroup = 'Billing';
    protected static ?string $navigationLabel = 'Coupons';
    protected static ?int $navigationSort = 20;

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\Section::make('Coupon')
                ->schema([
                    Forms\Components\TextInput::make('code')
                        ->required()
                        ->maxLength(50)
                        ->unique(ignoreRecord: true)
                        ->helperText('Uppercase recommended.'),

                    Forms\Components\TextInput::make('name')
                        ->required()
                        ->maxLength(150),

                    Forms\Components\Textarea::make('description')
                        ->rows(3)
                        ->nullable(),
                ])->columns(2),

            Forms\Components\Section::make('Discount')
                ->schema([
                    Forms\Components\Select::make('discount_type')
                        ->options([
                            'fixed' => 'Fixed',
                            'percent' => 'Percent',
                        ])
                        ->required(),

                    Forms\Components\TextInput::make('discount_value')
                        ->required()
                        ->numeric()
                        ->minValue(0),

                    Forms\Components\TextInput::make('currency')
                        ->maxLength(3)
                        ->placeholder('EUR'),

                    Forms\Components\TextInput::make('min_cart_amount_minor')
                        ->label('Min cart amount (minor)')
                        ->numeric()
                        ->minValue(0)
                        ->nullable(),

                    Forms\Components\TextInput::make('max_discount_amount_minor')
                        ->label('Max discount amount (minor)')
                        ->numeric()
                        ->minValue(0)
                        ->nullable(),
                ])->columns(2),

            Forms\Components\Section::make('Validity & limits')
                ->schema([
                    Forms\Components\DateTimePicker::make('valid_from')
                        ->seconds(false)
                        ->nullable(),

                    Forms\Components\DateTimePicker::make('valid_to')
                        ->seconds(false)
                        ->nullable(),

                    Forms\Components\TextInput::make('usage_limit_total')
                        ->numeric()
                        ->minValue(0)
                        ->nullable(),

                    Forms\Components\TextInput::make('usage_limit_per_company')
                        ->numeric()
                        ->minValue(0)
                        ->nullable(),

                    Forms\Components\TextInput::make('usage_limit_per_user')
                        ->numeric()
                        ->minValue(0)
                        ->nullable(),
                ])->columns(2),

            Forms\Components\Section::make('State')
                ->schema([
                    Forms\Components\Toggle::make('stackable')
                        ->default(false),

                    Forms\Components\Toggle::make('active')
                        ->default(true),
                ])->columns(2),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('code')->sortable()->searchable(),
                Tables\Columns\TextColumn::make('name')->sortable()->searchable(),
                Tables\Columns\TextColumn::make('discount_type')->sortable(),
                Tables\Columns\TextColumn::make('discount_value')->sortable(),
                Tables\Columns\TextColumn::make('currency')->sortable(),
                Tables\Columns\IconColumn::make('active')->boolean()->sortable(),
                Tables\Columns\IconColumn::make('stackable')->boolean()->sortable(),
                Tables\Columns\TextColumn::make('valid_from')->dateTime()->sortable(),
                Tables\Columns\TextColumn::make('valid_to')->dateTime()->sortable(),
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
