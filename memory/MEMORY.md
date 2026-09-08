---
name: memory-index
description: Index of arti-lms project memory files — pointers only
metadata:
  type: project
  tier: T0
  created: 2026-09-08 21:36
  updated: 2026-09-09 01:28
---

# arti-lms — Memory Index

- [ARTi Framework context](memories/arti-framework-context.md) — what the ebook/framework is and how it relates to this LMS
- [Tech stack options](memories/tech-stack-options.md) — 3 candidate stacks; Option 3 (git-backed Markdown, server-rendered) + relative-path image workflow chosen
- [Option 3 implementation plan](memories/option3-implementation-plan.md) — full build checklist + Laravel setup commands, not yet run
- [Testing DB isolation](memories/testing-db-isolation.md) — phpunit.xml must point at arti_lms_testing, not the dev DB; RefreshDatabase wiped dev data once already
- [Course layout responsive](memories/course-layout-responsive.md) — off-canvas sidebar + sections/chapters manifest grouping implemented; browser visual QA still open; manifest content cache must be cleared when re-verifying after edits

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
