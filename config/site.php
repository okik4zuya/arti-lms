<?php

return [

    /*
    | Public contact channels shown on /kontak. Left empty until the real ones are decided;
    | the page says so instead of showing placeholders.
    */
    'contact_email' => env('SITE_CONTACT_EMAIL'),
    'contact_whatsapp' => env('SITE_CONTACT_WHATSAPP'),

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
    | Landing page links filled in later. When empty the page shows "segera" instead of a dead link.
    */
    'free_chapter_url' => env('SITE_FREE_CHAPTER_URL'),
    'demo_video_embed_url' => env('SITE_DEMO_VIDEO_EMBED_URL'),
    'artipdf_url' => 'https://github.com/okik4zuya/artipdf/releases/latest',
    'youtube_url' => 'https://www.youtube.com/@dosenberarti',
    'scipeek_url' => 'https://chromewebstore.google.com/detail/arti-scipeek-%E2%80%94-journal-qu/dhbbdhnepmodmcheapjhcaolpcmpoanj',

];
