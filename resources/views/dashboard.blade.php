<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Course Saya — ARTi LMS</title>


    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body>
    <a class="skip-link" href="#main-content">Lewati ke konten</a>
    <div class="app-shell">
        <main class="main">
            <div class="topbar">
                <div class="topbar__left">
                    <img src="{{ asset('images/wordmark.png') }}" alt="ARTi LMS" class="topbar__wordmark">
                    <span class="topbar__title">Course Saya</span>
                </div>
                <x-user-menu :user="auth()->user()" />
            </div>
            <div class="content" id="main-content" tabindex="-1">
                @if ($courses->isEmpty())
                    <div class="empty-state">
                        <h1 class="empty-state__title">Belum ada course di akun ini</h1>
                        <p class="meta">Akun ini ({{ auth()->user()->email }}) belum punya akses ke course mana pun. Beberapa kemungkinan:</p>
                        <ul class="empty-state__list">
                            <li>Anda masuk dengan akun yang berbeda dari email pembelian. Pakai menu di kanan atas, keluar, lalu masuk dengan email yang dipakai saat membeli.</li>
                            <li>Pembelian belum selesai diproses. Email konfirmasi biasanya tiba dalam beberapa menit.</li>
                        </ul>
                        <p class="meta">Masih bingung? <a href="{{ route('kontak') }}">Hubungi kami</a>.</p>
                    </div>
                @else
                    <div class="card-grid">
                        @foreach ($courses as $course)
                            <a
                                href="{{ $course->resumeSlug
                                    ? route('content.show', ['course' => $course, 'slug' => $course->resumeSlug])
                                    : '#' }}"
                                class="card"
                            >
                                <div class="card__body">
                                    <div class="card__title">{{ $course->title }}</div>
                                    <div class="progress">
                                        <span class="progress__label">Kemajuan</span>
                                        <span class="progress__track" role="progressbar" aria-label="Kemajuan {{ $course->title }}" aria-valuemin="0" aria-valuemax="100" aria-valuenow="{{ $course->progressPercent }}">
                                            <span class="progress__fill" style="width: {{ $course->progressPercent }}%"></span>
                                        </span>
                                        <span class="progress__value">{{ $course->progressPercent }}%</span>
                                    </div>
                                    @if ($course->resumeState !== 'empty')
                                        <div class="card__resume">
                                            <span class="card__resume-label">
                                                @if ($course->resumeState === 'continue') Lanjutkan belajar
                                                @elseif ($course->resumeState === 'start') Mulai belajar
                                                @else Selesai, baca lagi
                                                @endif
                                            </span>
                                            @if ($course->resumeTitle)
                                                <span class="card__resume-title">{{ $course->resumeTitle }}</span>
                                            @endif
                                        </div>
                                    @endif
                                </div>
                            </a>
                        @endforeach
                    </div>
                @endif
            </div>
        </main>
    </div>
</body>
</html>
