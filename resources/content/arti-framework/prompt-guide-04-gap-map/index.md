---
title: "Gap Map"
order: 4
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

# Gap Map

Anda: RIS di arti-lit, Researcher Profile. Claude: Gap Map, cek novelty.

## Gap Map

**Lengkap**

```text
Pakai ARTi-idea, buat Gap Map untuk [artikel / review] saya
tentang [TOPIK], dari [N] referensi di arti-lit.db.
Saya belum punya ruang lingkup atau daftar eksklusi: simpulkan
dari korpus, tandai yang tampak di luar topik atau ambigu, jangan
diam-diam memasukkan atau membuang.
Munculkan juga hal lain yang perlu saya putuskan: review yang
sudah ada di korpus, gap yang tidak cocok dengan profil saya,
bagian yang tipis.
```

**Yolo**

```text
Start it. Yolo.
Output: gap map berbasis OpenAlex. Scopus nanti.
```

**Ulang dengan sudut lain**

```text
Jalankan ulang gap map dengan framing ke topik [N].
Ingat: tujuannya paper [komputasi saja].
```

## Novelty

**Review**

```text
Run the Prior Review Check untuk review ini.
```

**Hasil kill-test (tempel hasilnya)**

```text
Hasil Scopus ada di bawah. Untuk tiap hit yang tumpang tindih
dengan studi saya, jelaskan persis apa yang sama dan apa yang
beda. Tandai yang melemahkan klaim novelty.
Skor ulang C/M/E. Jangan klaim novelty melebihi yang didukung
hit; spekulasi diberi tag [SUGGESTED - USER MUST VERIFY].
```

**Tanya**

```text
Kapan novelty topik ini dianalisis? Apakah sudah ada di workflow?
```

**Keputusan**

```text
Beri rekomendasi go / redesign / stop untuk checkpoint [TANGGAL].
```

## Isi tiap gap (saran)

```text
Untuk tiap gap, tulis: pernyataan satu kalimat, bukti (minimal 3
referensi), kontradiksi yang ada, apa yang belum diketahui, dan
kekuatan bukti (kuat / sedang / lemah).
```

## Uji gap (saran)

```text
Untuk Gap [N], cari bukti yang menentangnya: paper yang sudah
menutupnya, sebagian atau seluruhnya. Tampilkan yang paling dekat
dan jelaskan apa yang masih tersisa.
```

## Peta kontradiksi (saran)

```text
Daftar kontradiksi antar-studi di korpus: siapa menyatakan apa,
dengan data apa, dan mengapa mungkin berbeda (kondisi, metode,
definisi).
```

## Kemampuan saya (saran)

```text
Dari gap yang ada, mana yang bisa saya kerjakan dengan profil,
alat, dan waktu saya? Tandai yang butuh sumber daya yang tidak
saya punya.
```

## Skor novelty (saran)

```text
Jelaskan skor C/M/E per komponen dengan alasan dan bukti. Kalau
skor berubah dari versi sebelumnya, apa penyebabnya?
```

Lanjut bila: gap yang dipilih bertahan dari cek novelty.
