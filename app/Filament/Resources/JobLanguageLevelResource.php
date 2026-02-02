<?php

namespace App\Filament\Resources;

use App\Filament\Resources\JobLanguageLevelResource\Pages;
use App\Models\JobLanguageLevel;
use App\Services\PermissionService;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class JobLanguageLevelResource extends Resource
{
    protected static ?string $model = JobLanguageLevel::class;

    protected static ?string $navigationGroup = 'Jobs';
    protected static ?string $navigationLabel = 'Job Language Levels';
    protected static ?string $modelLabel = 'Job Language Level';
    protected static ?string $pluralModelLabel = 'Job Language Levels';
    protected static ?string $navigationIcon = 'heroicon-o-adjustments-horizontal';

    public static function getPermissionKey(): string
    {
        return 'job-language-levels';
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
                        ->maxLength(20)
                        ->unique(ignoreRecord: true),

                    Forms\Components\TextInput::make('label')
                        ->required()
                        ->maxLength(100),

                    Forms\Components\TextInput::make('sort')
                        ->numeric()
                        ->default(0),

                    Forms\Components\Toggle::make('is_active')
                        ->default(true),
                ])
                ->columns(4),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->defaultSort('id', 'desc')
            ->columns([
                Tables\Columns\TextColumn::make('code')
                    ->sortable()
                    ->searchable(),
                Tables\Columns\TextColumn::make('label')
                    ->sortable()
                    ->searchable(),
                Tables\Columns\IconColumn::make('is_active')
                    ->boolean(),
                Tables\Columns\TextColumn::make('sort')
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
            'index' => Pages\ListJobLanguageLevels::route('/'),
            'create' => Pages\CreateJobLanguageLevel::route('/create'),
            'edit' => Pages\EditJobLanguageLevel::route('/{record}/edit'),
        ];
    }
}
