---
title: "Style dan Manuscript"
order: 13
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

# Style dan Manuscript

Anda: contoh paper untuk Writing Style. Claude: Manuscript dari Scratchbook, Journal Profile, dan Writing Style.

## Writing Style

```text
Untuk voice, gunakan skill [NAMA VOICE]. Terapkan secara
mendalam. Jangan pakai profil voice lain.
```

**Tambah modifier**

```text
Tambahkan preferensi:
- penulis non-native, pilihan kata sederhana
- tambahkan kesalahan non-fatal secukupnya
- setiap pernyataan didukung literatur atau data; elaborasi hanya
  setelah semua literatur disampaikan
- kalimat ringkas, rata-rata sekitar 20 kata
Simpan agar selalu dipakai selama penulisan.
```

## Rencana penulisan

```text
Buatkan strategi penyelesaian manuscript: bagi sesi agar optimal,
efisien, dan tetap teliti. Usulkan dulu rencananya sebelum
melakukan apa pun.
```

```text
Dokumentasikan rencana ini di memory. Siapkan memory khusus untuk
melacak progresnya.
```

## Menulis

**Ringkas**

```text
Write the introduction.
```

**Yolo**

```text
Mode yolo: sesi S[N] sampai S[M]. Putuskan sendiri dengan
default yang kamu rekomendasikan, catat di writing/decision-
flags. Tanya saya HANYA untuk fakta yang hanya saya punya.
Berhenti setelah S[M]; jangan menulis [BAGIAN MENUNGGU DATA].
```

**Aturan keras (setiap sesi menulis)**

```text
Aturan keras:
1. Kata sederhana, gaya non-native.
2. Kesalahan non-fatal sekitar 1 per 8 kalimat, dicatat di
   writing/voice-ledger.md; tidak pernah di angka, sitasi, nama,
   atau arah hasil.
3. Setiap pernyataan didukung literatur atau data; tanpa sitasi
   karangan, tulis [CITATION NEEDED].
4. Kalimat ringkas, rata-rata ~20 kata, maksimum ~30.
Sesi selesai hanya dengan claim-audit (nol baris tanpa
dukungan), audit suara berhitung, dan ledger terbarui.
Naskah tidak boleh menyebut bagian tertentu ("Section 3",
"see below"); Table dan Figure boleh.
Satu paragraf satu baris. Jangan timpa draft lama; salin ke draft
berikutnya.
```

## Finalisasi bagian

```text
Finalkan [BAGIAN]:
- strict ke voice dengan modifier yang disebut
- buang yang tidak perlu, redundan, lemah, atau kosmetik
- jangan overclaim; kalau ragu, buang
```

**Usul dulu**

```text
Ubah pilihan kata: usulkan dalam tabel. Jangan edit sebelum saya
setuju.
```

## Writing Style dari tulisan saya (saran)

```text
Pelajari [N] paper saya di [PATH]. Ekstrak pola gaya: panjang
kalimat, konektor, hedging, subjek kalimat di Results, dan
kebiasaan khas. Buat Writing Style, lalu tunjukkan 5 kalimat dari
draft yang cocok dan 5 yang tidak.
```

## Audit suara (saran)

```text
Audit suara [BAGIAN] dengan hitungan: rata-rata dan maksimum
panjang kalimat, panjang paragraf, konektor, pemakaian "we", dan
kata yang terlalu canggih. Potong yang menyimpang.
```

## Claim audit (saran)

```text
Buat tabel claim-audit untuk [BAGIAN]: tiap pernyataan, sumber
(LIT atau data), dan statusnya. Tandai yang tidak berdukungan.
```

## Per bagian (saran)

```text
Tulis [BAGIAN] hanya dari bagian Scratchbook yang sesuai. Jangan
mencari literatur baru. Klaim tanpa sumber ditandai
[CITATION NEEDED].
```

## Judul, abstrak, highlights (saran)

```text
Tulis abstrak [N] kata dalam lima langkah: konteks, celah,
metode, temuan, makna. Angka hanya dari bagian hasil. Beri 5 judul
sesuai pola jurnal.
```

```text
Tulis highlights ([N] butir, maks [N] karakter): hanya klaim yang
sudah didukung di naskah.
```

## Konsistensi (saran)

```text
Periksa konsistensi lintas bagian: angka di abstrak vs hasil,
singkatan, istilah (satu istilah per konsep), dan penunjuk figur
dan tabel.
```

## Referensi (saran)

```text
Regenerasi daftar referensi dari semua tag [LIT: kunci]. Laporkan
yang tidak terselesaikan atau dikutip tapi tidak ada di arti-lit.
```

```text
Periksa kemiripan teks dengan sumber: tandai kalimat yang terlalu
dekat dengan aslinya.
```

Lanjut bila: claim-audit tanpa baris tak berdukungan, dan flag keputusan sudah Anda baca.
