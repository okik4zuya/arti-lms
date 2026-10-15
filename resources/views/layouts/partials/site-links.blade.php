<a href="{{ route('fitur') }}" @if (request()->routeIs('fitur')) aria-current="page" @endif>Fitur</a>
<a href="{{ route('home') }}#harga">Harga</a>
<a href="{{ route('free.start') }}" @if (request()->routeIs('free.*')) aria-current="page" @endif>Baca bab 1 gratis</a>
<a href="{{ route('kontak') }}" @if (request()->routeIs('kontak')) aria-current="page" @endif>Kontak</a>
