---
title: "Journal Profile"
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
.content h2 { font-size: 1.5rem; margin-top: 1.6em; }
.content p, .content li {
    font-size: 1.2rem;
    line-height: 1.75;
    margin: 0 0 0.7em;
}
</style>

# Journal Profile

Anda: contoh paper dari jurnal target (fulltext). Claude: Journal Profile, pola yang diamati.

## Cari contoh paper

```text
Carikan 5 review paper dari [JURNAL] yang cocok dijadikan contoh
untuk membuat journal profile. Gunakan arti-search.
```

```text
Carikan 3 review terbaru ([TAHUN]-[TAHUN]) dari [JURNAL].
Saya akan kirim fulltext-nya.
```

## Buat profile

```text
Saya sudah menyiapkan [N] contoh paper di [PATH].
Analisis dan buat journal profile-nya.
```

```text
Fulltext [N] contoh paper dari [JURNAL] ada di literature/[FOLDER].
Buat journal profile.
```

## Periksa ulang

```text
Baca ulang contoh paper dan ikuti polanya: outline yang kamu
buat belum cukup rinci.
```

```text
Kutip judul contoh paper secara verbatim, lengkap dengan
subjudul.
```

Instruksi kedua menjaga dari kesalahan yang pernah terjadi: judul contoh terpotong, sehingga pola judul keliru.

## Kalibrasi novelty (saran)

```text
Apakah level novelty klaim saya sesuai dengan jurnal ini,
berdasarkan contoh paper?
```

## Hitung pola (saran)

```text
Dari [N] contoh paper, hitung frekuensi tiap pola (berapa paper
yang memakainya, mis. 3 dari 5): struktur section, panjang abstrak,
jumlah referensi, jenis figur dan tabel, frasa pembuka, dan
hedging. Tandai pola yang n-nya terlalu kecil.
```

## Verifikasi klaim frekuensi (saran)

```text
Periksa ulang tiap klaim frekuensi di journal profile dengan
membaca fulltext contoh. Laporkan yang salah.
```

## Pola judul dan abstrak (saran)

```text
Dari contoh paper, ekstrak pola judul dan struktur abstrak.
Sarankan 5 judul untuk naskah saya sesuai pola, satu tanpa titik
dua.
```

## Panduan penulis (saran)

```text
Tambahkan blok panduan penulis [JURNAL] ke profile: batas kata,
referensi, figur, data, dan kebijakan AI, lengkap dengan
sumbernya.
```

Lanjut bila: pola struktur, judul, sitasi, dan batas novelty jurnal sudah tercatat.
