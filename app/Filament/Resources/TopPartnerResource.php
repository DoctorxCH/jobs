<?php

namespace App\Filament\Resources;

use App\Filament\Resources\TopPartnerResource\Pages;
use App\Models\TopPartner;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class TopPartnerResource extends Resource
{
    protected static ?string $model = TopPartner::class;

    protected static ?string $navigationIcon = 'heroicon-o-star';
    protected static ?string $navigationGroup = 'Content';
    protected static ?string $navigationLabel = 'Top Partner';
    protected static ?int $navigationSort = 20;

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\Section::make('Top Partner')
                ->schema([
                    Forms\Components\Select::make('company_id')
                        ->label('Company')
                        ->relationship('company', 'legal_name')
                        ->searchable()
                        ->preload()
                        ->required(),

                    Forms\Components\Toggle::make('is_active')
                        ->label('Active / Inactive')
                        ->default(true)
                        ->required(),

                    Forms\Components\DatePicker::make('active_from')
                        ->label('Aktiv seit')
                        ->helperText('Leer = sofort/immer'),

                    Forms\Components\DatePicker::make('active_until')
                        ->label('Aktiv bis')
                        ->helperText('Leer = unbegrenzt'),

                    Forms\Components\FileUpload::make('logo_path')
                        ->label('Logo (Homepage)')
                        ->image()
                        ->imageEditor()
                        ->disk('public')
                        ->directory('top-partners')
                        ->visibility('public')
                        ->required(),

                    Forms\Components\TextInput::make('sort')
                        ->label('Reihenfolge')
                        ->numeric()
                        ->default(0)
                        ->minValue(0),

                    Forms\Components\Toggle::make('is_top_partner')
                        ->label('Flag (intern)')
                        ->default(true)
                        ->disabled()
                        ->dehydrated(true),
                ])
                ->columns(2),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->defaultSort('sort')
            ->columns([
                Tables\Columns\ImageColumn::make('logo_path')
                    ->label('Logo')
                    ->disk('public')
                    ->height(40),

                Tables\Columns\TextColumn::make('company.legal_name')
                    ->label('Company')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\IconColumn::make('is_active')
                    ->label('Active')
                    ->boolean()
                    ->sortable(),

                Tables\Columns\TextColumn::make('active_from')
                    ->label('Seit')
                    ->date()
                    ->sortable(),

                Tables\Columns\TextColumn::make('active_until')
                    ->label('Bis')
                    ->date()
                    ->sortable(),

                Tables\Columns\TextColumn::make('sort')
                    ->label('Sort')
                    ->sortable(),

                Tables\Columns\TextColumn::make('activated_at')
                    ->label('Activated at')
                    ->dateTime()
                    ->sortable(),
            ])
            ->filters([
                Tables\Filters\TernaryFilter::make('is_active')->label('Active'),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListTopPartners::route('/'),
            'create' => Pages\CreateTopPartner::route('/create'),
            'edit' => Pages\EditTopPartner::route('/{record}/edit'),
        ];
    }
}
