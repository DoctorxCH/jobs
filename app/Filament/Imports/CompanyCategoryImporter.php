<?php

namespace App\Filament\Imports;

use App\Models\CompanyCategory;
use Filament\Actions\Imports\Importer;
use Filament\Actions\Imports\ImportColumn;
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

            ImportColumn::make('sort')
                ->numeric()
                ->rules(['nullable', 'integer', 'min:0']),

            ImportColumn::make('description')
                ->rules(['nullable', 'string', 'max:1000']),
        ];
    }

    public function resolveRecord(): ?CompanyCategory
    {
        $name = (string) ($this->data['name'] ?? '');
        $slug = (string) ($this->data['slug'] ?? '');

        $slug = trim($slug) !== '' ? $slug : Str::slug($name);

        // Upsert Logik: gleiche slug => update, sonst create
        return CompanyCategory::firstOrNew(['slug' => $slug]);
    }

    protected function mutateRecordDataUsing(array $data): array
    {
        $name = (string) ($data['name'] ?? '');
        $data['slug'] = trim((string) ($data['slug'] ?? '')) !== ''
            ? (string) $data['slug']
            : Str::slug($name);

        if (! array_key_exists('active', $data) || $data['active'] === null) {
            $data['active'] = true;
        }

        if (! array_key_exists('sort', $data) || $data['sort'] === null) {
            $data['sort'] = 0;
        }

        return $data;
    }
}
