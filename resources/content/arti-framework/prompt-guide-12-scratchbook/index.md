---
title: "Scratchbook"
order: 12
published: true
---

<style>
@import url('https://fonts.googleapis.com/css2?family=Atkinson+Hyperlegible:wght@400;700&display=swap');
.content {
    max-width: 600px;
    font-family: 'Atkinson Hyperlegible', var(--font-sans);
}
.content h1 { font-size: 1.85rem; }
.content h2 { font-size: 1.5rem; margin-top: 1.6em; }
.content p, .content li {
    font-size: 1.2rem;
    line-height: 1.75;
    margin: 0 0 0.7em;
}
</style>

# Scratchbook

Anda: References, Data, Blueprint sudah ada. Claude: Scratchbook, bahan mentah bertag per bagian naskah.

## Strategi sesi

```text
Jangan mengisi dulu. Buat dulu strategi pembagian sesi pengisian
Scratchbook agar lebih efisien.
```

```text
Apakah sudah ada memory untuk progress tracker Scratchbook?
```

## Sesi pengisian

```text
Lanjutkan proyek [NAMA]. Tahap: mengisi Scratchbook dengan skill
ARTi-scratchbook, bukan drafting. Jalankan S[N]: [KLASTER], [N]
kunci.

BACA DULU: memory/MEMORY.md, todo-list.md, status.md, preferensi
kerja, memory/memories/scratchbook-pacing-plan.md,
scratchbook-population-progress.md, [BAGIAN] Blueprint.
Cocokkan fakta: [PERIKSAAN, mis. jumlah baris pustaka].

ATURAN TETAP:
- Isi Scratchbook bahasa Inggris, percakapan Indonesia.
- Parafrase sumber, pertahankan angka persis (nilai dari grafik
  ditandai "chart").
- Setiap entri bertag: [LIT: kunci], [OWN], [SOURCE NEEDED],
  [CONNECTION: ...], [CONTRADICTION: ...].
- [LIT: kunci] harus ada di arti-lit.
- Perbarui baris "Argumen saat ini" pada edit yang sama.
- Jangan membuat Iteration Log, jangan git commit.

Akhiri dengan density audit dan satu baris kunci yang tersisa.
```

## Lanjut dan kumpulkan

```text
Lanjutkan S[N].
```

```text
Kumpulkan semua yang perlu dicek di semua sesi.
```

```text
Berapa sesi lagi?
```

## Verifikasi angka

```text
Verifikasi fulltext angka bercaveat, maksimal 5 kunci: [KUNCI].
Baca fulltext hanya untuk angka yang akan dikutip. Jika angka
berubah, ubah kode di skrip, jalankan ulang, lalu perbarui
Scratchbook dari keluaran skrip; jangan edit angka manual.
```

## Gerbang sebelum drafting

```text
Tulis di scratchbook-finishing.md semua yang harus selesai
sebelum menulis manuskrip. Pertimbangkan folder penyimpanannya.
```

```text
Sajikan [N] keputusan sekaligus, tiap butir dengan satu
rekomendasi (Recommended) dan alasannya.
```

**Yolo**

```text
Setelah S[N] selesai, lanjut sampai S[M]. Kalau butuh
konfirmasi, jawab sendiri seolah-olah kamu saya. Flag semua
keputusan dan hal yang perlu verifikasi di akhir.
```

## Buat kerangka (saran)

```text
Buat writing/scratchbook.md dari template dengan heading persis
seperti Blueprint, termasuk bagian tanpa literatur (Methods) yang
bisa diisi dari memory proyek.
```

## Triase kunci (saran)

```text
Dari [N] kunci ini, mana yang perlu fulltext dan mana cukup dari
ringkasan di arti-lit? Beri alasan satu baris.
```

## Kontradiksi (saran)

```text
Cari kontradiksi antar entri di bagian [BAGIAN]. Tandai dengan
[CONTRADICTION: A vs B] dan jangan menyelesaikannya sendiri.
```

## Kepadatan (saran)

```text
Audit kepadatan bagian [BAGIAN]: jumlah entri bertag per
paragraf Blueprint, [SOURCE NEEDED] yang tersisa, dan bagian yang
paling tipis.
```

## Argumen saat ini (saran)

```text
Periksa baris "Argumen saat ini" tiap bagian: masih konsisten
dengan entri di bawahnya? Perbarui yang tidak.
```

## Dari data sendiri (saran)

```text
Masukkan temuan dari [PATH keluaran analisis] ke Scratchbook
sebagai [OWN] di bagian yang relevan, dengan angka persis dan
skrip sumbernya.
```

Lanjut bila: semua kunci tersapu, tidak ada kontradiksi terbuka tanpa keputusan, dan hanya tersisa hal yang memang tugas Anda.
