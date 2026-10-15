<div class="space-y-6 text-sm">
    <section>
        <h3 class="mb-2 font-semibold">Ringkasan</h3>
        <dl class="grid grid-cols-3 gap-x-4 gap-y-1">
            <dt class="text-gray-500">Status</dt>
            <dd class="col-span-2">{{ $row->status }}@if ($row->expires_at), berlaku sampai {{ $row->expires_at->translatedFormat('j F Y') }}@endif</dd>
            <dt class="text-gray-500">Diterbitkan oleh</dt>
            <dd class="col-span-2">{{ $row->issuer?->name ?? '-' }}, {{ $row->created_at?->translatedFormat('j F Y') }}</dd>
            <dt class="text-gray-500">Perangkat</dt>
            <dd class="col-span-2">{{ $row->activations_count ?? 0 }} / {{ $row->seats ?? '-' }}</dd>
        </dl>
    </section>

    @if ($error)
        <p class="rounded-lg bg-warning-50 p-3 text-warning-700 dark:bg-warning-500/10 dark:text-warning-400">
            Data Worker tidak bisa diambil: {{ $error }} Hanya peristiwa lokal yang ditampilkan.
        </p>
    @endif

    <section>
        <h3 class="mb-2 font-semibold">Aktivasi</h3>
        @forelse ($activations as $a)
            <p>
                <span class="font-mono">{{ $a['machine'] }}</span>
                @if ($a['label']) ({{ $a['label'] }}) @endif
                , {{ $a['first_seen']?->translatedFormat('j M Y') ?? '-' }}
                , {{ $a['active'] ? 'aktif' : 'dilepas' }}
            </p>
        @empty
            <p class="text-gray-500">Belum ada aktivasi.</p>
        @endforelse
    </section>

    <section>
        <h3 class="mb-2 font-semibold">Peristiwa (terbaru di atas)</h3>
        @forelse ($events as $e)
            <p>
                {{ $e['at']?->translatedFormat('j M Y H:i') ?? '-' }}
                , {{ $e['who'] }}
                , {{ $e['event'] }}@if ($e['detail']) ({{ $e['detail'] }})@endif
            </p>
        @empty
            <p class="text-gray-500">Belum ada peristiwa.</p>
        @endforelse
    </section>

    <section>
        <h3 class="mb-2 font-semibold">Galat sinkron terakhir</h3>
        <p>{{ $row->sync_error ?: '-' }}</p>
    </section>
</div>
