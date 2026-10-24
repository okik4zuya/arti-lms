---
name: next-session-prompt
description: Ready-to-paste prompt for the arti-lms session after the 2026-10-18 sandbox run: understand the context first, then brainstorm with the researcher (finishing their own tasks, then debugging); implement nothing until agreed
metadata:
  type: project
  created: 2026-10-18 16:40
  updated: 2026-10-18 21:54
---

**Deferred (2026-10-18 21:54): the next session is the landing copywriting one ([[landing-finalisation-prompt]]); this purchase brainstorm waits for a later session.** What happened since this prompt was written, so the first summary can start from the real state: Mayar.id was researched and saved ([[mayar-gateway]], not pursued); a dashboard bug (a browser signed in as another account) was explained and fixed, plus a large learner-app UX pass, `/app/password`, instant "Tandai selesai" and a Redis content cache ([[learner-app-ux]]); cache, session and queue run on Redis in dev (2 `SeriesMail` jobs were waiting in the queue); subject of resent mails, the copy-paste link in the subscription mail and the Snap merchant name were still not decided; the sandbox steps and the licence cleanup are still open.

Paste the block below into a later chat. It replaces the 2026-10-18 brainstorming prompt (that session is done). Context: [[automatic-purchase-plan]] (section "Sandbox 2026-10-18 (evening)"), [[purchase-production-checklist]] (section 5b), [[worker-changes-spec]], [[duitku-fallback]], [[legal-review-notes]].

