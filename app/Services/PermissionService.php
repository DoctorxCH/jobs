<?php

namespace App\Services;

use App\Models\ResourcePermission;
use Closure;
use Illuminate\Cache\TaggableStore;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Str;

class PermissionService
{
    private const CACHE_TAG = 'resource-permissions';
    private const CACHE_INDEX_KEY = 'resource-permissions:keys';
    private const CACHE_TTL_SECONDS = 60;

    public static function can(string $resource, string $action): bool
    {
        $user = Auth::user();
        if (! $user) {
            return false;
        }

        $roleNames = $user
            ->getRoleNames()
            ->filter(fn (string $roleName) => Str::startsWith($roleName, 'platform.'))
            ->values();

        if ($roleNames->contains('platform.super_admin')) {
            return true;
        }

        if ($roleNames->isEmpty()) {
            return false;
        }

        $actionColumn = self::actionColumn($action);
        if ($actionColumn === null) {
            return false;
        }

        $roleHash = md5($roleNames->sort()->implode('|'));
        $cacheKey = sprintf(
            'resource-permissions:%s:%s:%s:%s',
            $user->getAuthIdentifier(),
            $roleHash,
            $resource,
            $actionColumn
        );

        return self::remember($cacheKey, function () use ($resource, $roleNames, $actionColumn): bool {
            return ResourcePermission::query()
                ->where('resource', $resource)
                ->whereIn('role_name', $roleNames)
                ->where($actionColumn, true)
                ->exists();
        });
    }

    public static function invalidateCache(): void
    {
        if (self::supportsTags()) {
            Cache::tags(self::CACHE_TAG)->flush();
            return;
        }

        $keys = Cache::pull(self::CACHE_INDEX_KEY, []);
        foreach ($keys as $key) {
            Cache::forget($key);
        }
    }

    private static function actionColumn(string $action): ?string
    {
        return match ($action) {
            'view' => 'can_view',
            'create' => 'can_create',
            'edit' => 'can_edit',
            'delete' => 'can_delete',
            default => null,
        };
    }

    private static function remember(string $cacheKey, Closure $callback): bool
    {
        $ttl = now()->addSeconds(self::CACHE_TTL_SECONDS);

        if (self::supportsTags()) {
            return Cache::tags(self::CACHE_TAG)->remember($cacheKey, $ttl, $callback);
        }

        $value = Cache::remember($cacheKey, $ttl, $callback);
        self::trackCacheKey($cacheKey);

        return $value;
    }

    private static function trackCacheKey(string $cacheKey): void
    {
        $keys = Cache::get(self::CACHE_INDEX_KEY, []);
        if (! in_array($cacheKey, $keys, true)) {
            $keys[] = $cacheKey;
            Cache::put(self::CACHE_INDEX_KEY, $keys, now()->addHour());
        }
    }

    private static function supportsTags(): bool
    {
        return Cache::getStore() instanceof TaggableStore;
    }
}
