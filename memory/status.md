---
name: status
description: Living current-state narrative for arti-lms
metadata:
  type: project
  tier: T0
  created: 2026-09-08 21:36
  updated: 2026-10-14 07:08
---

## Current state

**Filament 4 + ARTi App phase 1 + dev seeder done (2026-10-14).** Filament is v4.15.2 (converted by
hand after composer could not fetch the upgrade tool's deps; details in [[arti-app-architecture]]).
Phase 1 public routes (landing, flat-file articles, domain gating, shared `tokens.css`, `/app` with
`/dashboard` 301) are built. `DatabaseSeeder` is now idempotent, refuses to run in production, and
creates `admin@example.com` / `password` (admin, `/jamrud`) and `test@example.com` / `password`
(learner with access to `example-course`); it has only been run against `arti_lms_testing` via
`DatabaseSeederTest`, never against the dev DB, so run `php artisan db:seed` once on dev.
**Full suite green: 49 tests** on `arti_lms_testing`. Vite dev port moved 5273 → 5600 (Windows
reserved range 5204-5303).

Still not done: a real-browser look at login → `/app` → a `learn/*` page and at the v4 `/jamrud`
panel (tests cover logic, not looks); `composer.json`/`composer.lock`, the phase-1 files and the
memory edits are all uncommitted (no commit was requested). Nothing deployed, no Cloudflare or
license key touched.

Next in order: seed dev DB + browser check, LMS/ebook content (14 of 18 chapters undrafted, see
[[content-authoring-progress]]), first real articles in `resources/articles/`. Toko module and
license UI after. Tech stack: [[tech-stack-options]]; DB is MySQL (`arti_lms` dev /
`arti_lms_testing` for PHPUnit, [[testing-db-isolation]]).

## Archive

- 2026-10-14 — Filament 3 → 4 upgrade done by hand (v4.15.2), `FilamentCourseResourceTest` added, phase-1 DB-backed tests green (46 tests).
- 2026-10-13 — ARTi App phase 1 built (public routes, flat-file articles, domain gating, shared tokens, `/app`); see [[arti-app-architecture]].
- 2026-09-11 — Chapter 3 (Why ARTi Framework) fully drafted, chapter 2 4 of 6 outline sections, chapter 1 done; verification switched from `artisan tinker <file>` (no longer executes) to a standalone PHP script booting Laravel directly ([[content-authoring-workflow]]). Still open then: mobile-drawer real-browser QA, Laragon vhost browser-verify, Step 9 (R2 video).
- 2026-09-10 21:27 — Two user-reported course-layout bugs fixed: sidebar subchapter numbering
  restarting at each chapter (now a `$subchapterNumber` counter resetting once per section, see
  [[subchapter-hierarchy]]), and Markdown tables / `[[Title]]`-style cross-reference links not
  rendering (switched to `GithubFlavoredMarkdownConverter`, un-blocked `<style>` in
  `disallowed_raw_html`, added `resolveWikiLinks()`, designed table CSS — see
  [[content-authoring-workflow]]'s "Markdown rendering pipeline" section). Both traced to real gaps
  hit by chapter 2's content, not pre-existing latent bugs. One known dangling ref left as an open
  todo: `[[Solusinya]]` in `06-masih-ada-yang-kurang`. Verified via tinker against real chapter-2
  files; no real-browser visual check that session.
- 2026-09-10 19:27 — Pendahuluan chapter 1 fully drafted: all 3 subchapters exist, manifest-linked,
  folder names renamed to match `manifest.yaml` slugs exactly (`01-2-masalah-utama`,
  `02-solusi-parsial`, `03-hilang-memori-tak-berarti`). 3rd subchapter retells the
  Rithah/pemintal-benang parable from `resources/inbox/pemintal-benang.md`, tied to the "AI has no
  memory across sessions" theme; includes an image the user swapped in for the downloaded PD
  placeholder. All three tinker-verified post-rename. New Wikimedia-Commons image-sourcing pattern
  added to [[content-authoring-workflow]]. The orphaned `01-intro/` leftover was deleted by the user,
  closing that item.
- 2026-09-10 04:48 — Sidebar readability/visual-hierarchy redesign
  (`~/.claude/plans/i-want-to-optimize-cozy-crystal.md`): section/chapter/subchapter given distinct
  type scale (section shrunk + real divider, chapter promoted above subchapter size), subchapters
  show a numbered blue circle badge (`$loop->iteration`, restarts per chapter) swapping to a green
  checkmark on completion. Full detail in [[subchapter-hierarchy]]. Also produced a real
  browser-tooling correction: this environment does have working headless Chrome/Edge binaries —
  verified visually via `curl` login + a `<base href>` trick to screenshot the authenticated page
  from a local file, recipe in [[course-layout-responsive]]. Fixed 2 dangling `[[link]]` refs found
  in [[tech-stack-options]] and [[testing-db-isolation]] while updating memory afterward.
- 2026-09-10 04:29 — Content authoring started: replaced the `01-intro` pipeline placeholder with
  real material for chapter 1's two subchapters (`2-masalah-halusinasi-dan-memori`, `solusinya`),
  drafted from `resources/inbox/` source outline in first-person conversational Bahasa Indonesia,
  tinker-verified. Backfilled the approved per-subchapter readability `<style>` block (Atkinson
  Hyperlegible font, 600px column, one-sentence-per-paragraph) onto `solusinya/index.md`, which
  predated the pattern's approval. Full detail in [[content-authoring-workflow]].
- 2026-09-10 03:43 — Sidebar accordion fix: real-browser use surfaced the chapter label wasn't
  clickable and, since every chapter's lone placeholder subchapter shared its exact title, the two
  levels read as one flat duplicated list. User confirmed (AskUserQuestion) a real accordion was
  wanted. Fixed in `content/show.blade.php` (`.tree-subgroup` div → `<details class="tree-chapter">`
  /`<summary>`, `open` set when the chapter contains the active slug — no JS) + `app.css` (rotating
  chevron, left-indent). Tinker-verified against all 18 `arti-framework` chapters. Full detail in
  [[subchapter-hierarchy]].
- 2026-09-08 to 2026-09-09 07:10 — Laravel scaffold, pure-CSS design system, DB schema (steps 1-4);
  content dir + example-course (step 5); login/logout + content controller/routes (step 6); step 7
  progress tracking (ProgressController, mark-complete UI, dashboard completion %); step 8 Filament
  admin (CourseResource + AccessRelationManager, panel gate tests); course-layout-responsive plan
  (grouped sidebar + off-canvas drawer + fixed-viewport shell); RefreshDatabase-wiped-dev-DB
  incident and its fix. Full detail in [[option3-implementation-plan]], [[course-layout-responsive]],
  [[testing-db-isolation]], and the change log below.
- 2026-09-09 13:40 — Finalized ebook outline (5 sections/18 chapters) into `ebook-outline-v2.md`;
  added the 3rd content level (Subchapter) end-to-end — migration/model/3 controllers renamed
  `chapter_slug`→`subchapter_slug`, `DashboardController::flattenChapters()`→`flattenSubchapters()`
  (3 levels), sidebar gained a `.tree-subgroup` chapter-label level, 3 manifests updated to the
  nested schema. `migrate:fresh --seed` + tinker-verified end to end. Full detail in
  [[subchapter-hierarchy]].

## Change log
- 2026-09-08 — scaffolded, empty project
- 2026-09-08 23:20 — drafted two stack options, deferred decision to a redesign brainstorm next session
- 2026-09-08 23:30 — decided Option 3 + image workflow; wrote full implementation plan, ready to build next session
- 2026-09-08 23:56 — Laravel scaffolded (steps 1-2); dropped Sass plan, built pure-CSS design system from wdyt screenshots (step 3)
- 2026-09-09 08:00 — step 4 done: DB schema migrated on MySQL (switched off default sqlite, no driver), models wired, users.role + FilamentUser panel gate added
- 2026-09-09 00:07 — step 5 done: content dir scaffold, manifest.yaml schema decided, example-course/01-intro seeded with index.md + placeholder image
- 2026-09-09 00:18 — closed step-6 login gap (AuthController + login/logout routes + view + form CSS); fixed unrelated Vite EACCES (port pinned to 5273, host has 5094-5193 excluded)
- 2026-09-09 00:32 — end-to-end HTTP verification via artisan serve + curl (all core paths pass); found + user-fixed Laragon vhost DocumentRoot misconfig; found still-open logout-UI gap in content topbar
- 2026-09-09 00:53 — step 7 done: ProgressController + progress.store route + mark-complete UI + sidebar check state, curl/PowerShell-verified end-to-end; dashboard completion % explicitly deferred
- 2026-09-09 — closed the dashboard-% deferral: DashboardController computes per-course completion from manifest.yaml + Progress rows, dashboard card renders the existing .progress bar component, curl-verified (100% shown for the already-completed example chapter)
- 2026-09-09 01:05 — step 8 done: CourseResource + AccessRelationManager (grant-existing-learner and create-learner-and-grant actions), Filament panel gate verified via tinker + new FilamentPanelAccessTest (admin/learner at /jamrud)
- 2026-09-09 01:20 — fixed RefreshDatabase wiping the real dev MySQL DB (no testing DB was configured in phpunit.xml); added arti_lms_testing DB + phpunit.xml env; restored dev users/course/access by hand
- 2026-09-09 01:28 — implemented course-layout-responsive plan (grouped sidebar + off-canvas mobile drawer), functionally verified via artisan tinker (no browser tool available); real-browser visual QA left open
- 2026-09-09 07:10 — fixed-viewport shell: topbar + sidebar header pinned, content/sidebar nav independently scrollable, scrollbars hidden on both; build-verified only
- 2026-09-09 13:40 — finalized ebook outline (5 sections/18 chapters) into ebook-outline-v2.md; added Subchapter content level end-to-end (migration, model, controllers, views, CSS, manifests), migrate:fresh + tinker-verified; condensed this file's stacked Current-state entries into Archive per memory-rules convention
- 2026-09-10 03:43 — sidebar accordion fix (chapter label clickable + visual nesting), tinker-verified; moved prior session's Current-state paragraph into Archive per the no-stacking rule
- 2026-09-10 04:29 — content authoring underway: drafted chapter 1's two subchapters, added [[content-authoring-workflow]] memory, backfilled the approved readability style onto `solusinya`; moved sidebar-accordion paragraph into Archive per the no-stacking rule
- 2026-09-10 04:48 — sidebar readability/visual-hierarchy pass (per-level type scale, dividers, numbered badges), verified via real headless-Chrome screenshot; corrected the stale "no browser automation" fact in [[course-layout-responsive]]; fixed 2 dangling memory refs; moved content-authoring paragraph into Archive per the no-stacking rule
- 2026-09-10 19:19 — renamed 3 subchapter folders to match manifest.yaml slugs; drafted and
  tinker-verified `03-hilang-memori-tak-berarti` (pemintal-benang parable) with a downloaded
  public-domain placeholder image; Pendahuluan chapter 1 now fully drafted; added the
  Wikimedia-Commons image-sourcing pattern to [[content-authoring-workflow]]; moved
  sidebar-readability paragraph into Archive per the no-stacking rule
- 2026-09-10 19:27 — user replaced the downloaded-placeholder image with their own
  `pemintal-benang.png` (md reference updated to match) and dropped a second unreferenced image
  (`3-spinners.PNG`) into the same folder; user also deleted the orphaned `01-intro/` leftover
  flagged in the prior entry, closing that item
- 2026-09-10 21:27 — fixed two user-reported course-layout bugs (sidebar numbering restart,
  GFM table/wiki-link parsing gaps) surfaced by chapter 2's real content; added table CSS design;
  moved the Pendahuluan-ch.1-drafted paragraph into Archive per the no-stacking rule
- 2026-10-13 20:40 — ARTi App phase 1 built and verified except DB-backed tests (MySQL down); moved the chapter-3 paragraph into Archive per the no-stacking rule
- 2026-10-14 06:24 — Filament 4 upgrade done and tested; phase-1 DB-backed tests confirmed green; moved the phase-1 paragraph into Archive per the no-stacking rule
- 2026-10-14 06:44 — Vite port 5273 fell into a Windows reserved range (5204-5303, EACCES); moved to 5600 (free range 5558-5706). Check `netsh interface ipv4 show excludedportrange protocol=tcp` if it recurs.
- 2026-10-14 07:08 — session end: seeder + Vite port recorded in Current state; Filament paragraph moved into Archive
