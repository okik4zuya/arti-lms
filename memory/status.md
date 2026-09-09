---
name: status
description: Living current-state narrative for arti-lms
metadata:
  type: project
  tier: T0
  created: 2026-09-08 21:36
  updated: 2026-09-10 03:43
---

## Current state

**Sidebar accordion fix (this session)**: real-browser use of the subchapter sidebar (added last
session) surfaced two bugs a screenshot showed: the chapter label wasn't clickable, and — since
every chapter's lone placeholder subchapter currently shares its exact title — the two levels read
as one flat duplicated list. User confirmed (AskUserQuestion) they want a real accordion, and that
the blue subchapter links 404ing on the 17 undrafted chapters is expected, not in scope here. Fixed
in `content/show.blade.php` (`.tree-subgroup` div → `<details class="tree-chapter">`/`<summary>`,
`open` set when the chapter contains the active slug — no JS) + `app.css` (rotating chevron,
`--space-5` left-indent on `.tree-item`). Tinker-verified against all 18 `arti-framework` chapters:
only the active chapter's `<details>` renders `open`. Full detail in [[subchapter-hierarchy]].

Test credentials: `test@example.com` / `password` (learner, `example-course` access only),
`admin@example.com` / `password` (admin, panel at `/jamrud` not `/admin`); user id 5 has
`CourseAccess` to `second-course` specifically. Known still-open items: real-browser visual QA of
the course-layout-responsive drawer (animation, backdrop/Escape close, no horizontal scroll at
~375/~768px — see [[course-layout-responsive]]), the Laragon vhost browser-verify (never confirmed
end to end through `arti-lms.local`), Step 9 (Cloudflare R2 for video, not started).

Tech stack: **Option 3** (git-backed Markdown, server-rendered Blade, no Docsify/Filament-authored
content) + relative-path git image workflow, R2 reserved for video only — full rationale in
[[tech-stack-options]]. Steps 1-8 of [[option3-implementation-plan]] plus its follow-on §1-2
(logout UI, learner dashboard, progress tracking) are done. DB is MySQL (`arti_lms` dev /
`arti_lms_testing` for PHPUnit — kept separate since an earlier `RefreshDatabase` run wiped dev data
once, see [[testing-db-isolation]]).

## Archive

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
