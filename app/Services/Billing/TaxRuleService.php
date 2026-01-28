<?php

namespace App\Services\Billing;

use App\Models\Billing\Setting;
use App\Models\Company;

class TaxRuleService
{
    public function determineForCompany(Company $company): array
    {
        $supplierCountry = Setting::query()->where('key', 'supplier_country_code')->value('value') ?? 'SK';
        $isVatRegistered = (bool) (Setting::query()->where('key', 'supplier_vat_registered')->value('value') ?? true);

        $country = $company->country_code ?? $supplierCountry;
        $vatId = $company->vat_id ?? null;

        if ($country === $supplierCountry) {
            return ['tax_rule' => 'SK_DOMESTIC', 'reverse_charge' => false, 'tax_rate_percent' => null];
        }

        if ($this->isEuCountry($country) && $vatId !== null && $vatId !== '') {
            return ['tax_rule' => 'EU_B2B_REVERSE_CHARGE', 'reverse_charge' => true, 'tax_rate_percent' => 0.00];
        }

        return ['tax_rule' => 'NON_EU_OUT_OF_SCOPE', 'reverse_charge' => false, 'tax_rate_percent' => 0.00];
    }

    private function isEuCountry(string $countryCode): bool
    {
        $euCountries = [
            'AT', 'BE', 'BG', 'HR', 'CY', 'CZ', 'DK', 'EE', 'FI', 'FR',
            'DE', 'GR', 'HU', 'IE', 'IT', 'LV', 'LT', 'LU', 'MT', 'NL',
            'PL', 'PT', 'RO', 'SK', 'SI', 'ES', 'SE',
        ];

        return in_array(strtoupper($countryCode), $euCountries, true);
    }
}
