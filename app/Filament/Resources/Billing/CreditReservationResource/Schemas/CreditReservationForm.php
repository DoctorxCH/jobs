<?php

namespace App\Filament\Resources\Billing\CreditReservationResource\Schemas;

use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class CreditReservationForm
{
    public static function schema(Schema $schema): Schema
    {
        return $schema->components([
            Section::make('Reservation')
                ->schema([
                    TextInput::make('company_id')
                        ->disabled(),
                    TextInput::make('amount')
                        ->disabled(),
                    TextInput::make('purpose')
                        ->disabled(),
                    TextInput::make('reference_type')
                        ->disabled(),
                    TextInput::make('reference_id')
                        ->disabled(),
                    TextInput::make('status')
                        ->disabled(),
                    TextInput::make('expires_at')
                        ->disabled(),
                ])
                ->columns(2),
        ]);
    }
}
