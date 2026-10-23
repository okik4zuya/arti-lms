---
title: "Journal Target"
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
.content h2 { font-size: 1.5rem; margin-top: 1.6em; }
.content p, .content li {
    font-size: 1.2rem;
    line-height: 1.75;
    margin: 0 0 0.7em;
}
</style>

# Journal Target

Anda: kriteria, teks aims and scope. Claude: Journal Target Sheet dari database Jfinder.

## Kandidat

```text
Rekomendasikan 5 jurnal. Gunakan arti-jfinder.
```

```text
Carikan jurnal dengan SJR sekitar [N].
```

```text
Cari kandidat dari [PENERBIT 1, 2, 3].
```

**Dari referensi sendiri**

```text
List semua jurnal dari semua paper di referensi, lalu pilih yang
cocok. Jurnal Q2-Q3 juga masukkan.
```

## Menyaring

```text
Eliminasi nomor [N-N]. Tambahkan: [JURNAL A, B, C].
Riset aims dan scope-nya. Exclude yang open access only.
```

```text
Aims and scope sudah saya kumpulkan di aims-scope/.
```

## Memutuskan

```text
Primary: [JURNAL]. Usulkan 2 fallback.
```

```text
Temukan [N] jurnal Q2 yang paling direkomendasikan sebagai
fallback.
```

## Bandingkan kandidat (saran)

```text
Bandingkan [JURNAL A, B, C] dalam satu tabel: kecocokan scope,
SJR dan kuartil, jenis artikel yang diterima, waktu review, biaya
OA/APC, dan risiko predator. Tandai data yang belum
terverifikasi.
```

## Contoh serupa (saran)

```text
Adakah paper serupa dengan topik saya di [JURNAL] dalam 3 tahun
terakhir? Kalau ada, apa bedanya dengan klaim saya?
```

## Batas jurnal (saran)

```text
Dari panduan penulis [JURNAL], ambil: batas kata, format
referensi, jenis artikel, kebijakan data, dan kebijakan AI.
Sertakan sumbernya.
```

Lanjut bila: ada target, fallback, dan teks aims and scope tersedia di proyek.
