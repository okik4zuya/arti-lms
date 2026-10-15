---
name: landing-scipeek-artipdf
description: Concept for the two free-tool pages /scipeek and /artipdf (called from #coba-gratis): purpose, outlines, honest limits, keyword evidence, decisions, build prerequisites
metadata:
  type: project
  created: 2026-10-15 11:30
  updated: 2026-10-15 12:50
---

Both pages built (see the build sections). Facts: [[artipdf-landing-facts]], SciPeek repo `C:\python_tools\arti_jfinder\ARTi Journal Finder\extension` (README, `store-assets/`, `memory/memories/webstore-listing.md`). Page rules as in [[landing-page-concept]] (600px column, "saya/Anda", "Jujur:" passages, `PublicPageQualityTest` rules). Keyword run: below.

## Role
Free entry points that build trust; goal is install or download, not a sale. The `#coba-gratis` cards link to the internal pages `/scipeek` and `/artipdf`; the install/download CTA lives inside each page. Add both to the sitemap and `PublicPageQualityTest`.

## Decided (researcher, 2026-10-15)
1. **SciPeek is not a paid-package bonus** and its page has no price or "Beli". Reason: Scimago data is free only for non-commercial use with citation. Only a neutral "Bagian dari ARTi" card (link to bab 1 gratis and `/fitur`). Remove SciPeek from the "real bonuses" line in [[landing-page-concept]] (Marketing rules) when the pages are built.
2. **ARTi PDF page waits for v1.0.7.** The researcher releases it; the installer was built 2026-10-15 (see [[artipdf-landing-facts]]). The page must not describe the Compress right-click before that release.
3. Keyword audit done (below); concept updated from it.

## Keyword evidence (yt-keyword-audit, `--lang id --region ID`, 2026-10-15; autocomplete = relative popularity only)
- **SciPeek.** Empty seeds: "cek kuartil jurnal", "kuartil jurnal", "cek sjr jurnal", "sjr scimago", "scimago jurnal", "cek jurnal scopus q1", "cek h index jurnal", "ekstensi chrome jurnal". Words with demand: "**cara cek jurnal scopus**" (variants: di scimago, q1, q3, di google scholar, gratis, sesuai judul), "**cara cek jurnal terindeks scopus**" (atau tidak, di google scholar, gratis), "cek jurnal predator", "scimago journal rank". Videos show the real demand: "Cara Mencari Jurnal SCOPUS sesuai Bidang Penelitian" 275k, "cara cek akreditasi Jurnal internasional (Q1,Q2,Q3)" 21k, "CARA CEPAT CARI JURNAL TERINDEKS SCOPUS Q1-Q4" 27k, "Cara mengetahui quartile jurnal langsung dari scopus.com" 19k, "Cara Menentukan Quartile Jurnal SCOPUS (via Scimago)" 14k, "Q1 atau Q4? Cek peringkat quartile" 2.4k (2025). So people say "**cek jurnal Scopus / terindeks Scopus**" and "**quartile**" (spelling "quartile"/"quartil"), not "kuartil" or "SJR". The task is real and taught by many videos, all manual (open Scimago, type the name).
- **ARTi PDF.** Strong, crowded demand: "gabung pdf" (+ offline, jadi satu), "compress/kompres pdf" (+ offline, gratis, "menjadi 500kb/200kb/1mb"), "pisah pdf", "ubah pdf ke word", "ocr pdf". Top videos 100k-940k views, mostly "tanpa aplikasi" / online tools. "Offline" variants exist but are small ("gabung pdf offline", "compress pdf offline", "aplikasi merge pdf offline", "aplikasi kompres pdf offline pc"); offline videos are 35 to 460k. "pdf to markdown" is English only and developer-flavoured (python, obsidian, marker, docling, pandoc); no Indonesian demand, so the research use must be explained, not searched. Empty: "urutkan halaman pdf", "aplikasi pdf gratis windows", "pdf ke markdown".

## `/scipeek` outline
1. Hero. H1 candidate (colloquial, matches the searched task): "**Cek kuartil jurnal Scopus tanpa buka tab baru.**" Sub: ekstensi Chrome gratis, badge Q1-Q4, SJR, H-index di Scopus, ScienceDirect, Google Scholar. CTA "Pasang dari Chrome Web Store". Trust line: Gratis · Offline · Tanpa akun · Chrome.
2. Masalah: today's way (open Scimago, type the name, per journal) is what the 275k-view videos teach; here it appears beside each result.
3. Wujudnya: the 40 s tutorial (lazy `<video preload="none">`, poster) and one screenshot per site; Q1 green, Q2 amber, Q3 orange, Q4 red as an HTML legend. Note the badge values in composed Web Store shots are illustrative; say so or use real captures.
4. Cara membaca angka (teach, the extension UI is English): Q1-Q4, SJR, H-index in one sentence each; "panduan, bukan vonis".
5. Di mana jalan: Scopus, ScienceDirect, Scholar (badge) and 20 publishers (edge tab) in an accordion.
6. Privasi: data bundled, no network request, no account; only the tab's position kept locally.
7. Jujur: journals not in Scimago, proceedings, books, preprints show "—"; Scholar matches by title and can miss; yearly snapshot (verify the year before stating it); Chrome only (Edge and Brave are Chromium but untested, do not claim); IEEE may block pages; **not a predatory-journal detector and not an "indexed in Scopus" certificate**, only "listed in Scimago" (Scimago is built on Scopus data); ranks Q by Scimago, not Impact Factor. Not affiliated with Scimago, Elsevier, Google or publishers; Scimago is cited.
8. Pasang in 3 steps. FAQ: Edge/Brave? Firefox? Kenapa tidak ada Impact Factor (it is Clarivate's, different)? Kenapa jurnal saya "—"? Kenapa badge tidak muncul?
9. "Bagian dari ARTi" card: SciPeek is the journal-choosing stage; link to bab 1 gratis and `/fitur`. No price.

## `/artipdf` outline
1. Hero. H1 candidate: "**Gabung, pisah, kompres, dan ubah PDF tanpa mengunggahnya ke mana pun.**" CTA "Unduh untuk Windows" with version and size (v1.0.7 once released; the new installer is 62.9 MB). Trust line: Gratis · Offline · Tanpa akun · Windows.
2. Kenapa offline: unpublished manuscripts and data should not go to an online PDF site. No invented incident. The crowded "tanpa aplikasi / online" videos are the foil; the offline desktop angle is the differentiator.
3. Fitur by tab with the three existing screenshots (editor, merge, convert); gabung, pisah, kompres, atur halaman; klik-kanan Explorer "Convert to Markdown" and "Compress PDF" (v1.0.7).
4. Untuk riset: PDF jurnal jadi Markdown yang bisa dibaca Claude. Soft bridge to ARTi, no price.
5. Jujur: PDF to Markdown is text only (tables and equations lost, margin watermarks can add noise); OCR is English only; MD to PDF needs Edge or Chrome; no percentage claim for compress; installer unsigned.
6. Instalasi: download, then the SmartScreen steps ("Info lebih lanjut" then "Tetap jalankan") in a calm, honest tone.
7. FAQ: aman? macOS (belum)? beda dengan `arti-pdf` di `/fitur` (that one is internal Markdown to A4 PDF)? Do not mention licence or open source until LICENSE is pushed.

## Prerequisites before building `/artipdf` (met 2026-10-15)
Researcher: release v1.0.7 (installer is built), push LICENSE, THIRD-PARTY-NOTICES and screenshots, delete `C:/arti-demo`. Then check the page against the released asset (name, size, features). The Windows-only decision stands.

## `/scipeek` built (2026-10-15, uncommitted)
- `site/scipeek.blade.php` (route `scipeek`, in the sitemap, `#coba-gratis` SciPeek button now goes to it; the install CTA is inside the page). Follows the outline above: hero, problem, video plus real screenshots per site, how to read the numbers, where it works, privacy, "Jujur" limits, 3 install steps, FAQ, neutral "Bagian dari ARTi" card (bab 1 gratis, /fitur; no price, no Beli, no `data-kontak`).
- Media in `public/`: `videos/landing/scipeek-tutorial.mp4` (3.8 MB, lazy), `images/landing/scipeek-poster.webp` (frame at 14 s), real captures `scipeek-scopus-asli`, `-sciencedirect`, `-scholar`, `-tab` (webp, from the extension's `screenshots/`). CSS: `.q`, `.q-legend`, `.figure--small`, `.steps-list`, `.tool-group__body`.
- Tests: `SciPeekPageTest` (CTA, limits, no price or buy, media files exist, sitemap and home link) and `/scipeek` added to `PublicPageQualityTest`; suite green. Checked headless at 375 and 1100 px, no horizontal overflow.
- Not verified: the video playing in a real browser, the live Web Store listing, the snapshot year (so the page states none), and "about 32 thousand journals" comes from the bundled `journals.sqlite` row count (32,193).

## `/artipdf` built (2026-10-15, uncommitted)
- Release verified before building: GitHub release v1.0.7 (2026-10-15) has one asset, `ArtiPDFSetup-1.0.7.exe` (62.9 MB), no macOS dmg; the repo now shows the MIT licence and LICENSE and screenshots are on `main`. So the page may say MIT and Windows-only.
- `site/artipdf.blade.php` (route `artipdf`, in the sitemap; the `#coba-gratis` ARTi PDF button now goes to it, the download CTA is inside). Sections: hero, why offline, features with 3 real screenshots (editor, merge, convert), the two right-click commands (Convert to Markdown, Compress PDF), for-research bridge with a "Jujur" note, limits (text only, English-only OCR, Edge/Chrome for MD to PDF, Windows only, unsigned installer), 4 install steps with the SmartScreen walkthrough, FAQ, neutral "Bagian dari ARTi" card. No price, no Beli.
- **Left out on purpose:** the "Summarize with AI" right-click (it needs the ARTi dashboard; the public installer still registers it, an open todo), any macOS or Mac wording (`PublicPageQualityTest` bans it), any compression percentage, "open source" beyond MIT plus the third-party notices line.
- Version and size come from `config/site.php` (`artipdf_version`, `artipdf_size`); update them with each release, the download link is `releases/latest`.
- Tests: `ArtiPdfPageTest`, `/artipdf` in `PublicPageQualityTest`, the landing test now checks the internal link; suite green (144). Layout checked headless at 375 and 1100 px, no horizontal overflow; images were not visible in that run only because the throwaway `php -S` router did not serve static files (files exist and a test asserts it), so look at the page once in the real dev site.

## Change log
- 2026-10-15 11:30 — created from the concept discussion, decisions 1-3 and the keyword audit.
- 2026-10-15 12:10 — `/scipeek` built and tested.
- 2026-10-15 12:50 — `/artipdf` built and tested after the v1.0.7 release.
