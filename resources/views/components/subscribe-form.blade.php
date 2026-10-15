{{--
    Mailing-list sign-up. Works as a plain POST without JavaScript; resources/js/subscribe.js upgrades it
    to a fetch call. $source must be one of Subscriber::SOURCES, $prefix keeps ids unique when a page has
    more than one form. The "website" field is a honeypot for bots, hidden with CSS.
--}}
@props(['source', 'button' => 'Kirim', 'prefix' => 'sub'])
@php
    $flash = session('subscribe');
    $answered = $flash && ($flash['source'] ?? null) === $source;
    $error = old('source') === $source ? $errors->subscribe->first('email') : null;
    $id = $prefix.'-'.$source;
@endphp
<form method="POST" action="{{ route('subscribe.store') }}" class="stack" data-subscribe novalidate>
    @csrf
    <input type="hidden" name="source" value="{{ $source }}">
    <div class="subscribe__trap" aria-hidden="true">
        <label>Jangan diisi <input type="text" name="website" tabindex="-1" autocomplete="off"></label>
    </div>

    <div class="form-field" data-subscribe-fields>
        <label for="{{ $id }}-email" class="form-label">Alamat email</label>
        <input id="{{ $id }}-email" name="email" type="email" class="form-input" required autocomplete="email" inputmode="email"
               value="{{ $error ? old('email') : '' }}" @if ($error) aria-invalid="true" aria-describedby="{{ $id }}-error" @endif>
        <span id="{{ $id }}-error" class="form-error" data-subscribe-error @unless ($error) hidden @endunless>{{ $error }}</span>
    </div>

    <button type="submit" class="btn btn--primary" data-subscribe-button @if ($answered) hidden @endif>{{ $button }}</button>
    <p class="subscribe__status" role="status" data-subscribe-status @unless ($answered) hidden @endunless>{{ $answered ? $flash['message'] : '' }}</p>

    <p class="muted subscribe__consent">Dengan menekan tombol ini Anda setuju menerima email dari ARTi. Kami meminta konfirmasi lewat email lebih dulu, dan Anda bisa berhenti kapan saja. <a href="{{ route('privasi') }}">Kebijakan privasi</a>.</p>
</form>
