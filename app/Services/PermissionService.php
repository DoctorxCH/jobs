<?php

namespace App\Services;

class PermissionService
{
    public static function can(string $resource, string $action): bool
    {
        // TODO: Codex implementiert DB lookup + caching + super_admin fallback
        return true;
    }
}

