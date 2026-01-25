<?php

namespace App\Services\CompanyLookup;

use Illuminate\Support\Facades\Http;

class RpoClient
{
    private string $base = 'https://api.statistics.sk/rpo/v1';

    public function byIco(string $ico): ?array
    {
        $res = Http::timeout(10)
            ->acceptJson()
            ->get($this->base . '/search', [
                'identifier' => $ico,
            ]);

        if (! $res->ok()) {
            return null;
        }

        $json = $res->json();

        // RPO returns: { "results": [ {...}, ... ] }
        $results = is_array($json) ? ($json['results'] ?? null) : null;
        if (! is_array($results) || !isset($results[0]) || !is_array($results[0])) {
            return null;
        }

        $r = $results[0];

        $legalName = $r['fullNames'][0]['value'] ?? null;

        $addr = $r['addresses'][0] ?? null;

        $street = is_array($addr) ? ($addr['street'] ?? null) : null;
        $building = is_array($addr) ? ($addr['buildingNumber'] ?? null) : null;
        $reg = is_array($addr) ? ($addr['regNumber'] ?? null) : null;
        $postal = is_array($addr) ? (($addr['postalCodes'][0] ?? null)) : null;
        $city = is_array($addr) ? ($addr['municipality']['value'] ?? null) : null;

        // RPO typically doesn't provide DIC / IC DPH -> leave null
        return [
            'id' => (string)($r['id'] ?? ''),
            'ico' => $ico,
            'legal_name' => $legalName,
            'dic' => null,
            'ic_dph' => null,
            'address' => [
                'street' => trim((string)$street . ' ' . (string)$building . ($reg !== null ? '/' . $reg : '')),
                'postal_code' => $postal,
                'city' => $city,
            ],
            'raw' => $r,
        ];
    }
}
