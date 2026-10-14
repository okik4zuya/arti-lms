---
name: todo-list
description: Checklist of open tasks for arti-lms — no narrative, checkboxes only
metadata:
  type: project
  tier: T0
  created: 2026-09-08 21:36
  updated: 2026-10-14 07:08
---

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
- [x] Fix dangling `[[Solusinya]]` wiki-link ref in `07-masih-ada-yang-kurang/index.md` — changed to `[[Solusi Parsial]]`, its correct title (fixed 2026-09-11 while auditing all `[[...]]` links after the subchapter-3 insert)
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
- [ ] Set `APP_DOMAIN_MAIN`/`APP_DOMAIN_TOKO` and `SITE_CONTACT_EMAIL`/`SITE_CONTACT_WHATSAPP` in the prod `.env`
- [ ] Decide real prices for `/harga`, legally review `/privasi` and `/syarat` drafts, publish the installer on `/unduh`
- [ ] Toko module: `customers` table + `auth:customer` guard + append-only ledger on its own domain (after LMS content)
- [ ] `/app/lisensi` and Filament license page against the licensing Worker (blocked until the Worker is deployed and Phase 0 is settled)

### App build / infra tasks

- [x] Brainstorm and redesign the tech stack next session (see [[tech-stack-options]] for the two prior drafts)
- [x] Get the design-system screenshot from the user (blocks phase 2 in either stack draft)
- [ ] Decide content-repo layout and deploy mechanism (only relevant if Docsify option is kept)
- [x] Step 1-2: Laravel + Filament/commonmark/yaml-front-matter scaffolded
- [x] Step 3: pure CSS design system built (Tailwind + Sass both dropped)
- [x] Step 4: database schema (Course, CourseAccess, Progress migrations)
- [x] Step 5: content directory + first course
- [x] Step 6: content controller + routes
- [x] Close login gap (AuthController + login/logout routes + view) — flagged at end of step 6
- [x] Fix `npm run dev` EACCES (Vite port pinned to 5273)
- [x] HTTP-verify login → content → image → access-control via `artisan serve` (curl walkthrough)
- [ ] Browser-verify same flow through the `arti-lms.local` vhost now that its DocumentRoot is fixed
- [x] Add a logout control to content/show.blade.php's topbar (route/controller exist, no UI link)
- [x] Build learner dashboard (DashboardController + /dashboard route + view); login now redirects there
- [x] Step 7: progress tracking (mark-complete + sidebar check state + dashboard completion %)

### Step 8 — Filament admin

- [x] `php artisan make:filament-resource Course` (manage slug/title/published_at only)
- [x] "Create Learner" custom Filament action: creates a User (admin-set temp password, role=learner) + a CourseAccess grant in one step
- [x] CourseAccess relation manager on the Course resource (grant/revoke access inline)
- [x] Verify `role === 'admin'` FilamentUser gate actually blocks learner accounts from /jamrud (panel path, not /admin)

### Step 9 — Cloudflare R2 (video only)
- [ ] `composer require league/flysystem-aws-s3-v3`
- [ ] Configure `r2` disk in `config/filesystems.php` (endpoint/credentials via `.env`, not committed)
- [ ] Confirm video workflow stays manual-paste-URL-in-Markdown — no upload UI, not wired into ContentController@image

### Testing

- [ ] Replace stub tests (`tests/Feature/ExampleTest.php`, `tests/Unit/ExampleTest.php`) with real coverage: login, access control, image path-traversal guard, dashboard scoping
- [x] Filament panel gate coverage (`tests/Feature/FilamentPanelAccessTest.php`: admin 200, learner 403)

- [ ] `php artisan test` clean run

### Security / production hardening

- [ ] Flip `.env` for prod: `APP_ENV=production`, `APP_DEBUG=false`, real `APP_URL`
- [ ] Rate-limit the login route (`throttle` middleware) — currently unthrottled
- [ ] Confirm session/cookie config is prod-appropriate (`SESSION_SECURE_COOKIE`, `SESSION_DRIVER`)
- [ ] Re-confirm the vhost dotfile-exposure fix holds (add to pre-deploy checklist)
- [ ] Styled 403/404 error pages matching the app shell

### Deploy

- [ ] Document native-VPS deploy steps (composer install --no-dev, npm run build, migrate --force, config/route/view cache, storage:link)
- [ ] Provision R2 credentials + other secrets on the VPS `.env`
- [ ] Confirm MySQL/MariaDB set up on target VPS (no pdo_sqlite)
- [ ] Decide real learner-facing domain/vhost for production

### Content polish

- [x] Replace `welcome.blade.php` placeholder copy with real landing content (landing built in phase 1, `welcome.blade.php` deleted)
- [ ] Add real course content beyond the single example-course/01-intro placeholder
- [x] Sanity-check manifest.yaml schema + image workflow scale to more than one course/chapter (nested sections: schema now covers both existing manifests)

### Ebook outline + Subchapter level (this session)

