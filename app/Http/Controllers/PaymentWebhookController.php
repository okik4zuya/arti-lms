<?php

namespace App\Http\Controllers;

use App\Actions\Orders\SettleInvoice;
use App\Models\Order;
use App\Services\Payments\XenditClient;
use App\Services\Payments\PaymentGatewayException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

/**
 * Xendit invoice callback. Rules, in order:
 *  1. the `x-callback-token` header must match (constant time, fail closed);
 *  2. the body is only a pointer: status and amount are re-read from Xendit's API before anything changes;
 *  3. the paid amount must equal the order's amount, otherwise the order waits for an admin;
 *  4. the status change is one compare-and-set, so a repeated callback answers 200 and fulfils nothing twice.
 * Non-2xx answers make Xendit retry; they are used only when we could not decide (Xendit unreachable).
 */
class PaymentWebhookController extends Controller
{
    public function __invoke(Request $request, XenditClient $xendit, SettleInvoice $settle): JsonResponse
    {
        if (! $xendit->isConfigured()) {
            return response()->json(['status' => 'not_configured'], 503);
        }

        if (! $xendit->validCallbackToken($request->header('x-callback-token'))) {
            return response()->json(['status' => 'forbidden'], 403);
        }

        $invoiceId = $request->input('id');
        $externalId = $request->input('external_id');

        if (! is_string($invoiceId) || ! is_string($externalId) || $invoiceId === '' || $externalId === '') {
            return response()->json(['status' => 'ignored']);
        }

        $order = Order::where('public_id', $externalId)->first();

        if ($order === null) {
            return response()->json(['status' => 'ignored']);
        }

        try {
            $invoice = $xendit->getInvoice($invoiceId);
        } catch (PaymentGatewayException $e) {
            // Unknown invoice: nothing to settle. Anything else: let Xendit retry later.
            return $e->status === 404
                ? response()->json(['status' => 'ignored'])
                : response()->json(['status' => 'retry'], 503);
        }

        if (! $settle->handle($order, $invoice)) {
            return response()->json(['status' => 'ignored']);
        }

        return response()->json(['status' => 'ok']);
    }
}
