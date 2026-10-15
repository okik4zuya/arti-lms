---
name: status
description: Living current-state narrative for arti-lms
metadata:
  type: project
  tier: T0
  created: 2026-09-08 21:36
  updated: 2026-10-15 09:30
---

## Current state

**Landing finalisation done, 2026-10-15 (committed, not pushed, not deployed).** 12 commits on `main`: the backlog in 6 groups, then the audit fixes. Full suite green (98 tests, 0 failures; the deprecation notices are the old PDO constant). Audited `/`, `/fitur`, `/kontak`, `/privasi`, `/syarat`, `/baca/*`, `/artikel`, feed, sitemap, robots, header, footer, login and error pages. Fixed: chapters called Google Fonts (now self-hosted), a Mac mention, "kami" vs "ARTi", duplicate descriptions, English login and error pages, no skip link/focus ring/reduced motion, low muted-text contrast, empty `/artikel` indexable. Legal pages now restate the real offer and data flows; lawyer items and proposed wording are in [[legal-review-notes]]. Checked at 375, 768 and 1100 px. Detail: [[landing-page-concept]], [[landing-page-copy]].

**Waiting on the researcher:** WhatsApp number and sold count for the prod `.env` (without a number every "Beli" button goes to `/kontak`, which says no channel is open); hide `/artikel` and "Umpan artikel" until the first article (recommended: yes, not done); "ARTi Workflow" vs "ARTi Framework" (the app still says "ARTi LMS"); "Tentang penulis"; real demo video and hero screenshot; manual checks on a real phone (autoplay on `/fitur`, anchors). In the separate artipdf repo, still yours: commit/push LICENSE, THIRD-PARTY-NOTICES, screenshots; release v1.0.7; delete `C:/arti-demo`. Also open: production Redis and gzip, Filament `/jamrud` under Redis sessions, `composer audit` (4 advisories on laravel/framework 11.x), 14 of 18 chapters undrafted ([[content-authoring-progress]]).

## Archive

- 2026-10-15 — Landing and site rework: /fitur rewrite, media, menu and redirects, speed pass, Redis via Predis ([[page-speed]], [[redis-setup]], [[artipdf-landing-facts]]).

- 2026-10-14 — Landing built and media added: one 600px Blade column (`/`, `/harga`, `/fitur`, free chapter 1 at `/baca`, icons, example cards, OG image), 40 s explainer video and a redacted dashboard screenshot ([[landing-explainer-storyboard]], [[landing-page-copy]], [[landing-page-keywords]], [[landing-page-info]]).
- 2026-10-14 — Filament 3 → 4 upgrade (v4.15.2) and ARTi App phase 1 (public routes, flat-file articles, domain gating, shared tokens, `/app`), dev seeder, Vite port moved to 5600 after a Windows reserved-range EACCES; DB-backed tests green ([[arti-app-architecture]]).
- 2026-09-11 — Chapter 3 (Why ARTi Framework) fully drafted, chapter 2 at 4 of 6 outline sections, chapter 1 done; verification switched from `artisan tinker <file>` (no longer executes) to a standalone PHP script booting Laravel ([[content-authoring-workflow]]).
- 2026-09-10 — Course layout bugs fixed (sidebar numbering per section, GFM tables and wiki-link resolution, table CSS), sidebar accordion and readability pass, chapter 1 and the first chapter 2 subchapters drafted; headless Chrome/Edge screenshots work ([[subchapter-hierarchy]], [[course-layout-responsive]]).
- 2026-09-08 to 2026-09-09 — Laravel scaffold, pure-CSS design system, DB schema, content dir, login, progress tracking, Filament admin, responsive layout, ebook outline (5 sections / 18 chapters) and the Subchapter level; the RefreshDatabase-wiped-dev-DB incident ([[option3-implementation-plan]], [[testing-db-isolation]]).
