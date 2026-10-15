<?php

namespace App\Providers;

use App\Models\SiteSetting;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\RateLimiter;
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
        // The owner can do everything; every other role is limited to its permissions.
        Gate::before(fn ($user) => $user->hasRole('admin') ? true : null);

        $this->applyAdminSettings();
        $this->defineRateLimiters();
    }

    /**
     * Named limiters, each with its own counter. The plain `throttle:N,M` form keys only on domain and IP,
     * so every such route would drain one shared bucket (a download click would eat into the sign-up allowance).
     * Visitors on one campus network share an IP, hence the generous per-IP numbers.
     */
    protected function defineRateLimiters(): void
    {
        RateLimiter::for('subscribe', fn (Request $request) => Limit::perMinute(20)->by($request->ip()));
        RateLimiter::for('download', fn (Request $request) => Limit::perMinute(60)->by($request->ip()));
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
