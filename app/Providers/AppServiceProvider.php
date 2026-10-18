<?php

namespace App\Providers;

use App\Models\SiteSetting;
use App\Services\Payments\PaymentGateway;
use App\Services\Payments\PaymentGateways;
use App\Services\WhatsApp\LogSender;
use App\Services\WhatsApp\NullSender;
use App\Services\WhatsApp\WhatsAppSender;
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
        // New orders use the configured gateway (PAYMENT_GATEWAY); an existing order is looked up through PaymentGateways::for().
        $this->app->bind(PaymentGateway::class, fn () => $this->app->make(PaymentGateways::class)->current());

        $this->app->bind(WhatsAppSender::class, fn () => match (config('services.whatsapp.driver')) {
            'log' => new LogSender,
            default => new NullSender,
        });
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
        // Forgot password: one counter per IP and a separate one per address, so one person cannot flood a mailbox
        // and one address behind a shared NAT cannot lock the others out.
        RateLimiter::for('forgot-password', fn (Request $request) => Limit::perMinute(5)->by($request->ip()));
        RateLimiter::for('forgot-password-email', fn (Request $request) => Limit::perHour(5)->by(mb_strtolower((string) $request->input('email'))));
        RateLimiter::for('reset-password', fn (Request $request) => Limit::perMinute(10)->by($request->ip()));
        // Each order holds a launch place for the payment window, so starting orders is limited more tightly than reading one.
        RateLimiter::for('checkout', fn (Request $request) => Limit::perMinute(6)->by($request->ip()));
        RateLimiter::for('checkout-status', fn (Request $request) => Limit::perMinute(60)->by($request->ip()));
        RateLimiter::for('webhook', fn (Request $request) => Limit::perMinute(120)->by($request->ip()));
    }

    /** Admin-edited settings (Filament "Pengaturan situs") win over .env; blank values keep the .env default. */
    protected function applyAdminSettings(): void
    {
        try {
            $values = SiteSetting::values();
        } catch (Throwable) {
            return; // table not migrated yet (fresh deploy, CI)
        }

        foreach (SiteSetting::CONFIG_KEYS + SiteSetting::PRICING_KEYS as $key => $configKey) {
            if (filled($values[$key] ?? null)) {
                config([$configKey => $values[$key]]);
            }
        }
    }
}
