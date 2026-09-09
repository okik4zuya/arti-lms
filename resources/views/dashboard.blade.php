<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Dashboard — ARTi LMS</title>

    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600,700&display=swap" rel="stylesheet" />

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body>
    <div class="app-shell">
        <main class="main">
            <div class="topbar">
                <div class="topbar__left">
                    <img src="{{ asset('images/wordmark.png') }}" alt="ARTi LMS" class="topbar__wordmark">
                    <span class="topbar__title">My Courses</span>
                </div>
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" class="btn btn--outline">Log out</button>
                </form>
            </div>
            <div class="content">
                @if ($courses->isEmpty())
                    <p class="meta">You don't have access to any courses yet.</p>
                @else
                    <div class="card-grid">
                        @foreach ($courses as $course)
                            <a
                                href="{{ $course->firstChapterSlug
                                    ? route('content.show', ['course' => $course, 'slug' => $course->firstChapterSlug])
                                    : '#' }}"
                                class="card"
                            >
                                <div class="card__body">
                                    <div class="card__title">{{ $course->title }}</div>
                                    <div class="progress">
                                        <span class="progress__label">Progress</span>
                                        <span class="progress__track">
                                            <span class="progress__fill" style="width: {{ $course->progressPercent }}%"></span>
                                        </span>
                                        <span class="progress__value">{{ $course->progressPercent }}%</span>
                                    </div>
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
