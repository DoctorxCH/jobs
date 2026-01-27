<?php

namespace App\Services\Billing;

class TaxRuleService
{
    public const RULE_SK_DOMESTIC = 'SK_DOMESTIC';
    public const RULE_EU_B2B_REVERSE_CHARGE = 'EU_B2B_REVERSE_CHARGE';
    public const RULE_NON_EU_OUT_OF_SCOPE = 'NON_EU_OUT_OF_SCOPE';

    public function determineRule(string $customerCountryCode, bool $hasValidVatId, bool $isEuMember): array
    {
        if ($customerCountryCode === 'SK') {
            return [
                'rule' => self::RULE_SK_DOMESTIC,
                'reverse_charge' => false,
                'tax_rate_percent' => null,
            ];
        }

        if ($isEuMember && $hasValidVatId) {
            return [
                'rule' => self::RULE_EU_B2B_REVERSE_CHARGE,
                'reverse_charge' => true,
                'tax_rate_percent' => 0,
            ];
        }

        return [
            'rule' => self::RULE_NON_EU_OUT_OF_SCOPE,
            'reverse_charge' => false,
            'tax_rate_percent' => 0,
        ];
    }
}
