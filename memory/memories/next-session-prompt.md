---
name: next-session-prompt
description: Ready-to-paste prompt for the next arti-lms session after the 2026-10-18 automatic-purchase build: understand the context first, then brainstorm with the researcher before changing anything
metadata:
  type: project
  created: 2026-10-18 16:40
  updated: 2026-10-18 16:40
---

Paste everything below the line into the next chat. It replaces [[automatic-purchase-prompt]] (that session is done). Context: [[automatic-purchase-plan]], [[purchase-production-checklist]], [[worker-changes-spec]], [[legal-review-notes]].

---

Lanjutan pembelian otomatis ARTi (repo arti-lms). Sesi ini **hanya memahami konteks dulu, lalu brainstorming dengan saya**. Jangan membangun, mengubah kode, atau mengubah halaman sebelum kita sepakat apa yang dikerjakan.

**Baca dulu (jangan ulangi discovery):** memory/MEMORY.md, todo-list.md, status.md, `memories/automatic-purchase-plan.md` (bagian "Built 2026-10-18" dan "Gateway abstraction"), `purchase-production-checklist.md`, `worker-changes-spec.md`, `legal-review-notes.md` (bagian 2026-10-18), `memories/php-binary-and-shell.md`, `wdyt/index.md`, `~/.arti/memory/working-preferences.md`, dan `~/.arti/memory/memories/automatic-purchase-product-tasks.md`. Sampaikan dulu pertanyaan terbuka yang dicatat memory, lalu ringkas keadaan dalam beberapa kalimat untuk saya koreksi.

**Keadaan singkat (2026-10-18):** pembelian otomatis sudah dibangun dan lolos tes (`Http::fake`, database `arti_lms_testing`), semuanya belum di-commit: gateway Midtrans (Snap + Status API + `signature_key`, default) dan Xendit (driver kedua, butuh NIB), tabel `orders`, webhook idempoten, `FulfillOrder` (akun belajar, lisensi `months: 1000` = lifetime, email pembelian), tautan atur password sekali pakai tanpa batas waktu (mati saat password diatur atau diganti), lupa password untuk learner (admin dan support lewat `admin:reset-password` dan `admin:reset-mfa`), tahap WhatsApp terpisah (driver `none`), kuota perintis atom, harga dan promo bisa diatur di Filament ("Harga", "Promo"), refund dan `orders:reconcile`, menu Filament "Pesanan", garansi dihapus dari landing, `/syarat`, `/kontak`, `/privasi` ditulis ulang (kebijakan refund baru, klausul lifetime; belum ditinjau ahli hukum). **Belum pernah dicoba:** panggilan nyata ke Midtrans sandbox, email Brevo dari template baru, tampilan browser halaman baru, Worker dengan `months: 1000`.

**Bahan brainstorming (usulkan urutan, beri rekomendasi, tunggu jawaban saya):**
1. Hasil uji sandbox Midtrans bila saya sudah mencobanya (apakah Snap menerima `callbacks.pending` dan `callbacks.error`, notifikasi tiba, alur penuh sampai email dan login), dan jawaban support Midtrans (e-course dan lisensi perangkat lunak boleh? NPWP untuk QRIS/VA/e-wallet?).
2. Penyedia WhatsApp otomatis (Cloud API dengan nomor khusus atau gateway pihak ketiga; biaya, risiko blokir, kepatuhan), karena tahap itu masih `none`.
3. Tinjauan teks hukum yang saya tulis (syarat refund dan batas waktunya, klausul lifetime dan penghentian layanan, identitas penyelenggara `SITE_SELLER_NAME`/`SITE_SELLER_CITY`), dan apa yang perlu ditanyakan ke ahli hukum.
4. Sisi produk: Worker (`resend` dengan `silent`, `months: 1000`), label "Lifetime" di klien, EULA `LICENSE.txt` yang masih menyebut garansi, perilaku klien bila Worker mati.
5. Persiapan produksi: urutan migrasi, variabel `.env`, cron, Cloudflare, Brevo, dan kapan menjalankan uji nyata lupa password.
6. Apa lagi yang mau saya ubah dari keputusan yang diambil atas nama saya (daftar di plan, bagian "Decisions taken"), termasuk apakah pembeli sebaiknya masuk otomatis setelah mengatur password.

**Aturan keras (sama seperti sesi sebelumnya):**
- Jangan membuat commit atau push kecuali saya minta commit tertentu itu. Perubahan tetap di working tree; beri daftar berkas bila ada yang diubah.
- Jangan membuka, membaca, atau men-grep `.env` (apa pun), token gateway, token Cloudflare atau wrangler, `arti-services\`, atau keluaran `arti_admin.py`. Sebut variabel apa yang harus saya isi atau cek.
- Jangan menjalankan perintah ke gateway atau Worker produksi; hanya sandbox. Jangan men-deploy.
- Tes memakai database `arti_lms_testing` (lihat testing-db-isolation.md), per berkas; PHP ada di `C:\laragon6\bin\php\php-8.4.12-nts-Win32-vs17-x64\php.exe`; MySQL Laragon harus jalan; periksa RAM bebas dulu.
- Jangan menyimpan rahasia di repo atau log; tanggapan webhook dan log tidak boleh memuat key lisensi atau password.
- Ubah warna merek di halaman publik hanya setelah saya melihat pilihannya. Jangan menginstal perangkat lunak sistem.
- Obrolan dalam bahasa Indonesia; semua berkas proyek (kode, komentar, memory) dalam bahasa Inggris. Ikuti ritual penutup di CLAUDE.md arti-lms.

## Change log
- 2026-10-18 16:40 — created at the end of the automatic-purchase session.
