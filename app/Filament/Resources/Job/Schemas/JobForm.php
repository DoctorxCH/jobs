<?php

namespace App\Filament\Resources\Job\Schemas;

use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\KeyValue;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class JobForm
{
    public static function schema(Schema $schema): Schema
    {
        return $schema->components([
            Section::make('Core Details')
                ->schema([
                    Grid::make(2)
                        ->schema([
                            TextInput::make('title')
                                ->required()
                                ->maxLength(255),
                            TextInput::make('short_description')
                                ->label('Short Description')
                                ->maxLength(30),
                            TextInput::make('occupation')
                                ->label('Occupation (ISK/ISCO)')
                                ->maxLength(255),
                            TextInput::make('category')
                                ->maxLength(255),
                            Textarea::make('description')
                                ->columnSpanFull(),
                        ]),
                ]),
            Section::make('Location & Workload')
                ->schema([
                    Grid::make(3)
                        ->schema([
                            TextInput::make('location')
                                ->maxLength(255),
                            Toggle::make('is_remote')
                                ->label('Remote'),
                            TextInput::make('workload')
                                ->label('Pensum')
                                ->maxLength(255),
                        ]),
                ]),
            Section::make('Requirements')
                ->schema([
                    Grid::make(2)
                        ->schema([
                            Select::make('education_level')
                                ->label('Education Level')
                                ->options([
                                    'none' => 'None',
                                    'secondary' => 'Secondary',
                                    'tertiary' => 'Tertiary',
                                ])
                                ->native(false),
                            KeyValue::make('languages')
                                ->label('Languages (Level A1–C2)')
                                ->keyLabel('Language')
                                ->valueLabel('Level'),
                            KeyValue::make('requirements')
                                ->label('Requirements (Certificates, Licenses)')
                                ->keyLabel('Requirement')
                                ->valueLabel('Details'),
                        ]),
                ]),
            Section::make('Contact & Media')
                ->schema([
                    Grid::make(2)
                        ->schema([
                            TextInput::make('contact_name')
                                ->maxLength(255),
                            TextInput::make('contact_email')
                                ->email()
                                ->maxLength(255),
                            KeyValue::make('attachments')
                                ->label('Attachments (PDFs)')
                                ->keyLabel('Name')
                                ->valueLabel('URL/Path'),
                        ]),
                ]),
            Section::make('Publishing')
                ->schema([
                    Grid::make(3)
                        ->schema([
                            Select::make('status')
                                ->options([
                                    'pending_created' => 'Pending Created',
                                    'active' => 'Active',
                                    'expired' => 'Expired',
                                ])
                                ->native(false)
                                ->required(),
                            Toggle::make('is_featured')
                                ->label('Featured'),
                            Toggle::make('is_top')
                                ->label('Top Listing'),
                            DateTimePicker::make('expires_at')
                                ->label('Expires At'),
                        ]),
                ]),
        ]);
    }
}
