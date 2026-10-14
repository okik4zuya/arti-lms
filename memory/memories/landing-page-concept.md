---
name: landing-page-concept
description: Agreed concept and build record for the ARTi landing page (positioning, copy formula, outline, offer and marketing rules, media placed: explainer video, dashboard and e-course screenshots, open decisions)
metadata:
  type: project
  created: 2026-10-14 22:11
  updated: 2026-10-14 23:58
---

Concept from the 2026-10-14 brainstorm. Facts and decisions it rests on: [[landing-page-info]]. Not built; current `site/home.blade.php` is still the phase-1 skeleton ([[arti-app-architecture]]). Page language: Indonesian, voice "saya/Anda", short sentences, honest "Jujur: ..." passages (see [[content-authoring-workflow]]).

## Positioning
- Product: e-course (LMS) + ARTi Framework license, one package. Audience: dosen first, then S2/S3.
- Sub-headline claim: **"framework riset berbahasa Indonesia untuk Claude Code, dari ide sampai submit"**. Do **not** claim "pertama" (unverifiable, contradicts the "no fabrication" brand) until a web/GitHub check is done; if kept, only as "pertama yang kami ketahui". The researcher's idea of "first AI research framework on Claude Code" was reviewed and deferred, not accepted.
- "Claude Code" is the engine (line 2 and SEO title/description), not the hero headline; the hero leads with the outcome.
- Tagline **"Dari ide sampai publish, bersama ARTi"** (channel tagline) stays verbatim as a brand line in the logo lockup/footer and before the final CTA, **not** as the main claim: "publish" is an outcome ARTi cannot promise. Product claims say "submit". One honest line near how-it-works: ARTi helps the process, acceptance is the journal's decision. "Publish" may rise to the hero only once real acceptances exist.

## Copy formula
- **PAS** as the spine (problem: halusinasi + hilang memori; agitate with one concrete incident, not threats; solution: three foundations). **BAB** for how-it-works, **FAB** for package contents. AIDA rejected (the pain is already felt).

## Pain language (from [[landing-page-keywords]], 2026-10-14)
- The pain words "halusinasi", "referensi palsu", "lupa konteks" are almost never searched, so the page **teaches** the problem through story (Alzheimer, Rithah, one concrete fake-citation incident) and explains "halusinasi" in one sentence.
- Use everyday words in hero and agitate: "ngawur", "lupa", "mulai dari nol". Phrase benefits as the positive terms people search: "referensi asli dan bisa dilacak", "ide dan novelty yang diukur", "revisi dan balasan reviewer yang rapi".
- Four pains in the Problem-Agitate section: (1) ngawur/referensi tidak asli and the credibility risk, (2) lupa antar sesi, (3) takut terdeteksi/ditolak (AI detector, desk rejection), (4) alur riset terpecah di banyak alat (still a hypothesis; validate with 5-10 dosen).
- Do not lead with a speed promise ("jurnal Scopus dalam 1 jam"); if used, pair with "tanpa mengarang". Never promise to beat AI detectors.

## Section outline (v2, 2026-10-14; copy draft in [[landing-page-copy]])
0. Top bar: logo, links (Fitur, Harga, Bab 1 gratis), WhatsApp button.
1. Hero: outcome headline, Claude Code sub-headline, CTA WhatsApp + "Baca bab 1 gratis", one trust line (garansi 7 hari, Windows).
2. Problem: three pains in everyday words (ngawur/referensi tidak asli, lupa antar sesi, takut ditolak atau terdeteksi).
3. Agitate: the credibility chain ("dosa jariyah") and the Rithah thread; no invented incidents.
4. Solution: memori per topik, referensi asli dan terlacak, scratchbook.
5. How it works (BAB): Amati, Replikasi, Tambah Inovasi; honest line that acceptance is the journal's decision; tagline here.
6. Demo: 1-2 minute recording of real ARTi use.
7. Package (FAB): e-course + license; skills grouped by stage; ARTi-SLR block; "di balik layar" line; link to `/fitur`.
8. Comparison: ARTi vs Claude Code polos, Claude AI, NotebookLM, other chat LLMs (from ebook ch. 3 subchapter 17), plus the honest trade-off.
9. Free entry: chapter 1 free and SciPeek (live on the Web Store).
10. Honest requirements: Claude plan, Claude Code, VS Code, Windows only, internet to install, ebook progress.
11. Offer: Rp99.000 perintis (first 100 buyers, live quota), normal Rp199.000/year, renewal rule, garansi 7 hari, why the price is low now.
12. FAQ: coding needed, AI detector and ethics, data safety, Claude changes, after one year, total cost, refund.
13. Closing CTA with tagline and "tidak berafiliasi dengan Anthropic". Footer: privasi, syarat, kontak.

## Skills and tools placement
- Three layers. Page: skills grouped by stage inside "cara kerja", benefit language, skill name as a small label. Tools almost invisible: one "di balik layar" line (PDF to Markdown, Word/PDF export, literature search via OpenAlex/Semantic Scholar/Scholar, local dashboard, runs locally). Full list of 10 skills and tools only on `/fitur`, linked from the page. Say "10 skill" only after the README's "nine skills" is fixed.

