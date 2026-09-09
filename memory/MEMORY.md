---
name: memory-index
description: Index of arti-lms project memory files — pointers only
metadata:
  type: project
  tier: T0
  created: 2026-09-08 21:36
  updated: 2026-09-10 03:43
---

# arti-lms — Memory Index

- [ARTi Framework context](memories/arti-framework-context.md) — what the ebook/framework is (finalized 5-section/18-chapter outline), drafted in this repo's own resources/inbox/
- [Subchapter hierarchy](memories/subchapter-hierarchy.md) — Section>Chapter>Subchapter content level, progress tracked per-subchapter; sidebar is now a `<details>` accordion
- [Tech stack options](memories/tech-stack-options.md) — 3 candidate stacks; Option 3 (git-backed Markdown, server-rendered) + relative-path image workflow chosen
- [Option 3 implementation plan](memories/option3-implementation-plan.md) — full build checklist + Laravel setup commands, not yet run
- [Testing DB isolation](memories/testing-db-isolation.md) — phpunit.xml must point at arti_lms_testing, not the dev DB; RefreshDatabase wiped dev data once already
- [Course layout responsive](memories/course-layout-responsive.md) — off-canvas sidebar + sections/chapters manifest grouping implemented; browser visual QA still open; manifest content cache must be cleared when re-verifying after edits
- [Branding: login page](memories/branding-login.md) — login redesign (no header, centered, real logo/favicon from ~/.arti/logo/export), `/` redirects to login/dashboard, primary blue re-sourced from logo dot color (#2563eb)
- [Feedback: check memory before env probes](memories/feedback-check-memory-before-env-probes.md) — don't re-run `where`/`npm ls -g` discovery for facts memory already recorded (e.g. no browser tooling here)

## Change log
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
