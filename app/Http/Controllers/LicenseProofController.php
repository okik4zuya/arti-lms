<?php

namespace App\Http\Controllers;

use App\Models\LicenseIssuance;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\Response;

/** Serves a payment proof from the private disk, only to a signed link opened by a user who may view licenses. */
class LicenseProofController extends Controller
{
    public function __invoke(LicenseIssuance $issuance): Response
    {
        abort_unless(auth()->user()?->can('license.view'), 403);
        $disk = Storage::disk(config('filesystems.proofs_disk'));

        abort_unless(filled($issuance->payment_proof_path) && $disk->exists($issuance->payment_proof_path), 404);

        // Streamed through Laravel (not a public or presigned bucket URL) so the permission check always applies.
        return $disk->response($issuance->payment_proof_path, null, ['Cache-Control' => 'private, no-store']);
    }
}
