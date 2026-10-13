---
title: "Fitur-fitur ARTi"
order: 8
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

# Fitur-fitur ARTi

Sejauh ini saya sudah menjelaskan filosofi dan arsitekturnya.

Sekarang saatnya lebih konkret: apa saja sebenarnya yang bisa dilakukan ARTi Framework, satu per satu.

## Inti: memori, referensi, scratchbook

Tiga pilar yang sudah saya bahas di [[Sistem Memori]], [[Sistem Referensi]], dan [[Scratchbook: File Sakti Anti Halusinasi]] adalah fondasi semuanya. Ringkas ulang di sini sebagai daftar fitur, bukan filosofi:

| Fitur | Yang dilakukan | Masalah yang dijawab |
|---|---|---|
| Memori per-topik | File kecil per topik di `memory/`, bukan satu gundukan besar; ada file indeks satu baris per topik | Hilang memori tiap sesi baru ([[2 Masalah Utama]]) |
| Source-tagging wajib | Setiap klaim harus bisa ditelusur balik ke kutipan bertag sumber | Halusinasi |
| Scratchbook | Kutipan mentah ditampung dulu sebelum disintesis jadi draf | Halusinasi menyelinap lewat "ingatan" AI |
| Literatur ke Markdown | PDF/referensi diolah dulu jadi format yang bisa dicari sebelum jadi konteks diskusi | Referensi yang sulit ditelusuri ulang |

## Skill: alur kerja yang sudah dipaketkan

Ini bagian yang belum saya jelaskan detail — sudah disinggung sekilas di [[Arsitektur]], sekarang saya rincikan satu per satu. Tiap skill adalah alur kerja terstruktur yang saya jalankan di atas Claude Code, bukan sekadar prompt sekali pakai.

**ARTi-setup** — onboarding sekali di awal (bikin folder `~/.arti`, profil peneliti) dan penyiapan tiap proyek baru (scaffolding memori lokalnya). Ini yang dijalankan duluan sebelum skill lain, karena skill lain mengasumsikan profil dan struktur ini sudah ada.

**ARTi-idea** — tahap pra-penulisan sebelum satu kata pun ditulis di naskah: memetakan gap penelitian, menyaring ide, menilai novelty, mendesain eksperimen yang feasible, sampai memilih jurnal tujuan. Ini yang jadi tulang punggung tahap Amati, dibahas panjang lebar setelah chapter ini selesai.

**ARTi-writing** — dari analisis profil jurnal sampai draf, submission, balas rebuttal, dan catatan pasca-terbit. Di sinilah memori, referensi, dan scratchbook yang saya bahas di subchapter sebelumnya benar-benar dipakai bekerja, bukan cuma konsep.

**ARTi-figure** — bikin diagram alur/skema studi dan tabel manuskrip dari spesifikasi terstruktur, bukan digambar manual dari nol tiap kali dibutuhkan.

## Dashboard: bukan cuma sekadar file

Salah satu hal yang sering luput saat orang bayangin "sistem memori" — Anda kira ini semua cuma folder teks yang diketik manual, bukan?

Tidak. Ada dashboard lokal yang menampilkan progres per topik, mempermudah menavigasi memori tanpa harus buka satu per satu file `.md`. Buku yang sedang Anda baca ini pun berjalan di atasnya.

## Keamanan data: satu fitur yang gampang dilewatkan

Semua yang saya sebutkan di atas jalan lokal di komputer sendiri. Tidak ada server pihak ketiga yang menyimpan naskah atau literatur Anda diam-diam.

Ini bukan fitur tempelan — ini yang membuat saya berani menaruh draf manuskrip yang belum publish sekalipun ke dalam sistem ini, karena saya tahu persis di mana datanya berada.

## Belum semuanya

Daftar di atas baru permukaan. Detail tiap skill — bagaimana persisnya scoring novelty bekerja, bagaimana iteration log menjaga kualitas revisi, bagaimana cover letter dan rebuttal disusun — akan muncul satu per satu di bagian Amati, Replikasi, dan Tambah Inovasi.

Sebelum ke sana, ada satu pertanyaan wajar yang mungkin sudah muncul di kepala Anda: kalau semua ini cuma susunan skill dan folder di atas Claude Code, apa bedanya dengan pakai Claude Code polos, atau tools lain yang sudah ada? Itu yang saya bahas di subchapter berikutnya.
