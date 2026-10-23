---
name: course-packages-prompt
description: Ready-to-paste prompt for the next session — brainstorm course packages beyond ARTi Framework (tool course vs workflow courses, one-time offer (OTO) discount with ARTi), with the current product and checkout facts that constrain it
metadata:
  type: project
  created: 2026-10-19 08:15
  updated: 2026-10-19 08:39
---

Written at the end of the 2026-10-19 session (landing checkout form, WhatsApp removal, four-problems copy: [[landing-checkout-form]], [[landing-copy-four-problems]]). The researcher continues in a fresh chat on **product and package design**, not on landing copy. In Indonesian because it is the researcher's own message to Claude. The purchase brainstorm in [[next-session-prompt]] stays deferred and is mostly superseded (the purchase is built, see [[automatic-purchase-plan]]).

The word the researcher could not recall is **One-Time Offer (OTO)**: a discounted offer shown once, tied to buying one product, gone afterwards. First guess "bundle" was wrong for what they mean (they ruled out bundle, order bump and upsell). Related: *post-purchase offer* (OTO on the thank-you page, after paying; the most natural fit here because the buyer already has an account), *order bump* (ticked in the form before paying), *buyer-only price*. Honesty rule for an OTO: the price must really be gone once the offer has passed; the same price reappearing elsewhere is fake scarcity and breaks the hard rule in [[landing-page-concept]].

```
Sesi ini: **brainstorming desain paket kursus**, di luar ARTi Framework. Jangan mengubah kode, halaman, atau copy sebelum kita sepakat apa yang dikerjakan. Tidak ada commit atau push kecuali saya minta.

**Baca dulu (jangan ulangi discovery):** memory/MEMORY.md, todo-list.md, status.md, `content-repo-pointer.md` dan `subchapter-hierarchy.md` (isi e-course sekarang: 5 section, 18 bab, baru bagian Pendahuluan yang ditulis), `memories/automatic-purchase-plan.md` dan `landing-checkout-form.md` (cara order, harga, dan fulfilment bekerja sekarang), `memories/landing-page-concept.md` (aturan pemasaran jujur), `memories/landing-copy-four-problems.md`, `memories/legal-review-notes.md`, `~/.arti/memory/working-preferences.md`, `wdyt/index.md`. Sampaikan dulu pertanyaan terbuka yang dicatat memory, lalu ringkas produk dan alur beli saat ini dalam beberapa kalimat untuk saya koreksi.

**Keadaan produk sekarang:** satu produk, "ARTi Framework" = e-course + lisensi (10 skill, dashboard lokal, alat), Rp99.000 untuk 100 pembeli pertama lalu Rp199.000, bayar sekali, berlaku selamanya. Pembelian otomatis lewat Midtrans (Snap), form di landing page, dua kartu metode (VA/GoPay/ShopeePay dan QRIS), akun belajar + kunci lisensi dikirim lewat email. Harga dan promo diatur di Filament lewat `Offer` (promo > perintis > normal). Dukungan lewat email hi@arti.ai.id, WhatsApp sudah dihapus dari UI. Batasan teknis yang perlu dipikirkan: order dan item Midtrans masih satu produk tetap; akses e-course dicatat per pengguna dan kursus tanpa tanggal kedaluwarsa; lisensi dibuat di Worker terpisah (`months: 1000`).

**Ide saya (belum matang, kritik dan perbaiki):**
- Pisahkan **kursus alat** (cara memakai ARTi dan alatnya) dari **kursus workflow** untuk satu jenis luaran, misalnya "Workflow Paper Riset", "Workflow Paper SLR", "Workflow Proposal BIMA", dan lainnya.
- "Workflow Paper Riset dengan ARTi" ditawarkan **hanya sekali, saat pembelian produk ARTi itu** (istilahnya One-Time Offer atau OTO), dengan harga diskon yang tidak tersedia lagi sesudahnya, dan mungkin sebaliknya untuk produk lain. Tolong jelaskan perbedaan OTO dengan post-purchase offer, order bump, bundle, dan upsell, tunjukkan di mana penawaran itu muncul (sebelum atau sesudah bayar), dan rekomendasikan yang cocok. Syarat jujur: harga OTO benar-benar hilang setelah penawaran lewat.

**Bahan brainstorming (usulkan urutan, beri rekomendasi, tunggu jawaban saya):**
1. **Peta produk:** kursus apa yang masuk akal, apa isi tiap kursus, mana yang alat dan mana yang workflow, apa yang tersisa untuk e-course ARTi Framework sekarang, dan apakah satu e-course yang sudah ada perlu dipecah. Kursus yang belum ditulis tidak boleh dijual sebagai jadi (aturan keras memory).
2. **Audiens dan permintaan:** siapa yang membeli tiap kursus (dosen yang menulis paper, peneliti SLR, pengusul hibah), bukti permintaan (pakai yt-keyword-audit dan kompetitor yang sudah diaudit, jangan mengarang), dan urutan peluncuran.
3. **Arsitektur harga:** harga tiap kursus dan lisensi, berapa harga OTO, di mana ia muncul (sebelum atau sesudah bayar), apakah berlaku dua arah, bagaimana ini bergabung dengan hierarki promo yang ada (promo > perintis > normal), dan syarat etis (tidak ada kelangkaan palsu, diskon harus nyata dan jelas hitungannya).
4. **Dampak ke sistem:** katalog produk, order dengan banyak item, akses kursus per paket, fulfilment (lisensi hanya untuk paket yang memuatnya?), pengembalian dana per paket, tampilan di form landing (pilih paket) dan halaman per kursus (SEO). Beri perkiraan besar kerja, jangan membangun.
5. **Dampak ke copy dan hukum:** perubahan di landing, `/syarat`, `/privasi`, tabel perbandingan, serta apa yang harus dibaca ulang oleh pengacara.
6. **Urutan kerja:** usulkan tahapan kecil (misalnya satu paket tambahan dulu), apa yang diuji dulu, dan keputusan yang menunggu saya.

**Aturan keras:**
- Jangan membuka, membaca, atau men-grep `.env`, token gateway atau Cloudflare, `arti-services\`, atau keluaran `arti_admin.py`. Jangan men-deploy atau menjalankan perintah ke gateway atau Worker produksi, jangan menjalankan `queue:work`.
- Jangan membuat klaim "yang pertama", janji lolos detektor, kelangkaan atau countdown palsu, testimoni rekaan, atau menjual bab yang belum ditulis.
- Obrolan dalam bahasa Indonesia; semua berkas proyek (kode, komentar, memory) dalam bahasa Inggris, kecuali copy halaman. Kumpulkan tugas yang hanya bisa saya kerjakan dalam satu daftar bernomor di akhir. Ikuti ritual penutup di CLAUDE.md arti-lms.
```

## Used 2026-10-19
This prompt was run the same day. Outcome and decisions: [[course-packages-decisions]]; SLR page material: [[landing-slr-workflow]]. Agenda items 3, 5, 6 are still open.

## Change log
- 2026-10-19 08:39 — marked as used, pointers to the decisions.
- 2026-10-19 08:40 — the forgotten term is One-Time Offer (OTO), prompt and note updated.
- 2026-10-19 08:15 — created.
