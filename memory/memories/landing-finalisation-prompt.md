---
name: landing-finalisation-prompt
description: Ready-to-paste prompt for the next session: brainstorm first, then finalise the landing page including its copywriting, evaluated against the reptilian-brain (six stimuli) concept; replaces the 2026-10-15 finalisation prompt (that pass is done)
metadata:
  type: project
  created: 2026-10-15 09:10
  updated: 2026-10-18 21:54
---

Paste the block below into the next chat. It replaces the 2026-10-15 prompt (the audit, SEO and commit pass it described is done, see [[landing-page-concept]] "Finalisation pass"). Written in Indonesian because it is the researcher's own message to Claude. The purchase-flow brainstorm that was planned for "the next session" is deferred, see [[next-session-prompt]].

```
Finalisasi landing page ARTi (repo arti-lms), termasuk copywriting. Sesi ini **dimulai dengan brainstorming bersama saya**. Jangan mengubah kode, halaman, atau copy sebelum kita sepakat apa yang dikerjakan.

**Baca dulu (jangan ulangi discovery):** memory/MEMORY.md, todo-list.md, status.md, `memories/landing-page-concept.md` (positioning, formula copy, aturan pemasaran jujur, keputusan yang sudah diambil, bagian "Changes 2026-10-18"), `memories/landing-page-copy.md` (copy saat ini per section), `memories/landing-page-keywords.md` (bahasa nyeri audiens), `memories/landing-page-info.md`, `memories/legal-review-notes.md` (batas klaim), `memories/artipdf-landing-facts.md`, `memories/learner-app-ux.md` (bagian "How the UI was checked"), `~/.arti/memory/working-preferences.md`, `wdyt/index.md`. Sampaikan dulu pertanyaan terbuka yang dicatat memory, lalu ringkas keadaan landing page dalam beberapa kalimat untuk saya koreksi.

**Keadaan singkat (2026-10-18 malam):** beranda `/` punya 13 section dalam satu kolom 600 px. Perubahan terakhir, semuanya belum di-commit: tombol "Beli sekarang" dihapus dari header; tombol hero kedua menjadi "Coba gratis" (ke `#coba-gratis`); badge "Free" hijau (warna tombol WhatsApp) di menu ARTi PDF dan ARTi SciPeek; "berbahasa Indonesia" dihapus dari deskripsi framework; section masalah ditulis ulang jadi tiga paragraf naratif ("Pernah…", "Capek…", "Bingung…") tanpa kartu dan ikon; `/scipeek` memakai "Lihat demo" dan judul "Begini cara kerjanya.". Harga: Rp99.000 untuk 100 pembeli pertama lalu Rp199.000, bayar sekali (lifetime); tidak ada klaim garansi di halaman pemasaran; pembelian otomatis lewat Midtrans sudah jalan di sandbox.

**Bahan brainstorming (usulkan urutan, beri rekomendasi, tunggu jawaban saya):**
1. **Evaluasi copy dengan konsep otak reptil** (neuromarketing, enam stimulus): (a) *self-centered*: bicara tentang pembaca, bukan tentang ARTi; (b) *contrast*: sebelum dan sesudah, ada dan tanpa ARTi, jelas dan cepat terlihat; (c) *tangible*: konkret, bukan abstrak (angka, contoh, hasil yang bisa dibayangkan); (d) *memorable*: awal dan akhir halaman yang diingat; (e) *visual*: gambar membawa pesan, bukan hiasan; (f) *emotional*: rasa sakit atau lega yang nyata. Beri saya tabel per section beranda: skor tiap stimulus, apa yang kuat, apa yang lemah, dan perubahan konkret yang diusulkan dengan contoh kalimat. Periksa juga alur nyeri, klaim, hasil, urutan section, tombol ajakan (jumlah, penempatan, kata), dan apa yang dilihat orang di layar pertama tanpa scroll.
2. **Batas etis** (aturan keras dari memory): tidak ada klaim "pertama" atau janji lolos detektor AI, tidak ada countdown atau kelangkaan palsu, tidak ada bukti atau testimoni rekaan, tidak menyebut macOS, tidak mengklaim akurasi tabel atau persamaan ARTi PDF, jangan jual bab BONUS yang belum ditulis. Jawab terus terang bila sebuah teknik otak reptil melanggar aturan ini atau tidak cocok untuk audiens dosen dan peneliti, dan beri alternatif yang jujur.
3. **Sisa finalisasi landing yang terbuka:** media (demo video asli 1 sampai 2 menit dan posternya, screenshot hero VS Code + Claude Code, foto atau bagian "Tentang penulis", mana yang memang dibutuhkan), cek tampilan 375 px dan ponsel sungguhan, autoplay `/fitur`, ketidakkonsistenan sisa ("Baca bab 1 gratis" di `/scipeek`, `/artipdf`, `/fitur`, 404 dan email seri 4; kata "wujud" di beranda, `/fitur`, email seri 2), keputusan nama "ARTi Workflow" atau "ARTi Framework" di luar ebook, kapan `/artikel` ditampilkan, nilai `.env` produksi untuk situs (`SITE_SELLER_NAME`, `SITE_SELLER_CITY`, nomor WhatsApp).
4. **Cara menerapkan:** usulkan urutan perubahan, satu section per putaran: tampilkan dulu teks lama dan baru berdampingan, tunggu persetujuan saya, baru ubah Blade dan tes, lalu screenshot hasilnya dan periksa sendiri sebelum melapor. Untuk perubahan warna atau visual merek: tampilkan pilihannya dulu (screenshot dari replika), jangan langsung menerapkan.

**Aturan keras:**
- Jangan membuat commit atau push kecuali saya minta commit tertentu itu. Perubahan tetap di working tree; beri daftar berkas yang diubah.
- Copy yang sudah saya setujui jangan ditulis ulang tanpa alasan kuat; di sesi ini saya memang meminta evaluasi, tetapi tiap perubahan tetap menunggu persetujuan saya. Perbaikan yang jelas (typo, tautan rusak) langsung kerjakan.
- Ubah warna merek di halaman publik hanya setelah saya melihat pilihannya. Jangan menginstal perangkat lunak sistem.
- Jangan membuka, membaca, atau men-grep `.env`, token gateway atau Cloudflare, `arti-services\`, atau keluaran `arti_admin.py`; sebut variabel apa yang harus saya isi. Jangan men-deploy, jangan menjalankan perintah ke gateway atau Worker produksi, jangan menjalankan `queue:work`.
- Tes per berkas ke database `arti_lms_testing`; PHP ada di `C:\laragon6\bin\php\php-8.4.12-nts-Win32-vs17-x64\php.exe`; MySQL dan Redis Laragon harus jalan; periksa RAM bebas dulu (mesin 8 GB). Verifikasi tampilan dengan Chrome headless (resep di `learner-app-ux.md`); JavaScript dan tampilan ponsel yang tidak bisa diuji di sana saya cek manual, sebutkan dengan jelas apa yang belum terverifikasi.
- Obrolan dalam bahasa Indonesia; semua berkas proyek (kode, komentar, memory) dalam bahasa Inggris, kecuali copy halaman yang memang berbahasa Indonesia. Kumpulkan tugas yang hanya bisa saya kerjakan dalam satu daftar bernomor di akhir. Ikuti ritual penutup di CLAUDE.md arti-lms.
```

## Change log
- 2026-10-18 21:54 — rewritten for the copywriting and reptilian-brain session (the 2026-10-15 finalisation prompt is done).
- 2026-10-15 09:10 — created.