- [x] Finalize ebook outline into `resources/inbox/ebook-outline-v2.md` (5 sections / 18 chapters, folds in Claude/Claude Code chapter + old Publication Loop part + BONUS entries)
- [x] Add Subchapter level to Progress migration + model (`subchapter_slug` column/unique constraint)
- [x] Update ContentController/ProgressController/DashboardController for subchapter-level progress
- [x] 3-level sidebar in `content/show.blade.php` (`.tree-subgroup` for chapter labels) + CSS
- [x] Update example-course/second-course/arti-framework manifests to the 3-level schema
- [x] `migrate:fresh --seed` + tinker-verified end to end (dashboard %, sidebar checkmark, progress row)
- [ ] Draft actual subchapter content for `arti-framework`'s remaining 17 chapters (see "Content authoring progress" section at the top of this file for the live checklist; ch.1 done)
- [x] Sidebar chapter label clickable + visual chapter/subchapter nesting (accordion via `<details>`, tinker-verified) — found via real-browser use, see [[subchapter-hierarchy]]
- [x] Sidebar readability/visual-hierarchy pass: per-level type scale, section dividers, numbered/checkmark badges — real headless-Chrome-verified, see [[subchapter-hierarchy]]

### Course layout — responsive + grouped sidebar

- [x] Nest manifest `chapters:` under `sections:`, add per-chapter `title` (both manifests); fix second-course's stale copy-pasted title/slug
- [x] `DashboardController::flattenChapters()` to match new manifest shape
- [x] `content/show.blade.php` grouped sidebar (`.tree-group` headers) + hamburger toggle + backdrop markup
- [x] `app.css` `.tree-group` styling + `@media (max-width: 900px)` off-canvas drawer block
- [x] `app.js` toggle/backdrop-click/Escape handling
- [x] Fix two console errors found during user-driven real-browser QA: Vite IPv6/CORS dev-server binding, relative-Markdown-image-path 404
- [x] Fixed-viewport shell: topbar + sidebar header pinned to top, content + sidebar nav independently scrollable, scrollbars hidden on both (build-verified only)
- [ ] Real-browser visual QA of the mobile drawer specifically (animation, backdrop/Escape close, no horizontal scroll at ~375px/~768px) — still open; the desktop sidebar path now has a working screenshot technique (see [[course-layout-responsive]]), just not exercised at mobile width yet

### Dashboard polish (branding + hover effects)

- [x] Add wordmark branding to dashboard topbar
- [x] Course card hover: removed underline, added fill-color effect ([[branding-login]] area, see app.css `.card:hover`)
- [x] All `.btn`/`.btn--outline` hover effects made consistent app-wide (fill instead of opacity fade)
- [x] Course-layout sidebar (`.tree-item`) hover: removed underline, added primary-tint fill effect
- [ ] Real-browser visual QA of the above (build-verified only; a headless-Chrome screenshot technique now exists, see [[course-layout-responsive]], just not applied to this specific item yet)

## Change log
- 2026-09-11 07:50 — Drafted chapter 3's final 3 subchapters (Fitur-fitur ARTi, Perbandingan ARTi
  Framework dengan yang Lain, Cara Instalasi ARTi Framework); renumbered `dan-lainnya` to make room;
  chapter 3 (Why ARTi Framework) is now fully drafted, 11/11 subchapters. See
  [[content-authoring-progress]] for detail.
- 2026-09-08 — scaffolded, empty
- 2026-09-08 23:20 — added redesign-brainstorm and open-item tasks
- 2026-09-08 23:56 — checked off stack decision + design-system screenshot; Laravel scaffolding and
  pure-CSS design system done; expanded remaining items to match [[option3-implementation-plan]]
- 2026-09-09 — step 4 done: Course/CourseAccess/Progress migrations + models, users.role column,
  switched .env to MySQL (arti_lms db), migrated clean
- 2026-09-09 00:18 — step 5-6 checked off; closed login gap + Vite EACCES fix; added browser-verify item
- 2026-09-09 00:32 — checked off curl-based end-to-end HTTP verification; split remaining work into
  vhost re-verify + logout-UI-missing items (found during verification)
- 2026-09-09 09:15 — closed §1 of option3-implementation-currently-finish-wobbly-swan plan: logout
  button + learner dashboard, curl-verified end-to-end via artisan serve
- 2026-09-09 00:53 — closed §2 (step 7) of the same plan: progress tracking (ProgressController +
  route + mark-complete UI + sidebar check state), curl-verified end-to-end via artisan serve
- 2026-09-09 — closed step 7's deferred dashboard-% item: DashboardController now computes
  completed/total chapters per course from manifest.yaml, dashboard card shows the existing
  `.progress` bar component, curl-verified (100% for the one already-completed chapter)
- 2026-09-09 01:01 — expanded to the full comprehensive plan (§3-8 of
  `~/.claude/plans/option3-implementation-currently-finish-wobbly-swan.md`): replaced the flat
  Step 8/9 lines with the plan's actual sub-items and added the Testing, Security/production
  hardening, Deploy, and Content polish sections it also covers
- 2026-09-09 01:05 — step 8 done: CourseResource (slug/title/published_at), AccessRelationManager on
  Course with grant-existing-learner + create-learner-and-grant actions, Filament panel gate
  test-verified (admin 200 / learner 403 at /jamrud, not /admin — panel id is "jamrud")
