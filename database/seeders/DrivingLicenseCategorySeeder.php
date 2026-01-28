<?php

namespace Database\Seeders;

use App\Models\DrivingLicenseCategory;
use Illuminate\Database\Seeder;

class DrivingLicenseCategorySeeder extends Seeder
{
    public function run(): void
    {
        $categories = [
            ['code' => 'A', 'label' => 'Motorcycles'],
            ['code' => 'B', 'label' => 'Passenger vehicles'],
            ['code' => 'C', 'label' => 'Trucks'],
            ['code' => 'D', 'label' => 'Buses'],
            ['code' => 'E', 'label' => 'Trailers'],
            ['code' => 'T', 'label' => 'Tractors'],
        ];

        foreach ($categories as $category) {
            DrivingLicenseCategory::query()->updateOrCreate(
                ['code' => $category['code']],
                ['label' => $category['label']]
            );
        }
    }
}
