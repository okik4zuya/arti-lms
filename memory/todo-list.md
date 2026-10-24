---
name: todo-list
description: Checklist of open tasks for arti-lms — no narrative, unchecked items only, one line each
metadata:
  type: project
  tier: T0
  created: 2026-09-08 21:36
  updated: 2026-10-24 18:30
---

Closed items are deleted; their history lives in the linked topic files. Tidied 2026-10-19: ~120 checked items removed, merged duplicates, restored a lost block of old items from git (section "Older build items").

### VPS stability and backup (outage 2026-10-19), see [[vps-oom-incident-2026-10-19]], [[vps-maintenance-guide]]

- [ ] Researcher: delete `~/dump-2026-10-19.sql` on the VPS if not yet (unencrypted; the laptop copy exists); look at the unknown `~/backup` folder
- [ ] Researcher: on 2026-10-20 check the first scheduled backup (`tail /var/log/backup-r2.log`, `rclone ls r2:arti-backup/`), then weekly
- [ ] Researcher: apply the pending hardening: MySQL `zz-lowmem.cnf` and restart, 2 GB extra swap; confirm FPM `ondemand`, `flock` cron lines and Laravel `config:cache`/`route:cache`/`event:cache` really ran
- [ ] Researcher: delete the DNS record `monitor` in the `invait.id` zone; confirm `sites-enabled/netdata` is gone (config kept in `~/netdata-nginx.bak`)
- [ ] Researcher: confirm `orders:reconcile` shows in `php artisan schedule:list` on the VPS (no orders were lost in the outage: not launched yet)
- [ ] Monthly restore drill from R2 into an empty database; nightly encrypted D1 export of `license_issuances` is still not scheduled
- [ ] Deferred: thin Worker buffer in front of `/webhooks/pembayaran` (queue and retry), only if sales volume justifies it

### Course packages (brainstorm 2026-10-19), see [[course-packages-decisions]]

- [ ] **Continue the brainstorm:** price architecture (SLR price, pre-launch price, package discount arithmetic, promo hierarchy), copy and legal impact, work order
- [ ] Later: design the SLR course page from [[landing-slr-workflow]]
- [ ] Write Workflow Paper Riset (Amati, Replikasi, Tambah Inovasi) first, then SLR; no multi-product system before SLR has enough published chapters
- [ ] BIMA on hold: decide whether ARTi has or needs a grant-proposal skill and whether to maintain it yearly; optional test video first

### Landing copy, four problems, see [[landing-copy-four-problems]]

- [ ] Researcher: look at the whole landing at 375 px and on a phone (only 700 px was seen in headless Chrome)
- [ ] Align the rest with the four problems: `/fitur`, email series 1 to 4, OG image text; casual replacement for the h2 "Anda pasti pernah mengalaminya."; unify "kutipan" vs "referensi" in pain 1
- [ ] Check the Claude AI rows of the second comparison table (Word files, "Tidak, harus disalin") against current abilities before adding rows; no rival claims without evidence
- [ ] Image of a Word file with Zotero fields (show before building); hero visual above the fold; real 1 to 2 minute demo moved nearer the solution
- [ ] Optional: measured numbers (time, effort) in the value cards, only from the researcher's own measurements
- [ ] Continue landing finalisation: ethics boundaries, then the six-stimulus table per section, hero first ([[landing-finalisation-prompt]])
- [ ] Align the "Baca bab 1 gratis" buttons (`/scipeek`, `/artipdf`, `/fitur`, 404, series mail 4) and the "wujud" wording (home `#wujud`, `/fitur`, series mail 2) if wanted

