{{-- Shared top of every workbench: input, example picker, report with the "Salin kesalahan" button. --}}
<div class="workbench" data-workbench>
    <label for="wb-input" class="form-label">{{ $label }}</label>
    <textarea id="wb-input" class="form-input wb-input" rows="12" spellcheck="false" autocomplete="off" placeholder="{{ $placeholder }}" data-wb-input></textarea>

    <div class="hero__actions">
        <button type="button" class="btn btn--primary" data-wb-run>{{ $convertLabel }}</button>
        <select class="form-input wb-example" data-wb-example aria-label="Muat contoh">
            <option value="">Muat contoh&hellip;</option>
            @foreach ($examples as $key => $name)
                <option value="{{ $key }}">{{ $name }}</option>
            @endforeach
        </select>
        <button type="button" class="btn btn--outline" data-wb-clear>Kosongkan</button>
    </div>

    <div class="wb-report" data-wb-report role="status" aria-live="polite" hidden>
        <ul class="wb-report__list" data-wb-report-list></ul>
        <div class="hero__actions" data-wb-fix hidden>
            <button type="button" class="btn btn--outline" data-wb-copy-errors>Salin kesalahan untuk AI</button>
            <span class="muted" data-wb-copy-status></span>
        </div>
    </div>
</div>
