@extends('layouts.site')

@section('title', 'Beli ARTi')
@section('description', 'Beli ARTi Framework: bayar dengan QRIS, virtual account, atau e-wallet. Akun belajar, kunci lisensi, dan tautan unduhan dikirim ke email Anda.')

@push('head')
    <meta name="robots" content="noindex">
@endpush

@section('content')
<div class="container container--narrow">
    <section class="hero">
        <p class="hero__eyebrow">Pembelian</p>
        <h1>Beli ARTi Framework.</h1>
        <p class="hero__lead">Bayar dengan QRIS, virtual account, atau e-wallet. Setelah pembayaran diterima, akun belajar, kunci lisensi, dan tautan unduhan dikirim ke email Anda.</p>
    </section>

    <div class="panel">
        <p class="price-line">
            @if ($promotion)
                <strong>{{ $promotion->label }}</strong>:
                <s>{{ $offer->format($offer->normalPrice()) }}</s>
                <strong>{{ $offer->format($promotion->price) }}</strong>
                <span class="muted">sampai {{ $promotion->ends_at->locale('id')->translatedFormat('j F Y, H.i') }}</span>
            @elseif ($offer->launchActive())
                Harga perintis: <s>{{ $offer->format($offer->normalPrice()) }}</s>
                <strong>{{ $offer->format($offer->launchPrice()) }}</strong>
                <span class="muted">tersisa {{ $offer->remaining() }} dari {{ $offer->quota() }}</span>
            @else
                Harga: <strong>{{ $offer->format($offer->normalPrice()) }}</strong>
            @endif
        </p>

        @error('pembayaran') <p class="form-error" role="alert">{{ $message }}</p> @enderror

        <form method="POST" action="{{ route('checkout.store') }}" class="stack">
            @csrf
            <div class="form-field" style="position:absolute;left:-9999px" aria-hidden="true">
                <label for="beli-website">Website</label>
                <input id="beli-website" name="website" type="text" tabindex="-1" autocomplete="off">
            </div>

            <div class="form-field">
                <label for="beli-nama" class="form-label">Nama</label>
                <input id="beli-nama" name="nama" type="text" class="form-input" value="{{ old('nama') }}" maxlength="80" required autocomplete="name" @error('nama') aria-invalid="true" @enderror>
                @error('nama') <span class="form-error">{{ $message }}</span> @enderror
            </div>

            <div class="form-field">
                <label for="beli-email" class="form-label">Email</label>
                <input id="beli-email" name="email" type="email" class="form-input" value="{{ old('email') }}" maxlength="190" required autocomplete="email" @error('email') aria-invalid="true" @enderror>
                <span class="muted">Akun belajar dan kunci lisensi dikirim ke sini. Pastikan benar.</span>
                @error('email') <span class="form-error">{{ $message }}</span> @enderror
            </div>

            <div class="form-field">
                <label for="beli-wa" class="form-label">WhatsApp <span class="muted">(boleh dikosongkan)</span></label>
                <input id="beli-wa" name="whatsapp" type="tel" class="form-input" value="{{ old('whatsapp') }}" maxlength="30" autocomplete="tel" placeholder="081234567890" @error('whatsapp') aria-invalid="true" @enderror>
                <span class="muted">Bila diisi, ringkasan yang sama dikirim juga ke WhatsApp.</span>
                @error('whatsapp') <span class="form-error">{{ $message }}</span> @enderror
            </div>

            <div class="form-field">
                <label for="beli-setuju" class="form-label">
                    <input id="beli-setuju" name="setuju" type="checkbox" value="1" @checked(old('setuju')) required>
                    Saya setuju dengan <a href="{{ route('syarat') }}" target="_blank" rel="noopener">syarat</a> dan <a href="{{ route('privasi') }}" target="_blank" rel="noopener">kebijakan privasi</a>.
                </label>
                @error('setuju') <span class="form-error">{{ $message }}</span> @enderror
            </div>

            <button type="submit" class="btn btn--primary btn--lg">Lanjut ke pembayaran</button>
            <p class="muted">Pembayaran diproses {{ $gateway->label() }}. ARTi tidak menyimpan data kartu atau rekening Anda. Password tidak pernah dikirim: Anda memilihnya sendiri lewat tautan di email.</p>
        </form>

        <p>Lebih suka lewat WhatsApp? <a href="{{ $offer->buyUrl() }}" data-kontak="pembelian">Beli lewat WhatsApp</a>.</p>
    </div>
</div>
@endsection
