---
name: todo-list
description: Checklist of open tasks for arti-lms — no narrative, checkboxes only
metadata:
  type: project
  tier: T0
  created: 2026-09-08 21:36
  updated: 2026-10-15 09:30
---

### Landing page (arti.invait.id), see [[landing-page-concept]]

- [ ] Listen to the explainer's music and watch it through once (only levels and 8 frames were checked) [[landing-explainer-storyboard]]
- [ ] Move or delete the unredacted `arti-dashboard.png` in the repo root (gitignored, but it shows the unpublished manuscript)
- [ ] Decide whether the dashboard screenshot should also hide the project name and file counts
- [ ] Media: record the real 1-2 min demo, make its poster, set `SITE_DEMO_VIDEO_EMBED_URL`
- [ ] Media: real hero screenshot (VS Code + Claude Code)
- [ ] Media: author photo / "Tentang penulis" section (undecided)
- [ ] Media: lift the staircase in `ad-story` (bottom ~250 px is Facebook UI); optimize the Rithah image use if reused elsewhere
- [ ] Check at 375px the sections not yet checked (free chapter pages `/baca`, explainer video, dashboard image, icons); the solution, memory pair, ARTi PDF card and `/fitur` were checked
- [x] Rewrite `/fitur` per the agreed concept (stages, all tools grouped, honest "what ARTi does not do"); animated `model-skill-alat.mp4` placed there [[landing-page-concept]]
- [ ] Review the Indonesian example commands and the new copy on `/fitur`, and confirm the autoplay loop in a real browser
- [x] Top menu: Fitur, Harga (to `/#harga`), Baca bab 1 gratis, Kontak; `/harga` and `/unduh` removed with 301 redirects
- [x] ARTi PDF card in the free section (Windows only), screenshot and MIT licence prepared in the artipdf repo
- [ ] In the artipdf repo: commit and push `LICENSE`, `THIRD-PARTY-NOTICES.md`, screenshots and readme; release v1.0.7 so the landing's screenshot matches the download; delete `C:/arti-demo`
- [x] Page speed pass (self-hosted fonts, small logo and favicon, content-visibility with anchor safeguard, caching rules) [[page-speed]]
- [ ] Confirm gzip/brotli and cache headers on the real production host (local Apache lacks the modules), and test on a real phone
- [x] Redis wired for local dev: cache, session, queue (Predis, DBs 10/11) [[redis-setup]]
- [ ] Redis for production (own instance or keep database drivers), and check `/jamrud` and LMS login under Redis sessions
- [ ] `composer audit`: 4 advisories on laravel/framework 11.x; plan an upgrade
- [ ] Decide whether to hide `/artikel`, its sitemap entry and the footer "Umpan artikel" until the first article exists
- [x] Model + skill + tool illustration: 3 variants built, variant 1 placed in the Solution section with HTML legend and test
- [ ] Fill `SITE_CONTACT_WHATSAPP`, `SITE_LAUNCH_SOLD`, `APP_DOMAIN_MAIN=arti.invait.id` in the prod `.env`
- [ ] Remove or disable the unused trial path (`/trial`, `arti-license trial`, `trial` tier) in the licensing Worker before deploy (see `~/.arti` licensing plan)
- [ ] Legal review of `/syarat` and `/privasi` including the 7-day refund line; fix README "nine skills" to ten
- [ ] Revise ebook chapter 3 wording ("not a product, not a tool to install") to match the paid-bundle story
- [ ] Decide: name "ARTi Workflow" vs "ARTi Framework" outside the ebook; offline grace after activation
- [x] Landing, `/harga`, `/fitur`, mobile header, free chapter 1 (`/baca`), icons, example cards, OG image, 600px column, tests
- [x] 40 s explainer (storyboard approved, rendered, lazy `<video>` in the demo section) and the dashboard screenshot with the manuscript pane pixelated, in the licence card

### Landing finalisation (next session), prompt in [[landing-finalisation-prompt]]

- [x] Commit the large uncommitted set in logical groups (landing and /fitur, media, menu and redirects, speed, Redis and Predis, tests, memory); nothing from the last sessions is committed
- [x] Audit every landing-related page for copy, consistency and facts: `/`, `/fitur`, `/kontak`, `/privasi`, `/syarat`, `/baca` pages, `/artikel`, header, footer, 404
- [x] Per-page SEO: unique title and description, canonical, OG image, sitemap and robots, `lang`; then accessibility and keyboard checks
- [x] Sync `landing-page-copy` to the final Blade copy, and retire or update stale notes in `landing-page-info`
- [ ] Real-browser checks that were only emulated: autoplay loop on `/fitur`, anchors, phone widths, speed on a real phone (375, 768, 1100 px were checked with puppeteer)
- [ ] Legal review of `/syarat` and `/privasi` using [[legal-review-notes]]; confirm the licensing Worker sends only the key and a hashed machine code
- [ ] Decide whether to hide `/artikel` and the footer feed link (recommended: hide until the first article; empty index is already noindex)
- [ ] Decide the app name: the learner app still says "ARTi LMS", the site "ARTi Framework"
- [ ] artipdf repo (not touched): push LICENSE, notices and screenshots, release v1.0.7, delete `C:/arti-demo`

### Content authoring progress (arti-framework, 18 chapters) — dedicated, most-active section

Rules for drafting: see [[content-authoring-workflow]]. Narrative/status detail (why a chapter is
blocked, style decisions made while drafting it, image notes): see [[content-authoring-progress]].
One line per chapter here; sub-bullets are the subchapters actually in `manifest.yaml` for that
chapter (not the full imagined scope).

