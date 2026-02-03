<?php

if (! function_exists('format_money_minor')) {
    /**
     * Format minor units (e.g. cents) to human-readable money string.
     *
     * Example: format_money_minor(12345, 'EUR') => "123.45 EUR"
     */
    function format_money_minor($amountMinor, ?string $currency = null, int $decimals = 2): string
    {
        if ($amountMinor === null || $amountMinor === '') {
            return '—';
        }

        if (! is_numeric($amountMinor)) {
            return '—';
        }

        $amountMinor = (int) $amountMinor;

        $value = $amountMinor / (10 ** $decimals);

        // predictable formatting (no locale surprises)
        $formatted = number_format($value, $decimals, '.', '\'');

        $currency = strtoupper((string) ($currency ?: 'EUR'));

        return trim($formatted . ' ' . $currency);
    }
}
