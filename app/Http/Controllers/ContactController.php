<?php

namespace App\Http\Controllers;

use App\Support\Offer;
use Illuminate\Http\Request;

class ContactController extends Controller
{
    /** Reasons offered in the form; the label opens the WhatsApp message. */
    public const TOPICS = [
        'pembelian' => 'Pembelian',
        'instalasi' => 'Bantuan instalasi',
        'uang-kembali' => 'Pengembalian dana',
        'lainnya' => 'Lainnya',
    ];

    /**
     * Pre-filled message per topic, so a buyer only has to tap send. The greeting is dropped because
     * the composed message already opens with "Halo".
     */
    public static function templates(): array
    {
        $buy = preg_replace('/^Halo,?\s*/i', '', (string) config('site.offer.whatsapp_message'));

        return ['pembelian' => ucfirst($buy)];
    }

    public function show(Request $request)
    {
        $topic = $request->query('topik');

        return view('site.kontak', [
            'topics' => self::TOPICS,
            'hasWhatsapp' => Offer::whatsappNumber() !== null,
            'selectedTopic' => array_key_exists((string) $topic, self::TOPICS) ? $topic : null,
        ]);
    }

    /**
     * Builds the message and sends the visitor to WhatsApp; nothing is stored on the server.
     * resources/js/contact-panel.js builds the same text, keep the two formats identical.
     */
    public function send(Request $request)
    {
        $data = $request->validate([
            'nama' => ['nullable', 'string', 'max:80'],
            'topik' => ['required', 'in:'.implode(',', array_keys(self::TOPICS))],
            'pesan' => ['required', 'string', 'max:1000'],
        ], [
            'topik.required' => 'Pilih keperluan Anda.',
            'topik.in' => 'Pilih keperluan dari daftar.',
            'pesan.required' => 'Tulis pesan Anda.',
            'pesan.max' => 'Pesan terlalu panjang (maksimal 1000 karakter).',
        ]);

        $nama = trim((string) ($data['nama'] ?? ''));

        $url = Offer::whatsappUrl(sprintf(
            "%s\nKeperluan: %s\n\n%s",
            $nama !== '' ? "Halo, saya {$nama}." : 'Halo.',
            self::TOPICS[$data['topik']],
            trim($data['pesan'])
        ));

        abort_if($url === null, 404);

        return redirect()->away($url);
    }
}
