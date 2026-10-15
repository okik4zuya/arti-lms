<?php

namespace App\Providers;

use App\Models\SiteSetting;
use Illuminate\Support\ServiceProvider;
use Throwable;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        $this->applyAdminSettings();
    }

    /** Admin-edited settings (Filament "Pengaturan situs") win over .env; blank values keep the .env default. */
    protected function applyAdminSettings(): void
    {
        try {
            $values = SiteSetting::values();
        } catch (Throwable) {
            return; // table not migrated yet (fresh deploy, CI)
        }

        foreach (SiteSetting::CONFIG_KEYS as $key => $configKey) {
            if (filled($values[$key] ?? null)) {
                config([$configKey => $values[$key]]);
            }
        }
    }
}
