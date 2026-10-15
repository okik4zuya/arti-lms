---
name: landing-page-concept
description: Agreed concept and build record for the ARTi landing page (positioning, copy formula, outline, offer and marketing rules, media placed: explainer video, dashboard and e-course screenshots, open decisions)
metadata:
  type: project
  created: 2026-10-14 22:11
  updated: 2026-10-15 09:28
---

Concept from the 2026-10-14 brainstorm. Facts and decisions it rests on: [[landing-page-info]]. Built and committed (see Build status and the finalisation section below). Page language: Indonesian, voice "saya/Anda", short sentences, honest "Jujur: ..." passages (see [[content-authoring-workflow]]).

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
- **Use:** real scarcity (live quota), one price step (99rb to 199rb after 100 buyers), crossed-out Rp199.000, "Rp16.600/bulan" comparison, real bonuses only (chapter updates during the subscription; **not SciPeek**, its Scimago data is non-commercial, see [[landing-scipeek-artipdf]]), total-cost transparency (Claude plan is extra), risk reversal if a refund policy is decided.
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
- Still missing: real hero screenshot, the real demo recording and poster, an author photo/"Tentang penulis" section (suggested, not decided). Icons, the thread illustration (was the Rithah image), the e-course and dashboard screenshots and the explainer are done (see below).

## One 600px column (2026-10-14, researcher's decision)
- Every landing section sits in a 600px reading column (`.landing .container` = 632px incl. side padding); grids are single-column; header and footer keep the site-wide 960px. The step diagram was re-rendered at 1200x640 with names only (descriptions live in the panels below) so text stays readable at 600px; it is hidden under 480px wide. Compare tables still fit (the 5-column one scrolls horizontally only if needed).

## Existing media now placed (2026-10-14)
- WebP copies in `public/images/landing/`: `benang-terurai.webp` (33 KB, 1200x560, agitation figure, replaced the Rithah clip-art/engraving image on 2026-10-15: same robot as `model-skill-alat`, three panels Hari 1/2/3 where the blue ball of thread and work cards of day 1 are a dashed ghost with loose grey thread by day 2-3, "sesi berakhir" pills between; caption "Tiap sesi baru, kerja kemarin terurai lagi. Ilustrasi, bukan data."; source `resources/media-src/landing/benang-terurai.html`, PNG via `stills.mjs --size 1200x560`, WebP via vendored PIL q88; the Rithah story stays in the paragraph text), `scipeek-scopus.webp` (56 KB, from the Web Store screenshot 1, English UI, in the SciPeek card), `youtube-channel.webp` (10 KB, the channel banner, in the YouTube card). Sources: ebook ch. 4 `pemintal-benang.png`, extension `store-assets/screenshot-1.png`, `YT Dosen Berarti/assets/branding/banner.png`. Not placed on purpose: the Facebook ad (an ad creative) and the chain PNG (the page uses the HTML version). The SciPeek screenshot is a composed image of real Scopus crops.

