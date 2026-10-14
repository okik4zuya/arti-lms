---
name: status
description: Living current-state narrative for arti-lms
metadata:
  type: project
  tier: T0
  created: 2026-09-08 21:36
  updated: 2026-10-14 23:58
---

## Current state

**Landing page built and media added (2026-10-14).** `arti.invait.id` sells one package (e-course + ARTi Framework license): Rp199.000/year, Rp99.000 for the first 100 buyers, 7-day refund, key by WhatsApp. One 600px Blade column: `/`, `/harga`, `/fitur`, free chapter 1 at `/baca`, icons, example cards, OG image. This session added the 40 s explainer video (lazy `<video>` plus poster in "Lihat caranya bekerja", above the still-empty real-demo slot) and a dashboard screenshot in the licence card, with the unpublished manuscript pane pixelated. Landing tests pass; everything is uncommitted and nothing is deployed. Detail: [[landing-page-concept]], [[landing-explainer-storyboard]], [[landing-page-copy]], [[landing-page-keywords]], [[landing-page-info]].

**Next:** listen to and watch the explainer once (audio never heard); move or delete the unredacted `arti-dashboard.png` from the repo root; record the real demo and its poster; hero screenshot; check new sections at true 375px; fill prod `.env` values. Still open from before: Filament v4 `/jamrud` and authenticated flow browser check, LMS content (14 of 18 chapters undrafted, [[content-authoring-progress]]), first real articles, `CLAUDE.md` has uncommitted edits not reviewed this session.

## Archive

- 2026-10-14 — Filament 3 → 4 upgrade (v4.15.2) and ARTi App phase 1 (public routes, flat-file articles, domain gating, shared tokens, `/app`), dev seeder, Vite port moved to 5600 after a Windows reserved-range EACCES; DB-backed tests green ([[arti-app-architecture]]).
- 2026-09-11 — Chapter 3 (Why ARTi Framework) fully drafted, chapter 2 at 4 of 6 outline sections, chapter 1 done; verification switched from `artisan tinker <file>` (no longer executes) to a standalone PHP script booting Laravel ([[content-authoring-workflow]]).
- 2026-09-10 — Course layout bugs fixed (sidebar numbering per section, GFM tables and wiki-link resolution, table CSS), sidebar accordion and readability pass, chapter 1 and the first chapter 2 subchapters drafted; headless Chrome/Edge screenshots work ([[subchapter-hierarchy]], [[course-layout-responsive]]).
- 2026-09-08 to 2026-09-09 — Laravel scaffold, pure-CSS design system, DB schema, content dir, login, progress tracking, Filament admin, responsive layout, ebook outline (5 sections / 18 chapters) and the Subchapter level; the RefreshDatabase-wiped-dev-DB incident ([[option3-implementation-plan]], [[testing-db-isolation]]).
