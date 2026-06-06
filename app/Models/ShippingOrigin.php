<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Schema;

class ShippingOrigin extends Model
{
    protected $table = 'shipping_origins';

    protected $fillable = [
        'origin_city_id',
        'origin_city_name',
        'origin_district_id',
        'origin_district_name',
        'address',
        'enabled_couriers',
    ];

    protected $casts = [
        'enabled_couriers' => 'array',
    ];

    public static function current(): ?self
    {
        if (!Schema::hasTable('shipping_origins')) {
            return null;
        }

        return Cache::remember('shipping_origin:current', 3600, function () {
            return self::query()->orderByDesc('id')->first();
        });
    }

    public static function currentOriginDistrictId(): ?string
    {
        return self::current()?->origin_district_id;
    }

    public static function currentOriginCityId(): ?string
    {
        return self::current()?->origin_city_id;
    }

    /**
     * Default couriers shown to customers if admin hasn't configured anything yet.
     */
    public static function defaultCouriers(): array
    {
        return ['jne', 'tiki', 'pos', 'jnt', 'sicepat'];
    }

    /**
     * Returns enabled courier codes from DB (or null if not configured).
     */
    public static function currentEnabledCouriers(): ?array
    {
        $couriers = self::current()?->enabled_couriers;

        if (!is_array($couriers)) {
            return null;
        }

        $normalized = array_values(array_unique(array_filter(array_map(function ($c) {
            $c = strtolower(trim((string) $c));
            return $c !== '' ? $c : null;
        }, $couriers))));

        return $normalized ?: null;
    }

    /**
     * Courier param for Komerce RajaOngkir endpoint: "jne:tiki:pos".
     *
     * Return empty string to let API decide / return all available couriers.
     */
    public static function currentCourierParam(): string
    {
        $couriers = self::currentEnabledCouriers();

        if (!$couriers) {
            return '';
        }

        $couriers = array_values(array_unique(array_filter(array_map(fn ($c) => strtolower(trim((string) $c)), $couriers))));

        return implode(':', $couriers);
    }

    public static function forgetCache(): void
    {
        Cache::forget('shipping_origin:current');
    }
}
