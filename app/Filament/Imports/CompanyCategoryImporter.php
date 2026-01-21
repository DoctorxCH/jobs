<?php

namespace App\Filament\Imports;

use App\Models\CompanyCategory;
use Filament\Actions\Imports\ImportColumn;
use Filament\Actions\Imports\Importer;
use Illuminate\Support\Str;

class CompanyCategoryImporter extends Importer
{
    protected static ?string $model = CompanyCategory::class;

    public static function getColumns(): array
    {
        return [
            ImportColumn::make('name')
                ->requiredMapping()
                ->rules(['required', 'string', 'max:255']),

            ImportColumn::make('slug')
                ->rules(['nullable', 'string', 'max:255']),

            ImportColumn::make('active')
                ->boolean()
                ->rules(['nullable', 'boolean']),
        ];
    }

    public function resolveRecord(): ?CompanyCategory
    {
        $name = (string) ($this->data['name'] ?? '');
        $slug = trim((string) ($this->data['slug'] ?? ''));
        $slug = $slug !== '' ? $slug : Str::slug($name);

        return CompanyCategory::firstOrNew(['slug' => $slug]);
    }

    protected function mutateRecordDataUsing(array $data): array
    {
        $name = (string) ($data['name'] ?? '');
        $slug = trim((string) ($data['slug'] ?? ''));

        $data['slug'] = $slug !== '' ? $slug : Str::slug($name);

        if (! array_key_exists('active', $data) || $data['active'] === null) {
            $data['active'] = true;
        }

        return $data;
    }
}
