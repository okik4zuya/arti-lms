@include('site.tools.partials.workbench-start', [
    'label' => 'Tempel spesifikasi Vega-Lite (JSON) dari AI',
    'placeholder' => '{ "$schema": "https://vega.github.io/schema/vega-lite/v6.json", "data": { "values": [ ... ] }, "mark": "bar", "encoding": { ... } }',
    'examples' => ['batang' => 'Contoh: batang per tahun', 'kelompok' => 'Contoh: batang berkelompok', 'garis' => 'Contoh: garis dua seri', 'sebar' => 'Contoh: sebar'],
    'convertLabel' => 'Buat grafik',
])

<div class="wb-preview" data-wb-preview hidden>
    <p class="muted" data-wb-meta></p>
    <div class="wb-frame wb-frame--scroll" data-wb-frame></div>
</div>

<div class="hero__actions" data-wb-downloads hidden>
    <label class="wb-width">Lebar PNG
        <select data-wb-width class="form-input">
            <option value="85">85 mm, satu kolom (300 dpi)</option>
            <option value="170" selected>170 mm, dua kolom (300 dpi)</option>
        </select>
    </label>
    <button type="button" class="btn btn--primary" data-dl="png">Unduh PNG</button>
    <button type="button" class="btn btn--outline" data-dl="svg">Unduh SVG</button>
</div>

<details class="faq-item">
    <summary>Punya data di Excel atau CSV?</summary>
    <p>Tempel di sini, lalu salin hasilnya ke chat AI supaya data tidak perlu diketik ulang.</p>
    <textarea class="form-input wb-input wb-input--small" rows="5" spellcheck="false" placeholder="tahun,jumlah&#10;2021,12&#10;2022,19" data-csv-in></textarea>
    <div class="hero__actions">
        <button type="button" class="btn btn--outline" data-csv-run>Ubah ke data.values</button>
        <button type="button" class="btn btn--outline" data-gem-copy="[data-csv-out]">Salin hasil</button>
    </div>
    <textarea class="form-input wb-input wb-input--small" rows="4" readonly spellcheck="false" data-csv-out></textarea>
</details>
