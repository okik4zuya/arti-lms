---
name: landing-page-copy
description: Draft v1 of the ARTi landing page copy in Indonesian, section by section (hero to footer), with placeholders and fact-check notes; follows landing-page-concept outline v2
metadata:
  type: project
  created: 2026-10-14 23:05
  updated: 2026-10-18 22:37
---

Synced to the Blade views on 2026-10-15 (finalisation session). **The Blade views stay the source of truth**; this file is a readable mirror, and later edits go to the views first. Outline and rules: [[landing-page-concept]]. Facts: [[landing-page-info]]. Pain wording: [[landing-page-keywords]]. Voice: "saya/Anda", short sentences, honest passages (`Jujur:`), no hype; the site says "ARTi" rather than "kami". Sections 4, 4b, 6, 7, 9 and 13 below changed since draft v1; the fact-check list at the bottom is updated.

---

## 0. Top bar
Logo ARTi · Beranda · Fitur · Harga (`/#harga`) · ARTi PDF **[Free]** · ARTi SciPeek **[Free]** · Masuk (Buka aplikasi when signed in). 2026-10-18: no buy button in the header any more; the "Free" badge is a small pill pinned to the top right of the link text, `#25d366` with white text (same green as the floating WhatsApp button). A skip link ("Lewati ke isi") comes first.

## 1. Hero
**Eyebrow:** ARTi Framework

**Judul:** Riset dengan AI yang tidak lupa dan tidak mengarang.

**Sub-judul:** Framework riset untuk Claude Code, dari ide sampai submit. Dilengkapi e-course untuk mempelajarinya.

**Tombol utama:** Beli sekarang (ke `/beli`, bayar lewat Xendit; bila gateway belum aktif `/beli` meneruskan ke WhatsApp). Cadangan: tautan "beli lewat WhatsApp" di bagian penawaran *(pesan terisi: "Halo, saya ingin membeli ARTi paket perintis.")*
**Tombol kedua:** Coba gratis (2026-10-18; jumps to `#coba-gratis`, whose first card links to chapter 1)

**Baris kepercayaan:** Bayar sekali · Windows · Butuh Claude Code *(2026-10-18: klaim garansi dihapus dari halaman pemasaran; mekanisme refund tetap ada di sistem)*

## 2. Masalah
**Judul:** Anda pasti pernah mengalaminya.

AI menulis kutipan dengan percaya diri, kelihatan meyakinkan, tapi pas dicek ternyata **referensi tidak ada di jurnal** yang disebut, jadi tetap harus dicek satu per satu.

