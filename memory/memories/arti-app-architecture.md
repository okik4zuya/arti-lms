---
name: arti-app-architecture
description: ARTi web ecosystem — this repo grows into the ARTi App (landing, articles, LMS, admin, later toko); licensing stays on Cloudflare; decisions, route map, phase-1 implementation status and what is still unverified
metadata:
  type: project
  created: 2026-10-13 20:03
  updated: 2026-10-14 07:08
---

Outcome of a design discussion started in the `~/.arti` project and continued here. Supersedes the
Next.js/Astro-first suggestion made earlier. Builds on [[tech-stack-options]] (Option 3 stays) and
[[option3-implementation-plan]]. **Phase 1 (public route foundation) is built; see Implementation status.**

## What exists around this project
- **artitoko** (`D:\coding\artitoko`): one static `index.html` (vanilla JS, 600px column, ARTi design tokens). Journal-download shop; orders only via WhatsApp `wa.me` + QRIS; balance (saldo) recorded manually; 105 publishers hard-coded; top-up bonus 5% from Rp100.000, 10% from Rp500.000. Its memory notes the paywalled-article source is not stated (legal gray area), so keep it separable from the academic ARTi brand.
- **ARTi licensing** (`~/.arti`, plan in `~/.arti/memory/memories/licensing-update-plan.md`): Cloudflare Worker + dedicated D1, Ed25519-signed license file, unbound keys bound at first activation, API under `/v1/`. Phase 1 items 2-6 built and verified locally, nothing deployed. Phase 0 open: EULA, gateway (Midtrans vs Xendit), email provider, Worker domain.
- **This repo**: Laravel 11 + Filament 4 (panel `/jamrud`), spatie/laravel-permission, UUID keys, models `Course/CourseAccess/Progress/User`, flat-file Markdown content, pure CSS.

## Decisions (final)
- **ARTi App = this repo grown**: one Laravel app hosts the LMS, the admin (Filament `/jamrud`), landing/articles and later the toko modules.
- **Licensing is NOT moved into Laravel.** Cloudflare Worker + D1 stays (clients call it from every install, must stay up when the VPS is down, isolated DB, already built). Laravel is only an API client: `/app/lisensi` and a Filament page, no Eloquent license tables. Link users by email, no cross-DB FK. Payment webhook stays on the Worker.
- **artitoko stays static until automatic saldo is needed**, then becomes a separate domain in this app with its own `customers` table and `auth:customer` guard (apart from `users`). Saldo = append-only ledger.
- Naming caveat: Laravel `customers` (shop buyers) and the Worker's D1 `customers` (license holders) are different things; one person buying both has two accounts. Accepted consequence.

## Decisions answered 2026-10-13 (open items of the first discussion)
- **Filament 3 → 4: upgrade now**, as its own step after phase 1. Done, see the Filament 4 section.
- **Priority: LMS/ebook content first**, then artitoko saldo, then license UI (blocked until the Worker is deployed and Phase 0 is settled).
- **Landing/articles: Blade in this app**, not Astro. Markdown content stays portable if that changes.
- **Dashboard moves to `/app`**; `/dashboard` is a 301 to `/app`. Route name `dashboard` kept, so every `route('dashboard')` call is unchanged.
- **Customer login: password first** (admin-created accounts), magic link later when the email provider is chosen.

## Business decisions 2026-10-14 (override earlier notes where they conflict)
- **Product = e-course (LMS) + ARTi Framework license in one package.** Price Rp199.000/year; Rp99.000 for the first 100 buyers; install requires internet; no trial; landing domain `arti.invait.id`. Details and the manual-issuing/fail-closed activation rules: `~/.arti/memory/memories/licensing-update-plan.md`.
- **Sales flow:** admin sends the license key by WhatsApp; the framework calls the Cloudflare Worker to confirm the key is valid and unused, else install fails. The earlier Facebook-ads/WhatsApp/manual-payment note is therefore consistent; the Midtrans/Xendit webhook flow is deferred.
- Consequence for this app: `/harga` becomes one package with one price (not five tiers); `/unduh` is for buyers; learner accounts stay admin-created (matches the manual flow). Landing work: [[landing-page-info]].