## Marketing rules (honest persuasion only)
- **Use:** real scarcity (live quota), one price step (99rb to 199rb after 100 buyers), crossed-out Rp199.000, "Rp16.600/bulan" comparison, real bonuses only (SciPeek, chapter updates during the subscription), total-cost transparency (Claude plan is extra), risk reversal if a refund policy is decided.
- **Avoid:** countdown timers or deadlines (quota is the only limiter), invented "value" prices for the course or bonuses, fake-urgency wording, selling unwritten BONUS chapters (Toolkit, Panduan Instalasi, Prompt Guide) as bonuses.

## Decided 2026-10-14 (researcher)
- **Renewal** follows the normal price (Rp199.000) unless a promo is running; the page states this.
- **Quota:** the 100-buyer Rp99.000 price is **one batch only**. Promos at specific events may come later; they must be named differently, carry a real end date, and never be presented as the perintis price again.
- **CTA:** WhatsApp directly. **Chapter 1:** free to read. **Demo:** a 1-2 minute recording of real ARTi use will be made.

- **Guarantee:** 7-day full refund counted from the **purchase date** (researcher's choice, for clear counting), no reason required, requested by WhatsApp; see below.

## Open decisions
- Name "ARTi Workflow" vs "ARTi Framework" outside the ebook; offline grace after activation.

## Guarantee (decided 2026-10-14)
7-day full refund counted from the purchase date, no reason required, requested by WhatsApp. On refund the admin revokes the license (`arti-admin revoke`) and removes LMS access. Separate promise kept: if installation fails because of ARTi's side, refund at any time within the subscription. Needs a line in the terms/EULA (`/syarat` is still an unreviewed draft). Operational notes: "purchase date" should be the payment-confirmation date shown in the WhatsApp chat; send the key promptly so the window is not spent waiting. Limited exposure at Rp99.000.

## Build status (2026-10-14, uncommitted)
- Built in Blade from [[landing-page-copy]]: `site/home.blade.php` (13 sections), `site/harga.blade.php` (one package, no tiers, no trial), `/fitur` gets ARTi-setup (10 skills), layout gained `header_cta` and `main_class` yields, `site.css` landing block.
- Offer logic in `App\Support\Offer` + `config/site.php` (`offer.*`, `free_chapter_url`, `demo_video_embed_url`, `scipeek_url`; env `SITE_LAUNCH_SOLD`, `SITE_CONTACT_WHATSAPP`, `SITE_FREE_CHAPTER_URL`, `SITE_DEMO_VIDEO_EMBED_URL`). Empty values degrade honestly: no WhatsApp number -> buttons go to `/kontak`; no sold count -> no live counter; no chapter/demo URL -> "Segera" badge; sold >= 100 -> normal price page.
- Tests: `OfferTest`, `LandingPageTest`, `PublicSiteTest` updated; 23 pass (PHP 8.5 deprecation notices from the PDO constant are pre-existing).
- Verified: a headless Edge desktop screenshot (1280 wide) renders all sections. **Not verified:** true 375px mobile (headless Edge has a minimum window width; the iframe measurement did not return), FAQ section at the bottom of the desktop shot (cut off), `/harga` and `/fitur` visually.
- Decisions applied: bab baru during the subscription are included; after expiry, skills and files mostly still work via VS Code but the ARTi dashboard will not open (with a warning and grace period); "10 skill" used.
- Still to fill: WhatsApp number, free chapter URL (needs a public route for chapter 1), demo video embed URL, real sold count. Chapter 1 free access is not built (LMS is login-gated).

## Mobile header (2026-10-14, done)
- Sticky 56px bar: logo, short "Beli" CTA (full label from 560px up), hamburger (`<details>`, no JS) opening a full-width panel with 48px links and "Masuk" as a quiet last row; desktop keeps inline links. Links live in `layouts/partials/site-links.blade.php`. Verified in true 375px and 320px iframes (closed and open) and a 1280px screenshot; note headless Edge's own window is wider than 375, so measure mobile through an iframe. Optional later: a bottom sticky buy bar on the landing, deferred until there is click data.
- Added a third "Tonton di YouTube" card to `#coba-gratis` (`site.youtube_url`).

## Media made with html2media (2026-10-14)
- Sources in `resources/media-src/landing/*.html` (Figtree from fonts.bunny.net, so re-rendering needs internet); renders in `resources/media-src/landing/out/`. Render: `node ~/.claude/skills/html2media/scripts/stills.mjs --html "og-image.html@1200x630,diagram-tangga.html@1600x900,diagram-rantai-sitasi.html@1600x640,ad-square.html@1080x1080" --out out`, then copy the site ones to `public/images/landing/`.
- On the page: `og-image.png` (meta og:image + twitter large card in `layouts/site`), `diagram-tangga.png` in "Cara kerja" (desktop only; text is unreadable when scaled to phone width), and the citation chain as a responsive HTML component (`.chain`, a vertical stepper with arrows at every width, max 600px, and the agitation section uses `.container--600` so text and figure align; labelled "Ilustrasi ... bukan data"). `diagram-rantai-sitasi.png` and `ad-square.png` (Facebook ad, headline "AI-nya ngawur dan lupa terus?", no price) are spare assets, not on the page.
- Still missing: real hero screenshot, the real demo recording and poster, an author photo/"Tentang penulis" section (suggested, not decided). Icons, the Rithah image, the e-course and dashboard screenshots and the explainer are done (see below).

## One 600px column (2026-10-14, researcher's decision)
- Every landing section sits in a 600px reading column (`.landing .container` = 632px incl. side padding); grids are single-column; header and footer keep the site-wide 960px. The step diagram was re-rendered at 1200x640 with names only (descriptions live in the panels below) so text stays readable at 600px; it is hidden under 480px wide. Compare tables still fit (the 5-column one scrolls horizontally only if needed).

## Existing media now placed (2026-10-14)
- WebP copies in `public/images/landing/`: `rithah.webp` (33 KB, agitation figure with caption), `scipeek-scopus.webp` (56 KB, from the Web Store screenshot 1, English UI, in the SciPeek card), `youtube-channel.webp` (10 KB, the channel banner, in the YouTube card). Sources: ebook ch. 4 `pemintal-benang.png`, extension `store-assets/screenshot-1.png`, `YT Dosen Berarti/assets/branding/banner.png`. Not placed on purpose: the Facebook ad (an ad creative) and the chain PNG (the page uses the HTML version). The SciPeek screenshot is a composed image of real Scopus crops.

## Added 2026-10-14 (late)
- **Icons:** Blade component `<x-glyph name="...">` (`resources/views/components/glyph.blade.php`; `x-icon` clashes with Filament's blade-icons) with 11 own line icons, used on pain cards, the three foundations, the three steps and the package cards.
- **Example cards** ("Seperti apa wujudnya?"): the real `memory/` folder tree of this project, and a scratchbook sample whose content is invented and labelled "contoh format".
- **Free chapter 1:** public `/baca` (redirects to the first subchapter), `/baca/{slug}`, `/baca/{slug}/images/{file}` via `FreeChapterController`; the free set is the first chapter of the first manifest section (4 subchapters), other slugs 404, wiki-links (double square brackets) to locked pages become plain text, the last page shows the buy CTA, pages are in the sitemap. The landing "Baca bab 1 gratis" button points to it (`SITE_FREE_CHAPTER_URL` can still override).
- **Ads:** `ad-landscape` 1200x628 and `ad-story` 1080x1920 rendered next to the square one (story: keep the bottom ~250 px clear of Facebook UI; the staircase sits low and may need lifting).
- **E-course screenshot:** real capture of the "Sistem Memori" page logged in as the dev learner (`test@example.com`, which already had access to `arti-framework`; the seeder was not run), WebP 76 KB, in the e-course card.- 40 s explainer: approved, rendered and placed in "Lihat caranya bekerja"; see [[landing-explainer-storyboard]].
- Removed the landing "Tulisan terbaru" block earlier, so `ArticlesTest` no longer expects an empty-state message on `/`.

## Added 2026-10-15 (explainer + dashboard)
- **Explainer video:** `public/videos/landing/arti-explainer.mp4` (2.6 MB) with poster `images/landing/explainer-poster.webp`, a `<video controls preload="none">` above the real-demo slot, which stays as is. Note under it says it is a concept illustration.
- **Dashboard screenshot:** `images/landing/dashboard.webp` (63 KB) in the licence card of "Isi paket". It is the researcher's real SnO2 SLR project (Workspace with Files, References and manuscript panes). The manuscript pane (text and chart) is pixelated and greyed out because it is unpublished, and the caption says so. Project name, file counts and the references pane (published papers only) are left visible. Fine AI does not appear in it, so no claim about it was made. The unredacted original `arti-dashboard.png` in the repo root is gitignored but should be moved or deleted by the researcher.
- Tests: 75 pass plus one test each for the video and the dashboard image (13 landing tests green after the last change).

## Next steps
Record the real demo and its poster; real hero screenshot; decide the author section; check the new sections at true 375px; fill the prod `.env` values. Checklist in `todo-list.md`.

## Change log
- 2026-10-14 23:58 — explainer video and dashboard screenshot placed; stale "nothing built" and next-steps text refreshed.
- 2026-10-14 23:30 — landing, /harga and /fitur built in Blade with Offer config and tests; build status recorded.
- 2026-10-14 22:50 — guarantee decided: 7 days from purchase date.
- 2026-10-14 22:40 — recorded renewal rule, one-batch quota, CTA/free chapter/demo decisions; added guarantee proposal.
- 2026-10-14 22:25 — added Pain language section, comparison block, and AI-detector FAQ from [[landing-page-keywords]].
- 2026-10-14 22:11 — created from the brainstorm (positioning, PAS formula, outline, tagline and skills placement, marketing rules).
