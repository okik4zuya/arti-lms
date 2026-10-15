{{-- Contact form shared by /kontak and the WhatsApp panel. $idPrefix keeps ids unique; $selectedTopic is optional. --}}
@php $selected = old('topik', $selectedTopic ?? null); @endphp
<form method="POST" action="{{ route('kontak.send') }}" class="stack" data-contact-form novalidate>
    @csrf

    <div class="form-field">
        <label for="{{ $idPrefix }}-nama" class="form-label">Nama <span class="muted">(boleh dikosongkan)</span></label>
        <input id="{{ $idPrefix }}-nama" name="nama" type="text" class="form-input" value="{{ old('nama') }}" maxlength="80" autocomplete="name" @error('nama') aria-invalid="true" aria-describedby="{{ $idPrefix }}-nama-error" @enderror>
        @error('nama') <span id="{{ $idPrefix }}-nama-error" class="form-error">{{ $message }}</span> @enderror
    </div>

    <div class="form-field">
        <label for="{{ $idPrefix }}-topik" class="form-label">Keperluan</label>
        <select id="{{ $idPrefix }}-topik" name="topik" class="form-input" required @error('topik') aria-invalid="true" aria-describedby="{{ $idPrefix }}-topik-error" @enderror>
            @foreach ($topics as $value => $label)
                <option value="{{ $value }}" @selected($selected === $value)>{{ $label }}</option>
            @endforeach
        </select>
        @error('topik') <span id="{{ $idPrefix }}-topik-error" class="form-error">{{ $message }}</span> @enderror
    </div>

    <div class="form-field">
        <label for="{{ $idPrefix }}-pesan" class="form-label">Pesan</label>
        <textarea id="{{ $idPrefix }}-pesan" name="pesan" rows="4" class="form-input" maxlength="1000" required @error('pesan') aria-invalid="true" aria-describedby="{{ $idPrefix }}-pesan-error" @enderror>{{ old('pesan') }}</textarea>
        @error('pesan') <span id="{{ $idPrefix }}-pesan-error" class="form-error">{{ $message }}</span> @enderror
        <span class="form-error" data-error-for="pesan" hidden></span>
    </div>

    <button type="submit" class="btn btn--primary">Kirim lewat WhatsApp</button>
    <p class="muted">Tombol ini membuka WhatsApp dengan pesan yang sudah terisi. Pesan baru terkirim setelah Anda menekan kirim di WhatsApp, dan tidak disimpan di situs ini.</p>
</form>
