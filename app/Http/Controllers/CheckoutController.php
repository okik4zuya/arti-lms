<?php

namespace App\Http\Controllers;

use App\Actions\Orders\AlreadyPurchasedException;
use App\Actions\Orders\CreateCheckout;
use App\Models\Order;
use App\Services\Payments\PaymentGateway;
use App\Services\Payments\PaymentGatewayException;
use App\Support\Offer;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

/**
 * Buying through the payment gateway. WhatsApp stays as the fallback: when the gateway is not configured, or
 * fails, the buyer is pointed there. The success page reveals nothing but the order's state; the key and the
 * account only ever travel by email and WhatsApp.
 */
class CheckoutController extends Controller
{
    public function show(Offer $offer, PaymentGateway $gateway): View|RedirectResponse
    {
        if (! $gateway->isConfigured()) {
            return redirect()->away($offer->buyUrl());
        }

        return view('site.beli', ['offer' => $offer, 'promotion' => $offer->promotion(), 'gateway' => $gateway]);
    }

    public function store(Request $request, CreateCheckout $checkout): RedirectResponse
    {
        $data = $request->validate([
            'nama' => ['required', 'string', 'max:80'],
            'email' => ['required', 'email:rfc', 'max:190'],
            'whatsapp' => ['nullable', 'string', 'max:30', 'regex:/^\+?[0-9][0-9 \-]{7,18}$/'],
            'setuju' => ['accepted'],
            'website' => ['nullable', 'max:0'], // honeypot: a person never fills it
        ], [
            'nama.required' => 'Isi nama Anda.',
            'email.required' => 'Isi alamat email Anda.',
            'email.email' => 'Alamat email tidak valid.',
            'whatsapp.regex' => 'Isi nomor saja, contoh 081234567890.',
            'setuju.accepted' => 'Centang persetujuan syarat dan kebijakan privasi.',
            'website.max' => 'Permintaan tidak dapat diproses.',
        ]);

        try {
            $order = $checkout->handle($data['nama'], $data['email'], Offer::normalizeNumber($data['whatsapp'] ?? null));
        } catch (AlreadyPurchasedException) {
            return back()->withInput()->withErrors(['email' => 'Email ini sudah punya lisensi aktif. Masuk ke akun Anda, atau hubungi kami bila ada yang keliru.']);
        } catch (PaymentGatewayException) {
            return back()->withInput()->withErrors(['pembayaran' => 'Halaman pembayaran belum bisa dibuka. Coba lagi sebentar, atau beli lewat WhatsApp.']);
        }

        return redirect()->away($order->checkout_url);
    }

    public function done(Request $request, string $order): View
    {
        $order = Order::where('public_id', $order)->firstOrFail();

        $state = match (true) {
            in_array($order->status, [Order::PAID, Order::FULFILLING, Order::FULFILLED, Order::NEEDS_ATTENTION, Order::REFUNDED], true) => 'paid',
            $order->status === Order::PENDING && $order->expires_at?->isFuture() => 'pending',
            default => 'closed',
        };

        return view('site.beli-selesai', [
            'state' => $state,
            'order' => $order,
            // Masked: the page is reachable by anyone who holds the order id (it is in the redirect URL).
            'email' => preg_replace('/^(.).*?(@.*)$/u', '$1***$2', $order->email),
            'failed' => $request->boolean('gagal'),
            'offer' => app(Offer::class),
            'gateway' => app(PaymentGateway::class),
        ]);
    }
}
