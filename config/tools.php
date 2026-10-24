<?php

/*
| Free tools hub (/tools). One entry per tool page, in hub order. `gem_url` is the share link of the Gemini Gem
| (env, empty until the Gem exists): empty hides the "Buka Gem" button, the copyable instruction always shows.
| `instruction` / `knowledge` are files in resources/gems/. `entry` is the Vite entry of the page's workbench
| (null = no workbench, the page is only the Gem block).
*/
return [

    'tools' => [
        'composite' => [
            'title' => 'Gambar gabungan',
            'name' => 'Composite',
            'lane' => 'Graphical abstract, gambar multi-panel',
            'summary' => 'Ubah HTML/SVG dari AI menjadi PNG dan PDF dengan ukuran tepat, lengkap dengan pemeriksaan teks terpotong dan ukuran huruf.',
            'gem_url' => env('GEM_COMPOSITE_URL'),
            'instruction' => 'composite.instruksi.md',
            'knowledge' => 'composite.pengetahuan.md',
            'limits' => [
                '<strong>PDF dibuat lewat dialog cetak browser.</strong> Alat tidak bisa memeriksa jumlah halaman atau apakah huruf tertanam; periksa sendiri PDF-nya sebelum dikirim ke jurnal.',
                '<strong>Hanya SVG dan HTML/CSS bawaan.</strong> Gambar dari berkas, skrip, huruf web, dan tautan luar ditolak. Foto atau mikrograf tidak bisa dimasukkan di jalur gratis.',
                '<strong>PNG dibuat oleh browser Anda.</strong> Huruf selain Arial atau Helvetica bisa berganti, dan Safari kadang menolak membuat PNG; coba Chrome, Edge, atau Firefox.',
            ],
            'entry' => 'resources/js/tools/composite.js',
        ],
        'diagram' => [
            'title' => 'Diagram alur',
            'name' => 'Diagram',
            'lane' => 'Desain studi, alur PRISMA, diagram metode',
            'summary' => 'Ubah daftar kotak dan panah dari AI menjadi diagram yang tertata otomatis, lalu unduh sebagai .drawio yang bisa diedit, SVG, atau PNG.',
            'gem_url' => env('GEM_DIAGRAM_URL'),
            'instruction' => 'diagram.instruksi.md',
            'knowledge' => 'diagram.pengetahuan.md',
            'limits' => [
                '<strong>Tata letaknya otomatis.</strong> Anda tidak mengatur posisi kotak; untuk diagram yang harus persis, unduh .drawio lalu rapikan di draw.io.',
                '<strong>Pembungkusan teks di draw.io bisa sedikit berbeda</strong> dari pratinjau di halaman ini.',
                '<strong>Hanya kotak dan panah.</strong> Pengelompokan, ikon, dan garis melengkung tidak ada di versi ini; untuk itu pakai alat Gambar gabungan.',
            ],
            'entry' => 'resources/js/tools/diagram.js',
        ],
        'plot' => [
            'title' => 'Grafik data',
            'name' => 'Plot',
            'lane' => 'Batang, garis, sebar dari data yang Anda tempel',
            'summary' => 'Ubah spesifikasi Vega-Lite dari AI menjadi grafik bertema ARTi, lalu unduh sebagai SVG atau PNG dengan lebar jurnal.',
            'gem_url' => env('GEM_PLOT_URL'),
            'instruction' => 'plot.instruksi.md',
            'knowledge' => 'plot.pengetahuan.md',
            'limits' => [
                '<strong>Data harus ada di dalam spesifikasi</strong> (<code>data.values</code>). Tautan data ke luar ditolak, dan sebaiknya tidak lebih dari beberapa ratus baris.',
                '<strong>Ini grafik umum dari data yang Anda tempel,</strong> bukan analisis. Angka dan kesimpulannya tetap harus Anda periksa terhadap data asli.',
                '<strong>Temanya meniru gaya ARTi,</strong> tetapi tidak identik dengan grafik matplotlib di paket berbayar.',
            ],
            'entry' => 'resources/js/tools/plot.js',
        ],
        'profil-jurnal' => [
            'title' => 'Profil jurnal',
            'name' => 'Profil jurnal',
            'lane' => 'Memilih dan memahami jurnal target',
            'summary' => 'Gem yang menyusun profil jurnal dari aims and scope, panduan penulis, dan contoh artikel yang Anda tempel. Tanpa alat gambar.',
            'gem_url' => env('GEM_JOURNAL_URL'),
            'instruction' => 'profil-jurnal.instruksi.md',
            'knowledge' => 'profil-jurnal.pengetahuan.md',
            'limits' => [
                '<strong>Gem hanya membaca teks yang Anda tempel.</strong> Ia tidak membuka situs jurnal dan tidak mengecek kuartil atau indeksasi; untuk itu pakai ARTi SciPeek.',
                '<strong>Skor kebaruan hanya dihitung bila contoh artikel ditempel;</strong> tanpa itu bagian tersebut dikosongkan.',
                '<strong>Setiap isian bertanda sumbernya</strong> (dari teks yang Anda tempel, atau belum diverifikasi). Lebar kolom dan resolusi gambar tidak ditebak.',
            ],
            'entry' => null,
        ],
    ],

];
