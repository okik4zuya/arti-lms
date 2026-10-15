<?php

namespace App\Support;

use App\Models\LicenseIssuance;
use App\Services\Licensing\LicenseApi;
use App\Services\Licensing\LicenseApiException;
use Illuminate\Support\Carbon;

/**
 * Data for the read-only history panel: the Worker's activations and events (fetched live, never stored)
 * merged with the events this app logged. Contains no key and no password.
 */
class LicenseHistory
{
    /** @return array{row:LicenseIssuance,activations:list<array>,events:list<array>,error:?string} */
    public static function for(LicenseIssuance $row): array
    {
        $activations = [];
        $events = [];
        $error = null;

        if ($row->key_id !== null) {
            try {
                $state = app(LicenseApi::class)->show($row->key_id);

                foreach ($state['activations'] ?? [] as $a) {
                    $activations[] = [
                        'machine' => static::shortHash((string) ($a['machine_hash'] ?? '')),
                        'label' => $a['label'] ?? null,
                        'first_seen' => static::time($a['first_seen'] ?? null),
                        'last_seen' => static::time($a['last_seen'] ?? null),
                        'active' => empty($a['deactivated']),
                    ];
                }

                foreach ($state['events'] ?? [] as $e) {
                    $events[] = [
                        'at' => static::time($e['at'] ?? null),
                        'who' => 'Worker',
                        'event' => (string) ($e['event'] ?? ''),
                        'detail' => $e['detail'] ?? null,
                    ];
                }
            } catch (LicenseApiException $e) {
                $error = $e->getMessage();
            }
        }

        foreach ($row->events()->with('user')->get() as $e) {
            $events[] = [
                'at' => $e->created_at,
                'who' => $e->user?->name ?? 'Sistem',
                'event' => $e->event,
                'detail' => $e->detail,
            ];
        }

        usort($events, fn ($a, $b) => ($b['at']?->getTimestamp() ?? 0) <=> ($a['at']?->getTimestamp() ?? 0));

        return ['row' => $row->loadMissing('issuer'), 'activations' => $activations, 'events' => $events, 'error' => $error];
    }

    private static function shortHash(string $hash): string
    {
        return strlen($hash) > 10 ? substr($hash, 0, 4).'...'.substr($hash, -4) : $hash;
    }

    private static function time(mixed $value): ?Carbon
    {
        return $value ? Carbon::parse($value) : null;
    }
}