## Route map
Domains are placeholders (`config('app.domains.main'|'toko')`), one app, `Route::domain()`.

Main domain: `/` landing, `/fitur`, `/harga`, `/unduh`, `/artikel`, `/artikel/{slug}`, `/artikel/tag/{tag}`, `/feed.xml`, `/sitemap.xml`, `/robots.txt`, `/kontak`, `/privasi`, `/syarat`; auth `/login`, `/logout` (later `/lupa-sandi`, `/atur-sandi/{token}`); learner app `/app`, `/learn/{course}/{slug}` (+ `images/{file}`, `progress`); later `/app/lisensi`, `/app/profil`; admin `/jamrud` (later resources: licenses API-backed, toko-customers/orders/topups/ledger/publishers).

Toko domain (later): `/`, `/penerbit`, `/syarat`; customer `/masuk`, `/keluar`, `/akun`, `/akun/saldo`, `/akun/saldo/isi`, `/akun/pesan`, `/akun/pesanan`, `/akun/pesanan/{order}`; `POST /webhooks/pembayaran` (signature-verified), `GET /file/{order}` (signed URL, 7 days).

Licensing Worker (separate domain): the `/v1/*` API, not in this app.

## Implementation status — phase 1 (2026-10-13)
Built:
- `routes/web.php`: everything inside one `Route::group(array_filter(['domain' => config('app.domains.main')]))`. **Null domain = no constraint**, which is what dev (`arti-lms.local`, `localhost`) and tests use; set `APP_DOMAIN_MAIN` / `APP_DOMAIN_TOKO` in prod `.env`. Nothing is registered for toko yet. The Filament panel got `->domain(config('app.domains.main'))`.
- Public pages as `Route::view` (`site.*` views, `layouts/site.blade.php`): `/`, `/fitur`, `/harga`, `/unduh`, `/kontak`, `/privasi`, `/syarat`. `/` replaces the old redirect to login (`welcome.blade.php` deleted). Copy states no invented facts: `/harga` shows planned tiers without prices, `/unduh` says not released, `/kontak` shows channels only if `SITE_CONTACT_EMAIL` / `SITE_CONTACT_WHATSAPP` are set, `/privasi` and `/syarat` are marked draft (not legally reviewed).
- Articles: flat files `resources/articles/<slug>/index.md` (front matter `title, description, date, tags, published`; optional `images/`), path from `config('articles.path')` / `ARTICLES_PATH`. `App\Support\ArticleRepository` caches the parsed index keyed on file mtimes. Routes `/artikel`, `/artikel/tag/{tag}` (registered before `{slug}`), `/artikel/{slug}`, `/artikel/{slug}/images/{file}`, `/feed.xml` (Atom), `/sitemap.xml`, `/robots.txt`. Slug `tag` is reserved; future-dated, unpublished and badly-slugged articles are skipped. `resources/articles/` is empty (only `.gitkeep`), so the live site shows the empty state; the test fixtures in `tests/Fixtures/articles/` are not shipped content.
- `public/robots.txt` deleted (git-staged deletion): the web server would have served it before PHP and the dynamic route would never run.
- `App\Support\MarkdownRenderer` is now the one Markdown pipeline (`toHtml`, `rewriteImages`); `ContentController` uses it instead of its own converter.
- CSS: `resources/css/tokens.css` is the single shared token file (extracted from `app.css`, which now `@import`s it); `resources/css/site.css` is the public-site stylesheet (imports tokens, no Tailwind). `vite.config.js` lists it as an input. artitoko can copy or link `tokens.css` later.
- `/dashboard` → 301 `/app`; login redirect still goes to `route('dashboard')`.
- Tests: `PublicSiteTest`, `ArticlesTest`, `DomainRoutingTest` (sets `APP_DOMAIN_MAIN` before the app boots), `MarkdownRendererTest` (unit), `LegacyRoutesTest` (characterization of login/logout/dashboard/learn/progress). `tests/TestCase.php` calls `withoutVite()`. The stub feature `ExampleTest` was removed (it asserted 200 on `/`, which had been a redirect).

