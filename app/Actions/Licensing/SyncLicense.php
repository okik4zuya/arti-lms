<?php

namespace App\Actions\Licensing;

use App\Models\LicenseIssuance;
use App\Services\Licensing\LicenseApi;
use App\Services\Licensing\LicenseApiException;

/**
 * Refreshes one row from the Worker. The Worker wins on any difference. A pending row is retried as an
 * issue (same order_ref, so it can never issue twice).
 */
class SyncLicense
{
    public function __construct(private readonly LicenseApi $api, private readonly IssueLicense $issue) {}

    /**
     * @param  bool  $verifyKey  also ask the Worker whether the stored key is still the current one
     *                           (public endpoint, rate-limited, so callers should do this sparingly)
     * @throws LicenseApiException when the Worker could not be reached or refused
     */
    public function handle(LicenseIssuance $row, bool $verifyKey = false): LicenseIssuance
    {
        if ($row->isPending() && $row->key_id === null) {
            return $this->issue->handle(new IssueRequest(
                email: $row->email,
                name: $row->name,
                months: $row->months,
                orderRef: $row->order_ref,
                seats: $row->seats,
                amountIdr: $row->amount_idr,
                source: $row->source,
                buyerWhatsapp: $row->buyer_whatsapp,
                notes: $row->notes,
                userId: $row->user_id,
                issuedBy: $row->issued_by,
                paymentRef: $row->payment_ref,
                paymentProofPath: $row->payment_proof_path,
            ));
        }

        try {
            $state = $this->api->show($row->key_id);
        } catch (LicenseApiException $e) {
            if ($e->isNotFound()) {
                $row->forceFill(['status' => LicenseIssuance::MISSING, 'sync_error' => $e->getMessage(), 'last_synced_at' => now()])->save();

                return $row;
            }

            $row->forceFill(['sync_error' => $e->getMessage()])->save();

            throw $e;
        }

        $row->applyWorkerState($state['license'], $state['activations'] ?? []);

        if ($verifyKey && filled($row->key)) {
            $this->checkKey($row);
        }

        $row->save();

        return $row;
    }

    /** Mark the stored key stale when the Worker no longer recognises it (rotated elsewhere, e.g. by the CLI). */
    private function checkKey(LicenseIssuance $row): void
    {
        try {
            $this->api->verifyKey($row->key);
            $row->key_stale = false;
            $row->key_checked_at = now();
        } catch (LicenseApiException $e) {
            if ($e->isNotFound()) {
                $row->key_stale = true;
                $row->key_checked_at = now();
            }
            // rate_limited, unreachable, ...: leave the flag as it was and try again on a later run
        }
    }
}
