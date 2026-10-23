---
title: "Revisi"
order: 14
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

# Revisi

Anda: baca draft, beri anotasi di dashboard. Claude: revisi per anotasi, figur, dan tabel.

## Anotasi

```text
Perbaiki manuskrip berdasarkan anotasi:
[PATH naskah]
```

```text
Perbaiki anotasi baru.
```

```text
Resolve anotasi di paragraf [N].
```

Penanda anotasi hanya ditangani lewat skill ARTi-annotate; jangan hapus manual.

## Ide baru saat membaca

**Tanya**

```text
Karena ada kata "[KLAIM]", sepertinya perlu tabel atau gambar
yang menunjukkan hal itu. Caranya dengan pencarian. Bagaimana
menurut Anda?
```

```text
Setelah [KLAIM] dinyatakan, pembaca pasti bertanya "kenapa?".
Tonjolkan jawabannya di abstract dan awal introduction;
pastikan paragraf koheren.
```

## Figur dan tabel

```text
Buat panel [N] yang berisi [ISI]. Usulkan layoutnya dulu.
```

```text
Caption terlalu panjang. Ringkas. Judul figur atau tabel
ringkas; penjelasan masuk ke badan teks.
```

```text
Saya sudah mengedit caption ini. Pelajari polanya, tambahkan ke
preferensi saya.
```

```text
Bagaimana Anda menghitung estimasi untuk [ANGKA]?
```

## Struktur dan SI

```text
Periksa kembali urutan Supplementary Information berdasarkan
urutan pemanggilannya di manuscript.
```

```text
Hapus istilah internal dan nama tool (mis. arti-search) dari SI.
```

```text
Update highlights.
```

## Triase anotasi (saran)

```text
Kelompokkan anotasi saya: hanya kata, butuh bukti, butuh data
baru, atau butuh keputusan saya. Kerjakan yang pertama dulu.
```

## Bantah dulu (saran)

```text
Kalau anotasi saya keliru secara ilmiah, bantah dengan sumber
sebelum mengubah naskah.
```

## Cek regresi (saran)

```text
Setelah revisi, periksa lagi: angka, sitasi, penunjuk figur dan
tabel, dan konsistensi istilah. Laporkan yang bergeser.
```

## Beda draft (saran)

```text
Ringkas perbedaan draft [N] dan [N-1]: apa yang berubah per
bagian, dan apa yang tidak boleh berubah tapi berubah.
```

## Simulasi reviewer (saran)

```text
Bertindak sebagai reviewer [JURNAL] untuk draft ini. Beri
komentar utama dan kecil, dan tandai yang berisiko ditolak. Jangan
menulis ulang naskah.
```

## Bukti baru (saran)

```text
Ada temuan baru: [TEMUAN]. Bagian mana yang terdampak? Usulkan
perubahan minimal dan tandai yang perlu pencarian atau figur
baru.
```

Lanjut bila: tidak ada penanda anotasi tersisa dan angka di teks cocok dengan figur dan tabel.