Verified with real artifacts: 27 DB-free tests (108 assertions) pass; standalone PHP boot script (forced array session/cache and `arti_lms_testing`, aborts otherwise) requested 24 routes with and without a main domain (right host: all as expected; wrong host: all 404 except `/up`); old vs new Markdown pipeline gave identical HTML on all 21 real content files; headless Chrome screenshots of `/`, `/fitur`, `/harga`, `/artikel`, article page at 1280px and at a true 375px viewport (iframe), no horizontal overflow.

**DB-backed tests (run once MySQL was up):** `LegacyRoutesTest` and `FilamentPanelAccessTest` pass, so login, `/app`, `learn/*` through the refactored `ContentController`, progress and the admin gate with `->domain()` are confirmed by requests. Still not looked at in a real browser.

## Filament 4 upgrade (2026-10-14)
Done: `filament/filament` v4.15.2. The `filament/upgrade` tool could not be installed (composer failed downloading rector/phpstan: `Permission denied` on `vendor/composer/tmp-*.zip` plus git-clone timeouts; likely antivirus locking or flaky GitHub dist downloads), so the user ran the `composer require` and the two Filament classes were converted by hand: `Schema` replaces `Form`, `Filament\Actions\*` replaces `Tables\Actions\*`, `recordActions`/`toolbarActions`, `->schema()` on the custom action, `mutateDataUsing`, `string|BackedEnum|null $navigationIcon`. `FilamentCourseResourceTest` exercises list/create/edit, create-learner, grant and revoke. Relation managers load lazily in v4, so assert them through Livewire, not the page HTML. Course route key is `slug`, so edit URLs use it.

## Dev accounts and tooling notes
`DatabaseSeeder` seeds `admin@example.com` (admin) and `test@example.com` (learner, `example-course` access), both password `password`; idempotent, skips in production. Vite dev port is 5600 (5273 became a Windows reserved port, 5204-5303; recheck with `netsh interface ipv4 show excludedportrange protocol=tcp`).

## Risks found in review (keep in mind for later phases)
- `auth` middleware redirects to `route('login')`; the customer guard needs its own `redirectGuestsTo` and unique route names across domains.
- `SESSION_DOMAIN=null` (host-only cookie) is what keeps main and toko sessions apart: do not set a shared cookie domain. `sessions.user_id` is a UUID and only records the default guard, so `web` must stay the default and `customers` must use UUID keys.
- Linking licenses to `users.email` is safe only while accounts are admin-created and emails unverified-but-trusted; before opening self-registration, verify email.
- Filament panel `/jamrud/login` is a second login separate from `/login`.
- Article index/feed/sitemap scan every article file; the mtime-keyed cache covers it for now, revisit if the count grows to hundreds.
- `Route::domain` URLs become absolute; behind a proxy set trusted proxies/`APP_URL` correctly.

## Next
1. Browser check of the authenticated flow and the v4 `/jamrud` look.
2. LMS/ebook content (priority b), real first articles in `resources/articles/`.
3. Later: toko domain (`customers`, `auth:customer`, ledger), `/app/lisensi` + Filament license page against the Worker once deployed.

## Change log
- 2026-10-14 21:59 — recorded bundle/price/manual-issuing decisions.
- 2026-10-14 07:08 — recorded dev seeder accounts and the Vite port move.
- 2026-10-14 06:24 — Filament 4 upgrade done (manual conversion, tested); phase-1 DB-backed tests confirmed green.
- 2026-10-13 20:37 — answered the five open decisions (Filament 4 now, content first, Blade landing, /app + 301, password login first), built and verified phase 1 (public routes, flat-file articles, domain gating, shared tokens), recorded risks and the unverified DB-backed tests.
- 2026-10-13 20:03 — created from the cross-project design discussion (artitoko, licensing, routes).
