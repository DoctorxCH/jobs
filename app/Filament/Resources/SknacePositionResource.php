<?php

namespace App\Filament\Resources;

use App\Filament\Resources\SknacePositionResource\Pages;
use App\Models\SknacePosition;
use App\Services\PermissionService;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class SknacePositionResource extends Resource
{
    protected static ?string $model = SknacePosition::class;

    protected static ?string $navigationGroup = 'Jobs';
    protected static ?string $navigationLabel = 'Sknace Positions';
    protected static ?string $modelLabel = 'Sknace Position';
    protected static ?string $pluralModelLabel = 'Sknace Positions';
    protected static ?string $navigationIcon = 'heroicon-o-clipboard-document';

    public static function getPermissionKey(): string
    {
        return 'sknace-positions';
    }

    public static function canViewAny(): bool
    {
        return PermissionService::can(static::getPermissionKey(), 'view');
    }

    public static function canCreate(): bool
    {
        return PermissionService::can(static::getPermissionKey(), 'create');
    }

    public static function canEdit($record): bool
    {
        return PermissionService::can(static::getPermissionKey(), 'edit');
    }

    public static function canDelete($record): bool
    {
        return PermissionService::can(static::getPermissionKey(), 'delete');
    }

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\Section::make('Details')
                ->schema([
                    Forms\Components\TextInput::make('code')
                        ->required()
                        ->maxLength(255)
                        ->unique(ignoreRecord: true),
                    Forms\Components\TextInput::make('title')
                        ->required()
                        ->maxLength(255),
                    Forms\Components\TextInput::make('sort')
                        ->numeric()
                        ->default(0),
                    Forms\Components\Toggle::make('is_active')
                        ->label('Active')
                        ->default(true),
                ])
                ->columns(2),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->defaultSort('sort')
            ->columns([
                Tables\Columns\TextColumn::make('code')
                    ->sortable()
                    ->searchable(),
                Tables\Columns\TextColumn::make('title')
                    ->sortable()
                    ->searchable(),
                Tables\Columns\TextColumn::make('sort')
                    ->sortable(),
                Tables\Columns\IconColumn::make('is_active')
                    ->boolean()
                    ->sortable(),
            ])
            ->actions([
                Tables\Actions\EditAction::make()
                    ->visible(fn () => static::canEdit(null)),
                Tables\Actions\DeleteAction::make()
                    ->visible(fn () => static::canDelete(null)),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make()
                        ->visible(fn () => static::canDelete(null)),
                ]),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListSknacePositions::route('/'),
            'create' => Pages\CreateSknacePosition::route('/create'),
            'edit' => Pages\EditSknacePosition::route('/{record}/edit'),
        ];
    }
}