```
Lanjutan pembelian otomatis ARTi (repo arti-lms). Sesi ini **hanya memahami konteks dulu, lalu brainstorming dengan saya**. Jangan mengimplementasikan, mengubah kode, atau mengubah halaman sebelum kita sepakat apa yang dikerjakan.

**Baca dulu (jangan ulangi discovery):** memory/MEMORY.md, todo-list.md, status.md, `memories/automatic-purchase-plan.md` (bagian "Sandbox 2026-10-18 (evening)"), `memories/purchase-production-checklist.md` (bagian 5 dan 5b), `memories/duitku-fallback.md`, `memories/worker-changes-spec.md`, `memories/php-binary-and-shell.md`, `~/.arti/memory/working-preferences.md`, `~/.arti/memory/memories/automatic-purchase-product-tasks.md`, `wdyt/index.md`. Sampaikan dulu pertanyaan terbuka yang dicatat memory, lalu ringkas keadaan dalam beberapa kalimat untuk saya koreksi.

**Keadaan singkat (2026-10-18 malam):** pembelian otomatis lewat Midtrans sandbox (tunnel ngrok) sudah berjalan sampai akhir: Snap menerima `callbacks`, notifikasi tiba, order `paid` lalu `fulfilled`, akun belajar dan lisensi dibuat, email pembelian masuk, tautan atur password, login, dan dashboard bekerja. Perubahan setelah uji: halaman atur password menyebut syarat sekali dengan satu pesan error gabungan; tautan atur password juga ditulis sebagai teks salin-tempel di bawah tombol (email pembelian dan reset); langkah terminal dan tautan installer langsung dihapus dari email pembelian dan dua templat WhatsApp (diganti arahan ke bagian Download di course). Semuanya belum di-commit. Tes berkas yang disentuh lolos; sisa suite belum dijalankan ulang. Midtrans tetap gateway, Duitku hanya cadangan.

**Bahan brainstorming (usulkan urutan, beri rekomendasi, tunggu jawaban saya):**
1. Cara menyelesaikan tugas di sisi saya: sisa uji sandbox (kirim ulang notifikasi untuk idempotensi, queue dihentikan lalu `orders:reconcile`, lupa password dengan email nyata, halaman Download setelah login, email kirim ulang dengan pengirim `arti.ai.id`), mencabut lisensi uji yang ada di Worker produksi, `.env` (MAIL_SCHEME kosong di port 587, MAIL_FROM_ADDRESS, restart queue worker), templat WhatsApp tersimpan di Filament, nama toko di Snap, pertanyaan ke support Midtrans, `permissions.deny` untuk `.env`, `SITE_SELLER_NAME` dan `SITE_SELLER_CITY`. Usulkan urutan dan cara memandu saya.
2. Rencana debugging hasil implementasi: apa yang patut dicurigai dan bagaimana membuktikannya tanpa menyentuh produksi: event `expired` sebelum `paid` pada order uji (dugaan kartu ditolak, belum diverifikasi), kuota perintis dan tahan tempat 60 menit bila banyak checkout ditinggalkan, `orders:reconcile` untuk order lama, email kirim ulang bersubjek sama (Gmail melipat isinya; subjek berbeda hanya usulan), tampilan browser halaman baru (`/beli`, `/beli/selesai/...`, `/lupa-sandi`, `/atur-sandi/...`, Filament Pesanan, Harga, Promo), sisa suite tes.
3. Sisi produk dan Worker yang masih terbuka: `resend` dengan `silent`, `months: 1000`, label "Lifetime" di klien, `LICENSE.txt` yang masih menyebut garansi, perilaku klien bila Worker mati, dan apakah penerima pesan manual tanpa akun belajar perlu cara lain mengunduh installer.
4. Penyedia WhatsApp otomatis (driver masih `none`) dan tinjauan teks hukum (refund, lifetime, penghentian layanan, identitas penjual).
5. Persiapan produksi: urutan migrasi, variabel `.env`, cron, Cloudflare (aturan skip untuk `/webhooks/midtrans`), Brevo, kapan uji nyata lupa password.
6. Keputusan yang masih terbuka dari sesi ini: subjek email kirim ulang, tautan salin-tempel di email konfirmasi langganan, nama merchant di Snap.

**Aturan keras (sama seperti sesi sebelumnya):**
- Jangan membuat commit atau push kecuali saya minta commit tertentu itu. Perubahan tetap di working tree; beri daftar berkas bila ada yang diubah.
- Jangan membuka, membaca, atau men-grep `.env` (apa pun), token gateway, token Cloudflare atau wrangler, `arti-services\`, atau keluaran `arti_admin.py`. Sebut variabel apa yang harus saya isi atau cek; nilai konfigurasi boleh dibaca lewat proses PHP baru (`config()`), bukan dari berkasnya.
- Jangan menjalankan perintah ke gateway atau Worker produksi; hanya sandbox. Jangan men-deploy. Jangan menjalankan `queue:work` sendiri (tahap lisensinya memanggil Worker produksi dan email Brevo terkirim sungguhan); beri perintahnya untuk saya jalankan.
- Tes memakai database `arti_lms_testing` (lihat testing-db-isolation.md), per berkas; PHP ada di `C:\laragon6\bin\php\php-8.4.12-nts-Win32-vs17-x64\php.exe`; MySQL Laragon harus jalan; periksa RAM bebas dulu (mesin 8 GB, tes bisa lambat di bawah 1 GB). Jangan menahan perintah dengan `sleep` untuk menunggu tindakan saya.
- Jangan menyimpan rahasia di repo atau log; tanggapan webhook dan log tidak boleh memuat key lisensi atau password.
- Ubah warna merek di halaman publik hanya setelah saya melihat pilihannya. Jangan menginstal perangkat lunak sistem (ngrok sudah ada di `C:\laragon6\bin\ngrok\`, versi 3.39.11).
- Obrolan dalam bahasa Indonesia; semua berkas proyek (kode, komentar, memory) dalam bahasa Inggris. Kumpulkan tugas yang hanya bisa saya kerjakan dalam satu daftar bernomor di akhir. Ikuti ritual penutup di CLAUDE.md arti-lms.
```

## Change log
- 2026-10-18 21:54 — deferred behind the landing copywriting session; state since written added.
- 2026-10-18 20:20 — changed to understand-then-brainstorm, nothing implemented until agreed.
- 2026-10-18 20:05 — rewritten for the sandbox-debugging session.
- 2026-10-18 16:40 — created.