### Checkout form and purchase, see [[landing-checkout-form]], [[automatic-purchase-plan]], [[purchase-production-checklist]]
- [ ] Mayar go-live ([[mayar-gateway]], [[purchase-production-checklist]] 5c): account verified, subdomain and shop name fixed, production API key and Webhook Token, Url Webhook with a new secret, `MAYAR_IS_PRODUCTION=true`; keep `SITE_CHECKOUT_ENABLED=false` until one real payment passes
- [ ] Researcher: finish the Mayar sandbox checks: order reaches `fulfilled`, webhook retry makes no second key/mail, login and Download page after set-password; then revoke the test licence ("Kembalikan dana") and delete test orders/users
- [ ] Remove the VA/QRIS method cards and `metode` validation (Mayar ignores the pick); add the sentence about Mayar's 4 receipt emails on `/beli` and the finish page; update the tests
- [ ] Ask Mayar support: hold time and VAT, refunds, turning off or branding its emails, Webhook Token header name, 1.5% vs 4% rate, VA/QR by API ([[mayar-gateway]])
- [ ] Optional: store Mayar `transactionId` and read `GET /transactions/{id}` for `expired` status and payment method; check the Webhook Token header in the production log
- [ ] Production Redis: set queue to `database` (drain Redis first), session/cache on Redis only with safe settings ([[redis-setup]]); dev: free RAM or `stop-writes-on-bgsave-error no`
- [ ] Commit the Mayar driver, tests, config, route and the gateway-neutral wording (nothing of 2026-10-23 is committed)
- [ ] Production deploy of 2026-10-19: commit and push (`vite.config.js` entries, "Kenapa murah" removed, checkout kill switch), then `git pull`, `npm run build`, `config:cache`, `queue:restart` on the VPS as `deploy`
- [ ] Gmail send-as `hi@arti.ai.id` through Brevo SMTP plus merged SPF record ([[custom-domain-email]])
- [ ] Decide on the SES migration (option B, do AWS prep now, bounce webhook timing) ([[ses-migration-plan]])
- [ ] Researcher: `php artisan migrate` (dev, then production) for `orders.method_choice`; `npm run build` before any deploy (`sticky-buy.js`)
- [ ] Researcher: with ngrok and `queue:work`, pay once with QRIS and once with the VA card in the sandbox, change method on a second try, check Snap shows only the chosen methods
- [ ] Researcher: confirm `other_qris` and `shopeepay` are active in the Midtrans dashboard (else adjust `MIDTRANS_METHODS_VA` / `_QRIS`); decide whether DANA may be claimed
- [ ] Researcher: look at the form, method cards, buy bar and the new purchase pages (`/beli/selesai/...`, `/lupa-sandi`, `/atur-sandi/...`, Filament Pesanan, Harga, Promo) in a browser and on a phone
- [ ] Researcher: finish the sandbox steps (replay a notification, stopped queue then `orders:reconcile`, real forgot-password, Download page after login); then revoke every test licence with "Kembalikan dana" (they are on the production Worker) and delete test orders and users
- [ ] Researcher: restart the queue worker after any `.env` change; `MAIL_FROM_ADDRESS` on `arti.ai.id`, `MAIL_SCHEME` empty on port 587
- [ ] Researcher: if the WhatsApp templates were ever edited in Filament "Pengaturan situs", update them by hand (they override the new defaults)
- [ ] Researcher: check the merchant name shown on Snap (Midtrans dashboard, Snap Checkout / General Settings)
- [ ] Researcher: add `permissions.deny` rules for `.env` in `.claude/settings.json` (`.claudeignore` exists but Claude Code ignores it)
- [ ] Researcher: re-read `/syarat`, `/kontak`, `/privasi` (refund conditions, time limits, WhatsApp sentences removed); fill `SITE_SELLER_NAME`, `SITE_SELLER_CITY`; ask a lawyer about the lifetime and shutdown clauses ([[legal-review-notes]])
- [ ] Researcher, `~/.arti` repo: bring the EULA `LICENSE.txt` in line with `/syarat` (it still says refund "sesuai garansi" and a period from key issue)
- [ ] Researcher: confirm an activated machine keeps working offline if the Worker is ever shut down; confirm the lifetime prices and read the copy on `/`, `/fitur`, series mail 4, purchase mail
- [ ] Researcher, Worker repo: silent `resend`, check `months: 1000`, optional idempotent `extend`; client shows "Lifetime" ([[worker-changes-spec]])
- [ ] Researcher, production: migrate, `permissions:sync`, env variables, gateway notification URL, Cloudflare skip rule, `ADMIN_MFA_REQUIRED=true`
- [ ] Decide: WhatsApp delivery note on the form (driver still `none`): build a provider or soften the note; WhatsApp provider brainstorm in a separate session
- [ ] Decide: remove the unused WhatsApp number and default-message fields from Filament "Pengaturan situs" (and tests, `SITE_CONTACT_WHATSAPP`), or keep them for the manual licence flow
- [ ] Decide: different subject for resent purchase mails (Gmail folds the repeated body); copy-paste link in the `confirm-subscription` mail
- [ ] Backup gateway: Duitku researched, not built ([[duitku-fallback]]); Midtrans driver stays as a switch (`PAYMENT_GATEWAY`); use Duitku only if Mayar refuses the account
- [ ] Run the full `php artisan test` suite once (only touched files were run); `DomainRoutingTest::test_generated_urls_use_the_main_domain` fails even on a clean stash (pre-existing)
- [ ] Review `LicenseIssuanceResource` row action labels after the switch to links; README mentions; README "nine skills" to ten

### Tools hub /tools, see [[tools-hub-plan]]

