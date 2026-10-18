@props(['id', 'name', 'autocomplete' => 'current-password', 'autofocus' => false, 'rules' => false])
{{-- Password field with a show/hide button (resources/js/password-field.js). With `rules`, the three requirements light up as they are met. --}}
<div class="password-field" data-password-field>
    <input
        id="{{ $id }}"
        type="password"
        name="{{ $name }}"
        class="form-input"
        required
        autocomplete="{{ $autocomplete }}"
        @if ($autofocus) autofocus @endif
        @if ($rules) aria-describedby="{{ $id }}-rules" @endif
        {{ $attributes }}
    >
    <button type="button" class="password-field__toggle" aria-pressed="false" aria-label="Tampilkan password" aria-controls="{{ $id }}" data-password-toggle><svg class="password-field__icon password-field__icon--show" width="18" height="18" viewBox="0 0 24 24" aria-hidden="true" focusable="false"><path d="M2 12s3.6-7 10-7 10 7 10 7-3.6 7-10 7S2 12 2 12Z" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/><circle cx="12" cy="12" r="3" fill="none" stroke="currentColor" stroke-width="1.8"/></svg><svg class="password-field__icon password-field__icon--hide" width="18" height="18" viewBox="0 0 24 24" aria-hidden="true" focusable="false"><path d="M3 3l18 18M10.6 6.1A9.8 9.8 0 0 1 12 5c6.4 0 10 7 10 7a17 17 0 0 1-3.2 4.2M6.6 6.7C3.9 8.5 2 12 2 12s3.6 7 10 7c1.7 0 3.2-.4 4.5-1M9.9 9.9a3 3 0 0 0 4.2 4.2" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/></svg></button>
</div>
@if ($rules)
    <p class="password-rules__title"><strong>Syarat password:</strong></p>
    <ul class="password-rules" id="{{ $id }}-rules" data-password-rules="{{ $id }}">
        <li data-rule="length">Minimal 10 karakter</li>
        <li data-rule="letter">Memuat huruf</li>
        <li data-rule="number">Memuat angka</li>
    </ul>
@endif
