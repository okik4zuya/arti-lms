@props(['user'])
@php
    $label = trim((string) $user->name) !== '' ? trim($user->name) : $user->email;
    $initial = mb_strtoupper(mb_substr($label, 0, 1));
@endphp
<div class="user-menu" data-user-menu>
    <button type="button" class="user-menu__button" aria-expanded="false" aria-controls="user-menu-panel" aria-label="Menu akun">
        <span class="user-menu__avatar" aria-hidden="true">{{ $initial }}</span>
        <svg class="user-menu__chevron" width="12" height="12" viewBox="0 0 12 12" aria-hidden="true" focusable="false"><path d="M2.5 4.5 6 8l3.5-3.5" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"/></svg>
    </button>
    <div class="user-menu__panel" id="user-menu-panel" hidden>
        <div class="user-menu__who">
            <strong class="user-menu__name">{{ $label }}</strong>
            @if ($label !== $user->email)
                <span class="user-menu__email">{{ $user->email }}</span>
            @endif
        </div>
        <a href="{{ route('password.change') }}" class="user-menu__item">Ubah password</a>
        <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button type="submit" class="user-menu__item user-menu__item--danger btn">Logout</button>
        </form>
    </div>
</div>
