---
name: memory-index
description: Index of arti-lms project memory files — pointers only
metadata:
  type: project
  tier: T0
  created: 2026-09-08 21:36
  updated: 2026-09-11 07:50
---

# arti-lms — Memory Index

- [ARTi Framework context](memories/arti-framework-context.md) — what the ebook/framework is (finalized 5-section/18-chapter outline), drafted in this repo's own resources/inbox/
- [Subchapter hierarchy](memories/subchapter-hierarchy.md) — Section>Chapter>Subchapter content level, progress tracked per-subchapter; sidebar is a `<details>` accordion with per-level type scale + numbered badges that now count continuously per section
- [Tech stack options](memories/tech-stack-options.md) — 3 candidate stacks; Option 3 (git-backed Markdown, server-rendered) + relative-path image workflow chosen
- [Option 3 implementation plan](memories/option3-implementation-plan.md) — full build checklist + Laravel setup commands, not yet run
- [Testing DB isolation](memories/testing-db-isolation.md) — phpunit.xml must point at arti_lms_testing, not the dev DB; RefreshDatabase wiped dev data once already
- [Course layout responsive](memories/course-layout-responsive.md) — off-canvas sidebar + sections/chapters manifest grouping implemented; manifest cache must be cleared when re-verifying; corrected "no browser" claim — headless Chrome/Edge binaries work directly, mobile drawer QA still open
- [Branding: login page](memories/branding-login.md) — login redesign (no header, centered, real logo/favicon from ~/.arti/logo/export), `/` redirects to login/dashboard, primary blue re-sourced from logo dot color (#2563eb)
- [Feedback: check memory before env probes](memories/feedback-check-memory-before-env-probes.md) — don't re-run `where`/`npm ls -g` discovery for facts memory already recorded (e.g. no browser tooling here)
- [Content authoring workflow](memories/content-authoring-workflow.md) — rules for drafting arti-framework subchapters (voice, file/manifest mechanics, tinker verification) plus the markdown rendering pipeline (GFM tables, `[[Title]]` wiki-links, disallowed-raw-html config for per-subchapter `<style>`)
- [Content authoring progress](memories/content-authoring-progress.md) — narrative status per chapter (why blocked, source used, decisions made); checklist itself lives in todo-list.md's top section
- [ARTi App architecture](memories/arti-app-architecture.md) — 2026-10-13 design outcome: this repo grows into the ARTi App (landing, articles, toko modules, admin); licensing stays on its Cloudflare Worker, Laravel is only an API client; proposed routes and open decisions

## Change log
- 2026-10-13 20:03 — added arti-app-architecture pointer
- 2026-09-11 07:50 — chapter 3 (Why ARTi Framework) fully drafted: added its last 3 subchapters
  (Fitur-fitur ARTi, Perbandingan ARTi Framework dengan yang Lain, Cara Instalasi ARTi Framework),
  renumbered `dan-lainnya` to keep outline order; not yet render-verified (MySQL down)
- 2026-09-08 — scaffolded memory/ for arti-lms (Dev project, non-Paper category)
- 2026-09-08 23:20 — added tech-stack-options memory (two candidate stacks, redesign pending)
- 2026-09-08 23:22 — added arti-framework-context memory
- 2026-09-08 23:30 — decided Option 3 + image workflow; added implementation plan
- 2026-09-08 23:56 — Laravel scaffolded, pure-CSS design system built (dropped Sass), design-system screenshots consumed from wdyt/
- 2026-09-09 08:00 — step 4 done (DB schema on MySQL, not the Laravel-11 sqlite default — no driver installed)
- 2026-09-09 00:07 — step 5 done (content dir scaffold, manifest.yaml schema decided, example course seeded)
- 2026-09-09 00:18 — step 6 login gap closed (AuthController/routes/view + form CSS); fixed unrelated Vite EACCES (port pinned to 5273)
- 2026-09-09 00:32 — step 6 end-to-end HTTP verification passed; found + user-fixed Laragon vhost docroot bug; found open logout-UI gap
- 2026-09-09 01:05 — step 8 done: Filament CourseResource + AccessRelationManager (grant/create-learner actions), panel gate test-verified
- 2026-09-09 01:28 — implemented + functionally verified the course-layout-responsive plan (grouped sidebar sections + off-canvas mobile drawer); browser visual QA still open
- 2026-09-09 06:39 — login page redesigned (no header, centered, real logo/favicon), `/` now redirects to login/dashboard, primary blue re-sourced from logo dot color; added branding-login memory
- 2026-09-09 12:00 — flagged redundant chromium-cli/playwright probe during mobile-sidebar-toggle debugging; hardened course-layout-responsive memory, added feedback memory on checking memory before env probes
- 2026-09-09 07:04 — real-browser QA started (user-driven); fixed Vite IPv6/CORS dev-server config and a relative-Markdown-image-path 404, both logged in course-layout-responsive
- 2026-09-09 07:10 — fixed-viewport shell (pinned topbar + sidebar header, independently scrollable content/nav, hidden scrollbars) added to course-layout-responsive; build-verified only
- 2026-09-09 13:40 — finalized ebook outline (5 sections/18 chapters) into ebook-outline-v2.md; added Subchapter level to the LMS (migration+model+3 controllers+2 views+CSS+3 manifests), migrate:fresh + tinker-verified end to end; updated arti-framework-context, added subchapter-hierarchy
- 2026-09-10 03:43 — sidebar accordion fix (chapter label clickable, visual chapter/subchapter nesting via `<details>`/CSS chevron), found via real-browser use + AskUserQuestion, tinker-verified; updated subchapter-hierarchy
- 2026-09-10 04:08 — drafted first real chapter content (2-masalah-halusinasi-dan-memori, solusinya subchapters, manifest updated, tinker-verified); added content-authoring-workflow memory
- 2026-09-10 04:29 — backfilled the approved readability `<style>` block onto `solusinya/index.md` (it predated the pattern's approval), closing chapter 1's styling gap
- 2026-09-10 04:48 — sidebar readability/visual-hierarchy pass (per-level type scale, section dividers, numbered badges), verified with a real headless-Chrome screenshot; fixed 2 dangling `[[link]]` refs found while doing this update
- 2026-09-10 19:19 — renamed 3 subchapter folders to match manifest.yaml slugs, drafted 3rd Pendahuluan ch.1 subchapter (pemintal-benang parable) with a downloaded PD placeholder image, tinker-verified; Pendahuluan ch.1 is now fully drafted
- 2026-09-10 19:27 — user swapped in their own image for the subchapter-3 placeholder and deleted the orphaned `01-intro/` leftover
- 2026-09-10 19:40 — added dedicated content-authoring-progress memory (narrative status per chapter); moved the "Content authoring progress" checklist to the top of todo-list.md as its own dedicated section
- 2026-09-10 19:52 — drafted chapter 2's Taaruf + Claude: Model dengan Branding Logika subchapters, deleted a stray erroneous placeholder dir, tinker-verified both
- 2026-09-10 21:27 — fixed 2 user-reported course-layout bugs surfaced by chapter 2's real content: sidebar subchapter numbering restarting per chapter (now runs continuously per section), and GFM tables/`[[Title]]` wiki-links not rendering (switched to `GithubFlavoredMarkdownConverter`, fixed its raw-HTML guard silently escaping `<style>` blocks, added wiki-link resolution, designed table CSS); tinker-verified against real chapter-2 files; updated subchapter-hierarchy and content-authoring-workflow
- 2026-09-10 22:07 — drafted all 8 subchapters of chapter 3 (Why ARTi Framework), including an ASCII-art architecture diagram; manifest.yaml updated; found `php artisan tinker <file>` no longer executes in this environment, switched verification to a standalone PHP script booting Laravel directly; updated content-authoring-progress and content-authoring-workflow
