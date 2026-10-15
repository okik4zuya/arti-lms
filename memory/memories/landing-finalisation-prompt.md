---
name: landing-finalisation-prompt
description: The ready-to-paste prompt for the next session, which finalises every public page tied to the landing page (audit, SEO, legal, contact, 404, a11y, checks, commits)
metadata:
  type: project
  created: 2026-10-15 09:10
  updated: 2026-10-15 09:10
---

Prompt written at the end of the 2026-10-15 session ([[status]], [[todo-list]]). Paste everything under the line. Written in Indonesian because it is the researcher's own message to Claude.

---

Sesi finalisasi landing page ARTi (repo arti-lms). Tujuan: merapikan dan memfinalkan semua halaman publik yang berhubungan dengan landing page sampai siap dideploy. Bekerja otonom, tanya hanya untuk keputusan yang memang milik saya.

**Baca dulu, jangan ulangi discovery:** memory/MEMORY.md, todo-list.md, status.md, memories/landing-page-concept.md (bagian paling bawah adalah keadaan terbaru), page-speed.md, redis-setup.md, artipdf-landing-facts.md, wdyt/index.md, ~/.arti/memory/working-preferences.md. Sampaikan dulu pertanyaan terbuka yang dicatat memory.

**Cakupan:** /, /fitur, /kontak, /privasi, /syarat, /baca dan halaman bab 1, /artikel (dan feed, sitemap, robots), header, footer, halaman 404 dan error, plus tag meta tiap halaman.

**Langkah:**
1. `git status`, ringkas perubahan yang belum di-commit, jalankan seluruh tes, lalu commit per kelompok logis (landing dan /fitur, media, menu dan redirect, kecepatan, Redis dan Predis, tes, memory). Saya mengizinkan commit setelah tes hijau; jangan push.
2. Audit tiap halaman dan sajikan sebagai tabel temuan per halaman: konsistensi copy (saya/Anda, kalimat pendek, passage "Jujur"), kebenaran fakta (harga Rp199.000 dan Rp99.000 untuk 100 pembeli pertama, garansi 7 hari dari tanggal pembelian, 10 skill, Windows saja, e-course belum lengkap), tautan dan anchor yang rusak, nama produk yang konsisten, sisa teks fase-1 yang usang.
3. SEO per halaman: title dan description unik, canonical, OG image, sitemap, robots, lang, heading hanya satu h1.
4. /kontak, /syarat, /privasi: cek isinya terhadap kenyataan (font kini self-hosted, session di Redis atau database, data ke Anthropic lewat Claude Code, garansi dan pengembalian dana). Jangan mengarang teks hukum; tandai bagian yang perlu ditinjau ahli hukum dan beri usulan redaksi.
5. Halaman 404 dan error dalam bahasa Indonesia, konsisten dengan situs.
6. Aksesibilitas: kontras, fokus keyboard, alt text, urutan heading, tautan lompat ke isi, details/summary, prefers-reduced-motion.
7. Cek tampilan di 375, 768, dan 1100 px dengan resep di page-speed.md; daftar yang hanya diemulasi (autoplay /fitur, anchor, HP sungguhan) saya cek manual.
8. Sinkronkan memories/landing-page-copy.md ke copy Blade final, perbarui landing-page-info bila usang.

**Aturan keras:** tidak boleh klaim "pertama" atau janji lolos detektor AI, tidak ada countdown atau kelangkaan palsu, tidak ada bukti atau testimoni rekaan, tidak menyebut macOS, tidak mengklaim akurasi tabel atau persamaan untuk ARTi PDF, jangan jual bab BONUS yang belum ditulis. Salinan yang sudah saya setujui jangan ditulis ulang tanpa alasan kuat; perbaikan jelas (typo, tautan rusak, inkonsistensi) langsung kerjakan, perubahan besar usulkan dulu.

**Keputusan yang mungkin perlu saya jawab:** nomor WhatsApp dan jumlah terjual untuk .env produksi, apakah /artikel dan umpan di footer disembunyikan sampai ada artikel pertama, nama "ARTi Workflow" atau "ARTi Framework" di luar ebook, bagian "Tentang penulis", demo video asli dan screenshot hero.

**Catatan teknis:** jalankan PHP lewat PowerShell (di bash `php` tidak ada), Python ARTi dengan `-I`, ukuran kecepatan harus memindahkan `public/hot` sementara dan mengembalikannya di blok finally. Repo artipdf terpisah (LICENSE, screenshot, rilis v1.0.7 menunggu saya): jangan diubah, cukup catat apa yang masih saya kerjakan di sana.

**Selesai bila:** tes hijau, halaman terverifikasi di 375 dan 1100 px, commit rapi, blok "Current state" di status.md ditulis ulang, todo-list dicentang.

## Change log
- 2026-10-15 09:10 — created at the end of the 2026-10-15 session.
