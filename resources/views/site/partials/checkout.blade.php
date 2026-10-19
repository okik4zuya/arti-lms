{{-- Buy form on the landing page (#checkout). Posts to /beli; errors and old input come back to this anchor. --}}
<div class="checkout" id="checkout">
    @if ($promotion = $offer->promotion())
        <p class="checkout__label">{{ $promotion->label }}</p>
        <p class="checkout__price"><sup>Rp</sup><strong>{{ number_format($promotion->price, 0, ',', '.') }}</strong></p>
        <p class="muted"><s>{{ $offer->format($offer->normalPrice()) }}</s> &middot; sampai {{ $promotion->ends_at->locale('id')->translatedFormat('j F Y, H.i') }}</p>
    @elseif ($offer->launchActive())
        <p class="checkout__label">Harga perintis. Tersisa {{ $offer->remaining() }} dari {{ $offer->quota() }}</p>
        <p class="checkout__price"><sup>Rp</sup><strong>{{ number_format($offer->launchPrice(), 0, ',', '.') }}</strong></p>
        <p class="muted"><s>{{ $offer->format($offer->normalPrice()) }}</s> &middot; setelah itu harga normal</p>
    @else
        <p class="checkout__label">Harga akses ARTi Framework</p>
        <p class="checkout__price"><sup>Rp</sup><strong>{{ number_format($offer->normalPrice(), 0, ',', '.') }}</strong></p>
    @endif
    <p class="muted">Sekali pembayaran &bull; tanpa biaya bulanan</p>

    <p class="checkout__product">Produk: ARTi Framework &ndash; e-course dan lisensi</p>

    @if (! $gateway->isConfigured())
        <p class="form-error" role="alert">Pembayaran otomatis sedang belum tersedia. Hubungi <a href="mailto:{{ config('site.contact_email') }}">{{ config('site.contact_email') }}</a> dan kami bantu.</p>
    @else
        <h3 class="checkout__heading">Data Pembeli</h3>

        @error('pembayaran') <p class="form-error" role="alert">{{ $message }}</p> @enderror

        <form method="POST" action="{{ route('checkout.store') }}" class="stack">
            @csrf
            <div class="form-field" style="position:absolute;left:-9999px" aria-hidden="true">
                <label for="beli-website">Website</label>
                <input id="beli-website" name="website" type="text" tabindex="-1" autocomplete="off">
            </div>

            <div class="form-field">
                <label for="beli-nama" class="form-label">Nama lengkap</label>
                <input id="beli-nama" name="nama" type="text" class="form-input" value="{{ old('nama') }}" maxlength="80" required autocomplete="name" placeholder="Masukkan nama lengkap" @error('nama') aria-invalid="true" @enderror>
                @error('nama') <span class="form-error">{{ $message }}</span> @enderror
            </div>

            <div class="checkout__pair">
                <div class="form-field">
                    <label for="beli-wa" class="form-label">Nomor WhatsApp <span class="muted">(opsional)</span></label>
                    <input id="beli-wa" name="whatsapp" type="tel" class="form-input" value="{{ old('whatsapp') }}" maxlength="30" autocomplete="tel" placeholder="08xxxxxxxxxx" aria-describedby="beli-wa-note" @error('whatsapp') aria-invalid="true" @enderror>
                    @error('whatsapp') <span class="form-error">{{ $message }}</span> @enderror
                </div>
                <div class="form-field">
                    <label for="beli-email" class="form-label">Email aktif</label>
                    <input id="beli-email" name="email" type="email" class="form-input" value="{{ old('email') }}" maxlength="190" required autocomplete="email" placeholder="nama@email.com" @error('email') aria-invalid="true" @enderror>
                    @error('email') <span class="form-error">{{ $message }}</span> @enderror
                </div>
            </div>
            <p class="muted checkout__note" id="beli-wa-note">Akun belajar, kunci lisensi, dan tautan unduhan dikirim ke email. Bila nomor WhatsApp diisi, notifikasi pengiriman produk dengan isi yang sama dikirim juga ke WhatsApp.</p>

            <fieldset class="checkout__methods">
                <legend class="form-label">Pilih metode pembayaran</legend>
                @php($pick = old('metode', 'va'))
                <label class="method">
                    <input type="radio" name="metode" value="va" @checked($pick === 'va') required>
                    <span class="method__card">
                        <svg viewBox="0 0 24 24" width="24" height="24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><rect x="3" y="6" width="18" height="13" rx="2"/><path d="M3 10h18M16 15h2"/></svg>
                        Virtual Account / GoPay / ShopeePay
                    </span>
                </label>
                <label class="method">
                    <input type="radio" name="metode" value="qris" @checked($pick === 'qris')>
                    <span class="method__card">
                        <svg viewBox="0 0 24 24" width="24" height="24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><rect x="3" y="3" width="7" height="7" rx="1"/><rect x="14" y="3" width="7" height="7" rx="1"/><rect x="3" y="14" width="7" height="7" rx="1"/><path d="M14 14h3v3h-3zM20 14v1M14 20h1M18 20h3v1"/></svg>
                        QRIS
                    </span>
                </label>
                @error('metode') <span class="form-error">{{ $message }}</span> @enderror
            </fieldset>

            <div class="form-field">
                <label for="beli-setuju" class="form-label checkout__agree">
                    <input id="beli-setuju" name="setuju" type="checkbox" value="1" @checked(old('setuju')) required>
                    <span>Saya setuju dengan <a href="{{ route('syarat') }}" target="_blank" rel="noopener">syarat</a> dan <a href="{{ route('privasi') }}" target="_blank" rel="noopener">kebijakan privasi</a>.</span>
                </label>
                @error('setuju') <span class="form-error">{{ $message }}</span> @enderror
            </div>

            <button type="submit" class="btn btn--cta btn--lg">Beli &amp; Akses Sekarang <span aria-hidden="true">&rarr;</span></button>
            <p class="muted checkout__note">Pesanan diproses aman melalui checkout {{ $gateway->label() }}. ARTi tidak menyimpan data kartu atau rekening Anda. Password tidak pernah dikirim: Anda memilihnya sendiri lewat tautan di email.</p>
        </form>

        <p class="checkout__note">Butuh bantuan? <a href="mailto:{{ config('site.contact_email') }}">{{ config('site.contact_email') }}</a></p>
    @endif
</div>
