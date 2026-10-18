<x-filament-panels::page>
    <form class="space-y-6" wire:submit.prevent>
        {{ $this->form }}
    </form>

    <p class="text-sm text-gray-500">
        Promo untuk event (harga khusus dengan tanggal berakhir) diatur di menu <strong>Promo</strong>.
        Perubahan di halaman ini dicatat: siapa, nilai lama, nilai baru.
    </p>
</x-filament-panels::page>
