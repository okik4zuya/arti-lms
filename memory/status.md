---
name: status
description: Living current-state narrative for arti-lms
metadata:
  type: project
  tier: T0
  created: 2026-09-08 21:36
  updated: 2026-10-18 16:40
---

## Current state

**2026-10-18 16:40. Automatic purchase is built and tested, all uncommitted ([[automatic-purchase-plan]]); next session is understand-then-brainstorm ([[next-session-prompt]]).** A buyer pays through the configured gateway (Midtrans by default, Xendit as a second driver; Xendit needs a NIB) from `/beli`. A verified, idempotent webhook marks the order paid, `FulfillOrder` creates the learner account, issues the license (lifetime, `months: 1000`) and queues the purchase mail with a one-time set-password link (no time limit, dies when used or when the password changes), key and download link; WhatsApp is a separate optional stage (driver `none`). Forgot password works for learners; admin and support recover with `admin:reset-password` / `admin:reset-mfa`. Launch places are atomic, prices and promo events are editable in Filament ("Harga", "Promo"), refunds, `orders:reconcile` and `orders:prune` exist, the manual Filament flow no longer touches passwords, the garansi claim is off the marketing pages, and `/syarat`, `/kontak`, `/privasi` were rewritten for the one-time purchase and a new refund policy (not lawyer-reviewed, [[legal-review-notes]]). Every test file passes when run alone except the old `DomainRoutingTest`. **Not verified:** a real Midtrans call or notification (the researcher tests in the sandbox), a real Brevo mail of the new templates, a browser look at the new pages, the Worker with `months: 1000`. Researcher's task list: [[todo-list]], [[purchase-production-checklist]], [[worker-changes-spec]]; product-side tasks also in `~/.arti/memory/memories/automatic-purchase-product-tasks.md`.

**Earlier, still true:** domain moved to arti.ai.id and production runs on the shared VPS ([[domain-migration-arti-ai-id]]); welcome series is off until `MAIL_SERIES_ENABLED=true` ([[list-building]]); license admin phases A to D, MFA code (off) and R2 storage as in [[license-admin-roadmap]] and [[r2-storage]]. Open: SciPeek 1.0.1 release, old R2 domain, admin 2FA, Bot Fight Mode, dev Redis `-MISCONF`.
## Archive

- 2026-10-16 — Domain move to arti.ai.id, welcome series, broadcasts, one-click unsubscribe, license admin phases A to D and MFA code (details in [[domain-migration-arti-ai-id]], [[list-building]], [[license-admin-roadmap]]); the long pre-2026-10-18 state block was folded into the line above.
- 2026-10-15 (afternoon) — Worker deployed and smoke-tested; license admin stage 1 checked against it; roles, payment proof, R2, extend/revoke/rotate/reset and MFA code built in one session ([[license-worker-setup]], [[license-admin-roadmap]], [[r2-storage]]).
- 2026-10-15 (earlier) — Landing finalisation committed in 12 commits (98 tests then), audit fixes for fonts, copy, accessibility, SEO; legal pages restated with the real offer ([[landing-page-concept]], [[landing-page-copy]], [[legal-review-notes]]).
- 2026-10-15 (earlier) — Contact and media: WhatsApp number and default messages editable in Filament Pengaturan situs, floating button opens a form panel, `/kontak` as the no-JS fallback, original `#25d366` kept, "benang terurai" illustration replaces the Rithah image ([[contact-whatsapp]], [[landing-page-concept]]).
- 2026-10-15 (earlier) — License page stage 1 built with `Http::fake` (140 tests): A+ architecture, issue, encrypted key copy, WhatsApp message, daily sync ([[license-admin]]).
- 2026-10-15 — Landing and site rework: /fitur rewrite, media, menu and redirects, speed pass, Redis via Predis ([[page-speed]], [[redis-setup]], [[artipdf-landing-facts]]).
- 2026-10-14 — Landing built and media added: one 600px Blade column (`/`, `/harga`, `/fitur`, free chapter 1 at `/baca`, icons, example cards, OG image), 40 s explainer video and a redacted dashboard screenshot ([[landing-explainer-storyboard]], [[landing-page-copy]], [[landing-page-keywords]], [[landing-page-info]]).
- 2026-10-14 — Filament 3 → 4 upgrade (v4.15.2) and ARTi App phase 1 (public routes, flat-file articles, domain gating, shared tokens, `/app`), dev seeder, Vite port moved to 5600 after a Windows reserved-range EACCES; DB-backed tests green ([[arti-app-architecture]]).
- 2026-09-11 — Chapter 3 (Why ARTi Framework) fully drafted, chapter 2 at 4 of 6 outline sections, chapter 1 done; verification switched from `artisan tinker <file>` (no longer executes) to a standalone PHP script booting Laravel ([[content-authoring-workflow]]).
- 2026-09-10 — Course layout bugs fixed (sidebar numbering per section, GFM tables and wiki-link resolution, table CSS), sidebar accordion and readability pass, chapter 1 and the first chapter 2 subchapters drafted; headless Chrome/Edge screenshots work ([[subchapter-hierarchy]], [[course-layout-responsive]]).
- 2026-09-08 to 2026-09-09 — Laravel scaffold, pure-CSS design system, DB schema, content dir, login, progress tracking, Filament admin, responsive layout, ebook outline (5 sections / 18 chapters) and the Subchapter level; the RefreshDatabase-wiped-dev-DB incident ([[option3-implementation-plan]], [[testing-db-isolation]]).
