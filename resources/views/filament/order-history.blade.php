<div class="space-y-2 text-sm">
    <p>
        <strong>{{ $order->name }}</strong> &middot; {{ $order->email }}<br>
        Rp{{ number_format($order->amount, 0, ',', '.') }} &middot; {{ $order->price_tier }}
        @if ($order->launch_slot) &middot; tempat perintis #{{ $order->launch_slot }} @endif
        @if ($order->gateway_ref) <br>Referensi {{ ucfirst($order->gateway) }}: {{ $order->gateway_ref }} @endif
    </p>
    @forelse ($events as $event)
        <p>
            <span class="text-gray-500">{{ $event->created_at?->format('d M Y H:i:s') }}</span>
            &middot; <strong>{{ $event->event }}</strong>
            @if ($event->detail) &middot; {{ $event->detail }} @endif
        </p>
    @empty
        <p class="text-gray-500">Belum ada kejadian.</p>
    @endforelse
</div>
