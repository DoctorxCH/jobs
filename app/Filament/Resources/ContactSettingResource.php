<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ContactSettingResource\Pages;
use App\Models\ContactForm;
use App\Models\ContactSetting;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class ContactSettingResource extends Resource
{
    protected static ?string $model = ContactSetting::class;

    protected static ?string $navigationIcon = 'heroicon-o-cog-6-tooth';
    protected static ?string $navigationGroup = 'Contact';
    protected static ?string $navigationLabel = 'Contact settings';
    protected static ?int $navigationSort = 30;

    public static function canCreate(): bool
    {
        return false;
    }

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\Section::make('Mail')
                ->schema([
                    Forms\Components\TextInput::make('inbox_email')
                        ->label('Inbox email')
                        ->email()
                        ->maxLength(150),

                    Forms\Components\TextInput::make('outbox_email')
                        ->label('Outbox email')
                        ->email()
                        ->maxLength(150),
                ])->columns(2),

            Forms\Components\Section::make('Defaults')
                ->schema([
                    Forms\Components\Select::make('default_form_id')
                        ->label('Default form')
                        ->options(fn () => ContactForm::query()->orderBy('name')->pluck('name', 'id'))
                        ->searchable()
                        ->nullable(),

                    Forms\Components\TagsInput::make('status_options')
                        ->label('Request statuses')
                        ->placeholder('Add status')
                        ->helperText('Used in request status dropdowns and for new requests.'),
                ])->columns(2),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('inbox_email')->label('Inbox email')->sortable(),
                Tables\Columns\TextColumn::make('outbox_email')->label('Outbox email')->sortable(),
                Tables\Columns\TextColumn::make('updated_at')->dateTime()->sortable(),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListContactSettings::route('/'),
            'edit' => Pages\EditContactSetting::route('/{record}/edit'),
        ];
    }
}
