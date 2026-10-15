<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title', 'ARTi')</title>
</head>
<body style="margin: 0; padding: 0; background: #f4f6f8;">
    <div style="max-width: 560px; margin: 0 auto; padding: 24px 16px; font-family: Arial, Helvetica, sans-serif; font-size: 16px; line-height: 1.65; color: #1f2937;">
        <p style="margin: 0 0 20px; font-size: 20px; font-weight: bold; color: #2563eb;">ARTi</p>

        <div style="background: #ffffff; border-radius: 10px; padding: 24px;">
            @yield('content')
        </div>

        <p style="margin: 20px 0 0; font-size: 13px; line-height: 1.5; color: #6b7280;">
            Anda menerima email ini karena mendaftar untuk kabar dari ARTi di {{ parse_url(route('home'), PHP_URL_HOST) }}@isset($consentedOn) pada {{ $consentedOn }}@endisset.
            @if (! empty($unsubscribeUrl))
                <a href="{{ $unsubscribeUrl }}" style="color: #6b7280;">Berhenti berlangganan</a>.
            @else
                (Ini email uji: tautan berhenti tidak aktif.)
            @endif
            <br>ARTi tidak berafiliasi dengan Anthropic. Claude dan Claude Code adalah produk Anthropic.
        </p>
    </div>
</body>
</html>
