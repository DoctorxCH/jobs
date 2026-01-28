<?php

namespace App\Services\CompanyLookup;

use Illuminate\Support\Facades\Cache;

class CompanyLookupService
{
    public function __construct(
        protected RpoClient $rpo,
    ) {}

    public function lookup(string $country, string $ico): ?array
    {
        $country = strtoupper($country);

        return Cache::remember("company_lookup:{$country}:{$ico}", now()->addDays(7), function () use ($country, $ico) {
            if ($country !== 'SK') {
                return null;
            }

            return $this->rpo->byIco($ico);
        });
    }
}
