---
title: "Data"
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
.content h2 { font-size: 1.5rem; margin-top: 1.6em; }
.content p, .content li {
    font-size: 1.2rem;
    line-height: 1.75;
    margin: 0 0 0.7em;
}
</style>

# Data

Hanya jalur artikel empiris. Anda: eksperimen, data mentah. Claude: analisis dengan skrip, temuan siap masuk Scratchbook.

## Membaca data

```text
Apakah Anda bisa membaca file [PATH]?
```

```text
Konversi tiap sheet ke file CSV untuk dianalisis.
```

## Instrumen sebagai sumber kebenaran

```text
Ini soal yang diujikan: [PATH]. Saya lupa file mana yang sesuai
setelah beberapa revisi; cek dengan teliti. Kalau ketemu, kunci
jawabannya ada di situ. Hapus semua file soal yang sudah tidak
update.
```

```text
Sumber kebenaran yang benar-benar diujikan: [PATH PDF].
Pakai pandas dan scipy. Buat plan.
```

## Analisis

```text
Apakah data ini sudah bisa diolah tanpa hasil [post test]?
```

```text
Analisis beberapa skenario kalau butir [N] dikeluarkan.
```

```text
Dengan data seperti ini, apa dampaknya terhadap penelitian ini?
```

## Ke Scratchbook dan dokumentasi

```text
Berdasarkan data ini, apakah ada yang sudah bisa ditambahkan ke
Scratchbook?
```

```text
Dokumentasikan hasil pengolahan data. Pikirkan dokumen
Supporting Information dan share data; buat rencana isinya.
```

## Pemeriksaan awal (saran)

```text
Sebelum analisis, periksa data: ID ganda, nilai di luar rentang,
jumlah per kelompok, data hilang, dan cocokkan jumlah dengan yang
saya laporkan ([N]). Jangan memperbaiki apa pun tanpa
mencatatnya.
```

## Log pengolahan (saran)

```text
Catat setiap keputusan pengolahan data (yang dibuang, diubah,
dibalik) di processing-log beserta alasannya. Pisahkan data mentah
dari data olahan.
```

## Kunci rencana analisis (saran)

```text
Kunci rencana analisis sebelum saya membuka hasil [POSTTEST /
KELOMPOK AKHIR]. Setelah dibuka, semua penyimpangan dicatat.
```

## Asumsi dan sensitivitas (saran)

```text
Periksa asumsi uji yang kamu pakai, dan jalankan analisis
sensitivitas: tanpa outlier, dengan kovariat berbeda.
```

## Reproduksi (saran)

```text
Pastikan semua angka di hasil bisa dibuat ulang dengan satu
perintah dari data mentah. Laporkan yang tidak bisa.
```

## Dibagikan (saran)

```text
Siapkan versi data untuk dibagikan: de-identifikasi, codebook,
dan README. Jelaskan apa yang dihapus.
```

Lanjut bila: setiap angka punya skrip dan keluaran di folder data.