#### Pendahuluan
- [x] 1. Kenapa AI Belum Memuaskan (Halusinasi & Memori)
  - [x] 2 Masalah: Halusinasi dan Memori (folder renamed `01-2-masalah-utama`, matches manifest)
  - [x] Solusinya (folder renamed `02-solusi-parsial`, matches manifest)
  - [x] Halusinasi = Meruntuhkan Kredibilitas Akademik (`03-halusinasi-meruntuhkan-kredibilitas-akademik`, new, inserted 2026-09-11)
  - [x] Hilang Memori: Pekerjaan jadi Tak Berarti (folder `04-hilang-memori-tak-berarti`, renumbered from `03-`; pemintal-benang parable from `resources/inbox/pemintal-benang.md`, image is the user's own)
- [ ] 2. Claude dan Claude Code (source outline: Ch.2 in `abstraksi-konten-dan-outline.md`, several `##` stubs still empty there)
  - [x] Taaruf (`05-taaruf`, renumbered from `04-`)
  - [x] Claude: Model dengan Branding Logika (`06-claude-model-dengan-branding-logika`, renumbered from `05-`; includes research-model benchmark table)
  - [x] Masih Ada yang Kurang (`07-masih-ada-yang-kurang`, renumbered from `06-`)
  - [x] Claude Code (`08-claude-code`, renumbered from `07-`; includes Claude Code vs Claude AI vs NotebookLM vs LLM-chat-lain comparison table)
  - [ ] Claude bisa pakai skill agar hasil lebih maksimal (outline stub, no bullets yet)
- [x] 3. Why ARTi Framework (source outline: Ch.3 in `abstraksi-konten-dan-outline.md`, all 11 subchapters drafted)
  - [x] Solusi dari Masalah Saya Selama Ini (Mungkin Anda Juga) (`09-solusi-dari-masalah-saya`, renumbered from `08-`)
  - [x] 3 Filosofi (`10-3-filosofi`, renumbered from `09-`)
  - [x] Menjadikan Pekerjaan Berarti (`11-menjadikan-pekerjaan-berarti`, renumbered from `10-`)
  - [x] Arsitektur (`12-arsitektur`, renumbered from `11-`; includes ASCII-art architecture diagram)
  - [x] Sistem Memori (`13-sistem-memori`, renumbered from `12-`)
  - [x] Sistem Referensi (`14-sistem-referensi`, renumbered from `13-`)
  - [x] Scratchbook: File Sakti Anti Halusinasi (`15-scratchbook`, renumbered from `14-`)
  - [x] Fitur-fitur ARTi (`16-fitur-fitur-arti`, new)
  - [x] Perbandingan ARTi Framework dengan yang Lain (`17-perbandingan-arti-framework`, new)
  - [x] dan Lainnya (`18-dan-lainnya`, renumbered from `16-`)
  - [x] Cara Instalasi ARTi Framework (`19-cara-instalasi-arti-framework`, new)
- [x] Fix the dangling wiki-link to the old "Solusinya" title in `07-masih-ada-yang-kurang/index.md` (now points at "Solusi Parsial", fixed 2026-09-11)
- [ ] Verify all renumbered folders render + manifest resolves once MySQL is running again (DB was down this session, `php artisan cache:clear` failed with a connection-refused error — manifest.yaml is cached 1hr, so the sidebar may still show pre-edit slugs until either the cache expires or clear succeeds)

#### Amati
- [ ] 4. The Researcher Profile
- [ ] 5. Mapping the Gap, Not Guessing It
- [ ] 6. Reading a Journal Tactically

#### Replikasi
- [ ] 7. Feasibility Before Ambition
- [ ] 8. The Scratchbook Discipline

#### Tambah Inovasi
- [ ] 9. Scoring Novelty Honestly
- [ ] 10. Building the Manuscript Blueprint
- [ ] 11. Matching Voice and Technical Depth
- [ ] 12. The Iteration Log and the Novelty Floor
- [ ] 13. Cover Letters and the Submission Checklist
- [ ] 14. Answering Reviewers Without Losing the Paper
- [ ] 15. After Acceptance: Feeding the Next Amati

#### BONUS
- [ ] 16. Toolkit Quick Reference
- [ ] 17. Panduan Instalasi
- [ ] 18. Prompt Guide

### ARTi App (see [[arti-app-architecture]])

- [x] Phase 1: public routes on the main domain (landing, fitur, harga, unduh, kontak, privasi, syarat), flat-file articles + tag/feed/sitemap/robots, `Route::domain()` via `config('app.domains')`, shared `tokens.css`, `/app` + `/dashboard` 301
- [x] Feature/unit tests for every new route (27 DB-free tests pass)
- [x] Idempotent dev `DatabaseSeeder` (admin + learner, refuses production) with `DatabaseSeederTest`
- [x] Start MySQL, run `php artisan test` — full suite green after phase 1 (2026-10-13)
- [ ] Run `php artisan db:seed` on the dev DB (seeder verified on the testing DB only; admin@example.com / test@example.com, both `password`)
- [ ] Browser-check an authenticated flow after phase 1 and the Filament 4 upgrade (login → `/app` → a `learn/*` page; `/jamrud` visual look under v4)
- [x] Upgrade Filament 3 → 4 (v4.15.2, done 2026-10-14; resource + relation manager converted by hand, covered by `FilamentCourseResourceTest`)
- [ ] Write the first real articles in `resources/articles/` (site shows the empty state now)
    // ... 180 lines omitted
  from `resources/inbox/260906_OK_narasi-ai-bar-bar-dan-analogi-alzheimer.md`. Renumbered every
// ... 179 more lines (total: 272)