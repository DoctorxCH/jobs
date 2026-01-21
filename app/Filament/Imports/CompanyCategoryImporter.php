<?php

namespace App\Filament\Imports;

use App\Models\CompanyCategory;
use Filament\Actions\Imports\ImportColumn;
use Filament\Actions\Imports\Importer;
use Filament\Actions\Imports\Models\Import;
use Illuminate\Support\Str;

class CompanyCategoryImporter extends Importer
{
    protected static ?string $model = CompanyCategory::class;

    public static function getColumns(): array
    {
        return [
            ImportColumn::make('name')
                ->requiredMapping()
                ->rules(['required', 'string', 'max:255'])
                ->example('IT & Software'),

            ImportColumn::make('slug')
                ->rules(['nullable', 'string', 'max:255'])
                ->example('it-software'),

            // bei dir heisst es jetzt is_active
            ImportColumn::make('is_active')
                ->boolean()
                ->rules(['nullable', 'boolean'])
                ->example('1'),
        ];
    }

    /**
     * Entscheidet, ob ein Record "neu" ist oder ein bestehender aktualisiert wird.
     * Wir matchen via slug (falls leer -> slug aus name).
     */
    public function resolveRecord(): ?CompanyCategory
    {
        $name = (string) ($this->data['name'] ?? '');
        $slug = trim((string) ($this->data['slug'] ?? ''));

        $slug = $slug !== '' ? $slug : Str::slug($name);

        return CompanyCategory::firstOrNew([
            'slug' => $slug,
        ]);
    }

    /**
     * Normalisiert Daten bevor gespeichert wird.
     */
    protected function mutateRecordDataUsing(array $data): array
    {
        $name = (string) ($data['name'] ?? '');
        $slug = trim((string) ($data['slug'] ?? ''));

        $data['slug'] = $slug !== '' ? $slug : Str::slug($name);

        // default true, wenn nicht geliefert
        if (! array_key_exists('is_active', $data) || $data['is_active'] === null) {
            $data['is_active'] = true;
        }

        return $data;
    }

    /**
     * REQUIRED by Filament v3 Importer.
     * Text, der nach Abschluss als Notification angezeigt wird.
     */
    public static function getCompletedNotificationBody(Import $import): string
    {
        $successful = (int) ($import->successful_rows ?? 0);
        $failed = (int) ($import->failed_rows ?? 0);

        $parts = [];

        $parts[] = "{$successful} Zeilen erfolgreich importiert/aktualisiert.";

        if ($failed > 0) {
            $parts[] = "{$failed} Zeilen fehlgeschlagen (siehe Import-Log).";
        }

        // Optional: Filament führt oft auch "processed_rows"
        if (isset($import->processed_rows)) {
            $processed = (int) $import->processed_rows;
            if ($processed > 0 && $processed !== ($successful + $failed)) {
                $parts[] = "{$processed} Zeilen verarbeitet.";
            }
        }

        return implode(' ', $parts);
    }
}
