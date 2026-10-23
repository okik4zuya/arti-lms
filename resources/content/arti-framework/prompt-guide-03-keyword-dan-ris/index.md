---
title: "Keyword dan RIS"
order: 3
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

# Keyword dan RIS

OpenAlex utama (Claude mencari sendiri). Scopus untuk verifikasi (Claude menyiapkan string, Anda mencari dan mengunggah RIS).

## OpenAlex

**Referensi untuk satu kalimat**

```text
Carikan referensi untuk:
"[KALIMAT]"
di [BAGIAN]. Gunakan arti-search.
```

**Lebar topik**

```text
Hitung jumlah karya dengan [ISTILAH A] AND "[ISTILAH B]",
dicari di judul dan abstrak, dikelompokkan menurut jenis.
```

**Bukti "belum banyak diteliti"**

```text
Naskah menyebut [TOPIK] "under-explored". Buktikan dengan
hitungan di OpenAlex, bandingkan dengan [PEMBANDING 1, 2, 3].
Simpan metode dan kata kuncinya untuk Supplementary Information.
```

**Menutup celah Scratchbook**

```text
Cari gap literatur pada Scratchbook di project ini, lalu cari
literatur untuk menutupnya.
```

**Pilih**

```text
Pilihkan yang paling penting dan prioritas untuk diunduh.
```

```text
List minimal 3 referensi untuk setiap metode, jangan semua.
```

## String Scopus

**Lengkap (gap map)**

```text
Buat kata kunci Scopus untuk memetakan lanskap riset di sekitar
[TOPIK], supaya terlihat gap yang akan diisi paper ini.

- String Boolean lengkap, siap tempel, tanpa pembungkus field.
- Tiap query: istilah inti + frasa teknis SPESIFIK (metode,
  metrik, mekanisme, kombinasi material/sistem bernama), bukan
  kata payung seperti "reactor" atau "treatment" saja.
- 5-8 query, tiap query satu sudut berbeda, supaya terlihat apa
  yang mapan dan di mana kombinasi kita tampak tipis.
- Tiap query cukup sempit: paling banyak beberapa ratus hasil.
- Frasa majemuk dalam tanda kutip.
- Satu code block per query, judul pendek (1-2 kata).
- Setelah daftar, ingatkan saya mencatat jumlah hasil tiap ronde,
  menilai sudutnya padat atau tipis, dan menandai kandidat
  referensi.
```

**Lengkap (review)**

```text
Buat kata kunci Scopus untuk systematic literature review
tentang [TOPIK]. Aturan sama, tetapi tiap query harus bisa jadi
satu putaran pencarian yang berdiri sendiri. Presisi per query
lebih penting dari recall; recall datang dari beberapa query
sempit.
```

**Ringkas**

```text
Berikan kata kunci Scopus yang komprehensif dan strategis.
Tulis di memory, satu code block per kata kunci.
```

```text
Saya mau langsung salin semua tanpa mengubah apa pun. Cek apakah
sudah siap tempel, atau perlu filter tahun dan jenis dokumen.
```

## Lapor balik

```text
Hasil kelompok A:
A1: [N] tanpa filter / [N] dengan filter
A2: [N] / [N]
```

```text
File RIS sudah saya salin ke literature/exports.
Kata kunci 1 sampai 5, urut berdasarkan waktu.
```

```text
Semua RIS sudah saya impor ke arti-lit: [N] tercatat setelah
dedupe. Cek dengan tool arti-lit.
```

**Bandingkan (saran)**

```text
Bandingkan hasil Scopus ini dengan OpenAlex untuk topik yang
sama: apa yang hanya ada di satu sisi?
```

## Catat ronde

```text
Catat ronde ini di literature/search-log.md.
```

```text
Ada [N] export yang belum tercatat. Ini string, tanggal, dan
tujuannya: [DATA]. Jangan menebak string dari nama file.
```

## String bermasalah

```text
Dapat [N] dokumen untuk string pertama. Revisi supaya lebih
strategis.
```

```text
Kata kunci terlalu sempit. Mengapa entri ini tidak ketemu di
Scopus?

[tempel RIS paper itu]
```

```text
Apakah [N] dokumen cukup untuk sebuah review? Bagaimana
menambahnya secara strategis?
```

## Rancang query OpenAlex (saran)

```text
Rancang 5-8 query OpenAlex untuk memetakan [TOPIK], tiap query
satu sudut. Ukur jumlah hit dulu; sempitkan atau lebarkan sampai
sekitar 50-300. Jalankan, lalu laporkan per query: jumlah hit,
10 hit teratas yang belum ada di arti-lit, dan catat query serta
OQL-nya di search-log.
```

## Telusuri dari paper kunci (saran)

```text
Dari [KUNCI 1] dan [KUNCI 2], telusuri yang mengutip dan yang
dikutip. Tampilkan karya yang paling sering muncul dari kedua
arah dan belum ada di arti-lit.
```

## Kill-test (saran)

```text
Klaim saya: "[KLAIM NOVELTY]". Susun string Scopus untuk
mematahkannya: (A) irisan tepat, harap 0-3 hit; (B) kompetitor
terdekat; (C) preseden metode; (D) kecocokan [JURNAL]. Satu code
block per string, dan nyatakan hasil yang akan membunuh klaim.
```

## Yang mungkin terlewat (saran)

```text
Apa yang kemungkinan terlewat OpenAlex untuk topik ini (jenis
dokumen, bahasa, tahun, bidang)? Susun string Scopus untuk tiga
sudut yang paling berisiko.
```

```text
Berapa persen hit tanpa abstrak untuk query ini? Perlakukan yang
tanpa abstrak sebagai belum terverifikasi.
```

## Perbarui sebelum submit (saran)

```text
Jalankan ulang semua query di search-log untuk periode sejak
[TANGGAL]. Tampilkan hanya yang baru, dan tandai yang menyentuh
klaim novelty.
```

Lanjut bila: tiap export punya satu baris di log, dan paper yang Anda tahu relevan ikut muncul.
