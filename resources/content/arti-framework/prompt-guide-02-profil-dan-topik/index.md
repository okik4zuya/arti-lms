---
title: "Profil dan Topik"
order: 2
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

# Profil dan Topik

Anda: topik awal, jawaban wawancara. Claude: Researcher Profile.

## Topik awal

**Ringkas**

```text
I want to do research about [TOPIK]. What to do next?
```

Claude lalu mewawancarai Anda. Jawab dengan fakta konkret, lebih banyak dari yang ditanya (jumlah sampel, tahap studi, waktu tersisa).

**Dengan batasan**

```text
Brainstorm.
Apa kemungkinannya, mengingat profil riset saya.

[PATH catatan call for papers]
```

```text
Batas pengumpulan: [TANGGAL].
```

```text
Go with computation only, recommend a sexy topic.
```

```text
Dari dua topik itu, mana yang lebih "blue sea"?
```

**Dari Idea Bank (saran)**

```text
Ambil dari Idea Bank satu ide yang paling cocok dengan profil
saya, lalu jadikan topik awal.
```

## Researcher Profile

**Bidang baru (saran)**

```text
Tambahkan bagian riset [BIDANG BARU] ke Researcher Profile saya.
Wawancarai saya dulu.
```

**Tanya (saran)**

```text
Apakah profil saya cocok dengan topik ini? Tandai yang tidak.
```

## Wawancara profil (saran)

```text
Wawancarai saya untuk Researcher Profile. Tanyakan satu kelompok
sekali: bidang dan sub-bidang, alat dan teknik yang saya kuasai,
keterbatasan (alat, dana, waktu), tiga paper terbaik saya, jurnal
yang biasa saya tuju, dan bahasa naskah. Rangkum, lalu minta
saya mengoreksi.
```

## Ide topik (saran)

```text
Beri 5 kandidat topik dari [BIDANG] yang cocok dengan profil
saya. Per topik: pertanyaan riset, kenapa layak sekarang, risiko
sudah jenuh, biaya dan waktu, dan apa yang perlu dicek dulu.
```

```text
Tanpa mencari dulu, nilai topik ini terhadap profil dan tenggat:
feasibility, risiko jenuh, dan tiga hal pertama yang harus dicek.
```

## Mulai dari naskah lama (saran)

```text
Naskah saya sudah ada di [PATH]. Petakan ke struktur proyek ARTi
(idea, writing, literature, data); jangan hapus berkas asli.
```

```text
Audit naskah ini: klaim tanpa sumber, angka tanpa data pendukung,
bagian yang menunggu data, dan referensi yang belum diverifikasi.
Jangan menulis ulang.
```

```text
Dari naskah ini, bantu saya mengisi Researcher Profile dan
Journal Target tanpa mengubah isi naskah.
```

Lanjut bila: profil dan topik sudah sama-sama jelas sebelum mencari literatur.
