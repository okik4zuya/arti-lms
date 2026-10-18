<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $title }} — {{ $manifest['title'] ?? $course->title }}</title>


    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body>
    <a class="skip-link" href="#main-content">Lewati ke konten</a>
    <div class="app-shell">
        <aside class="sidebar" id="sidebar">
            <div class="sidebar__header">
                <img src="{{ asset('images/wordmark.png') }}" alt="ARTi LMS" class="sidebar__wordmark">
                <span class="sidebar__name">{{ $manifest['title'] ?? $course->title }}</span>
            </div>
            <nav class="sidebar__nav">
                @foreach ($manifest['sections'] ?? [] as $section)
                    @php $subchapterNumber = 0; @endphp
                    <div class="tree-group">{{ $section['title'] ?? '' }}</div>
                    @foreach ($section['chapters'] ?? [] as $chapter)
                        @php
                            $chapterIsActive = collect($chapter['subchapters'] ?? [])->contains('slug', $slug);
                        @endphp
                        <details class="tree-chapter" @if ($chapterIsActive) open @endif>
                            <summary class="tree-subgroup">{{ $chapter['title'] ?? '' }}</summary>
                            @foreach ($chapter['subchapters'] ?? [] as $subchapter)
                                @php
                                    $subchapterNumber++;
                                    $subchapterIsDone = $completedSlugs->contains($subchapter['slug']);
                                @endphp
                                <a
                                    href="{{ route('content.show', ['course' => $course, 'slug' => $subchapter['slug']]) }}"
                                    class="tree-item @if ($subchapter['slug'] === $slug) tree-item--active @endif"
                                >
                                    <span class="tree-item__index @if ($subchapterIsDone) tree-item__index--done @endif">
                                        @if ($subchapterIsDone)
                                            &check;
                                        @else
                                            {{ $subchapterNumber }}
                                        @endif
                                    </span>
                                    <span>{{ $subchapter['title'] ?? $subchapter['slug'] }}</span>
                                </a>
                            @endforeach
                        </details>
                    @endforeach
                @endforeach
            </nav>
        </aside>
        <div class="sidebar-backdrop" hidden></div>
        <main class="main">
            <div class="topbar topbar--dark">
                <div class="topbar__left">
                    <button type="button" class="sidebar-toggle" aria-controls="sidebar" aria-expanded="false" aria-label="Buka menu course">☰</button>
                    <a href="{{ route('dashboard') }}" class="btn btn--outline topbar__back"><svg width="14" height="14" viewBox="0 0 14 14" aria-hidden="true" focusable="false"><path d="M8.5 3 4.5 7l4 4" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/></svg> Course Saya</a>
                </div>
                <x-user-menu :user="auth()->user()" />
            </div>
            <div class="content" id="main-content" tabindex="-1">
                {!! $html !!}

                <div class="chapter-complete" aria-live="polite">
                    @if ($isCompleted)
                        <span class="tree-item__check tree-item__check--done">&check;</span> Selesai
                    @else
                        <form method="POST" action="{{ route('progress.store', ['course' => $course, 'slug' => $slug]) }}" data-progress-form data-slug="{{ $slug }}">
                            @csrf
                            <button type="submit" class="btn btn--outline">Tandai selesai</button>
                        </form>
                    @endif
                </div>

                @if ($previous || $next)
                    <nav class="chapter-nav" aria-label="Navigasi subbab">
                        @if ($previous)
                            <a class="chapter-nav__link chapter-nav__link--prev" href="{{ route('content.show', ['course' => $course, 'slug' => $previous['slug']]) }}" rel="prev">
                                <span class="chapter-nav__label"><svg width="14" height="14" viewBox="0 0 14 14" aria-hidden="true" focusable="false"><path d="M8.5 3 4.5 7l4 4" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/></svg> Sebelumnya</span>
                                <span class="chapter-nav__title">{{ $previous['title'] }}</span>
                            </a>
                        @endif
                        @if ($next)
                            <a class="chapter-nav__link chapter-nav__link--next" href="{{ route('content.show', ['course' => $course, 'slug' => $next['slug']]) }}" rel="next">
                                <span class="chapter-nav__label">Selanjutnya <svg width="14" height="14" viewBox="0 0 14 14" aria-hidden="true" focusable="false"><path d="M5.5 3l4 4-4 4" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/></svg></span>
                                <span class="chapter-nav__title">{{ $next['title'] }}</span>
                            </a>
                        @endif
                    </nav>
                @endif
            </div>
        </main>
    </div>
</body>
</html>
