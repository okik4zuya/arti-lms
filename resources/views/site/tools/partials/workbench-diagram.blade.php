@include('site.tools.partials.workbench-start', [
    'label' => 'Tempel JSON diagram dari AI',
    'placeholder' => '{ "arti": "diagram/1", "direction": "TB", "nodes": [ ... ], "edges": [ ... ] }',
    'examples' => ['studi' => 'Contoh: desain studi', 'prisma' => 'Contoh: alur penyaringan (PRISMA)', 'metode' => 'Contoh: alur metode'],
    'convertLabel' => 'Buat diagram',
])

<div class="wb-preview" data-wb-preview hidden>
    <p class="muted" data-wb-meta></p>
    <div class="wb-frame wb-frame--scroll" data-wb-frame></div>
</div>

<div class="hero__actions" data-wb-downloads hidden>
    <button type="button" class="btn btn--primary" data-dl="drawio">Unduh .drawio (bisa diedit)</button>
    <button type="button" class="btn btn--outline" data-dl="svg">Unduh SVG</button>
    <button type="button" class="btn btn--outline" data-dl="png">Unduh PNG</button>
</div>
<p class="muted">Pembungkusan teks di draw.io bisa sedikit berbeda dari pratinjau. Buka .drawio di <a href="https://app.diagrams.net" rel="noopener">draw.io</a> untuk merapikan.</p>
