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
            $whatsapp = SiteSetting::values()['contact_whatsapp'] ?? null;
        } catch (Throwable) {
            return; // table not migrated yet (fresh deploy, CI)
        }

        if (filled($whatsapp)) {
            config(['site.contact_whatsapp' => $whatsapp]);
        }
    }
}