- [ ] Researcher: Gem limit check (6,000-character instruction accepted? knowledge file count? free account can Share, second account opens link?); the instructions are 2.5-3.4k characters, so only the knowledge-file count matters
- [ ] Researcher: create the 4 Gems from `resources/gems/` (paste `*.instruksi.md`, upload `*.pengetahuan.md`), set `GEM_*_URL`, try one real round per Gem with a weak model and fix the instruction where output is invalid
- [ ] Researcher: look at /tools and the 3 tool pages in a real browser and on a phone; test "Cetak / simpan PDF" (new tab and print dialog) and PNG in Firefox; open a downloaded .drawio in draw.io
- [ ] Journal-profile page: add a worked example output the researcher got from the Gem (not invented); hub decision tree as 4 questions if wanted; keyword run for titles (S5)
- [ ] Decide the funnel CTA (sign-up form plus bab 1 now; licence trial path is on the removal list)
- [ ] Commit the tools work (uncommitted with the Mayar work), then deploy: `git pull`, `npm ci`, `npm run build`, `config:cache`, set `SITE_TOOLS_ENABLED=true` and the Gem URLs
- [ ] Look into 3 failing tests outside tools: `FilamentPricingTest` (promotion) and two `RefundAndReconcileTest` cases (probably the Mayar default gateway)
- [ ] After release: validate with about 5 students (idea to usable image, where the paste-back loop breaks)

### Learner app UX, see [[learner-app-ux]]

- [ ] Researcher: look at the dashboard, avatar menu, `/app/password`, course page prev/next and instant mark, `/beli` spinner, set-password page, landing header badge and phone menu in a browser
- [ ] Researcher: `npm run build` before any deploy; decide Redis for production (own instance, or leave `CONTENT_CACHE_STORE` empty) ([[redis-setup]])
- [ ] Researcher: `php artisan queue:clear redis` if the 2 waiting `SeriesMail` jobs must not be sent before `queue:work` starts
- [ ] Optional, not built: undo "Tandai selesai", highlight "Selanjutnya", prefetch next subchapter, SPA-style navigation; dashboard "Download ARTi" and licence block declined for now
- [ ] Answer the persona question (dosen new to Claude Code vs disappointed AI user; default assumed: disappointed)

### Landing page and free tools, see [[landing-page-concept]], [[landing-scipeek-artipdf]], [[page-speed]]

- [ ] Listen to the explainer's music and watch it through once ([[landing-explainer-storyboard]])
- [ ] Move or delete the unredacted `arti-dashboard.png` in the repo root (gitignored, shows the unpublished manuscript); decide whether the dashboard screenshot should also hide project name and file counts
- [ ] Media: real 1 to 2 minute demo with poster and `SITE_DEMO_VIDEO_EMBED_URL`; real hero screenshot (VS Code + Claude Code); author photo / "Tentang penulis" (undecided); lift the staircase in `ad-story`
- [ ] Check at 375 px the sections not yet checked (free chapter pages `/baca`, explainer video, dashboard image, icons)
- [ ] Review the Indonesian example commands and copy on `/fitur`; confirm the autoplay loop in a real browser
- [ ] Look at `/scipeek` and `/artipdf` in a real browser and on a phone (video playback, images, layout); do one real download of the ARTi PDF installer from the CDN
- [ ] Decide whether the "Summarize with AI" right-click (needs the ARTi dashboard) ships in the public installer
- [ ] artipdf repo: commit and push `LICENSE`, `THIRD-PARTY-NOTICES.md`, screenshots, readme; delete `C:/arti-demo` ([[artipdf-landing-facts]])
- [ ] Confirm gzip/brotli and cache headers on the real production host; test speed on a real phone
- [ ] Redis for production and check `/jamrud` and LMS login under Redis sessions
- [ ] `composer audit`: 4 advisories on laravel/framework 11.x; plan an upgrade
- [ ] Decide whether to hide `/artikel`, its sitemap entry and the footer "Umpan artikel" until the first article exists; write the first real articles in `resources/articles/`
- [ ] Decide the app name ("ARTi LMS" in the learner app, "ARTi Framework" on the site); name "ARTi Workflow" vs "ARTi Framework" outside the ebook; offline grace after activation
- [ ] Fill `SITE_LAUNCH_SOLD` in the prod `.env` (`APP_DOMAIN_MAIN=arti.ai.id` is set)
- [ ] Remove or disable the unused trial path (`/trial`, `arti-license trial`, `trial` tier) in the licensing Worker before deploy

### List building, see [[list-building]]

- [ ] Connect SMTP (Brevo), start Redis and `queue:work`, send one real confirmation and click it; in production also `php artisan migrate`, `npm run build` and a supervised queue worker
- [ ] Production: `php artisan permissions:sync` (adds `subscriber.manage`), migrate (two migrations), cron `schedule:run` (hourly series, daily licence sync)
- [ ] Read the four series emails in `resources/views/mail/series/` and edit the copy; only then set `MAIL_SERIES_ENABLED=true` in production
- [ ] `php artisan queue:clear redis`, then re-test the series and a broadcast through the Redis queue and Brevo; look at a real test in Gmail and on a phone
- [ ] Rotate the R2 `arti-private` token (key id and secret prefix were shown in a tool result) ([[r2-storage]])

