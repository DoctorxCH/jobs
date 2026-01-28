<?php

if (! function_exists('format_money_minor')) {
    /**
     * Format money from minor units (cents) into "12.34 EUR".
     * Input: 1234, "EUR" => "12.34 EUR"
     */
    function format_money_minor(int $minor, ?string $currency = 'EUR'): string
    {
        $currency = $currency ?: 'EUR';
        $amount = $minor / 100;

        // 2 decimals, comma as decimal separator is not enforced; keep neutral
        return number_format($amount, 2, '.', ' ') . ' ' . strtoupper($currency);
    }
}
