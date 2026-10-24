# Gambar gabungan ARTi: aturan dan dua templat

## Ringkasan aturan
- Satu berkas HTML lengkap, diawali `<!doctype html>`. Hanya HTML, CSS, SVG inline. Tanpa skrip, tanpa gambar berkas, tanpa tautan luar, tanpa huruf web.
- Ukuran lewat meta: `arti:width`, `arti:height` (mm, cm, in, pt, atau px); opsional `arti:dpi` (600), `arti:min-pt` (7), `arti:min-px` (24), `arti:mode` (`print` atau `pixel`; default `pixel` bila satuannya px, selain itu `print`).
- Mode cetak: teks akhir minimal 7 pt. Mode piksel: minimal 24 px (graphical abstract 1328 x 531 px).
- Teks di dalam SVG ikut diperiksa setelah diskalakan oleh viewBox: font-size x skala >= batas.
- Palet: hitam #000000, putih, abu #4D4D4D (penekanan), abu #9a9a9a, aksen #2b6cb0 / #0072B2 / #E69F00 / #009E73 / #D55E00.
- Gambar eksperimen (foto, mikrograf, gel, blot) hanya boleh diberi label, bilah skala, atau dipotong; tidak diolah. Jalur gratis tidak menerima gambar berkas.

## Kode kesalahan
E_SIZE_MISSING, E_SIZE_UNREADABLE, E_MODE, E_SCRIPT, E_TAG, E_HANDLER, E_EXTERNAL, E_PAGE_OVERFLOW, E_OVERFLOW, E_SVG_TEXT_CLIPPED, E_IMG_BROKEN, E_OUTSIDE. Peringatan: W_DOCTYPE, W_TEXT_SMALL, W_NO_TEXT, W_EXPLANATORY_TEXT, W_RASTER_DPI.

## Templat 1: multi-panel 190 mm x 100 mm (mode cetak)
```html
<!doctype html>
<html lang="en">
<head>
<meta charset="utf-8">
<title>Multi-panel figure</title>
<meta name="arti:width" content="190mm">
<meta name="arti:height" content="100mm">
<meta name="arti:dpi" content="600">
<meta name="arti:min-pt" content="7">
<style>
:root {
  --font: Arial, Helvetica, sans-serif;
  --ink: #000000;
  --paper: #ffffff;
  --dark-fill: #4D4D4D;
  --accent: #2b6cb0;
  --muted: #9a9a9a;
  --fs-label: 9pt;
  --fs-body: 8pt;
}
* { box-sizing: border-box; print-color-adjust: exact; -webkit-print-color-adjust: exact; }
html, body { margin: 0; background: var(--paper); color: var(--ink); font-family: var(--font); }
body { width: var(--page-w); height: var(--page-h); overflow: visible; font-size: var(--fs-body); line-height: 1.2; }
.sheet { width: var(--page-w); height: var(--page-h); display: grid; gap: 3mm; padding: 3mm; grid-template-columns: 1fr 1fr; grid-template-rows: 1fr 1fr; }
.panel { position: relative; min-width: 0; min-height: 0; overflow: hidden; margin: 0; }
.panel > svg { display: block; width: 100%; height: 100%; }
.tag { position: absolute; left: 0; top: 0; z-index: 2; font-weight: 700; font-size: var(--fs-label); background: var(--paper); padding: 0 1mm 0 0; line-height: 1; }
.span2 { grid-column: 1 / span 2; }
</style>
</head>
<body>
<div class="sheet">
  <figure class="panel span2"><span class="tag">(a)</span>
    <svg viewBox="0 0 700 150" preserveAspectRatio="xMidYMid meet" role="img" aria-label="Three-step workflow">
      <defs><marker id="ah" markerWidth="8" markerHeight="8" refX="6" refY="4" orient="auto"><path d="M0,0 L8,4 L0,8 z"/></marker></defs>
      <rect x="40" y="40" width="170" height="70" fill="#fff" stroke="#000" stroke-width="2"/>
      <text x="125" y="82" font-size="22" text-anchor="middle" font-family="Arial">Synthesis</text>
      <rect x="265" y="40" width="170" height="70" fill="#fff" stroke="#000" stroke-width="2"/>
      <text x="350" y="82" font-size="22" text-anchor="middle" font-family="Arial">Activation</text>
      <rect x="490" y="40" width="170" height="70" fill="#4D4D4D" stroke="#000" stroke-width="2"/>
      <text x="575" y="82" font-size="22" text-anchor="middle" font-family="Arial" fill="#fff">Testing</text>
      <line x1="212" y1="75" x2="260" y2="75" stroke="#000" stroke-width="3" marker-end="url(#ah)"/>
      <line x1="437" y1="75" x2="485" y2="75" stroke="#000" stroke-width="3" marker-end="url(#ah)"/>
    </svg>
  </figure>
  <figure class="panel"><span class="tag">(b)</span>
    <svg viewBox="0 0 320 220" preserveAspectRatio="xMidYMid meet" role="img" aria-label="Bar chart of uptake">
      <line x1="50" y1="185" x2="300" y2="185" stroke="#000" stroke-width="2"/>
      <line x1="50" y1="25" x2="50" y2="185" stroke="#000" stroke-width="2"/>
      <rect x="90" y="125" width="60" height="60" fill="#9a9a9a"/>
      <rect x="190" y="55" width="60" height="130" fill="#2b6cb0"/>
      <text x="120" y="205" font-size="18" text-anchor="middle" font-family="Arial">Parent</text>
      <text x="220" y="205" font-size="18" text-anchor="middle" font-family="Arial">New</text>
      <text x="22" y="105" font-size="18" text-anchor="middle" font-family="Arial" transform="rotate(-90 22 105)">Uptake (a.u.)</text>
    </svg>
  </figure>
  <figure class="panel"><span class="tag">(c)</span>
    <svg viewBox="0 0 320 220" preserveAspectRatio="xMidYMid meet" role="img" aria-label="Line trend">
      <line x1="50" y1="185" x2="300" y2="185" stroke="#000" stroke-width="2"/>
      <line x1="50" y1="25" x2="50" y2="185" stroke="#000" stroke-width="2"/>
      <polyline points="70,160 130,130 190,95 250,60 290,45" fill="none" stroke="#2b6cb0" stroke-width="3"/>
      <polyline points="70,165 130,155 190,145 250,138 290,132" fill="none" stroke="#9a9a9a" stroke-width="3"/>
      <text x="175" y="210" font-size="18" text-anchor="middle" font-family="Arial">Time (h)</text>
    </svg>
  </figure>
</div>
</body>
</html>
```

