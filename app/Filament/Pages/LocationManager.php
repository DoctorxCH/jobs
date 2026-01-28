<?php

namespace App\Filament\Pages;

use App\Models\City;
use App\Models\Country;
use App\Models\Region;
use Filament\Forms;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Notifications\Notification;
use Filament\Pages\Page;

class LocationManager extends Page implements HasForms
{
    use InteractsWithForms;

    protected static ?string $navigationGroup = 'Jobs';
    protected static ?string $navigationLabel = 'Locations';
    protected static ?string $navigationIcon = 'heroicon-o-map';

    protected static string $view = 'filament.pages.location-manager';

    public ?int $region_country_id = null;
    public ?int $city_region_id = null;

    public string $country_code = '';
    public string $country_name = '';

    public string $region_name = '';
    public string $city_name = '';

    public function mount(): void
    {
        $this->form->fill();
    }

    public function form(Forms\Form $form): Forms\Form
    {
        return $form->schema([
            Forms\Components\Section::make('1) Country')
                ->schema([
                    Forms\Components\TextInput::make('country_code')
                        ->label('Code (ISO2)')
                        ->required()
                        ->maxLength(2),
                    Forms\Components\TextInput::make('country_name')
                        ->label('Name')
                        ->required()
                        ->maxLength(255),
                    Forms\Components\Actions::make([
                        Forms\Components\Actions\Action::make('createCountry')
                            ->label('Add Country')
                            ->action('createCountry'),
                    ]),
                ])->columns(3),

            Forms\Components\Section::make('2) Region')
                ->schema([
                    Forms\Components\Select::make('region_country_id')
                        ->label('Country')
                        ->options(fn () => Country::query()->orderBy('name')->pluck('name', 'id')->toArray())
                        ->searchable()
                        ->required(),
                    Forms\Components\TextInput::make('region_name')
                        ->label('Region name')
                        ->required()
                        ->maxLength(255),
                    Forms\Components\Actions::make([
                        Forms\Components\Actions\Action::make('createRegion')
                            ->label('Add Region')
                            ->action('createRegion'),
                    ]),
                ])->columns(3),

            Forms\Components\Section::make('3) City')
                ->schema([
                    Forms\Components\Select::make('city_region_id')
                        ->label('Region')
                        ->options(function () {
                            return Region::query()
                                ->with('country')
                                ->orderBy('name')
                                ->get()
                                ->mapWithKeys(fn (Region $r) => [$r->id => ($r->country?->name ? $r->country->name.' — ' : '').$r->name])
                                ->toArray();
                        })
                        ->searchable()
                        ->required(),
                    Forms\Components\TextInput::make('city_name')
                        ->label('City name')
                        ->required()
                        ->maxLength(255),
                    Forms\Components\Actions::make([
                        Forms\Components\Actions\Action::make('createCity')
                            ->label('Add City')
                            ->action('createCity'),
                    ]),
                ])->columns(3),
        ]);
    }

    public function createCountry(): void
    {
        $code = strtoupper(trim($this->country_code));
        $name = trim($this->country_name);

        Country::query()->updateOrCreate(
            ['code' => $code],
            ['name' => $name, 'is_active' => true, 'sort' => 0]
        );

        $this->country_code = '';
        $this->country_name = '';

        Notification::make()->title('Country saved')->success()->send();
    }

    public function createRegion(): void
    {
        $countryId = (int) $this->region_country_id;
        $name = trim($this->region_name);

        Region::query()->create([
            'country_id' => $countryId,
            'name' => $name,
            'is_active' => true,
            'sort' => 0,
        ]);

        $this->region_name = '';

        Notification::make()->title('Region saved')->success()->send();
    }

    public function createCity(): void
    {
        $regionId = (int) $this->city_region_id;
        $name = trim($this->city_name);

        City::query()->create([
            'region_id' => $regionId,
            'name' => $name,
            'is_active' => true,
            'sort' => 0,
        ]);

        $this->city_name = '';

        Notification::make()->title('City saved')->success()->send();
    }
}
