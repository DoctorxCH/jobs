<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ContactFormResource\Pages;
use App\Models\ContactForm;
use Filament\Forms;
use Filament\Forms\Get;
use Filament\Forms\Set;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Support\Str;

class ContactFormResource extends Resource
{
    protected static ?string $model = ContactForm::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';
    protected static ?string $navigationGroup = 'Contact';
    protected static ?string $navigationLabel = 'Contact forms';
    protected static ?int $navigationSort = 20;

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\Section::make('Form')
                ->schema([
                    Forms\Components\TextInput::make('name')
                        ->required()
                        ->maxLength(150)
                        ->live(onBlur: true)
                        ->afterStateUpdated(function ($state, Set $set, Get $get) {
                            if (! $get('slug')) {
                                $set('slug', Str::slug((string) $state));
                            }
                        }),

                    Forms\Components\TextInput::make('slug')
                        ->required()
                        ->maxLength(100)
                        ->unique(ignoreRecord: true),

                    Forms\Components\Textarea::make('description')
                        ->rows(3)
                        ->nullable()
                        ->columnSpanFull(),

                    Forms\Components\Toggle::make('active')
                        ->default(true),
                ])->columns(2),

            Forms\Components\Section::make('Fields')
                ->schema([
                    Forms\Components\Repeater::make('fields')
                        ->columns(2)
                        ->schema([
                            Forms\Components\TextInput::make('label')
                                ->required()
                                ->maxLength(150)
                                ->live(onBlur: true)
                                ->afterStateUpdated(function ($state, Set $set, Get $get) {
                                    if (! $get('key')) {
                                        $set('key', Str::slug((string) $state, '_'));
                                    }
                                })
                                ->columnSpan(1),

                            Forms\Components\Select::make('type')
                                ->options([
                                    'text' => 'Text',
                                    'email' => 'Email',
                                    'textarea' => 'Textarea',
                                    'select' => 'Select',
                                    'number' => 'Number',
                                    'tel' => 'Phone',
                                    'url' => 'URL',
                                    'date' => 'Date',
                                    'time' => 'Time',
                                    'datetime' => 'Date & Time',
                                    'checkbox' => 'Checkbox',
                                    'radio' => 'Radio',
                                    'file' => 'File Upload',
                                    'hidden' => 'Hidden',
                                    'password' => 'Password',
                                    'color' => 'Color Picker',
                                    'range' => 'Range Slider',
                                ])
                                ->required()
                                ->columnSpan(1),

                            Forms\Components\Select::make('width')
                                ->options([
                                    'full' => 'Full width',
                                    'half' => 'Half width',
                                    'third' => 'One third',
                                ])
                                ->default('full')
                                ->required()
                                ->columnSpan(1),

                            Forms\Components\Toggle::make('required')
                                ->default(false)
                                ->columnSpan(1),

                            Forms\Components\TextInput::make('placeholder')
                                ->maxLength(150)
                                ->nullable()
                                ->columnSpan(1),

                            Forms\Components\TagsInput::make('options')
                                ->label('Select options')
                                ->placeholder('Add option')
                                ->visible(fn ($get) => $get('type') === 'select')
                                ->nullable()
                                ->columnSpan(2),

                            Forms\Components\TextInput::make('key')
                                ->required()
                                ->maxLength(100)
                                ->columnSpan(1),

                        ])
                        ->columnSpanFull(),
                ]),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')->sortable()->searchable(),
                Tables\Columns\TextColumn::make('slug')->sortable(),
                Tables\Columns\IconColumn::make('active')->boolean()->sortable(),
                Tables\Columns\TextColumn::make('updated_at')->dateTime()->sortable()->toggleable(isToggledHiddenByDefault: true),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\DeleteBulkAction::make(),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListContactForms::route('/'),
            'create' => Pages\CreateContactForm::route('/create'),
            'edit' => Pages\EditContactForm::route('/{record}/edit'),
        ];
    }
}
