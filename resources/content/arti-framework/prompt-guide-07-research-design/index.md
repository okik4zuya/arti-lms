---
title: "Research Design"
order: 7
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

# Research Design

Anda: putuskan pilihan desain. Claude: Research Design, bahan untuk Eksperimen dan Blueprint.

## Artikel

```text
Buat Research Design untuk Idea [N] yang sudah dikonfirmasi:
bahan/sampel, protokol sintesis atau pengumpulan data, rencana
karakterisasi/pengukuran yang dicek terhadap profil saya, kontrol,
jadwal, dan feasibility flag (🔴🟡🟢).
```

```text
Construct research design.
```

## Review

```text
Buat Research Design untuk Idea [N]: metodologi review, cara
[kerangka klasifikasi/sintesis] diterapkan ke korpus, fulltext
atau literatur tambahan yang diperlukan sebelum klaim inti bisa
ditulis, rencana sintesis (tabel perbandingan, kerangka lintas
domain), dan feasibility flag.
```

**Cek konsistensi klasifikasi**

```text
Jalankan re-check buta: ambil 15% korpus yang sudah
diklasifikasi, klasifikasikan ulang tanpa melihat tabel utama,
bandingkan.
```

## Tanya

```text
Apa konsekuensinya kalau saya pilih opsi 1 dan 2, terkait
pekerjaan yang harus saya lakukan?
```

```text
Kalau saya pilih opsi B, apakah masih bisa dipertahankan di
[JURNAL]?
```

## Rencana lengkap

```text
Buat satu rencana penyelesaian sampai siap submit: eksperimen,
analisis data, daftar figur dan tabel, dan pembuatannya, agar
detail riset final dari awal. Bentuk HTML satu halaman, bisa
saya cetak.
```

## Pra-mortem (saran)

```text
Bayangkan hasilnya nol atau berlawanan dengan hipotesis. Apa tiga
penyebab paling mungkin, dan apa yang bisa saya ubah di desain
sekarang untuk mengurangi tiap penyebab?
```

## Ancaman validitas (saran)

```text
Daftar ancaman validitas desain ini (internal, eksternal,
konstruk) dan cara menanganinya atau mencatatnya sebagai
keterbatasan.
```

## Rencana analisis sebelum data (saran)

```text
Tulis rencana analisis sebelum saya melihat data: uji yang
dipakai, kovariat, penanganan data hilang, dan aturan membaca hasil
(kapan hipotesis didukung, kapan tidak).
```

## Jadwal mundur (saran)

```text
Susun jadwal mundur dari tenggat [TANGGAL]: tiap tahap, durasi,
dan titik yang tidak boleh telat.
```

## Figur dan tabel lebih dulu (saran)

```text
Sebelum data ada, daftar figur dan tabel yang direncanakan
beserta pesan tiap satunya dan data yang dibutuhkan.
```

## Etika dan data (saran)

```text
Apa yang harus beres sebelum pengumpulan data: persetujuan etik,
anonimisasi, dan tempat penyimpanan? Tandai yang belum.
```

## Komputasi (saran)

```text
Rancang pipeline eksperimen komputasi: data, baseline, data
held-out, metrik, ancaman validitas, kebutuhan komputasi, dan
pilot kecil lebih dulu.
```

Lanjut bila: tidak ada flag 🔴 yang belum punya jalan keluar.
