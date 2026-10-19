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
use Illuminate\Support\Facades\Validator;
use Illuminate\View\View;

/**
 * Buying through the payment gateway, fully automatic. When the gateway fails, the buyer is pointed to the support email. The success page reveals nothing but the order's state; the key and the
 * account only ever travel by email and WhatsApp.
 */
class CheckoutController extends Controller
{
    /** The form lives on the landing page; this address stays for old links, mails and the other pages' buttons. */
    public function show(): RedirectResponse
    {
        return redirect()->to(route('home').'#checkout');
    }

    public function store(Request $request, CreateCheckout $checkout): RedirectResponse
    {
        $form = route('home').'#checkout';

        if (! config('site.checkout.enabled')) {
            return redirect()->to($form)->withErrors(['pembayaran' => config('site.checkout.closed_notice')]);
        }

        $validator = Validator::make($request->all(), [
            'nama' => ['required', 'string', 'max:80'],
            'email' => ['required', 'email:rfc', 'max:190'],
            'whatsapp' => ['nullable', 'string', 'max:30', 'regex:/^\+?[0-9][0-9 \-]{7,18}$/'],
            'metode' => ['required', 'in:va,qris'],
            'setuju' => ['accepted'],
            'website' => ['nullable', 'max:0'], // honeypot: a person never fills it
        ], [
            'nama.required' => 'Isi nama Anda.',
            'email.required' => 'Isi alamat email Anda.',
            'email.email' => 'Alamat email tidak valid.',
            'whatsapp.regex' => 'Isi nomor saja, contoh 081234567890.',
            'metode.required' => 'Pilih metode pembayaran.',
            'metode.in' => 'Pilih metode pembayaran.',
            'setuju.accepted' => 'Centang persetujuan syarat dan kebijakan privasi.',
            'website.max' => 'Permintaan tidak dapat diproses.',
        ]);

        if ($validator->fails()) {
            return redirect()->to($form)->withInput()->withErrors($validator);
        }

        $data = $validator->validated();

        try {
            $order = $checkout->handle($data['nama'], $data['email'], Offer::normalizeNumber($data['whatsapp'] ?? null), $data['metode']);
        } catch (AlreadyPurchasedException) {
            return redirect()->to($form)->withInput()->withErrors(['email' => 'Email ini sudah punya lisensi aktif. Masuk ke akun Anda, atau hubungi kami bila ada yang keliru.']);
        } catch (PaymentGatewayException) {
            return redirect()->to($form)->withInput()->withErrors(['pembayaran' => 'Halaman pembayaran belum bisa dibuka. Coba lagi sebentar, atau hubungi '.config('site.contact_email').'.']);
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
