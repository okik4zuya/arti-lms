---
name: status
description: Living current-state narrative for arti-lms
metadata:
  type: project
  tier: T0
  created: 2026-09-08 21:36
  updated: 2026-10-15 13:43
---

## Current state

**2026-10-15 13:43. Licensing Worker is live; license admin stage 1 works against it; next stage designed, not built.** The licensing Worker is deployed at `https://lisensi-arti.invait.id/v1` (production keys, D1, secrets) and passed a production smoke test: issue, activate, second machine refused, revoke. The Filament page `/jamrud/license-issuances` was checked locally against it and works. Cloudflare blocks the default `Python-urllib` User-Agent, so `arti_admin.py` and the buyer-facing `arti-license` client now send their own; `~/.arti/tools/arti-license` points at the production API and public keys. Runbook and gotchas: [[license-worker-setup]]; what exists: [[license-admin]].

**Phase D (reset account password, no stored password, template fixed) is built (16:06) and MFA is coded but switched off by the researcher's decision (`ADMIN_MFA_REQUIRED`, default false); 100 license and MFA tests pass; two migrations Pending on dev. All four phases A to D exist; next is browser checking and one real round against the Worker, then deploy steps.** **Phase C (extend, release device, rotate key, revoke/undo, delete pending, mark reviewed, `review_note` queue) is built (15:35), 83 license tests pass, not yet browser-checked; three migrations are Pending on dev. Payment proofs run on R2 `arti-private` ([[r2-storage]]). Next: phase D (reset account password, drop `account_password`, fix the template), MFA before production use.** **Phase B (payment proof, verification, review queue, signed proof route, `/privasi` sentences) is also built (14:08), with a Pending migration on dev; 76 tests pass. Next is phase C.** **License admin phase A is built (2026-10-15 13:56), uncommitted:** roles `admin`/`support` with permissions (`permissions:sync`), default-deny for courses and site settings, "Diterbitkan oleh", Riwayat, Ubah kontak, quick filters, tier removed, compact issue modal checked in headless Chrome. License-related tests pass (35, per file). Next: phase B (payment proof, review queue), C (Worker actions), D (password reset, drop `account_password`). Decisions taken: revoke does not stop activated machines (accepted), paid installer on Cloudflare R2 with short-lived links. Spec: [[license-admin-roadmap]].

**Working tree, all uncommitted (researcher wants no commits unless asked):** landing and contact work from earlier today, the license pages, and an extra built this session: a learner login block (email and temporary password) in the WhatsApp message with an encrypted `account_password` column (migration Pending on dev). It is tested but will be dropped in phase D. `php artisan test --filter=License` passed (33 tests); the full suite ran out of PHP memory with about 900 MB free RAM, so it was not run.

**Waiting on the researcher:** empty the `LICENSE_*` lines in the local `.env` and revoke the licenses made from dev; Laravel production setup (`.env`, `migrate`, cron `schedule:run`); where the paid installer is hosted (`LICENSE_INSTALLER_URL`); restrict `/v1/admin/*` to the VPS IP; D1 export and a restore drill; MFA on `/jamrud`; permission to remove the trial path in the Worker repo; the product call on revoke not stopping already-activated machines. Landing items unchanged: the WhatsApp number and sold count for production, hide `/artikel` until the first article, "ARTi Workflow" vs "ARTi Framework", "Tentang penulis", real demo video and hero screenshot, real-phone checks, the artipdf repo commits and release, production Redis and gzip, `composer audit`, 14 of 18 chapters undrafted ([[content-authoring-progress]]).

## Archive

- 2026-10-15 (earlier) — Landing finalisation committed in 12 commits (98 tests then), audit fixes for fonts, copy, accessibility, SEO; legal pages restated with the real offer ([[landing-page-concept]], [[landing-page-copy]], [[legal-review-notes]]).
- 2026-10-15 (earlier) — Contact and media: WhatsApp number and default messages editable in Filament Pengaturan situs, floating button opens a form panel, `/kontak` as the no-JS fallback, original `#25d366` kept, "benang terurai" illustration replaces the Rithah image ([[contact-whatsapp]], [[landing-page-concept]]).
- 2026-10-15 (earlier) — License page stage 1 built with `Http::fake` (140 tests): A+ architecture, issue, encrypted key copy, WhatsApp message, daily sync ([[license-admin]]).
- 2026-10-15 — Landing and site rework: /fitur rewrite, media, menu and redirects, speed pass, Redis via Predis ([[page-speed]], [[redis-setup]], [[artipdf-landing-facts]]).
- 2026-10-14 — Landing built and media added: one 600px Blade column (`/`, `/harga`, `/fitur`, free chapter 1 at `/baca`, icons, example cards, OG image), 40 s explainer video and a redacted dashboard screenshot ([[landing-explainer-storyboard]], [[landing-page-copy]], [[landing-page-keywords]], [[landing-page-info]]).
- 2026-10-14 — Filament 3 → 4 upgrade (v4.15.2) and ARTi App phase 1 (public routes, flat-file articles, domain gating, shared tokens, `/app`), dev seeder, Vite port moved to 5600 after a Windows reserved-range EACCES; DB-backed tests green ([[arti-app-architecture]]).
- 2026-09-11 — Chapter 3 (Why ARTi Framework) fully drafted, chapter 2 at 4 of 6 outline sections, chapter 1 done; verification switched from `artisan tinker <file>` (no longer executes) to a standalone PHP script booting Laravel ([[content-authoring-workflow]]).
- 2026-09-10 — Course layout bugs fixed (sidebar numbering per section, GFM tables and wiki-link resolution, table CSS), sidebar accordion and readability pass, chapter 1 and the first chapter 2 subchapters drafted; headless Chrome/Edge screenshots work ([[subchapter-hierarchy]], [[course-layout-responsive]]).
- 2026-09-08 to 2026-09-09 — Laravel scaffold, pure-CSS design system, DB schema, content dir, login, progress tracking, Filament admin, responsive layout, ebook outline (5 sections / 18 chapters) and the Subchapter level; the RefreshDatabase-wiped-dev-DB incident ([[option3-implementation-plan]], [[testing-db-isolation]]).
