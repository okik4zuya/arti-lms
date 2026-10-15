<?php

namespace App\Console\Commands;

use App\Actions\Licensing\SyncLicense;
use App\Models\LicenseIssuance;
use App\Services\Licensing\LicenseApi;
use App\Services\Licensing\LicenseApiException;
use Illuminate\Console\Command;

class SyncLicenses extends Command
{
    protected $signature = 'licenses:sync {--verify=10 : How many stored keys to re-check against the Worker this run (rate-limited endpoint)}';

    protected $description = 'Refresh license rows from the licensing Worker (the Worker wins) and retry pending issues';

    public function handle(LicenseApi $api, SyncLicense $sync): int
    {
        if (! $api->isConfigured()) {
            $this->warn('Licensing Worker is not configured (LICENSE_API_URL / LICENSE_ADMIN_TOKEN); nothing to do.');

            return self::SUCCESS;
        }

        $verify = max(0, (int) $this->option('verify'));
        $toVerify = LicenseIssuance::query()
            ->whereNotNull('key_id')->whereNotNull('key')->where('key_stale', false)
            ->orderByRaw('key_checked_at is not null')->orderBy('key_checked_at')
            ->limit($verify)->pluck('id')->all();

        $done = 0;
        $failed = 0;

        foreach (LicenseIssuance::query()->orderBy('created_at')->cursor() as $row) {
            try {
                $sync->handle($row, verifyKey: in_array($row->id, $toVerify, true));
                $done++;
            } catch (LicenseApiException $e) {
                $failed++;
                $this->warn("{$row->order_ref}: {$e->getMessage()}");

                if ($e->errorCode === 'unreachable') {
                    $this->error('Worker unreachable; stopping.');

                    return self::FAILURE;
                }
            }
        }

        $this->info("Synced {$done} license(s), {$failed} failed.");

        return $failed ? self::FAILURE : self::SUCCESS;
    }
}
