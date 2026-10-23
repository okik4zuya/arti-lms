---
title: "Sesi"
order: 1
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

# Sesi

Dipakai di semua tahap.

- `[KURUNG]` = isi sendiri
- **Ringkas** = 1-2 baris, memory proyek sudah memuat konteks
- **Lengkap** = sesi baru atau tugas berisiko
- **Yolo** = jalan sendiri, flag keputusan di akhir
- **Tanya** = tanya dulu, belum perintah
- **(saran)** = rancangan baru untuk menutup celah, belum diuji di proyek yang selesai
- Tanpa tanda = dari prompt yang pernah saya pakai, dirapikan

## Mulai

**Ringkas**

```text
Saya ingin meneliti [TOPIK]. Apa langkah selanjutnya?
```

**Dengan skill**

```text
Ini project baru.
Saya ingin membuat [paper review SLR / artikel penelitian].
Topik: [TOPIK].
Gunakan /ARTi-[SLR / idea] skill.
```

## Lanjut

```text
Teruskan project ini. Sudah sampai mana? Apa selanjutnya?
```

```text
Lanjutkan: memory/memories/[NAMA BERKAS].md
```

## Lengkap (templat sesi)

```text
Lanjutkan proyek [NAMA]. Tahap sekarang: [TAHAP].
Ini bukan tahap [TAHAP YANG BELUM WAKTUNYA].

BACA DULU (urutan ini):
1. memory/MEMORY.md, todo-list.md, status.md, preferensi kerja.
2. [PATH berkas khusus tahap]
3. Tampilkan isi inbox/ jika ada.

KEADAAN TERKUNCI (jangan dibuka ulang; tandai jika ada fakta
baru yang bertentangan):
- [KEPUTUSAN / ANGKA FINAL]

TUGAS (berurutan, tunggu keputusan saya di tiap langkah):
1. [LANGKAH]
2. [LANGKAH]

ATURAN TETAP:
- Percakapan Indonesia, naskah Inggris.
- Python hanya lewat interpreter bawaan ARTi, path lengkap.
- Jangan git commit atau push.
- Setiap entri bertag; klaim tanpa sumber: [SOURCE NEEDED].

KEPUTUSAN TERBUKA (tanya singkat di awal, jangan blokir):
1. [PERTANYAAN]

Akhiri dengan: apa yang berubah, apa yang masih terbuka,
satu baris langkah berikutnya.
```

## Yolo

**Lengkap**

```text
Mode yolo: jalankan [LANGKAH A sampai B] tanpa konfirmasi antar
langkah. Ambil keputusan sendiri dengan default yang kamu
rekomendasikan, catat di [PATH berkas flag]. Tanya saya HANYA
untuk fakta yang hanya saya punya. Setelah [B] selesai, BERHENTI.
Di akhir, daftar semua keputusan dan hal yang perlu verifikasi.
```

**Ringkas**

```text
Yolo. Flag semua keputusan di akhir.
```

**Menjawab sendiri**

```text
Kalau butuh konfirmasi saya, jawab sendiri seolah-olah kamu saya.
Flag keputusan dan hal yang perlu verifikasi di akhir.
```

Jangan dipakai untuk memilih klaim novelty.

## Simpan dan tunda

```text
Simpan ini di memory, saya akan mengerjakannya besok lagi.
```

```text
Simpan temuan ini di memory. Saya kerjakan di sesi lain;
sekarang fokus ke [TUGAS SEKARANG].
```

## Cek memory

**Tanya**

```text
Apakah sudah ada memory mengenai [TOPIK]?
```

```text
Apakah memory sudah diperbarui? Berkas mana yang diubah?
```

```text
Catatan penting ini kira-kira disimpan di mana?
```

## Setuju, tanya, koreksi

**Setuju**

```text
Setuju semua kecuali no. [N], karena [ALASAN].
```

**Tanya**

```text
Apa konsekuensinya kalau saya pilih opsi 1 dan 2, terkait
pekerjaan yang harus saya lakukan?
```

```text
Bagaimana menurut Anda?
```

```text
Apakah angka ini sudah dicek dari sumber lain selain [KUNCI]?
```

**Koreksi**

```text
Bukan itu maksud saya. Yang terjadi sebenarnya: [PENJELASAN].
Revisi berdasarkan ini.
```

## Ringkas keadaan (saran)

```text
Ringkas keadaan proyek dalam maksimal 10 baris: tahap sekarang,
keputusan terkunci, yang menunggu saya, dan risiko terbesar.
```

```text
Cocokkan fakta dulu sebelum lanjut: [PERIKSAAN 1], [PERIKSAAN 2].
Laporkan selisih apa pun sebelum bekerja.
```

## Rem dan batas (saran)

```text
Berhenti. Jangan ubah apa pun dulu. Jelaskan berkas mana yang
akan kamu ubah dan kenapa.
```

```text
Hanya sentuh [PATH]. Berkas lain hanya dibaca.
```

```text
Buat rencananya dulu, jangan dieksekusi. Tunggu persetujuan saya.
```

## Audit klaim selesai (saran)

```text
Kamu bilang [X] sudah selesai. Buktikan: sebutkan berkas dan
bagian yang berubah, lalu jalankan satu pemeriksaan yang bisa
gagal.
```

```text
Apa yang kamu asumsikan di sesi ini tanpa konfirmasi saya?
Daftar semuanya.
```

## Pulih setelah konteks dipadatkan (saran)

```text
Konteks sesi ini sudah dipadatkan. Baca ulang memory proyek, lalu
sebutkan tahap sekarang dan lima keputusan terkunci sebelum
melanjutkan.
```

## Rekomendasi bawaan (saran)

```text
Untuk tiap keputusan, beri satu rekomendasi dengan alasan. Kalau
saya tidak menjawab, pakai rekomendasimu dan catat sebagai flag.
```

## Wrap-up

**Lengkap**

```text
Kita akhiri sesi ini.
1. Update memory proyek: berkas topik yang relevan, status.md
   (timpa blok Current state), todo-list. Tanggal dari `date`.
2. Tulis prompt awal sesi baru, siap salin, satu code block:
   tahap dan tugas; BACA DULU; KEADAAN TERKUNCI; TUGAS bernomor;
   ATURAN TETAP; KEPUTUSAN TERBUKA.
3. Daftar semua tugas yang hanya bisa saya kerjakan, bernomor,
   tandai yang memblokir langkah berikutnya.
4. Sebutkan apa yang belum diverifikasi.
```

**Ringkas**

```text
Update memory. Lalu buatkan prompt awal sesi baru, siap copy-paste.
```

**Dengan tujuan**

```text
Update memory. Pindah ke sesi baru, buatkan prompt awalnya.
Di sesi baru, saya akan [TUGAS BERIKUTNYA].
```

**Kumpulkan keputusan**

```text
Update memory. Kumpulkan semua keputusan dan hal yang perlu
diverifikasi dari sesi ini, masukkan ke prompt sesi baru.
```

**Tugas saya**

```text
List semua hal yang perlu saya siapkan.
```

**Dua sesi paralel**

```text
Saya akan buka dua sesi paralel: (1) [TUGAS A] (2) [TUGAS B].
Update memory, buatkan prompt untuk keduanya, pastikan tidak
menyentuh berkas yang sama.
```

Kalimat "tidak menyentuh berkas yang sama" (saran).
