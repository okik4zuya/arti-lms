<a href="{{ route('home') }}" @if (request()->routeIs('home')) aria-current="page" @endif>Beranda</a>
<a href="{{ route('fitur') }}" @if (request()->routeIs('fitur')) aria-current="page" @endif>Fitur</a>
<a href="{{ route('home') }}#harga">Harga</a>
<a href="{{ route('artipdf') }}" @if (request()->routeIs('artipdf')) aria-current="page" @endif>ARTi PDF</a>
<a href="{{ route('scipeek') }}" @if (request()->routeIs('scipeek')) aria-current="page" @endif>ARTi SciPeek</a>
