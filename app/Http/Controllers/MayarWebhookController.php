<?php

namespace App\Http\Controllers;

use App\Actions\Orders\SettleInvoice;
use App\Models\Order;
use App\Services\Payments\MayarClient;
use App\Services\Payments\PaymentGatewayException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

/**
 * Mayar webhook (`payment.received`). Mayar documents no signature, so the rules are:
 *  1. the URL carries a secret segment (MAYAR_WEBHOOK_SECRET), and Mayar's own Webhook Token (MAYAR_WEBHOOK_TOKEN)
 *     must arrive with the request, both compared in constant time;
 *  2. the body is only a pointer: it names the payment request, the status and amount come from Mayar's API;
 *  3. the paid amount must equal the order's amount, otherwise the order waits for an admin (SettleInvoice);
 *  4. the status change is one compare-and-set, so a repeated webhook answers 200 and fulfils nothing twice.
 * The body names the payment request in `data.productId`, looked up against our own `gateway_ref` column; an unknown
 * one is ignored. Non-2xx answers are used only when we could not decide.
 */
class MayarWebhookController extends Controller
{
    public function __invoke(Request $request, string $secret, MayarClient $mayar, SettleInvoice $settle): JsonResponse
    {
        if (! $mayar->validSecret($secret)) {
            return response()->json(['status' => 'forbidden'], 403);
        }

        if (! $mayar->validToken($request)) {
            // Header names only, never values: enough to learn which header Mayar puts its token in.
            Log::warning('Mayar webhook token mismatch', ['headers' => array_keys($request->headers->all())]);

            return response()->json(['status' => 'forbidden'], 403);
        }

        if (! $mayar->isConfigured()) {
            return response()->json(['status' => 'not_configured'], 503);
        }

        $event = $request->input('event');
        if (is_string($event) && $event !== 'payment.received') {
            return response()->json(['status' => 'ignored']);
        }

        // Verified in the sandbox (2026-10-23): `data.productId` is the payment request's id, `data.id` the transaction's.
        $requestId = $request->input('data.productId');

        $order = is_string($requestId) && $requestId !== ''
            ? Order::where('gateway', 'mayar')->where('gateway_ref', $requestId)->first()
            : null;

        if ($order === null) {
            return response()->json(['status' => 'ignored']);
        }

        try {
            $invoice = $mayar->getInvoice($order->gateway_ref);
        } catch (PaymentGatewayException $e) {
            return $e->isNotFound()
                ? response()->json(['status' => 'ignored'])
                : response()->json(['status' => 'retry'], 503);
        }

        // The payment request's detail does not name the method; the webhook does. Informational only (shown in Filament),
        // so it is taken from the body without being trusted for status or amount.
        $method = $request->input('data.paymentMethod');
        if (($invoice['payment_method'] ?? null) === null && is_string($method) && $method !== '') {
            $invoice['payment_method'] = mb_substr($method, 0, 40);
        }

        $settle->handle($order, $invoice);

        return response()->json(['status' => 'ok']);
    }
}