- 2026-09-09 01:28 — implemented the course-layout-responsive plan (grouped sidebar + off-canvas
  mobile drawer); added dedicated section above; left the browser-visual-QA line open
- 2026-09-09 07:04 — checked off the two console-error fixes found during user-driven real-browser
  QA (Vite IPv6/CORS binding, relative Markdown image path 404); drawer-specific visual QA item
  still open
- 2026-09-09 07:10 — checked off fixed-viewport shell (topbar + sidebar header pinned, content +
  sidebar nav independently scrollable, hidden scrollbars); expanded the open visual-QA item to
  cover it too
- 2026-09-09 13:40 — finalized ebook outline + added Subchapter level end-to-end (migration, model,
  controllers, views, CSS, manifests), migrate:fresh + tinker-verified; added new section above,
  left drafting actual subchapter content as the one open item
- 2026-09-10 03:43 — checked off the sidebar accordion fix (chapter-label click + visual nesting),
  found via real-browser use and confirmed with the user via AskUserQuestion
- 2026-09-10 04:08 — drafted chapter 1's two subchapters (halusinasi/memori problem + solutions);
  added dedicated "Content authoring progress" checklist (all 18 chapters) and
  [[content-authoring-workflow]] memory since content writing is now the main ongoing work
- 2026-09-10 04:29 — backfilled the approved readability `<style>` block onto `solusinya/index.md`,
  noted inline on its checklist line
- 2026-09-10 04:48 — checked off the sidebar readability/visual-hierarchy pass (type scale,
  dividers, badges); refreshed the two stale "no browser tool available" QA-item caveats now that
  a real headless-Chrome screenshot technique exists (see [[course-layout-responsive]])
- 2026-09-10 19:19 — renamed the three Pendahuluan ch.1 subchapter folders to match
  manifest.yaml slugs (`01-2-masalah-utama`, `02-solusi-parsial`, `03-hilang-memori-tak-berarti`);
  drafted the 3rd subchapter from `resources/inbox/pemintal-benang.md`, downloaded a public-domain
  placeholder image for it, tinker-verified all three render + image URL resolves; ch.1 of
  Pendahuluan now fully done
- 2026-09-10 19:40 — moved "Content authoring progress" to a dedicated section at the top of this
  file per the user's request; split its narrative detail out into the new
  [[content-authoring-progress]] memory (this file keeps checkboxes only, per the project's
  no-narrative-in-todo-list rule)
- 2026-09-10 20:10 — drafted chapter 2's first two subchapters (Taaruf, Claude: Model dengan
  Branding Logika); added the 4 remaining chapter-2 outline stubs as open sub-items
- 2026-09-10 — renumbered chapter 2's first two subchapters (`03-`→`04-taaruf`,
  `04-`→`05-claude-model-dengan-branding-logika`), added a research-model benchmark table to the
  latter, and drafted the new `06-masih-ada-yang-kurang` subchapter; manifest.yaml updated to
  match; all three tinker-verified rendering (controller signature now takes a `Course` model, not
  a slug string — verification snippet updated accordingly)
- 2026-09-10 21:27 — Fixed two user-reported course-layout bugs (sidebar numbering restart, GFM
  table/wiki-link parsing gaps) and added a table CSS design pass; added the dangling
  `[[Solusinya]]` reference as a new open item above.
- 2026-09-10 21:52 — Drafted `07-claude-code` (the chapter-2 finale, answering "masih ada yang
  kurang"'s open question); manifest.yaml updated, tinker-verified. Chapter 2 now has one open
  outline stub left ("Claude bisa pakai skill").
- 2026-09-10 22:07 — Drafted all 8 subchapters of chapter 3 (Why ARTi Framework), replacing the
  single `why-arti-framework-01` placeholder slug in manifest.yaml with 8 real slugs
  (`08-solusi-dari-masalah-saya` through `15-dan-lainnya`); Arsitektur subchapter includes an
  ASCII-art architecture diagram; verified all 8 render + wiki-links resolve via a standalone PHP
  script (tinker's file-include mode just echoed source without executing — used direct
  `require bootstrap/app.php` instead, see [[content-authoring-workflow]]).
- 2026-09-11 — Inserted a new chapter-1 subchapter, "Halusinasi = Meruntuhkan Kredibilitas
  Akademik" (`03-halusinasi-meruntuhkan-kredibilitas-akademik`), between Solusi Parsial and Hilang
  Memori, per the outline (`abstraksi-konten-dan-outline.md` line 50) plus the "AI bar-bar" narasi
  from `resources/inbox/260906_OK_narasi-ai-bar-bar-dan-analogi-alzheimer.md`. Renumbered every
  downstream subchapter folder by +1 (`04-hilang-memori-tak-berarti` through `16-dan-lainnya`) to
  keep the whole-book sequential numbering convention intact; updated manifest.yaml and the
  `order` frontmatter field on the one subchapter whose in-chapter position actually changed
  (hilang-memori: 3→4). Could not `cache:clear` or tinker-verify — MySQL wasn't running this
  session (Laragon likely needs starting) — flagged as an open item above.