Tiap sesi baru AI **mulai dari nol**, jadi literatur harus diunggah ulang, aturan dijelaskan ulang, tujuan diingatkan ulang, padahal kemarin sudah. *(2026-10-18: pains 1 and 2 rewritten in the researcher's voice, one "tapi ... jadi ..." sentence each, no opener word or question.)*

Output AI memang "bagus", tapi harus **copy paste** lagi ke word, sekalinya bisa buat word, formatnya tidak sesuai, belum lagi merapikan Zotero. *(ending changed 2026-10-18 to avoid repeating "copy paste", the researcher's wording)*

**Saya juga merasakannya, dan saya membuat solusinya untuk Anda.** *(closing line of the problem section, added 2026-10-18; "membuat" not "memberikan" so it does not read as free)* *(2026-10-18: the researcher's own sentence, verbatim; replaces the "Bingung" paragraph.)*

*(2026-10-18: three narrative paragraphs with a relatable opening word each; no cards, icons or per-point headings.)*

## 3. Agitasi
**Judul:** Kesalahan kecil yang tidak berhenti.

Satu kalimat keliru dari AI masuk ke artikel Anda tanpa dicek ke sumber aslinya.

Artikel terbit. Orang lain merujuknya. Lalu artikel yang merujuk itu dirujuk lagi.

Kesalahan itu lama-lama dianggap benar. Bukan karena benar, tapi karena terus disebarkan.

Itu baru soal kredibilitas. Soal tenaga, ada cerita lama tentang Rithah yang memintal benang seharian, lalu menguraikannya lagi tiap malam. Bekerja dengan AI yang tidak punya memori terasa persis begitu. Kerja hari ini tidak pernah jadi pijakan untuk besok.

*Bukan berarti AI-nya buruk. Alatnya hebat, tapi belum jadi sistem.*

## 4. Solusi
**Judul:** Solusinya: Claude diajari cara kerja riset, dan diberi alatnya. *(2026-10-18, the researcher's pick; replaces "Bukan alat baru. Cara kerja di atas Claude Code.")*

Claude sudah pintar. Yang belum ia punya adalah cara kerja riset dan perkakas untuk mengerjakannya.

[Image `model-skill-alat.webp`, then the legend: **Pengetahuan dasar** (Claude sudah tahu banyak hal, tapi belum tentu tahu cara meneliti dengan benar) · **Skill** (lembar cara kerja: langkah riset yang sudah tersusun, dari gap sampai balasan reviewer) · **Alat** (perkakas: cari literatur, ubah PDF, siapkan naskah dan gambar).]

Itu yang ARTi beri. Di atasnya ada tiga fondasi yang menjaga kerjanya tetap benar dan tidak lupa.

**Memori per topik.** Keputusan dan konteks proyek tersimpan sebagai berkas kecil per topik, dengan indeks. Sesi berikutnya melanjutkan, bukan mengulang.

**Referensi asli dan bisa dilacak.** Satu basis data rujukan per proyek. Setiap kutipan menunjuk ke sumber yang benar-benar ada, dan teks lengkap PDF diubah ke Markdown agar bisa dicari.

**Scratchbook.** Tempat menimbun temuan dan kutipan per bagian naskah, dengan penanda asal setiap klaim. Klaim tanpa sumber tidak boleh masuk naskah.

**Hebatnya lagi, naskah langsung jadi Word dengan Zotero** *(h3, added 2026-10-18)*
Naskah Anda ditulis dan diperbaiki bersama AI, lalu hasilnya langsung jadi berkas Word. Sitasinya sudah berupa field Zotero, tanpa **copy paste**.
> **ARTi mengeluarkan berkas Word yang sudah berisi field Zotero.** (callout)
*Satu klik Refresh di Zotero, dan format sitasi mengikuti gaya yang Anda pilih. Butuh Zotero di Word.*

## 4b. Seperti apa wujudnya? (`#wujud`)
Bukan kotak hitam. Semuanya berkas biasa di komputer Anda.

[Image `memori-duo.webp`: Tanpa memori (tiap sesi mulai dari nol) / Dengan memori (sesi berikutnya melanjutkan).] "Map itu, wujud aslinya seperti ini:" then the real `memory/` tree of a paper project, and a scratchbook sample with invented content labelled "contoh format".

## 5. Cara kerja
**Judul:** Amati, Replikasi, Tambah Inovasi.

*Nama ARTi punya tiga arti. Salah satunya, alur kerja ini.*

| | |
|---|---|
| **Amati** | Petakan gap dari literatur nyata, ukur kebaruan ide, dan kenali jurnal tujuan sebelum menulis satu kalimat pun. |
| **Replikasi** | Uji apakah idenya layak dikerjakan dengan alat yang Anda punya, lalu kumpulkan bahan mentah bertanda sumber. |
| **Tambah Inovasi** | Tulis, buat gambar dan tabel, revisi, dan jawab reviewer dengan kebaruan yang diukur, bukan ditebak. |

**Jujur:** ARTi membantu prosesnya. Diterima atau tidaknya naskah tetap keputusan jurnal.

> **Dari ide sampai publish, bersama ARTi.**

## 6. Demo
**Judul:** Lihat caranya bekerja.

40 s explainer (`arti-explainer.mp4`, lazy, poster) with the note "Ringkasan 40 detik, ilustrasi konsep. Demo layar sebenarnya ada di bawah." Below it a "Segera" badge: "Video demo 1-2 menit, rekaman layar apa adanya, sedang disiapkan." (until `SITE_DEMO_VIDEO_EMBED_URL` is set).

## 7. Isi paket
**Judul:** Satu paket: e-course dan lisensi.

**E-course ARTi Framework.** Dibaca langsung di aplikasi, ditulis dari pengalaman memakai ARTi untuk naskah sendiri. Bagian Pendahuluan sudah bisa dibaca. Bagian Amati, Replikasi, dan Tambah Inovasi sedang ditulis, dan bab baru yang terbit selama masa langganan Anda ikut Anda dapat.

**Lisensi ARTi Framework**, satu tahun. 10 skill, dashboard lokal, dan alat pendukung.

**Skill, per tahap:**
- **Amati:** *ARTi-idea* memetakan gap dan menilai kebaruan. *ARTi-jfinder* mencari dan membandingkan jurnal. *ARTi-crosscite* memeriksa apakah dua kelompok makalah pernah saling mengutip.
- **Replikasi:** *ARTi-scratchbook* menampung bahan bertanda sumber. *ARTi-ref* mengelola rujukan dan teks lengkap.
- **Tambah Inovasi:** *ARTi-writing* menuntun dari profil jurnal, kerangka, draf, sampai balasan reviewer. *ARTi-figure* membuat diagram, tabel, dan grafik. *ARTi-annotate* menyelesaikan komentar yang Anda tulis di naskah.
- **Persiapan dan review:** *ARTi-setup* menyiapkan profil Anda dan folder tiap proyek. *ARTi-SLR* menemani tinjauan pustaka berpola PRISMA, dari pertanyaan riset, penyaringan dengan alasan yang tercatat, sampai sintesis.

Screenshots: the e-course (Sistem Memori page) and the dashboard (manuscript pane blurred, captioned). **Di balik layar:** PDF jadi Markdown yang bisa dicari, ekspor ke Word dan PDF, pencarian literatur lewat OpenAlex, Semantic Scholar, dan Google Scholar.

[Lihat semua skill dan alat →] (`/fitur`: stages, 10 skills with example commands, all real tools grouped, dashboard, "Yang tidak dilakukan ARTi", honest e-course status)

## 8. Perbandingan
**Judul:** Apa bedanya dengan Claude Code polos atau alat lain?

ARTi tidak bersaing dengan Claude Code. ARTi berjalan di atasnya. Jadi perbandingan yang jujur: Claude Code polos, atau Claude Code yang sudah dipasangi ARTi.

| | Claude Code polos | Dengan ARTi |
|---|---|---|
| Memori antar sesi | Bisa, tapi strukturnya terserah Anda | File per topik + indeks, aturan baca dan tulis sudah baku |
| Anti-ngawur | Tergantung disiplin Anda | Wajib bertanda sumber, scratchbook sebagai gerbang |
| Alur riset | Prompt dibuat dari nol tiap tahap | Dipaketkan jadi skill |

Dibanding alat lain:

| | ARTi | Claude AI (bawaan) | NotebookLM | ChatGPT, Gemini, dll. |
|---|---|---|---|---|
| Memori | Per topik, tidak bercampur | Ada, tapi topik bisa bercampur | Bukan fiturnya | Bervariasi |
| Menulis dan mengedit berkas naskah | Langsung | Tidak, harus disalin | Tidak | Umumnya tidak |
| Metodologi kerja | Ada | Tidak | Tidak | Tidak |

**Jujur:** ARTi butuh setup awal dan kedisiplinan mengisi memori serta scratchbook. Kalau Anda hanya sesekali bertanya ke AI, chat biasa lebih praktis. ARTi terasa manfaatnya saat kerjanya panjang, berbulan-bulan, seperti menulis artikel ilmiah.

## 9. Coba gratis dulu
**Judul:** Kenalan dulu, tanpa membayar.

**Baca bab 1 e-course.** Cerita kenapa AI belum memuaskan untuk menulis artikel ilmiah, dan apa yang saya lakukan. [Baca bab 1 →]

**Tonton di YouTube.** Tutorial dan demo langsung di layar di channel Dosen BerARTi, dari mencari gap sampai menyiapkan naskah. [Buka channel Dosen BerARTi]

**Pasang ARTi SciPeek.** Ekstensi Chrome gratis. Menampilkan kuartil, SJR, dan H-index jurnal langsung di Scopus, ScienceDirect, dan Google Scholar. Berjalan offline, tanpa pelacakan. [Pasang dari Chrome Web Store →]

**Pakai ARTi PDF.** Aplikasi Windows gratis untuk PDF. Gabung, pisah, kompres, atur halaman, ubah PDF ke Markdown, dan Markdown ke PDF. Berjalan offline: tanpa unggah, tanpa akun. **Jujur:** ubah PDF ke Markdown di sini hanya mengambil teks, tabel dan persamaan tidak dipertahankan. Installer belum bertanda tangan, jadi Windows bisa menampilkan peringatan. [Unduh untuk Windows]

## 10. Syarat yang perlu Anda tahu
**Judul:** Jujur soal syaratnya.

- Butuh **Claude Code** dan langganan Claude berbayar, dibayar terpisah dari ARTi.
- Butuh **VS Code**.
- Untuk sitasi di berkas Word, butuh **Zotero** di Word. *(added 2026-10-18)*
- Saat ini **Windows saja**.
- **Perlu internet saat instalasi** untuk memeriksa lisensi. Kalau lisensi tidak valid atau sudah dipakai di perangkat lain, instalasi berhenti.
- **E-course belum lengkap.** Bagian Pendahuluan sudah ada, sisanya sedang ditulis.
- ARTi **tidak menjamin** naskah diterima, dan tidak menjanjikan hasil otomatis. Anda tetap yang berpikir dan memeriksa.

## 11. Penawaran
**Judul:** Harga perintis untuk 100 pembeli pertama.

~~Rp199.000~~ **Rp99.000** bayar sekali
Tersisa **[N] dari 100** (hitungan nyata: pesanan situs + hitungan manual).

Setelah 100 pembeli, harganya Rp199.000, tetap bayar sekali. Saat ada promo event, yang tampil adalah nama promo, harga promo, dan tanggal berakhirnya.

**Kenapa murah sekarang?** E-course masih ditulis. Anda membeli lebih awal dengan harga lebih rendah, dan bab baru ikut Anda dapat.

**Yang Anda dapat:** e-course ARTi Framework · lisensi ARTi Framework · pembaruan ARTi tanpa biaya tambahan.

**Cara membeli:** tekan tombol, isi nama dan email, lalu bayar dengan QRIS, virtual account, atau e-wallet. Akun belajar, kunci lisensi, dan tautan unduhan dikirim ke email Anda.

**[Beli sekarang]** · atau beli lewat WhatsApp

## 12. Pertanyaan yang sering muncul
**Perlu bisa coding?**
Tidak. Anda bekerja dengan Claude Code lewat percakapan. Yang perlu: mau mengikuti struktur folder dan kebiasaan yang diajarkan.

**Apakah ini membuat artikel saya lolos detektor AI?**
Tidak, dan ARTi tidak menjanjikannya. Yang ARTi lakukan: setiap klaim punya sumber, ide dan keputusan tetap dari Anda, dan prosesnya tercatat. Itu dasar penggunaan AI yang bisa dipertanggungjawabkan. Cek juga kebijakan AI jurnal tujuan Anda.

**Apakah ARTi menulis artikel untuk saya?**
Tidak. ARTi membantu menyusun, mencari, dan memeriksa. Gagasan dan tanggung jawab tetap milik Anda.

**Bagaimana dengan keamanan naskah saya?**
Berkas dan basis data ARTi ada di komputer Anda. Tidak ada server ARTi yang menyimpan naskah. Tapi isi yang Anda minta Claude proses tetap dikirim ke Anthropic sesuai layanan Claude Code. Baca kebijakan mereka bila naskah Anda sensitif.

**Bagaimana kalau Claude berubah?**
ARTi bergantung pada Claude Code. Kalau aturan atau harganya berubah, ARTi harus menyesuaikan, dan itu risiko yang nyata.

**Apakah ada biaya tahunan?**
Tidak. Anda membayar sekali. Skill dan berkas Anda berjalan di komputer Anda sendiri, dan data Anda tetap milik Anda. *(2026-10-18: pembelian diubah dari per tahun menjadi sekali bayar; teks "lifetime" di `/syarat` menunggu keputusan dan tinjauan hukum.)*

**Berapa total biayanya?**
Rp99.000 (perintis) atau Rp199.000 sekali bayar untuk ARTi, ditambah langganan Claude Anda sendiri.

## 13. Penutup
> **Dari ide sampai publish, bersama ARTi.**

[Beli lewat WhatsApp]  ·  [Coba gratis dulu] (anchor `#coba-gratis`)

*ARTi tidak berafiliasi dengan Anthropic. Claude dan Claude Code adalah produk Anthropic.*

Footer: Kontak · Privasi · Syarat · Umpan artikel (the last one still shown; see todo about hiding it).

---

## SEO
- **Title (as rendered):** Framework Riset untuk Claude Code, dari Ide sampai Submit — ARTi Framework (every page: `<title> — ARTi Framework`)
- **Meta description:** Framework riset untuk Claude Code: memori antar sesi, referensi asli yang bisa dilacak, dan alur dari gap sampai submit. Termasuk e-course.
- Kata kunci untuk H2/FAQ: referensi asli, novelty penelitian, research gap ai, systematic literature review prisma, claude code indonesia, jurnal predator.

## Catatan cek fakta (updated 2026-10-15)
- Checked against the Blade views: prices Rp199.000 / Rp99.000 (first 100 buyers, one batch), refund 7 days from purchase date, 10 skills (all named), Windows only (no macOS anywhere, a test enforces it), e-course incomplete, no "pertama" claim, no countdown, no testimonial.
- Still open: README "nine skills" fix in ~/.arti; Claude plan requirement wording at publish time; "tersisa N dari 100" needs the real issued count in SITE_LAUNCH_SOLD; offline grace after activation; chapter 2 still has one empty subchapter.
- Data-safety wording follows ebook ch. 3; recheck if the dashboard or chat starts calling other services.
- `/syarat` and `/privasi` now restate the offer, refund, Anthropic data flow and cookies, but still need legal review: see [[legal-review-notes]].

## Change log
- 2026-10-18 22:37 — problem section rewritten in the researcher's voice (pains 1 and 2, bold on pain 3, pain 4 ending "belum lagi merapikan Zotero", bold closing line "Saya juga merasakannya, ..."); section 4 title is the researcher's pick. Blade is the source for exact text.
- 2026-10-18 22:30 — fourth pain paragraph (figures "terlalu AI") and the section-4 block "Gambar dari data, bukan dari imajinasi AI" added; Blade is the source for the exact text.
- 2026-10-18 22:15 — section 2 third pain replaced by the researcher's own sentence; section 4 title changed and the Word-with-Zotero block added; Zotero line in section 10; `/fitur` arti-docx description mentions Zotero. Note: section 7's "Lisensi ... satu tahun" line above is stale (the Blade says "Bayar sekali, berlaku selamanya").
- 2026-10-18 21:54 — header (no buy button, Free badges), hero second button "Coba gratis", section 2 as narrative, "berbahasa Indonesia" dropped from the framework and e-course lines and the meta description. `/scipeek`: "Lihat demo", section title "Begini cara kerjanya." (anchor `#demo`).
- 2026-10-15 09:25 — synced to the Blade views (menu, sections 4/4b/6/7/9/10/11/12/13, SEO, fact-check list).
- 2026-10-14 23:05 — draft v1 written from outline v2.
