<?php

namespace App\Filament\Resources\CompanyVerificationRequestResource\Pages;

use App\Filament\Resources\CompanyVerificationRequestResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListCompanyVerificationRequests extends ListRecords
{
    protected static string $resource = CompanyVerificationRequestResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\Action::make('refresh')
                ->label('Refresh')
                ->action(fn () => null),
        ];
    }
}
