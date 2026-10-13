---
name: arti-app-architecture
description: Discussion outcome (2026-10-13) for the ARTi web ecosystem — landing, articles, artitoko shop, ARTi App admin; arti-lms grows into the ARTi App, licensing stays on Cloudflare; proposed routes and open decisions
metadata:
  type: project
  created: 2026-10-13 20:03
  updated: 2026-10-13 20:03
---

Outcome of a design discussion held in the `~/.arti` project. Nothing here is implemented yet; the
routes and module list are a proposal to build from. Supersedes the Next.js/Astro-first suggestion
made earlier in that discussion. Builds on [[tech-stack-options]] (Option 3 stays) and
[[option3-implementation-plan]].

## What exists around this project
- **artitoko** (`D:\coding\artitoko`): one static `index.html` (vanilla JS, 600px column, ARTi design tokens). Journal-download shop for ARTi needs; orders only via WhatsApp `wa.me` links + QRIS; balance (saldo) recorded manually; 105 publishers hard-coded in `PUBLISHERS_TXT`; top-up bonus 5% from Rp100.000, 10% from Rp500.000. Open v1 items: WhatsApp number, `assets/qris.png`, deploy. Its own memory notes the paywalled-article source is not stated (legal gray area) — keep it separable from the academic ARTi brand.
- **ARTi licensing** (`~/.arti`, plan in `~/.arti/memory/memories/licensing-update-plan.md`): Cloudflare Worker + dedicated D1, Ed25519-signed license file, unbound keys bound at first activation, API under `/v1/` (`/trial /activate /validate /deactivate /update/latest /download/<v> /webhooks/payment /admin/*`). Phase 1 items 2-6 built and verified locally (45 e2e checks), nothing deployed. Phase 0 decisions still open: EULA, gateway (Midtrans vs Xendit), email provider, Worker domain.
- **This repo today**: Laravel 11 + Filament 3 (panel at `/jamrud`), spatie/laravel-permission, UUID keys, models `Course/CourseAccess/Progress/User`, routes `/`, `login`, `logout`, `dashboard`, `learn/{course}/{slug}[...]`, flat-file Markdown content, pure CSS.

## Decisions
- **ARTi App = this repo grown**, not a new stack: one Laravel app hosts the LMS, the admin (Filament `/jamrud`), the landing/article pages and later the toko modules. Reuses auth, RBAC and course access already built.
- **Licensing is NOT moved into Laravel.** It stays a separate Cloudflare Worker + D1 service because clients call it from every install (must stay up when the VPS is down), the plan already fixes an isolated licensing DB behind an API, and it is built and verified. Laravel is only a client of the Worker's internal API: `/app/lisensi` page and a Filament page for license admin, both API-backed, no Eloquent license tables. Users link by email (`customers.email` ↔ `users.email`), no cross-DB foreign key. The payment webhook stays on the Worker.
- **artitoko stays static until automatic saldo is needed**, then moves into Laravel as a separate domain with its own `customers` table and `auth:customer` guard (kept apart from `users` to separate the shop from the framework). Saldo is an append-only ledger (corrections are new rows).
- Landing/articles: Blade in this app (Markdown articles in git, same flat-file pattern as the LMS), unless SEO needs push it to a separate Astro site.

## Proposed routes
Domains are placeholders (`config('app.domains.main'|'toko')`), served by one app via `Route::domain()`.

Main domain: `/` landing (replaces the current redirect-to-login), `/fitur`, `/harga`, `/unduh`, `/artikel`, `/artikel/{slug}`, `/artikel/tag/{tag}`, `/feed.xml`, `/sitemap.xml`, `/robots.txt`, `/kontak`, `/privasi`, `/syarat`; auth `/login`, `/logout`, `/lupa-sandi`, `/atur-sandi/{token}`; learner app `/app` (dashboard, old `/dashboard` redirects or is kept), `/learn/{course}/{slug}` (+ `images/{file}`, `progress`), `/app/lisensi`, `/app/profil`; admin `/jamrud` with resources users, courses, licenses (API-backed), toko-customers, toko-orders, toko-topups, toko-ledger, toko-publishers.

Toko domain: public `/`, `/penerbit`, `/syarat`; customer `/masuk`, `/keluar`, `/akun`, `/akun/saldo`, `/akun/saldo/isi`, `/akun/pesan`, `/akun/pesanan`, `/akun/pesanan/{order}`; `POST /webhooks/pembayaran` (signature-verified), `GET /file/{order}` (signed URL, 7 days).

Licensing Worker (separate domain, e.g. `license.<domain>`): the `/v1/*` API above — not in this app.

## Open decisions
- Filament 3 → 4 upgrade before adding modules (compatibility unchecked).
- Share ARTi design tokens as one CSS file across LMS, landing and artitoko.
- One login method for ARTi App customers: password with admin-created accounts (today) vs magic link by email (licensing plan Phase 5).
- Priority: LMS content/ebook vs artitoko automatic saldo vs license UI. Landing in Blade vs Astro. Real domains.
- Keep `/dashboard` or move to `/app` (existing tests cover `/dashboard`).

## Change log
- 2026-10-13 20:03 — created from the cross-project design discussion (artitoko, licensing, routes).
