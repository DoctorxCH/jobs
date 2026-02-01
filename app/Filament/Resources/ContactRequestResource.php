<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ContactRequestResource\Pages;
use App\Models\ContactRequest;
use App\Models\ContactSetting;
use App\Models\User;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class ContactRequestResource extends Resource
{
    protected static ?string $model = ContactRequest::class;

    protected static ?string $navigationIcon = 'heroicon-o-inbox';
    protected static ?string $navigationGroup = 'Contact';
    protected static ?string $navigationLabel = 'Contact requests';
    protected static ?int $navigationSort = 10;

    public static function form(Form $form): Form
    {
        $statusOptions = static::statusOptions();

        return $form->schema([
            Forms\Components\Section::make('Request')
                ->schema([
                    Forms\Components\TextInput::make('name')->disabled()->dehydrated(false),
                    Forms\Components\TextInput::make('email')->disabled()->dehydrated(false),
                    Forms\Components\TextInput::make('subject')->disabled()->dehydrated(false),
                    Forms\Components\Textarea::make('message')
                        ->rows(8)
                        ->disabled()
                        ->dehydrated(false)
                        ->columnSpanFull(),
                ])->columns(2),

            Forms\Components\Section::make('Handling')
                ->schema([
                    Forms\Components\Select::make('status')
                        ->options($statusOptions)
                        ->required(),

                    Forms\Components\Select::make('assigned_to_user_id')
                        ->label('Assigned to')
                        ->options(fn () => User::query()
                            ->whereHas('roles', function ($query) {
                                $query->where('name', 'like', 'platform.%');
                            })
                            ->orderBy('name')
                            ->pluck('name', 'id'))
                        ->searchable()
                        ->nullable(),

                    Forms\Components\Textarea::make('internal_notes')
                        ->rows(5)
                        ->columnSpanFull(),
                ])->columns(2),

            Forms\Components\Section::make('Reply')
                ->schema([
                    Forms\Components\RichEditor::make('reply_body')
                        ->label('Reply')
                        ->columnSpanFull(),
                ]),
        ]);
    }

    public static function table(Table $table): Table
    {
        $statusOptions = static::statusOptions();

        return $table
            ->columns([
                Tables\Columns\TextColumn::make('id')->sortable(),
                Tables\Columns\TextColumn::make('status')->badge()->sortable(),
                Tables\Columns\TextColumn::make('name')->searchable(),
                Tables\Columns\TextColumn::make('email')->searchable(),
                Tables\Columns\TextColumn::make('subject')->limit(40)->wrap(),
                Tables\Columns\TextColumn::make('assigned_to_user_id')
                    ->label('Assigned')
                    ->formatStateUsing(function ($state) {
                        return $state ? User::query()->whereKey($state)->value('name') : '—';
                    }),
                Tables\Columns\TextColumn::make('created_at')->dateTime()->sortable(),
            ])
            ->filters([
                SelectFilter::make('status')->options($statusOptions),
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
            'index' => Pages\ListContactRequests::route('/'),
            'edit' => Pages\EditContactRequest::route('/{record}/edit'),
        ];
    }

    protected static function statusOptions(): array
    {
        $settings = ContactSetting::query()->first();
        $options = $settings?->status_options ?? [];

        if (is_array($options) && count($options) > 0) {
            $mapped = [];
            foreach ($options as $value) {
                $mapped[(string) $value] = (string) $value;
            }
            return $mapped;
        }

        return [
            'new' => 'New',
            'open' => 'Open',
            'pending' => 'Pending',
            'replied' => 'Replied',
            'closed' => 'Closed',
        ];
    }
}
