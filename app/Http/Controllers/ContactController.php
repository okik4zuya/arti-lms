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
        'uang-kembali' => 'Uang kembali (garansi)',
        'lainnya' => 'Lainnya',
    ];

    public function show()
    {
        return view('site.kontak', ['topics' => self::TOPICS, 'hasWhatsapp' => Offer::whatsappNumber() !== null]);
    }

    /** Builds the message and sends the visitor to WhatsApp; nothing is stored on the server. */
    public function send(Request $request)
    {
        $data = $request->validate([
            'nama' => ['required', 'string', 'max:80'],
            'topik' => ['required', 'in:'.implode(',', array_keys(self::TOPICS))],
            'pesan' => ['required', 'string', 'max:1000'],
        ], [
            'nama.required' => 'Isi nama Anda.',
            'topik.required' => 'Pilih keperluan Anda.',
            'topik.in' => 'Pilih keperluan dari daftar.',
            'pesan.required' => 'Tulis pesan Anda.',
            'pesan.max' => 'Pesan terlalu panjang (maksimal 1000 karakter).',
        ]);

        $url = Offer::whatsappUrl(sprintf(
            "Halo, saya %s.\nKeperluan: %s\n\n%s",
            trim($data['nama']),
            self::TOPICS[$data['topik']],
            trim($data['pesan'])
        ));

        abort_if($url === null, 404);

        return redirect()->away($url);
    }
}
