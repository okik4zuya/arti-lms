<?php

namespace App\Http\Controllers;

use App\Actions\Subscribers\SendConfirmation;
use App\Models\Subscriber;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

/**
 * Mailing-list sign-up with double opt-in. The answer to a sign-up is always the same, so the form
 * cannot be used to find out whether an address is already on the list.
 */
class SubscriptionController extends Controller
{
    public const ANSWER = 'Hampir selesai. Kami mengirim email berisi tautan konfirmasi. Bila belum masuk dalam beberapa menit, periksa folder spam.';

    public function store(Request $request)
    {
        $data = $request->validateWithBag('subscribe', [
            'email' => ['required', 'email:rfc', 'max:190'],
            'source' => ['required', Rule::in(array_keys(Subscriber::SOURCES))],
        ], [
            'email.required' => 'Tulis alamat email Anda.',
            'email.email' => 'Alamat email tampaknya belum benar.',
            'email.max' => 'Alamat email terlalu panjang.',
        ]);

        // Bots fill the hidden field; they get the normal answer and nothing is stored.
        if (! $request->filled('website')) {
            $this->register(mb_strtolower(trim($data['email'])), $data['source']);
        }

        return $request->expectsJson()
            ? response()->json(['message' => self::ANSWER])
            : redirect(url()->previous().'#daftar')->with('subscribe', ['source' => $data['source'], 'message' => self::ANSWER]);
    }

    public function confirm(Subscriber $subscriber)
    {
        if ($subscriber->unsubscribed_at) {
            return view('site.subscription', [
                'heading' => 'Anda sudah berhenti berlangganan',
                'body' => 'Alamat ini sudah dihapus dari daftar. Bila ingin kembali, daftar lagi dari halaman bab 1 gratis atau ARTi PDF.',
            ]);
        }

        $subscriber->forceFill(['confirmed_at' => $subscriber->confirmed_at ?? now()])->save();

        return view('site.subscription', [
            'heading' => 'Terima kasih, alamat Anda terkonfirmasi',
            'body' => 'Kami hanya mengirim kabar penting: tulisan baru, pembaruan alat gratis, dan batch harga berikutnya. Setiap email memuat tautan untuk berhenti.',
        ]);
    }

    public function unsubscribe(Subscriber $subscriber)
    {
        $subscriber->forceFill(['unsubscribed_at' => $subscriber->unsubscribed_at ?? now()])->save();

        return view('site.subscription', [
            'heading' => 'Anda sudah berhenti berlangganan',
            'body' => 'Alamat ini tidak akan menerima email dari ARTi lagi. Untuk menghapus datanya sekalian, hubungi kami lewat halaman kontak.',
        ]);
    }

    /** Creates or revives the row and sends the confirmation when one is due. */
    protected function register(string $email, string $source): void
    {
        $subscriber = Subscriber::firstOrNew(['email' => $email]);

        if (! $subscriber->exists) {
            $subscriber->fill(['source' => $source, 'consented_at' => now()])->save();
        } elseif ($subscriber->unsubscribed_at) {
            // Signing up again after leaving starts over, and needs a fresh confirmation.
            $subscriber->forceFill([
                'source' => $source,
                'consented_at' => now(),
                'confirmed_at' => null,
                'unsubscribed_at' => null,
                'confirmation_sent_at' => null,
            ])->save();
        } elseif ($subscriber->confirmed_at || ! $subscriber->canResendConfirmation()) {
            return;
        }

        app(SendConfirmation::class)->handle($subscriber);
    }
}
