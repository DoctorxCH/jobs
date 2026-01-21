<?php

namespace App\Filament\Resources;

use App\Filament\Resources\CompanyCategoryResource\Pages;
use App\Models\CompanyCategory;
use App\Services\PermissionService;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Support\Str;

class CompanyCategoryResource extends Resource
{
    protected static ?string $model = CompanyCategory::class;

    protected static ?string $navigationGroup = 'Companies';
    protected static ?string $navigationLabel = 'Company Categories';
    protected static ?string $modelLabel = 'Company Category';
    protected static ?string $pluralModelLabel = 'Company Categories';
    protected static ?string $navigationIcon = 'heroicon-o-tag';

    public static function getPermissionKey(): string
    {
        return 'company_categories';
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
            Forms\Components\Section::make('Basics')
                ->schema([
                    Forms\Components\TextInput::make('name')
                        ->label('Name')
                        ->required()
                        ->maxLength(255)
                        ->live(onBlur: true)
                        ->afterStateUpdated(function ($state, callable $set, $get) {
                            $currentSlug = (string) ($get('slug') ?? '');
                            $currentName = (string) ($get('name') ?? '');
                            if ($currentSlug === '' || $currentSlug === Str::slug($currentName)) {
                                $set('slug', Str::slug((string) $state));
                            }
                        }),

                    Forms\Components\TextInput::make('slug')
                        ->label('Slug')
                        ->required()
                        ->maxLength(255)
                        ->unique(ignoreRecord: true),

                    Forms\Components\Toggle::make('active')
                        ->label('Active')
                        ->default(true),
                ])
                ->columns(2),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            // safe default sort: created_at existiert praktisch immer; falls nicht, kommentieren.
            ->defaultSort('created_at', 'desc')
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->label('Name')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('slug')
                    ->label('Slug')
                    ->searchable()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),

                Tables\Columns\IconColumn::make('active')
                    ->label('Active')
                    ->boolean()
                    ->sortable(),

                Tables\Columns\TextColumn::make('updated_at')
                    ->label('Updated')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),

                Tables\Columns\TextColumn::make('created_at')
                    ->label('Created')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                SelectFilter::make('active')
                    ->label('Active')
                    ->options([1 => 'Active', 0 => 'Inactive']),
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

                    Tables\Actions\BulkAction::make('activate')
                        ->label('Set Active')
                        ->requiresConfirmation()
                        ->action(fn ($records) => $records->each->update(['active' => true])),

                    Tables\Actions\BulkAction::make('deactivate')
                        ->label('Set Inactive')
                        ->requiresConfirmation()
                        ->action(fn ($records) => $records->each->update(['active' => false])),
                ]),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index'  => Pages\ListCompanyCategories::route('/'),
            'create' => Pages\CreateCompanyCategory::route('/create'),
            'edit'   => Pages\EditCompanyCategory::route('/{record}/edit'),
        ];
    }
}
