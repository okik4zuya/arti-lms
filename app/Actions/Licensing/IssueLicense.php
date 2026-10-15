<?php

namespace App\Actions\Licensing;

use App\Models\LicenseIssuance;
use App\Services\Licensing\LicenseApi;
use App\Services\Licensing\LicenseApiException;
use Illuminate\Database\UniqueConstraintViolationException;
use Illuminate\Support\Carbon;
use InvalidArgumentException;

/**
 * The only way a license is issued: from the Filament page today, from the payment-fulfilment job later.
 *
 * The local row is written (status pending) before the Worker is called, so a crash always leaves a
 * trace. The Worker is idempotent on order_ref, so a retry never issues twice.
 */
class IssueLicense
{
    public function __construct(private readonly LicenseApi $api) {}

    /**
     * @throws LicenseApiException when the Worker refused or could not be reached; the row stays
     *                             pending if the outcome is unknown, and is removed if the Worker refused outright
     */
    public function handle(IssueRequest $request): LicenseIssuance
    {
        $row = $this->pendingRow($request);

        if (! $row->isPending() && $row->hasUsableKey()) {
            return $row;
        }

        try {
            $issued = $this->api->issue(array_filter([
                'email' => $request->email,
                'name' => $request->name,
                'tier' => $request->tier,
                'months' => $request->months,
                'order' => $request->orderRef,
                'seats' => $request->seats,
                'provider' => $request->source,
                'amount_idr' => $request->amountIdr,
                'notes' => $request->notes,
            ], fn ($v) => $v !== null));
        } catch (LicenseApiException $e) {
            return $this->failed($row, $e);
        }

        $row->key = $issued['key'];
        $row->key_id = $issued['key_id'];
        $row->key_stale = false;
        $row->applyWorkerState($issued);
        $row->save();
        $row->logEvent('issued', $request->issuedBy, $request->orderRef);

        return $row;
    }

    private function pendingRow(IssueRequest $request): LicenseIssuance
    {
        $attributes = [
            'order_ref' => $request->orderRef,
            'source' => $request->source,
            'email' => $request->email,
            'name' => $request->name,
            'tier' => $request->tier,
            'seats' => $request->seats,
            'months' => $request->months,
            'amount_idr' => $request->amountIdr,
            'buyer_whatsapp' => $request->buyerWhatsapp,
            'user_id' => $request->userId,
            'issued_by' => $request->issuedBy,
            'notes' => $request->notes,
        ];

        try {
            $row = LicenseIssuance::firstOrCreate(
                ['order_ref' => $request->orderRef],
                $attributes + ['status' => LicenseIssuance::PENDING],
            );
        } catch (UniqueConstraintViolationException) {
            $row = LicenseIssuance::where('order_ref', $request->orderRef)->firstOrFail();
        }

        if (strcasecmp($row->email, $request->email) !== 0) {
            throw new InvalidArgumentException("order_ref {$request->orderRef} sudah dipakai untuk email lain.");
        }

        return $row;
    }

    /** Settle a failed issue call: recover from 409, keep the row pending when unreachable, drop it when refused. */
    private function failed(LicenseIssuance $row, LicenseApiException $e): LicenseIssuance
    {
        if ($e->isOrderExists()) {
            return $this->adoptExisting($row, (string) ($e->extra['key_id'] ?? ''));
        }

        if ($e->isUnreachable() || $e->errorCode === 'not_configured') {
            $row->forceFill(['sync_error' => $e->getMessage()])->save();
        } elseif ($row->key_id === null) {
            $row->delete();
        }

        throw $e;
    }

    /**
     * The Worker already issued this order_ref (an earlier response was lost). If this side has no key,
     * rotate to a new one: the Worker cannot reveal the old key, it only keeps its hash.
     */
    private function adoptExisting(LicenseIssuance $row, string $keyId): LicenseIssuance
    {
        if ($keyId === '') {
            throw new LicenseApiException('server_error', 'Worker menjawab order_exists tanpa key_id.');
        }

        $row->key_id = $keyId;

        if (! $row->hasUsableKey()) {
            $rotated = $this->api->resend($keyId, 'recovered after a lost issue response');
            $row->key = $rotated['key'] ?? null;
            $row->key_stale = false;
            $row->logEvent('key_rotated', $row->issued_by, 'recovered after a lost issue response');
        }

        $state = $this->api->show($keyId);
        $row->applyWorkerState($state['license'], $state['activations'] ?? []);
        $row->save();

        return $row;
    }
}
