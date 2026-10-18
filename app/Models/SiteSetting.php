<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

/**
 * Small admin-editable key/value settings. A stored value overrides the matching config('site.*')
 * entry (see AppServiceProvider); an empty value falls back to the .env default.
 */
class SiteSetting extends Model
{
    public const CACHE_KEY = 'site_settings.all';

    /** Setting key => the config entry it overrides when filled. */
    public const CONFIG_KEYS = [
        'contact_whatsapp' => 'site.contact_whatsapp',
        'whatsapp_message' => 'site.offer.whatsapp_message',
        'chat_message' => 'site.offer.chat_message',
    ];

    /**
     * Prices and quotas, edited on their own page ("Harga & promo"). Kept apart from CONFIG_KEYS on purpose: the
     * site-settings page saves every key in CONFIG_KEYS and would blank these.
     */
    public const PRICING_KEYS = [
        'price_normal' => 'site.offer.price_normal',
        'price_launch' => 'site.offer.price_launch',
        'launch_quota' => 'site.offer.launch_quota',
        'launch_sold' => 'site.offer.launch_sold',
    ];

    protected $primaryKey = 'key';

    public $incrementing = false;

    protected $keyType = 'string';

    protected $fillable = ['key', 'value'];

    /** @return array<string, string|null> */
    public static function values(): array
    {
        return Cache::rememberForever(self::CACHE_KEY, fn () => static::query()->pluck('value', 'key')->all());
    }

    public static function put(string $key, ?string $value): void
    {
        static::updateOrCreate(['key' => $key], ['value' => $value]);
        Cache::forget(self::CACHE_KEY);
    }
}
