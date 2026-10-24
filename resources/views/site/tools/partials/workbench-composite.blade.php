@include('site.tools.partials.workbench-start', [
    'label' => 'Tempel HTML/SVG dari AI',
    'placeholder' => '<!doctype html> ... (harus memuat <meta name="arti:width" content="190mm"> dan arti:height)',
    'examples' => ['multi-panel' => 'Contoh: multi-panel 190 mm', 'graphical-abstract' => 'Contoh: graphical abstract 1328x531'],
    'convertLabel' => 'Periksa dan tampilkan',
])

<div class="wb-preview" data-wb-preview hidden>
    <p class="muted" data-wb-meta></p>
    <div class="wb-frame" data-wb-frame></div>
</div>

<div class="hero__actions" data-wb-downloads hidden>
    <button type="button" class="btn btn--primary" data-dl="png">Unduh PNG</button>
    <button type="button" class="btn btn--outline" data-dl="pdf">Cetak / simpan PDF</button>
    <button type="button" class="btn btn--outline" data-dl="html">Unduh sumber .html</button>
</div>
<p class="muted">PDF dibuka di tab baru dengan ukuran halaman sesuai gambar; pilih &ldquo;Simpan sebagai PDF&rdquo; di dialog cetak dan matikan Header dan footer.</p>
