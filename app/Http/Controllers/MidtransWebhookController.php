<?php

namespace App\Http\Controllers;

use App\Actions\Orders\SettleInvoice;
use App\Models\Order;
use App\Services\Payments\MidtransClient;
use App\Services\Payments\PaymentGatewayException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

/**
 * Midtrans HTTP notification. Rules, in order:
 *  1. the body's `signature_key` must equal SHA-512(order_id + status_code + gross_amount + server key), constant time;
 *  2. the body is only a pointer: the status and amount used to settle come from Midtrans' Status API;
 *  3. the paid amount must equal the order's amount, otherwise the order waits for an admin (SettleInvoice);
 *  4. the status change is one compare-and-set, so a repeated notification answers 200 and fulfils nothing twice.
 * Non-2xx answers make Midtrans retry; they are used only when we could not decide (Midtrans unreachable).
 */
class MidtransWebhookController extends Controller
{
    public function __invoke(Request $request, MidtransClient $midtrans, SettleInvoice $settle): JsonResponse
    {
        if (! $midtrans->isConfigured()) {
            return response()->json(['status' => 'not_configured'], 503);
        }

        $orderId = $request->input('order_id');

        if (! $midtrans->validSignature($orderId, $request->input('status_code'), $request->input('gross_amount'), $request->input('signature_key'))) {
            return response()->json(['status' => 'forbidden'], 403);
        }

        $order = Order::where('public_id', $orderId)->where('gateway', 'midtrans')->first();

        if ($order === null) {
            return response()->json(['status' => 'ignored']);
        }

        try {
            $invoice = $midtrans->getInvoice($order->public_id);
        } catch (PaymentGatewayException $e) {
            // Unknown to Midtrans: nothing to settle. Anything else: let Midtrans retry later.
            return $e->isNotFound()
                ? response()->json(['status' => 'ignored'])
                : response()->json(['status' => 'retry'], 503);
        }

        // Midtrans looks payments up by order id, so the reference we keep is the order's own public id.
        if ($order->gateway_ref === null) {
            $order->forceFill(['gateway_ref' => $order->public_id])->save();
        }

        $settle->handle($order, $invoice);

        return response()->json(['status' => 'ok']);
    }
}
