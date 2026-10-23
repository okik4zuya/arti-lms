---
title: "Idea Canvas"
order: 6
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

# Idea Canvas

Anda: pilih gap, putuskan ide. Claude: Idea Canvas dengan skor novelty, simpan ke Idea Bank.

## Idea Canvas

```text
Konfirmasi [Gap N] sebagai [sudut utama review / gap target
artikel]. Buat Idea Canvas: hasilkan kandidat [judul review /
pertanyaan dan hipotesis], skor C/M/E terhadap profil saya, dan
tandai apakah [Gap M] layak dilipat sebagai sudut sekunder atau
tetap terpisah.
```

**Menyetujui**

```text
Konfirmasi Idea [N] apa adanya.
```

## Koreksi premis

```text
Revisi ide dan framing berdasarkan ini, lalu evaluasi novelty.

[PENJELASAN RINCI mekanisme sebenarnya]
```

## Tanya

```text
Idea Canvas ini untuk apa? Bukankah topik review sudah ada?
```

## Idea Bank

```text
Simpan Idea Canvas ini ke Idea Bank.
```

```text
Simpan semua ide yang pernah kita bahas ke Idea Bank, dengan tag
lajur: [DAFTAR].
```

## Beberapa ide dari satu gap (saran)

```text
Dari Gap [N], hasilkan 3-4 ide. Skor C/M/E tiap ide terhadap
profil saya, pilih satu, dan jelaskan ide lain dilipat atau
dibuang karena apa.
```

## Kalimat posisi (saran)

```text
Tulis satu kalimat posisi: "Berbeda dari [PENELITIAN TERDEKAT],
studi ini [PERBEDAAN] sehingga [MANFAAT]."
```

## Stress-test (saran)

```text
Bertindak sebagai reviewer [JURNAL]. Beri tiga alasan paling kuat
untuk menolak ide ini, dan apa yang harus saya siapkan untuk
menjawabnya.
```

## Cocokkan ke jurnal (saran)

```text
Apakah level novelty ide ini sesuai dengan batas jurnal target?
Kalau tidak, usulkan pembingkaian ulang, bukan ide baru.
```

## Tinjau Idea Bank (saran)

```text
Tampilkan Idea Bank. Peringkatkan terhadap profil dan tenggat
saya, dan tandai yang sekarang layak diangkat jadi topik awal.
```

Lanjut bila: satu ide terpilih dengan skor novelty yang Anda terima.
