<a href="{{ route('fitur') }}" @if (request()->routeIs('fitur')) aria-current="page" @endif>Fitur</a>
<a href="{{ route('harga') }}" @if (request()->routeIs('harga')) aria-current="page" @endif>Harga</a>
<a href="{{ route('unduh') }}" @if (request()->routeIs('unduh')) aria-current="page" @endif>Unduh</a>
<a href="{{ route('articles.index') }}" @if (request()->routeIs('articles.*')) aria-current="page" @endif>Artikel</a>
<a href="{{ route('kontak') }}" @if (request()->routeIs('kontak')) aria-current="page" @endif>Kontak</a>
