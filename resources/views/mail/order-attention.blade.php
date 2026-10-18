Pesanan {{ $reference }} berhenti dan butuh keputusan Anda.

Alasan: {{ $note }}
@if ($email)
Email pembeli: {{ $email }}
@endif
@if ($amount !== null)
Jumlah pesanan: Rp{{ number_format($amount, 0, ',', '.') }}
@endif

Buka Filament, menu Pesanan, cari referensi di atas. Uang mungkin sudah masuk; bila pembeli perlu dikembalikan, lakukan lewat dashboard penyedia pembayaran (Midtrans atau Xendit) lalu tandai pesanan.
