<?php

return [

    /*
    | Public contact channels shown on /kontak. Left empty until the real ones are decided;
    | the page says so instead of showing placeholders.
    */
    'contact_email' => env('SITE_CONTACT_EMAIL'),
    'contact_whatsapp' => env('SITE_CONTACT_WHATSAPP'),

    /*
    | Admin panel (/jamrud). `mfa_required`: admin and support must set up an authenticator app before they
    | can use the panel. OFF for now (decision 2026-10-15, MFA postponed); the code is built and tested, so
    | set ADMIN_MFA_REQUIRED=true before the panel is used in production.
    */
    'admin' => [
        'mfa_required' => env('ADMIN_MFA_REQUIRED', false),
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
        'guarantee_days' => 7,
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
            ."Kunci lisensi Anda ({seats} perangkat, berlaku sampai {expires}):\n\n{key}\n\n"
            ."1. Unduh dan jalankan installer: {installer}\n"
            ."2. Buka terminal lalu jalankan: arti-license activate {key}\n\n"
            .'{account}'
            .'Simpan kunci ini dan jangan dibagikan. Balas pesan ini bila butuh bantuan atau ingin memindahkan lisensi ke komputer lain.',
        // Placeholders: {email} {password} {login_url}. Used when the account was created with the license.
        'account_template' => "Akun belajar Anda:\nEmail: {email}\nPassword sementara: {password}\nMasuk di: {login_url}\nSimpan password ini baik-baik.\n\n",
        // Used when the email already had an account (the password is not known here).
        'account_existing_template' => "Akun belajar Anda memakai email {email} (password lama Anda tetap berlaku). Masuk di: {login_url}\n\n",
    ],

    /*
    | Landing page links filled in later. When empty the page shows "segera" instead of a dead link.
    */
    'free_chapter_url' => env('SITE_FREE_CHAPTER_URL'),
    'demo_video_embed_url' => env('SITE_DEMO_VIDEO_EMBED_URL'),
    'artipdf_url' => 'https://github.com/okik4zuya/artipdf/releases/latest',
    // Direct link to the installer on the public R2 bucket (cdn-arti.invait.id), built from the version
    // below; ARTIPDF_DOWNLOAD_URL overrides it. With neither, /artipdf/unduh sends the visitor to the
    // GitHub release page instead, so the page never has a dead button.
    'artipdf_download_url' => env('ARTIPDF_DOWNLOAD_URL') ?: 'https://cdn-arti.invait.id/downloads/artipdf/ArtiPDFSetup-1.0.7.exe',
    // SHA-256 of that exact file (the installer is not code-signed); ARTIPDF_SHA256 overrides it.
    // On a new release change the version, this URL and this hash together after uploading the file.
    'artipdf_sha256' => env('ARTIPDF_SHA256') ?: '337e15a23160c911c846e22d46269b6469baa7ad32331c5fac0a7cee66644f9b',
    // Shown on /artipdf; update with each release (the download link above always points to the latest one).
    'artipdf_version' => '1.0.7',
    'artipdf_size' => '63 MB',
    'youtube_url' => 'https://www.youtube.com/@dosenberarti',
    'scipeek_url' => 'https://chromewebstore.google.com/detail/arti-scipeek-%E2%80%94-journal-qu/dhbbdhnepmodmcheapjhcaolpcmpoanj',

];