## Added 2026-10-14 (late)
- **Icons:** Blade component `<x-glyph name="...">` (`resources/views/components/glyph.blade.php`; `x-icon` clashes with Filament's blade-icons) with 11 own line icons, used on pain cards, the three foundations, the three steps and the package cards.
- **Example cards** ("Seperti apa wujudnya?"): the real `memory/` folder tree of a paper project (topic files `journal-target.md`, `introduction-plan.md`, `data-gaps.md` from the CO2 Photoreduction project; changed 2026-10-15 from this repo's own dev topics to paper-relevant ones), and a scratchbook sample whose content is invented and labelled "contoh format".
- **Free chapter 1:** public `/baca` (redirects to the first subchapter), `/baca/{slug}`, `/baca/{slug}/images/{file}` via `FreeChapterController`; the free set is the first chapter of the first manifest section (4 subchapters), other slugs 404, wiki-links (double square brackets) to locked pages become plain text, the last page shows the buy CTA, pages are in the sitemap. The landing "Baca bab 1 gratis" button points to it (`SITE_FREE_CHAPTER_URL` can still override).
- **Ads:** `ad-landscape` 1200x628 and `ad-story` 1080x1920 rendered next to the square one (story: keep the bottom ~250 px clear of Facebook UI; the staircase sits low and may need lifting).
- **E-course screenshot:** real capture of the "Sistem Memori" page logged in as the dev learner (`test@example.com`, which already had access to `arti-framework`; the seeder was not run), WebP 76 KB, in the e-course card.- 40 s explainer: approved, rendered and placed in "Lihat caranya bekerja"; see [[landing-explainer-storyboard]].
- Removed the landing "Tulisan terbaru" block earlier, so `ArticlesTest` no longer expects an empty-state message on `/`.

## Added 2026-10-15 (explainer + dashboard)
- **Explainer video:** `public/videos/landing/arti-explainer.mp4` (2.6 MB) with poster `images/landing/explainer-poster.webp`, a `<video controls preload="none">` above the real-demo slot, which stays as is. Note under it says it is a concept illustration.
- **Dashboard screenshot:** `images/landing/dashboard.webp` (63 KB) in the licence card of "Isi paket". It is the researcher's real SnO2 SLR project (Workspace with Files, References and manuscript panes). The manuscript pane (text and chart) is pixelated and greyed out because it is unpublished, and the caption says so. Project name, file counts and the references pane (published papers only) are left visible. Fine AI does not appear in it, so no claim about it was made. The unredacted original `arti-dashboard.png` in the repo root is gitignored but should be moved or deleted by the researcher.
- Tests: 75 pass plus one test each for the video and the dashboard image (13 landing tests green after the last change).

## Model + skill + tool analogy (2026-10-15, researcher's concept)
- **Concept:** an AI model with basic knowledge (cloud over its head) is given a skill (a sheet) and a tool (a cangkul), like teaching a person a craft and handing them a hoe. Shown in the landing's Solution section (4), before the three foundations; the three foundations then read as what keeps the work correct and not forgotten. Deeper version belongs on `/fitur` (the sheet unfolds into the 10 skills, the hoe into the tool groups).
- **Placed:** variant 1 (single scene) in section 4: `images/landing/model-skill-alat.webp`, labels (Pengetahuan dasar, Skill, Alat) as an HTML `.legend` under the image, never inside it, so they stay readable at 375px. Verified at 1100px and 375px, no horizontal scroll; `LandingPageTest` has a test for it.
- **Built, not placed:** variant 2 "before" panel `images/landing/model-saja.webp` (puzzled model, empty hands, question marks, withered sprouts; pair it with variant 1 as the "after" if a before/after is wanted) and variant 3 `videos/landing/model-skill-alat.mp4` (10 s silent loop, 306 KB: puzzled, then skill arrives, then hoe, then sprouts grow; poster = variant 1). Both meant for `/fitur` or a later swap.
- **Sources:** `resources/media-src/landing/model-skill-alat.html`, `model-saja.html`, `model-skill-alat-anim.html` (timeline page, 1200x800); render stills with `stills.mjs`, video with `render.mjs --width 1200 --height 800 --fps 30 --duration 10`; WebP via the vendored Python PIL.
- **Design choices:** slate figure with a blue brand dot, blue only for what ARTi adds (skill header, hoe blade); skill on the viewer's left, tool on the right; no Anthropic/Claude logo (the "not affiliated" line). Memory is deliberately not in the picture; the foundations text covers it.
- **Memory pair (2026-10-15, researcher's decision):** the cloud means basic knowledge, never memory (memory is external files; a cloud-as-memory would imply ARTi fixes the model's head). Memory gets its own image, no skill or tool: one compact 1200x520 image `images/landing/memori-duo.webp`, left half "tanpa" (chat bubble with an empty reply, last session's bubbles fading, reset arrow, puzzled), right half "dengan" (holds a folder, past context flows into the chat, reply filled and ticked, sprouts = work that accumulates). The first version was two separate 1200x800 images stacked; the researcher asked for one side-by-side image. Source `resources/media-src/landing/memori-duo.html` (generated by a throwaway script, edit the HTML directly; canvas colour matches `--color-bg` so rounded corners blend). **Placed** in section 4b ("Seperti apa wujudnya?"), above the memory-folder example, in a `.pair` with two HTML captions ("Tanpa memori" / "Dengan memori") side by side under it, then the line "Map itu, wujud aslinya seperti ini:". Checked at 1100px and 375px, no horizontal scroll; `LandingPageTest` covers it (15 pass).
- **Tool list for `/fitur`** (decided to show all real tools, grouped by task, hide stubs): see the grouping proposed 2026-10-15 (literature, writing, figures, project and memory, connector); leave out `arti-table` (stub), `scopus-ris-batch-export` (no code), `scite` (unused), `arti-license`, `_shared`.

## `/fitur` built (2026-10-15)
- `site/fitur.blade.php`, same 600px landing layout as `/`: hero with in-page nav; analogy block with the looping `model-skill-alat.mp4` (autoplay unless prefers-reduced-motion, poster = the still) and the Pengetahuan dasar / Skill / Alat legend; three foundations as Masalah / Cara kerja / Hasil; 10 skills grouped by stage (Persiapan dan ide, Replikasi dan menulis, Tinjauan sistematis, Rujukan dan revisi), each an accordion with an example command and its result; all real tools in grouped accordions plus the OpenAlex connector labelled "didukung, bukan bagian paket"; dashboard screenshot; "Yang tidak dilakukan ARTi"; honest e-course status; closing CTA.
- **Example commands are Indonesian paraphrases of the real prompts in `~/.arti/prompt-templates.md`** (that file is English), with a note saying so. Not hidden anywhere: if a command's wording is changed, keep it faithful to what the skill actually does.
- **Hidden on purpose:** `arti-table` (stub), `scopus-ris-batch-export` (brief, no code), `scite` (unused), `arti-license`, `_shared`. `FeaturePageTest` asserts these never appear and that all 10 skills and 13 tools do.
- Home 4b section got `id="wujud"`, linked from `/fitur`. CSS added: `.page-nav`, `.facts`, `.skills` accordion chevron, `.prompt`, `.tool-group`, `.tools`. Verified at 1100px and 375px, no horizontal scroll. Full suite: no failures (deprecation notices are the old PDO constant).
- **Not verified:** the video actually autoplaying in a real browser (headless showed a frame; reduced-motion guard untested), and `/fitur` at desktop beyond the top and layout checks.

## Top menu simplified (2026-10-15, researcher's decision)
- Menu is now **Fitur, Harga, Baca bab 1 gratis, Kontak** (`layouts/partials/site-links.blade.php`). "Harga" links to `/#harga` (the landing's offer section). The `/harga` page and its view are deleted (its content, how to buy, guarantee, licence and expiry notes, was already on the landing); `/harga` 301-redirects to `/#harga`. The `/unduh` page is deleted (tools and downloads live in the LMS) and 301-redirects to `/`. Both dropped from the sitemap.
- "Artikel" left the menu; articles come in a later phase. The `/artikel` routes, feed, and sitemap entry are still there (empty list shows "Belum ada artikel."), and the footer still has "Umpan artikel". Open choice: hide those too until the first article exists.
- **Anchor offset:** the sticky header hid headings on in-page jumps (`/#harga`, `/fitur#skill`), so `html { scroll-padding-top: calc(var(--topbar-height) + var(--space-2)) }` in `site.css` applies to every anchor site-wide. Checked at 1100px wide with 600 and 900px tall viewports (section lands 64px from the top, just under the 57px header) and at 375px. A 380px-tall headless viewport overshot by 82px; treated as a headless artefact, not re-checked in a real browser.
- Other links to `/harga` (fitur closing, free-chapter last page) now point to `/#harga`; privacy text lists "beranda, fitur, bab gratis". Tests: redirect test, menu test; suite passes.

## ARTi PDF card in "Kenalan dulu" (2026-10-15)
- Card in `#coba-gratis` (order, set 2026-10-15: Baca bab 1, Tonton di YouTube, SciPeek, ARTi PDF): "Pakai ARTi PDF" with the Page Editor screenshot (`images/landing/artipdf-editor.webp`, 36 KB, from `C:/python_tools/artipdf/assets/screenshots/`), button "Unduh untuk Windows" to `config('site.artipdf_url')` = `https://github.com/okik4zuya/artipdf/releases/latest`. **Windows only** by the researcher's decision (macOS is not mentioned; the release page itself still lists a macOS dmg). Copy is honest: free, offline, no upload, no account; PDF to Markdown only extracts text (tables and equations not kept); installer unsigned so Windows may warn. It does not claim open source or a licence, because the MIT `LICENSE` in the artipdf repo is not pushed yet ([[artipdf-landing-facts]]).
- The card's screenshot is of v1.0.7 while the public latest release is v1.0.6 (same tabs; 1.0.7 adds the Compress right-click). `/fitur` now says the internal `arti-pdf` tool differs from the ARTi PDF app. `LandingPageTest` covers the card (no "macOS" text on the landing).

## Finalisation pass (2026-10-15)
- Committed the whole backlog in 6 groups, then fixed: chapters imported Google Fonts (self-hosted Atkinson Hyperlegible now, import stripped in `MarkdownRenderer`); Mac mention removed from the requirements list; "kami" replaced by "ARTi"; unique per-chapter descriptions; login page and all error pages in Indonesian (`resources/views/errors/`, standalone layout, no @auth); skip link, focus ring, reduced motion, muted-text contrast 4.46 to 5.4:1; empty `/artikel` is noindex and out of the sitemap; `/privasi` and `/syarat` restate the real offer and data flows ([[legal-review-notes]]). `PublicPageQualityTest` guards one h1, unique title/description, canonical, lang, skip link, no macOS, no third-party font.
- Checked at 375, 768 and 1100 px (puppeteer + Edge, nothing overflows horizontally, skip link works). Only emulated: autoplay on `/fitur`, anchors, a real phone.

## Next steps
Record the real demo and its poster; real hero screenshot; decide the author section; check the new sections at true 375px; fill the prod `.env` values. Checklist in `todo-list.md`.

## Change log
- 2026-10-15 10:37 — Rithah image replaced by `benang-terurai.webp` (concept A, three panels); `rithah.webp` deleted (still in git history).
- 2026-10-15 09:28 — finalisation pass recorded.
- 2026-10-15 08:10 — ARTi PDF card added to the free section (Windows only).
- 2026-10-15 06:44 — model + skill + tool illustration (3 variants) built, variant 1 placed in Solution; copy of section 4 changed (see [[landing-page-copy]]).
- 2026-10-14 23:58 — explainer video and dashboard screenshot placed; stale "nothing built" and next-steps text refreshed.
- 2026-10-14 23:30 — landing, /harga and /fitur built in Blade with Offer config and tests; build status recorded.
- 2026-10-14 22:50 — guarantee decided: 7 days from purchase date.
- 2026-10-14 22:40 — recorded renewal rule, one-batch quota, CTA/free chapter/demo decisions; added guarantee proposal.
- 2026-10-14 22:25 — added Pain language section, comparison block, and AI-detector FAQ from [[landing-page-keywords]].
- 2026-10-14 22:11 — created from the brainstorm (positioning, PAS formula, outline, tagline and skills placement, marketing rules).

## Top menu update (2026-10-15, researcher's decision)
Menu is now **Fitur, Harga, Baca bab 1 gratis, ARTi PDF, ARTi SciPeek** (`layouts/partials/site-links.blade.php`). "Kontak" left the top menu; it stays in the footer, the floating WhatsApp button and every "Beli" button. The hamburger breakpoint moved from 760px to 900px because five links wrapped to two lines at 768px. Test: `PublicSiteTest::test_top_menu_has_fitur_harga_free_chapter_and_the_two_free_tools`.
- Later the same day: **Beranda** added as first item and **Baca bab 1 gratis** removed from the top menu (the free chapter stays reachable from the landing hero, `#coba-gratis` and the closing cards of `/scipeek` and `/artipdf`). Menu: Beranda, Fitur, Harga, ARTi PDF, ARTi SciPeek.
