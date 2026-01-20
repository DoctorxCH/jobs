<?php

namespace App\Filament\Resources\CompanyResource\RelationManagers;

use App\Models\CompanyUser;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;

class CompanyUsersRelationManager extends RelationManager
{
    protected static string $relationship = 'users';

    protected static ?string $title = 'Team';

    public function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\Select::make('role')
                ->options([
                    'owner' => 'Owner',
                    'member' => 'Member',
                    'recruiter' => 'Recruiter',
                    'viewer' => 'Viewer',
                ])
                ->required()
                ->disabled(fn ($record) => $record?->pivot?->role === 'owner'),

            Forms\Components\Select::make('status')
                ->options([
                    'active' => 'Active',
                    'invited' => 'Invited',
                    'disabled' => 'Disabled',
                ])
                ->required(),
        ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')->label('Name'),
                Tables\Columns\TextColumn::make('email')->label('Email'),
                Tables\Columns\TextColumn::make('pivot.role')->badge()->label('Role'),
                Tables\Columns\TextColumn::make('pivot.status')->badge()->label('Status'),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DetachAction::make()
                    ->visible(fn ($record) => $record->pivot->role !== 'owner'),
            ]);
    }
}