### ARTi App and licensing, see [[arti-app-architecture]], [[license-admin]], [[license-admin-roadmap]], [[license-worker-setup]]

- [ ] `php artisan db:seed` on the dev DB (seeder verified on the testing DB only)
- [ ] Browser-check an authenticated flow (login to `/app` to a `learn/*` page; `/jamrud` under Filament 4) and licence admin phases A to D; then one real round against the Worker with a test licence (extend, release, rotate, revoke, undo), revoked afterwards
- [ ] Empty the `LICENSE_*` lines in the local `.env` (researcher), revoke test licences issued from dev, delete `%TEMP%\arti-prod-client`
- [ ] Production: cron `schedule:run` for `licenses:sync`; restrict `/v1/admin/*` to the VPS IP; D1 export and restore drill; set `LICENSE_API_URL`, `LICENSE_ADMIN_TOKEN`, `LICENSE_INSTALLER_URL`; `permissions:sync` and the `support` role
- [ ] Paid installer hosting (decided, not built): R2 with short-lived links issued by Laravel, then `LICENSE_INSTALLER_URL`
- [ ] Payment proofs on R2: private bucket and token, `composer require league/flysystem-aws-s3-v3 "^3.0"`, `PROOFS_DISK=r2` and `R2_*` in the production `.env`; delete the old test proof in the public `arti` bucket and re-attach or ignore two dev rows
- [ ] Add sentences: `/syarat` (revoking stops renewals and new activations, not activated machines), `/privasi` (buyer email and name kept at both Worker and app)
- [ ] `composer require bacon/bacon-qr-code:^3.1` to replace the `*` constraint (researcher runs it)
- [ ] MFA: set `ADMIN_MFA_REQUIRED=true` in production before the panel is used there; set up the authenticator and save recovery codes first
- [ ] Worker repo (needs the researcher's OK): `resend` without email, `extend` idempotent via `order_ref`; `arti_admin.py` should show the HTTP code on a non-JSON error body; `php tests/manual/verify_license.php` needs a local `wrangler dev` (optional)
- [ ] Backups for the licence record: MySQL and `APP_KEY` now go daily to R2 and one restore drill passed ([[vps-maintenance-guide]]); still open: nightly encrypted export of `license_issuances` from D1
- [ ] Phase A leftovers: the "Kelola" menu holds only Ubah kontak and Riwayat

### Domain migration, see [[domain-migration-arti-ai-id]]

- [ ] SciPeek 1.0.1 release (separate session, SciPeek repo): bump version, ZIP, push, submit to the Web Store
- [ ] Remove the old R2 custom domain `cdn-arti.invait.id` from bucket `arti` once nothing links to it
- [ ] Enable 2FA on the `/jamrud` admin account; consider a Cloudflare rate limit on `/jamrud/login`
- [ ] Re-enable Bot Fight Mode on arti.ai.id only after a WAF skip rule for `lisensi.arti.ai.id`
- [ ] Move arti-lms to a dedicated VPS at the first paying buyer with real volume, swap above 1.5 GB again, or another OOM (1.9 GB RAM shared by 7 sites; see [[vps-oom-incident-2026-10-19]])

### Content authoring

- [ ] Ebook content todos moved to `resources/content/arti-framework/memory/todo-list.md` ([[content-repo-pointer]])

### Older build items (restored 2026-10-19 from git `f220715`; a block was lost in commit `1037246`; may be stale, verify before acting)

- [ ] Rate-limit the login route; confirm session/cookie config for production (`SESSION_SECURE_COOKIE`, `SESSION_DRIVER`); re-check the vhost dotfile-exposure fix before deploy
- [ ] Flip `.env` for production (`APP_ENV=production`, `APP_DEBUG=false`, real `APP_URL`); document native-VPS deploy steps (composer install --no-dev, npm run build, migrate --force, caches, storage:link); MySQL/MariaDB on the VPS
- [ ] Replace the stub tests (`tests/Feature/ExampleTest.php`, `tests/Unit/ExampleTest.php`) with real coverage (login, access control, image path-traversal guard, dashboard scoping)
- [ ] Real-browser QA of the mobile drawer (animation, backdrop/Escape close, no horizontal scroll at 375 and 768 px) and of the dashboard branding and hover effects ([[course-layout-responsive]])
- [ ] Course video workflow stays manual paste-URL-in-Markdown (no upload UI); R2 disk for video only if wanted
- [ ] Toko module (customers table, `auth:customer` guard, append-only ledger on its own domain) after LMS content; probably superseded by the course-package work
