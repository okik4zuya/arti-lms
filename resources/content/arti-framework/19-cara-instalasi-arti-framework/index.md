---
title: "Cara Instalasi ARTi Framework"
order: 11
published: true
---

<style>
@import url('https://fonts.googleapis.com/css2?family=Atkinson+Hyperlegible:wght@400;700&display=swap');
.content {
    max-width: 600px;
    font-family: 'Atkinson Hyperlegible', var(--font-sans);
}
.content h1 { font-size: 1.85rem; }
.content h2 { font-size: 1.5rem; margin-top: 2em; }
.content p {
    font-size: 1.2rem;
    line-height: 1.75;
    margin: 0 0 0.9em;
}
</style>

# Cara Instalasi ARTi Framework

Sebelum lanjut ke tahap Amati, saya mau tunjukkan dulu apa saja yang perlu terpasang di komputer Anda supaya semua yang saya ceritakan di chapter ini benar-benar bisa dipakai, bukan cuma dibaca.

Ini baru gambaran umum komponennya. Langkah demi langkah yang lebih detail — versi klik-per-klik — ada di bagian BONUS, Panduan Instalasi.

## Yang perlu terpasang

**VSCode + ekstensi Claude Code**

Ini fondasinya. VSCode sebagai editor, dan ekstensi Claude Code sebagai "mesin" yang saya bahas di [[Arsitektur]] — tempat semua skill (ARTi-setup, ARTi-idea, ARTi-writing, ARTi-figure) benar-benar dijalankan.

**Drawio**

Dipakai untuk membuka dan mengedit file diagram yang dihasilkan ARTi-figure. Skill itu sendiri cuma menghasilkan file `.drawio` yang siap diedit — bukan gambar jadi — jadi Drawio ini yang jadi tempat merapikan ukuran, wrapping teks, dan ekspor ke PNG/PDF.

**ArtiFrameworkSetup**

Installer utama yang menyiapkan struktur `~/.arti` — folder profil peneliti dan memori lintas-proyek yang saya singgung di [[Fitur-fitur ARTi]] — serta skill-skill ARTi itu sendiri.

**ArtiPdfSetup**

Komponen pendukung untuk mengolah literatur PDF jadi format Markdown yang bisa dicari, seperti yang saya jelaskan di [[Sistem Referensi]].

## Jujur soal urutannya

Saya belum menuliskan urutan instalasi yang presisi di sini — itu memang bukan tujuan subchapter ini. Yang penting Anda tahu dulu apa saja komponennya dan untuk apa masing-masing, sebelum masuk ke panduan yang lebih teknis.

Setelah semua komponen ini terpasang, langkah berikutnya adalah menjalankan ARTi-setup sekali di awal — onboarding profil peneliti, lalu scaffolding memori untuk proyek pertama Anda. Detailnya ada di Panduan Instalasi.

Dengan ini, peta besar ARTi Framework di chapter ini benar-benar selesai. Saatnya masuk ke tahap pertama: Amati.