## Templat 2: graphical abstract 1328 x 531 px (mode piksel)
Isi teks pada contoh ini hanya ilustrasi; ganti dengan isi pengguna. Perhatikan: hanya judul, label 1-2 kata, dan angka.
```html
<!doctype html>
<html lang="en">
<head>
<meta charset="utf-8">
<title>Graphical abstract</title>
<meta name="arti:width" content="1328px">
<meta name="arti:height" content="531px">
<meta name="arti:min-px" content="24">
<style>
/* ARTi-figure compose base.
   Palette and fonts mirror references/figure-style-default.md; edit values here per figure, not the names. */
:root {
  --font: Arial, Helvetica, sans-serif;
  --ink: #000000;
  --paper: #ffffff;
  --dark-fill: #4D4D4D;   /* style "dark": emphasis boxes */
  --dark-text: #ffffff;
  --plain-fill: #ffffff;  /* style "plain": ordinary boxes */
  --accent: #2b6cb0;      /* add named colours here once a second figure reuses them */
  --muted: #9a9a9a;
  --stroke: 0.5pt;        /* line weight floor from references/artwork-default.md */
  --fs-label: 9pt;        /* panel tags (a) (b) - bold */
  --fs-body: 8pt;         /* body text; keep >= 7 pt at print width */
  --fs-small: 7pt;        /* captions inside panels; this is the floor */
}
* { box-sizing: border-box; print-color-adjust: exact; -webkit-print-color-adjust: exact; }
html, body { margin: 0; background: var(--paper); color: var(--ink); font-family: var(--font); }
body { width: var(--page-w); height: var(--page-h); overflow: visible; font-size: var(--fs-body); line-height: 1.2; }

/* multi-panel */
.sheet { width: var(--page-w); height: var(--page-h); display: grid; gap: 3mm; padding: 3mm; }
.panel { position: relative; min-width: 0; min-height: 0; overflow: hidden; }
.panel > img, .panel > svg { display: block; width: 100%; height: 100%; object-fit: contain; }
.tag { position: absolute; left: 0; top: 0; z-index: 2; font-weight: 700; font-size: var(--fs-label);
       background: var(--paper); padding: 0 1mm 0 0; line-height: 1; }
.scalebar { position: absolute; right: 2mm; bottom: 2mm; z-index: 2; color: #fff; font-size: var(--fs-small);
            text-align: center; text-shadow: 0 0 2px #000; }
.scalebar i { display: block; height: 0.8mm; background: #fff; margin-bottom: 0.5mm; box-shadow: 0 0 2px #000; }

/* boxes and arrows */
.box { border: var(--stroke) solid var(--ink); background: var(--plain-fill); padding: 1.5mm 2mm; text-align: center; }
.box.dark { background: var(--dark-fill); color: var(--dark-text); }

:root { --fs-label: 32px; --fs-body: 26px; --fs-small: 24px; --stroke: 2px; }
.ga { width: var(--page-w); height: var(--page-h); display: grid; grid-template-columns: 1fr 120px 1fr 120px 1fr;
      align-items: center; padding: 30px 40px; gap: 0; }
.zone { height: 100%; display: flex; flex-direction: column; align-items: center; justify-content: center; gap: 14px; }
.zone h2 { margin: 0; font-size: var(--fs-label); }
.zone p { margin: 0; font-size: var(--fs-body); text-align: center; }
.arrow { width: 100%; }
</style>
</head>
<body>
<div class="ga">
  <div class="zone">
    <h2>Precursors</h2>
    <svg width="220" height="210" viewBox="0 0 220 210" role="img" aria-label="Beaker with solution">
      <path d="M60 30 L60 150 Q60 175 85 175 L135 175 Q160 175 160 150 L160 30" fill="none" stroke="#000" stroke-width="3"/>
      <path d="M62 95 L158 95 L158 150 Q158 173 135 173 L85 173 Q62 173 62 150 Z" fill="#9ec5e8"/>
      <line x1="50" y1="30" x2="170" y2="30" stroke="#000" stroke-width="3"/>
      <circle cx="90" cy="130" r="6" fill="#fff" opacity=".8"/><circle cx="118" cy="115" r="4" fill="#fff" opacity=".8"/><circle cx="105" cy="148" r="5" fill="#fff" opacity=".8"/>
    </svg>
    <p><b>Metal salt</b> + linker<br>in solvent, 120 &deg;C</p>
  </div>
  <svg class="arrow" viewBox="0 0 120 40" role="img" aria-label="arrow"><defs><marker id="ah" markerWidth="8" markerHeight="8" refX="6" refY="4" orient="auto"><path d="M0,0 L8,4 L0,8 z"/></marker></defs><line x1="5" y1="20" x2="105" y2="20" stroke="#000" stroke-width="3" marker-end="url(#ah)"/></svg>
  <div class="zone">
    <h2>Framework</h2>
    <svg width="220" height="210" viewBox="0 0 220 210" role="img" aria-label="Crystal lattice schematic">
      <g stroke="#4D4D4D" stroke-width="3">
        <line x1="50" y1="50" x2="110" y2="50"/><line x1="110" y1="50" x2="170" y2="50"/><line x1="50" y1="110" x2="110" y2="110"/><line x1="110" y1="110" x2="170" y2="110"/><line x1="50" y1="170" x2="110" y2="170"/><line x1="110" y1="170" x2="170" y2="170"/>
        <line x1="50" y1="50" x2="50" y2="110"/><line x1="50" y1="110" x2="50" y2="170"/><line x1="110" y1="50" x2="110" y2="110"/><line x1="110" y1="110" x2="110" y2="170"/><line x1="170" y1="50" x2="170" y2="110"/><line x1="170" y1="110" x2="170" y2="170"/>
      </g>
      <g fill="#2b6cb0" stroke="#000" stroke-width="2"><circle cx="50" cy="50" r="13"/><circle cx="110" cy="50" r="13"/><circle cx="170" cy="50" r="13"/><circle cx="50" cy="110" r="13"/><circle cx="110" cy="110" r="13"/><circle cx="170" cy="110" r="13"/><circle cx="50" cy="170" r="13"/><circle cx="110" cy="170" r="13"/><circle cx="170" cy="170" r="13"/></g>
    </svg>
    <p><b>Porous framework</b><br>1.2 nm pores</p>
  </div>
  <svg class="arrow" viewBox="0 0 120 40" role="img" aria-label="arrow"><line x1="5" y1="20" x2="105" y2="20" stroke="#000" stroke-width="3" marker-end="url(#ah)"/></svg>
  <div class="zone">
    <h2>Performance</h2>
    <svg width="220" height="210" viewBox="0 0 220 210" role="img" aria-label="Bar chart: uptake before and after">
      <line x1="30" y1="180" x2="200" y2="180" stroke="#000" stroke-width="3"/><line x1="30" y1="20" x2="30" y2="180" stroke="#000" stroke-width="3"/>
      <rect x="60" y="130" width="50" height="50" fill="#9a9a9a"/><rect x="130" y="40" width="50" height="140" fill="#2b6cb0"/>
      <text x="85" y="123" font-size="24" text-anchor="middle" font-family="Arial">1.0&#215;</text><text x="155" y="33" font-size="24" text-anchor="middle" font-family="Arial">2.8&#215;</text>
    </svg>
    <p><b>2.8&times; higher uptake</b><br>than the parent material</p>
  </div>
</div>
</body>
</html>
```
