<?php

namespace App\Services\Licensing;

use Illuminate\Http\Client\ConnectionException;
use Illuminate\Http\Client\PendingRequest;
use Illuminate\Http\Client\Response;
use Illuminate\Support\Facades\Http;

/**
 * Client for the ARTi licensing Worker. Every method returns the decoded JSON body or throws
 * LicenseApiException. Keys are never logged here.
 */
class LicenseApi
{
    public function isConfigured(): bool
    {
        return filled(config('services.licensing.url')) && filled(config('services.licensing.admin_token'));
    }

    /**
     * Issue a new unbound key. Idempotent on order_ref: a repeat answers 409 order_exists with the key_id.
     *
     * @param  array{email:string,name?:?string,months:int,order:string,seats?:?int,provider?:string,amount_idr?:?int,notes?:?string}  $data
     * @return array{key:string,key_id:string,tier:string,seats:int,expires:string,updates_until:string,email:string}
     */
    public function issue(array $data): array
    {
        return $this->post('admin/issue', $data);
    }

    /** @return array{license:array,activations:array,events:array} */
    public function show(string $keyId): array
    {
        return $this->get('admin/show', ['key_id' => $keyId]);
    }

    /**
     * Rotate the key (same key_id, activations kept) and return the new one.
     *
     * @return array{key_id:string,key?:string,emailed:string}
     */
    public function resend(string $keyId, ?string $reason = null): array
    {
        return $this->post('admin/resend', ['key_id' => $keyId, 'show' => true, 'reason' => $reason]);
    }

    /**
     * Add months to the license. NOT idempotent at the Worker: a repeat adds again, so callers must lock.
     *
     * @return array{key_id:string,expires:string,updates_until:string}
     */
    public function extend(string $keyId, int $months): array
    {
        return $this->post('admin/extend', ['key_id' => $keyId, 'months' => $months]);
    }

    /**
     * Revoke a license, or undo a revocation. Does not stop machines that are already activated: they keep
     * their signed license until it expires.
     *
     * @return array{key_id:string,status:string}
     */
    public function revoke(string $keyId, ?string $reason = null, bool $undo = false): array
    {
        return $this->post('admin/revoke', array_filter(
            ['key_id' => $keyId, 'reason' => $reason, 'undo' => $undo ?: null],
            fn ($v) => $v !== null,
        ));
    }

    /** Free one activated machine (full machine hash from `show`). */
    public function deactivate(string $keyId, string $machineHash): array
    {
        return $this->post('admin/deactivate', ['key_id' => $keyId, 'machine_hash' => $machineHash]);
    }

    /**
     * Ask the Worker whether this key is still the current one for its license (public, rate-limited).
     *
     * @return array{key_id:string,seats:int,status:string,expires:string,activations:array}
     */
    public function verifyKey(string $key): array
    {
        return $this->post('activations', ['key' => $key], admin: false);
    }

    private function get(string $path, array $query): array
    {
        return $this->send(fn (PendingRequest $r) => $r->get($this->url($path), $query));
    }

    private function post(string $path, array $body, bool $admin = true): array
    {
        return $this->send(fn (PendingRequest $r) => $r->post($this->url($path), $body), $admin);
    }

    private function send(callable $call, bool $admin = true): array
    {
        if (! $this->isConfigured()) {
            throw new LicenseApiException('not_configured', 'Worker lisensi belum dikonfigurasi (LICENSE_API_URL dan LICENSE_ADMIN_TOKEN).');
        }

        $request = Http::acceptJson()->asJson()->timeout(15);

        if ($admin) {
            $request = $request->withToken((string) config('services.licensing.admin_token'));
        }

        try {
            /** @var Response $response */
            $response = $call($request);
        } catch (ConnectionException $e) {
            throw new LicenseApiException('unreachable', 'Worker lisensi tidak terjangkau.');
        }

        if ($response->successful()) {
            return $response->json() ?? [];
        }

        $body = $response->json();
        $body = is_array($body) ? $body : [];

        throw new LicenseApiException(
            (string) ($body['code'] ?? 'server_error'),
            (string) ($body['message'] ?? 'Worker lisensi menjawab dengan galat '.$response->status().'.'),
            $response->status(),
            array_diff_key($body, ['code' => 1, 'message' => 1]),
        );
    }

    private function url(string $path): string
    {
        return rtrim((string) config('services.licensing.url'), '/').'/'.$path;
    }
}
