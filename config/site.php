<?php

return [

    /*
    | Public contact channels shown on /kontak. Left empty until the real ones are decided;
    | the page says so instead of showing placeholders.
    */
    'contact_email' => env('SITE_CONTACT_EMAIL', 'hi@arti.ai.id'),
    'contact_whatsapp' => env('SITE_CONTACT_WHATSAPP'),

    /*
    | Who sells ARTi, shown on /kontak (electronic trade rules ask for the seller's identity). Left empty until the
    | owner fills it; nothing is invented. Name and the city of domicile are enough for an individual seller.
    */
    'seller' => [
        'name' => env('SITE_SELLER_NAME'),
        'city' => env('SITE_SELLER_CITY'),
    ],

    /*
    | Admin panel (/jamrud). `mfa_required`: admin and support must set up an authenticator app before they
    | can use the panel. OFF for now (decision 2026-10-15, MFA postponed); the code is built and tested, so
    | set ADMIN_MFA_REQUIRED=true before the panel is used in production.
    */
    'admin' => [
        'mfa_required' => env('ADMIN_MFA_REQUIRED', false),
    ],

    /*
    | Buy form on the landing page. Set SITE_CHECKOUT_ENABLED=false to show the form greyed out with a notice
    | (e.g. while the payment gateway application is still under review). `closed_notice` is the text shown.
    */
    'checkout' => [
        'enabled' => (bool) env('SITE_CHECKOUT_ENABLED', true),
        'closed_notice' => env('SITE_CHECKOUT_NOTICE', 'Pembelian belum dibuka. Verifikasi akun pembayaran kami sedang berjalan. Begitu disetujui, formulir ini aktif otomatis dan Anda bisa langsung membeli.'),
    ],

    /*
    | Landing page offer. Prices are whole rupiah. `launch_sold` is the number of licenses the admin
    | has actually issued at the launch price; leave it empty to hide the live counter. Never round it.
    */
    'offer' => [
        'price_normal' => 199000,
        'price_launch' => 99000,
        'launch_quota' => 100,
        'launch_sold' => env('SITE_LAUNCH_SOLD'),
        // Lifetime purchase: the Worker has no "no expiry", so a license is issued for this many months (about 83 years).
        'license_months' => 1000,
        // The e-course every buyer gets access to.
        'course_slug' => 'arti-framework',
        // How long a payment page stays open; a pending order holds its launch place for this long.
        'payment_window_minutes' => 60,
        'whatsapp_message' => 'Halo, saya ingin membeli ARTi paket perintis.',
        'chat_message' => 'Halo, saya ingin bertanya tentang ARTi.',
    ],

    /*
    | Message the admin sends a buyer with the license key (Filament license page). Placeholders:
    | {name} {key} {seats} {expires} {installer} {account}
    | {account} is the login block below; it is empty when the license has no learner account.
    */
    'license' => [
        'whatsapp_template' => "Halo {name}, terima kasih sudah membeli ARTi.\n\n"
            ."Kunci lisensi Anda ({seats} perangkat, {expires}):\n\n{key}\n\n"
            ."1. Masuk ke e-course Anda, buka bagian Download, lalu unduh installer: {installer}\n"
            ."2. Jalankan installer dan masukkan kunci lisensi di atas saat diminta.\n\n"
            .'{account}'
            .'Simpan kunci ini dan jangan dibagikan. Balas pesan ini bila butuh bantuan atau ingin memindahkan lisensi ke komputer lain.',
        // Automatic purchases (WhatsApp copy of the purchase mail). Placeholders: {name} {email} {password_link}
        // {key} {seats} {validity} {installer}. {password_link} already carries its own sentence and the URL.
        'whatsapp_purchase_template' => "Halo {name}, terima kasih sudah membeli ARTi. Pembayaran Anda sudah kami terima.\n\n"
            ."Akun belajar: {email}\n"
            ."{password_link}"
            ."Kunci lisensi ({seats} perangkat, {validity}):\n{key}\n\n"
            ."1. Masuk ke e-course Anda, buka bagian Download, lalu unduh installer: {installer}\n"
            ."2. Jalankan installer dan masukkan kunci lisensi di atas saat diminta.\n\n"
            .'Password tidak pernah dikirim; Anda memilihnya sendiri lewat tautan. Simpan kunci ini dan jangan dibagikan. Balas pesan ini bila butuh bantuan.',
        // Placeholders: {email} {password_link} {login_url}. Used when a one-time set-password link was just made.
        // No password is ever sent: the buyer chooses their own through the link.
        'account_template' => "Akun belajar Anda:\nEmail: {email}\nAtur password Anda lewat tautan sekali pakai ini (tanpa batas waktu, berlaku sampai password diatur):\n{password_link}\nSesudah itu masuk di: {login_url}\n\n",
        // Used when the email already had an account (the password is not known here).
        'account_existing_template' => "Akun belajar Anda memakai email {email} (password lama Anda tetap berlaku). Masuk di: {login_url}\n\n",
    ],

    /*
    | Landing page links filled in later. When empty the page shows "segera" instead of a dead link.
    */
    'free_chapter_url' => env('SITE_FREE_CHAPTER_URL'),
    'demo_video_embed_url' => env('SITE_DEMO_VIDEO_EMBED_URL'),
    'artipdf_url' => 'https://github.com/okik4zuya/artipdf/releases/latest',
    // Direct link to the installer on the public R2 bucket (cdn.arti.ai.id), built from the version
    // below; ARTIPDF_DOWNLOAD_URL overrides it. With neither, /artipdf/unduh sends the visitor to the
    // GitHub release page instead, so the page never has a dead button.
    'artipdf_download_url' => env('ARTIPDF_DOWNLOAD_URL') ?: 'https://cdn.arti.ai.id/downloads/artipdf/ArtiPDFSetup-1.0.7.exe',
    // SHA-256 of that exact file (the installer is not code-signed); ARTIPDF_SHA256 overrides it.
    // On a new release change the version, this URL and this hash together after uploading the file.
    'artipdf_sha256' => env('ARTIPDF_SHA256') ?: '337e15a23160c911c846e22d46269b6469baa7ad32331c5fac0a7cee66644f9b',
    // Shown on /artipdf; update with each release (the download link above always points to the latest one).
    'artipdf_version' => '1.0.7',
    'artipdf_size' => '63 MB',
    'youtube_url' => 'https://www.youtube.com/@dosenberarti',
    'scipeek_url' => 'https://chromewebstore.google.com/detail/arti-scipeek-%E2%80%94-journal-qu/dhbbdhnepmodmcheapjhcaolpcmpoanj',

    /*
    | Mailing list. The welcome series (subscribers:send-series, hourly) stays off until MAIL_SERIES_ENABLED=true,
    | so deploying never starts mailing people before the copy in resources/views/mail/series has been read.
    | MAIL_SERIES_BATCH caps how many people one run queues (Brevo's free plan allows 300 mails a day).
    */
    'mail' => [
        'series_enabled' => (bool) env('MAIL_SERIES_ENABLED', false),
        'series_batch' => (int) env('MAIL_SERIES_BATCH', 100),
    ],

];
