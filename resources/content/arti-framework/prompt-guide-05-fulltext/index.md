---
title: "Fulltext"
order: 5
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

# Fulltext

Anda: unggah PDF yang tidak open access. Claude: daftar DOI, unduh yang open access, ingest ke Reference Database.

## Daftar unduh

```text
List [N] paper terpilih. Tandai yang tidak punya fulltext.
```

```text
Simpan daftar itu di [PATH]. Saya akan merujuk file ini saat
mengumpulkan fulltext.
```

```text
Unduh yang open access, ekstrak PDF-nya, isi Scratchbook, lalu
beri list semua DOI yang belum terunduh.
```

## Saya sudah unggah

```text
Saya sudah menaruh [N] fulltext baru di literature/fulltext.
Cek [N] file terbaru.
```

```text
Provided: [KUNCI 1], [KUNCI 2]. Pilih DOI berikutnya.
```

```text
Yang belum ada fulltext-nya, cukup baca abstraknya.
```

```text
Saya tidak bisa mendapatkan [KUNCI]. Drop saja, lalu pikirkan
solusinya.
```

## Ingest

**PDF**

```text
Fulltext PDF untuk korpus inti [N] paper ada di [PATH].
Buat manifest ingestion, jalankan arti-pdf-ingest, daftarkan ke
arti-lit, laporkan yang gagal atau masih hilang.
```

**Sudah berupa Markdown**

```text
Fulltext sudah saya konversi ke Markdown di [PATH]. Cocokkan ke
kunci korpus, pindahkan ke literature/fulltext/[KUNCI].md,
daftarkan di arti-lit dengan --status fulltext. Laporkan kunci
yang tidak ada filenya.
```

Boros token untuk korpus besar.

**Citation chase**

```text
Setelah registrasi, jalankan citation-chase antara klaster
[GAP A] dan [GAP B]: apakah salah satunya sudah mengutip atau
dikutip yang lain?
```

## Prioritas unduh (saran)

```text
Urutkan daftar unduh: yang paling dibutuhkan untuk klaim inti,
yang paling banyak dikutip, lalu kompetitor terdekat. Beri alasan
satu baris per paper.
```

## Status korpus (saran)

```text
Buat tabel status korpus: kunci, fulltext / abstract-only /
hilang, dan alasannya. Soroti yang memengaruhi klaim inti.
```

## Periksa hasil ekstraksi (saran)

```text
Periksa [N] fulltext hasil ekstraksi: satuan rusak (mis. µ jadi
m), tabel terpotong, simbol hilang. Tandai yang perlu dicek ke
PDF aslinya.
```

## Cocokkan berkas (saran)

```text
Cocokkan nama berkas dengan kunci arti-lit. Laporkan selisih
jumlah, berkas tanpa kunci, dan kunci tanpa berkas.
```

## Supplementary dan versi (saran)

```text
SI untuk [KUNCI] sudah saya unggah di [PATH]. Gabungkan data
tabelnya ke catatan paper itu, dan tandai angka yang berasal dari
SI.
```

```text
Cek apakah ada paper yang sama dalam versi preprint dan jurnal.
Pakai versi jurnal, kecuali preprint-nya lebih lengkap.
```

Lanjut bila: semua kunci korpus punya fulltext atau status abstract-only yang tercatat.
