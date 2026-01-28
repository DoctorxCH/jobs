<?php

namespace App\Filament\Resources;

use App\Filament\Resources\JobLanguageResource\Pages;
use App\Models\JobLanguage;
use App\Services\PermissionService;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class JobLanguageResource extends Resource
{
    protected static ?string $model = JobLanguage::class;

    protected static ?string $navigationGroup = 'Jobs';
    protected static ?string $navigationLabel = 'Job Languages';
    protected static ?string $modelLabel = 'Job Language';
    protected static ?string $pluralModelLabel = 'Job Languages';
    protected static ?string $navigationIcon = 'heroicon-o-language';

    public static function getPermissionKey(): string
    {
        return 'job-languages';
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
                    Forms\Components\Select::make('job_id')
                        ->relationship('job', 'title')
                        ->searchable()
                        ->required(),

                    Forms\Components\TextInput::make('language_code')
                        ->required()
                        ->maxLength(2),

                    Forms\Components\Select::make('level')
                        ->options([
                            'A1' => 'A1',
                            'A2' => 'A2',
                            'B1' => 'B1',
                            'B2' => 'B2',
                            'C1' => 'C1',
                            'C2' => 'C2',
                            'native' => 'Native',
                        ])
                        ->required(),
                ])
                ->columns(3),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->defaultSort('id', 'desc')
            ->columns([
                Tables\Columns\TextColumn::make('job.title')
                    ->label('Job')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('language_code')
                    ->sortable(),
                Tables\Columns\TextColumn::make('level')
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
            'index' => Pages\ListJobLanguages::route('/'),
            'create' => Pages\CreateJobLanguage::route('/create'),
            'edit' => Pages\EditJobLanguage::route('/{record}/edit'),
        ];
    }
}
