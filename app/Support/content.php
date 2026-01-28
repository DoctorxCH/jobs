<?php

if (! function_exists('content')) {
    /**
     * Read UI content by dot-key.
     * Today: config/content.php
     * Later: swap to DB/provider without changing blades.
     */
    function content(string $key, mixed $default = null): mixed
    {
        return data_get(config('content'), $key, $default);
    }
}
