---
title: "Arsitektur"
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
.content h2 { font-size: 1.5rem; margin-top: 2em; }
.content p {
    font-size: 1.2rem;
    line-height: 1.75;
    margin: 0 0 0.9em;
}
.content pre {
    font-size: 0.85rem;
    line-height: 1.4;
    overflow-x: auto;
}
</style>

# Arsitektur

Supaya tidak abstrak lagi, mari saya tunjukkan gambaran besarnya.

Sesederhananya, ARTi Framework itu terdiri dari dua lapis: Claude Code sebagai mesin, dan struktur folder plus skill yang saya susun sendiri di atasnya sebagai peta jalan.

Mesinnya sudah ada, tinggal pakai. Peta jalannya, itu yang saya bangun.

## Gambaran besarnya

Berikut gambaran arsitekturnya, saya buat sesederhana mungkin:

```
                         ARTi FRAMEWORK
        ============================================

        MESIN
        +----------------------------------------+
        |  Claude Code                            |
        |  - baca, tulis, edit file langsung       |
        |  - jalan di komputer sendiri              |
        +----------------------------------------+
                          |
                          |  dijalankan mengikuti
                          v
        PETA JALAN (yang saya susun sendiri)
        +----------------------------------------+
        |  SKILLS                                 |
        |  - ARTi-setup   -> onboarding project    |
        |  - ARTi-idea    -> gap map, cari jurnal  |
        |  - ARTi-writing -> draft s.d. rebuttal    |
        |  - ARTi-figure  -> diagram & tabel        |
        +----------------------------------------+
        |  FOLDER                                  |
        |  - memory/  -> ingatan persisten          |
        |  - inbox/   -> catatan & scratchbook      |
        +----------------------------------------+
        |  DASHBOARD LOKAL                         |
        |  - status kerja, progres tiap project     |
        +----------------------------------------+

        semuanya jalan di komputer sendiri --
        tidak ada server pihak ketiga yang menyimpan data
```

Sederhana kelihatannya. Tapi justru itu intinya — bukan tumpukan teknologi baru, hanya cara mengatur apa yang sudah ada.

## Komponen utama

Biar lebih ringkas, saya rangkum komponen-komponennya begini:

| Komponen | Fungsi |
|---|---|
| Claude Code | Mesin utama — baca/tulis/edit file, jalankan perintah, di komputer sendiri |
| Skill: ARTi-setup | Onboarding project baru, menyiapkan struktur folder awal |
| Skill: ARTi-idea | Membantu memetakan gap literatur sampai menentukan target jurnal |
| Skill: ARTi-writing | Menulis draf sampai membalas rebuttal reviewer |
| Skill: ARTi-figure | Membuat diagram dan tabel untuk manuskrip |
| Folder `memory/` | Tempat ingatan persisten disimpan, per topik |
| Folder `inbox/` | Tempat catatan mentah dan bahan scratchbook masuk |
| Dashboard lokal | Melihat status dan progres tiap project sekilas |

## Semua jalan secara lokal

Satu hal yang penting saya tegaskan: semua ini jalan di komputer saya sendiri.

Tidak ada server pihak ketiga yang menyimpan data saya secara permanen.

Ingat tabel perbandingan di bagian [[Claude Code]]? Baris "keamanan data" — di situ saya sudah singgung, data tetap di komputer sendiri, terkirim ke API hanya saat sedang diproses.

Arsitektur ARTi Framework ini yang membuat poin itu benar-benar terwujud, bukan sekadar klaim.

## Jujur lagi

Saya perlu jujur satu hal lagi di sini.

Ini bukan software yang sekali diinstal lalu otomatis jadi, bekerja sendiri tanpa saya sentuh.

Struktur folder ini tetap perlu saya pakai dan isi sendiri, seiring proses menulis berjalan. Skill-nya membantu, tapi keputusan tetap ada di tangan saya.

## Dua yang paling menjawab masalah Chapter 1

Dari semua komponen di atas, ada dua yang paling langsung menjawab masalah yang saya ceritakan di [[2 Masalah Utama]]: sistem memori, dan sistem referensi.

Mari saya bahas satu-satu, dimulai dari sistem memori di bagian berikutnya.
