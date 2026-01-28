<?php

if (! function_exists('format_money_minor')) {
    function format_money_minor(?int $amountMinor, string $currency = 'EUR'): string
    {
        if ($amountMinor === null) {
            return '—';
        }

        $amount = $amountMinor / 100;
        $formatted = number_format($amount, 2, '.', ' ');

        return $formatted.' '.$currency;
    }
}
