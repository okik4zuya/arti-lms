<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $title }} — {{ $manifest['title'] ?? $course->title }}</title>

    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600,700&display=swap" rel="stylesheet" />

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body>
    <div class="app-shell">
        <aside class="sidebar" id="sidebar">
            <div class="sidebar__header">
                <img src="{{ asset('images/wordmark.png') }}" alt="ARTi LMS" class="sidebar__wordmark">
                <span class="sidebar__name">{{ $manifest['title'] ?? $course->title }}</span>
            </div>
            <nav class="sidebar__nav">
                @foreach ($manifest['sections'] ?? [] as $section)
                    <div class="tree-group">{{ $section['title'] ?? '' }}</div>
                    @foreach ($section['chapters'] ?? [] as $chapter)
                        @php
                            $chapterIsActive = collect($chapter['subchapters'] ?? [])->contains('slug', $slug);
                        @endphp
                        <details class="tree-chapter" @if ($chapterIsActive) open @endif>
                            <summary class="tree-subgroup">{{ $chapter['title'] ?? '' }}</summary>
                            @foreach ($chapter['subchapters'] ?? [] as $subchapter)
                                <a
                                    href="{{ route('content.show', ['course' => $course, 'slug' => $subchapter['slug']]) }}"
                                    class="tree-item @if ($subchapter['slug'] === $slug) tree-item--active @endif"
                                >
                                    <span>{{ $subchapter['title'] ?? $subchapter['slug'] }}</span>
                                    @if ($completedSlugs->contains($subchapter['slug']))
                                        <span class="tree-item__check tree-item__check--done">&check;</span>
                                    @endif
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
                    <button type="button" class="sidebar-toggle" aria-controls="sidebar" aria-expanded="false" aria-label="Toggle course menu">☰</button>
                    <a href="{{ route('dashboard') }}" class="btn btn--outline topbar__back">&larr; Courses</a>
                </div>
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" class="btn btn--outline">Log out</button>
                </form>
            </div>
            <div class="content">
                {!! $html !!}

                <div class="chapter-complete">
                    @if ($isCompleted)
                        <span class="tree-item__check tree-item__check--done">&check;</span> Completed
                    @else
                        <form method="POST" action="{{ route('progress.store', ['course' => $course, 'slug' => $slug]) }}">
                            @csrf
                            <button type="submit" class="btn btn--outline">Mark as complete</button>
                        </form>
                    @endif
                </div>
            </div>
        </main>
    </div>
</body>
</html>
