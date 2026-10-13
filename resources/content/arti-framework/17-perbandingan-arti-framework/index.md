---
title: "Perbandingan ARTi Framework dengan yang Lain"
order: 9
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

# Perbandingan ARTi Framework dengan yang Lain

Di [[Claude Code]], saya sudah bandingkan Claude Code dengan Claude AI, NotebookLM, dan LLM chat lain — sebagai alat.

Sekarang pertanyaannya naik satu tingkat: kalau ARTi Framework "cuma" susunan skill dan folder di atas Claude Code, apa bedanya dengan Claude Code polos, atau tools lain yang sudah ada di luar sana?

## Bukan Claude Code vs ARTi — tapi Claude Code polos vs Claude Code + ARTi

Perlu saya luruskan dulu: ARTi Framework bukan pesaing Claude Code. ARTi jalan di atasnya.

Jadi perbandingan yang lebih jujur bukan "Claude Code vs ARTi", tapi "Claude Code polos vs Claude Code yang sudah dipasangi ARTi".

| Aspek | Claude Code polos | Claude Code + ARTi Framework |
|---|---|---|
| Memori antar sesi | Bisa disimpan, tapi strukturnya terserah pemakai — gampang jadi satu file raksasa | Terstruktur: file per topik + indeks, ritual kapan ditulis/dibaca sudah baku |
| Anti-halusinasi | Tergantung disiplin pemakai sendiri | Wajib source-tagging + scratchbook sebagai gerbang sebelum klaim masuk naskah |
| Alur kerja riset | Tidak ada — pemakai bikin prompt sendiri dari nol tiap tahap | Dipaketkan jadi skill (ARTi-idea, ARTi-writing, ARTi-figure, ARTi-setup) mengikuti tahap Amati/Replikasi/Tambah Inovasi |
| Kurva belajar | Rendah untuk pemakaian umum | Perlu ikuti konvensi framework (struktur folder, kapan update memori) |

## Dibandingkan dengan tools lain

Supaya lebih lengkap, saya letakkan juga di samping tools yang sudah saya bahas di [[2 Masalah Utama]] dan [[Solusi Parsial]]:

| Aspek | ARTi Framework | Claude AI (native) | NotebookLM | LLM chat lain (ChatGPT/Gemini/dkk) |
|---|---|---|---|---|
| Memori | Per-topik, terstruktur, tidak bercampur | Ada, tapi lintas-sesi bercampur semua topik | Tidak relevan — bukan fitur memorinya | Bervariasi, umumnya sama seperti Claude AI native |
| Anti-halusinasi | Wajib source-tagging + scratchbook | Tergantung disiplin pemakai | Kuat untuk merangkum sumber yang diupload | Tergantung disiplin pemakai |
| Tulis & edit file naskah | Langsung, native | Tidak — hasil harus disalin manual | Tidak — cuma menghasilkan ringkasan/output terbatas | Bervariasi, umumnya tidak native |
| Metodologi kerja | Ada (skill terstruktur, bukan cuma alat) | Tidak ada | Tidak ada | Tidak ada |
| Keamanan data | Lokal, tidak ada server pihak ketiga yang menyimpan | Tersimpan di server penyedia | Tersimpan di server penyedia | Tersimpan di server penyedia |

## Jujur soal trade-off-nya

Tabel di atas kelihatan seperti ARTi menang semua baris. Tapi itu tidak lengkap tanpa saya sebutkan biayanya.

ARTi butuh setup awal (VSCode, ekstensi, beberapa installer — dibahas di subchapter berikutnya) dan butuh pemakai yang mau disiplin mengisi memori serta scratchbook-nya sendiri. Ini bukan tools yang begitu instal langsung otomatis pintar.

Kalau kebutuhan Anda cuma sesekali tanya-jawab santai, LLM chat biasa jelas lebih praktis. ARTi baru terasa manfaatnya ketika kerjanya panjang, berbulan-bulan, dan butuh konsistensi — seperti menulis artikel ilmiah.

Sebelum masuk ke bagian praktis cara memasangnya, saya mau menutup dulu peta besar chapter ini.
