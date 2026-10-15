<!DOCTYPE html>
<html lang="id">
<body style="font-family: Arial, Helvetica, sans-serif; color: #1f2937; line-height: 1.6; max-width: 520px; margin: 0 auto; padding: 16px;">
    <p>Halo,</p>
    <p>Seseorang (semoga Anda) meminta kabar dari ARTi dengan alamat email ini. Klik tombol di bawah untuk mengonfirmasi. Bila bukan Anda, abaikan saja email ini dan tidak ada yang akan dikirim.</p>
    <p style="margin: 24px 0;">
        <a href="{{ $confirmUrl }}" style="background: #2563eb; color: #ffffff; padding: 12px 20px; border-radius: 8px; text-decoration: none; font-weight: bold;">Ya, kabari saya</a>
    </p>
    <p>Tautan ini berlaku 7 hari.</p>
    <p>Sambil menunggu, bab 1 e-course ARTi Framework terbuka tanpa akun di <a href="{{ $readUrl }}">{{ $readUrl }}</a>.</p>
    @if ($fromPdf)
        <p>Untuk ARTi PDF: bila Windows menampilkan layar biru &ldquo;Windows protected your PC&rdquo; saat installer dijalankan, klik <strong>More info</strong>, lalu <strong>Run anyway</strong>. Itu muncul karena installer belum bertanda tangan digital. Penjelasan lengkap ada di <a href="{{ $pdfUrl }}">{{ $pdfUrl }}</a>.</p>
    @endif
    <p>Isinya nanti hanya kabar penting dari ARTi: tulisan baru, pembaruan alat gratis, dan batch harga berikutnya.</p>
    <hr style="border: 0; border-top: 1px solid #e5e7eb; margin: 24px 0;">
    <p style="font-size: 13px; color: #6b7280;">Tidak ingin menerima kabar dari ARTi? <a href="{{ $unsubscribeUrl }}">Berhenti berlangganan</a>.<br>ARTi tidak berafiliasi dengan Anthropic.</p>
</body>
</html>
