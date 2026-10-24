{{-- Soft bridge to the paid package: sign-up (double opt-in) and the free chapter. No price, no buy button. --}}
<section class="landing-closing">
    <div class="container container--narrow">
        <h2>Bagian dari ARTi.</h2>
        <p>Alat gratis ini hanya sebagian kecil dari ARTi, framework riset untuk Claude Code. Di ARTi, gambar dibuat oleh agen yang menjalankan alatnya, memeriksa hasilnya, dan mengingat proyek Anda.</p>
        <div class="hero__actions hero__actions--center">
            <a href="{{ route('free.start') }}" class="btn btn--outline btn--lg">Baca bab 1 gratis</a>
            <a href="{{ route('fitur') }}" class="btn btn--outline btn--lg">Lihat isi ARTi</a>
        </div>
        <p class="subscribe__lead">Mau dikabari saat ada alat gratis baru?</p>
        <x-subscribe-form source="tools" button="Kabari saya" prefix="tools" />
        <p class="disclaimer">ARTi tidak berafiliasi dengan Google, OpenAI, atau Anthropic. Gemini, ChatGPT, dan Claude adalah produk masing-masing.</p>
    </div>
</section>
