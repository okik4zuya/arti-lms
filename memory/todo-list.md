---
name: todo-list
description: Checklist of open tasks for arti-lms — no narrative, checkboxes only
metadata:
  type: project
  tier: T0
  created: 2026-09-08 21:36
  updated: 2026-10-19 06:34
---

### Landing checkout form and WhatsApp removal (2026-10-19), see [[landing-checkout-form]]

- [x] Buy form on the landing (`#checkout`), va/qris method cards to Snap `enabled_payments`, `orders.method_choice`, mobile buy bar, `/beli` redirects to the form; tests rewritten and green
- [x] Floating WhatsApp button, panel, `/kontak` WhatsApp form and every "Beli lewat WhatsApp" removed; support email hi@arti.ai.id in the footer, `/kontak`, status page
- [ ] Researcher: `php artisan migrate` (dev, then production) for `orders.method_choice`; `npm run build` before deploy (new entry `sticky-buy.js`)
- [ ] Researcher: with ngrok and `queue:work` running, pay once with QRIS and once with the VA card in the Midtrans sandbox; change the method on a second try; check Snap shows only the chosen methods
- [ ] Researcher: confirm `other_qris` and `shopeepay` are active in the Midtrans dashboard (else adjust `MIDTRANS_METHODS_VA` / `MIDTRANS_METHODS_QRIS`); decide whether DANA can be claimed on the card label
- [ ] Researcher: look at the form, the method cards and the buy bar in a real browser and on a phone (never seen rendered)
- [ ] Researcher: re-read `/syarat` and `/privasi` after the WhatsApp sentences were removed; set `SITE_CONTACT_EMAIL` only if the address should differ from hi@arti.ai.id
- [ ] Decide: the form note says a WhatsApp copy of the purchase mail is sent, but the driver is still `none`; either build the provider first or soften the note
- [ ] Decide: remove the now unused WhatsApp number and default-message fields from Filament "Pengaturan situs" (and their tests), and `SITE_CONTACT_WHATSAPP`, or keep them for the manual licence flow
- [ ] Run the full `php artisan test` suite once (only the touched files were run)

### Learner app UX, content cache and landing tweaks (2026-10-18 night), see [[learner-app-ux]]

- [x] Empty dashboard bug explained (browser signed in as another account) and fixed: `/lupa-sandi` no longer guest-only, set-password signs the browser out, the email is prefilled on login, the dead-link page offers login or logout
- [x] Avatar menu (name, email, Ubah password, red Logout), spinner on pressed buttons, password eye and live rules, `/app/password`, prev/next subchapter, instant "Tandai selesai", "Lanjutkan belajar" on the card, slim card and working progress bar, empty-state help, status page auto-refresh, skip link, Indonesian labels
- [x] Rendered course pages, manifests and meta cached in Redis (`CourseContent`, `CONTENT_CACHE_STORE`); tests pin `array`
- [x] Redis for cache, session and queue switched on in dev ([[redis-setup]])
- [x] Landing: "Free" badge on the ARTi PDF and SciPeek menu links, header buy button removed, hero "Coba gratis", SciPeek "Lihat demo" and title, "berbahasa Indonesia" dropped, problem section as three narrative paragraphs ([[landing-page-concept]])
- [x] Mayar.id researched and saved ([[mayar-fallback]]); not pursued
- [ ] Researcher: look at all of the above in a real browser (dashboard, avatar menu, `/app/password`, course page prev/next and instant mark, `/beli` spinner, set-password page, landing header badge, phone menu); the JavaScript was only covered by markup tests
- [ ] Researcher: `npm run build` before any deploy; decide Redis for production (own instance, or leave `CONTENT_CACHE_STORE` empty)
- [ ] Researcher: `php artisan queue:clear redis` if the 2 waiting `SeriesMail` jobs must not be sent before `queue:work` is started
- [ ] Optional UX, not built: undo "Tandai selesai", highlight "Selanjutnya" after marking, prefetch the next subchapter, SPA-style navigation (Turbo/htmx); the dashboard "Download ARTi" and licence block was declined for now
- [ ] Align the other "Baca bab 1 gratis" buttons (`/scipeek`, `/artipdf`, `/fitur`, 404, series mail 4) and the "wujud" wording (home `#wujud`, `/fitur` link, series mail 2) if wanted
- [x] 2026-10-18 22:15: pain 3 replaced (researcher's "copy paste" sentence), arti-docx/Zotero highlight block in section 4 with callout, section 4 title changed, Zotero line in requirements and `/fitur` ([[landing-page-concept]])
- [x] 2026-10-18 22:30: fourth pain (figures "terlalu AI") and section-4 block "Gambar dari data, bukan dari imajinasi AI" added; optional image of a real ARTi-figure diagram still undecided
- [x] 2026-10-18 22:37: problem section rewritten in the researcher's voice, closing line "Saya juga merasakannya, ..." added (bold)
- [ ] Decide: casual replacement for the h2 "Anda pasti pernah mengalaminya."; unify "kutipan" vs "referensi" in pain 1
- [ ] Researcher: look at the new section-4 block and problem section in a real browser and on a phone
- [ ] Image of a Word file with Zotero fields for the new block (LibreOffice preview hides fields, may be composed by hand); show it to the researcher before building
- [ ] Check the compare-table row "Menulis dan mengedit berkas naskah" (Claude AI: "Tidak, harus disalin") against current Claude AI abilities; no rival claims without evidence
- [ ] Answer the persona question (dosen new to Claude Code vs disappointed AI user; default assumed: disappointed) before the reptilian-brain table
- [ ] **Next session:** continue landing finalisation from step 1: ethics boundaries, then the six-stimulus table per section (hero first), one section per round ([[landing-finalisation-prompt]])
- [ ] **Deferred:** the purchase brainstorm (agenda items 1 to 6) in [[next-session-prompt]]
### Automatic purchase (built 2026-10-18, all uncommitted), see [[automatic-purchase-plan]]

- [x] Understand-then-brainstorm session done 2026-10-18 ([[next-session-prompt]]); sandbox run, UX fixes and Duitku fallback came out of it ([[automatic-purchase-plan]])
- [x] Midtrans sandbox end to end (card payment, notification, fulfilment, mail, set-password, login) worked 2026-10-18; recipe and gotchas in [[purchase-production-checklist]] 5b
- [x] Set-password page: rules stated once, one combined error; copy-paste link under the button in purchase and reset mails; terminal step and installer link removed from the purchase mail and WhatsApp templates
- [ ] Researcher: finish the sandbox steps (replay a notification, stopped queue then `orders:reconcile`, real forgot-password, Download page after login), then revoke every test licence with "Kembalikan dana" (they are on the production Worker) and delete test orders/users
- [ ] Researcher: restart the queue worker after any `.env` change; set `MAIL_FROM_ADDRESS` on `arti.ai.id`, `MAIL_SCHEME` empty on port 587
- [ ] Researcher: if the WhatsApp templates were ever edited in Filament "Pengaturan situs", update them by hand (they override the new defaults that dropped the terminal step and installer link)
- [ ] Researcher: check the merchant name shown on Snap (Midtrans dashboard, Snap Checkout / General Settings; the old name still showed in the sandbox)
- [ ] Researcher: add `permissions.deny` rules for `.env` in `.claude/settings.json` (`.claudeignore` exists but Claude Code ignores it)
- [ ] Decide: different subject for resent purchase mails (Gmail folds the repeated body behind "•••"); also add the copy-paste link to the `confirm-subscription` mail if wanted
- [ ] Backup gateway: Duitku researched, not built ([[duitku-fallback]]); ask support the five questions there only if Midtrans refuses the account

- [x] Orders, Xendit checkout (`/beli`), signed idempotent webhook, `FulfillOrder`, purchase mail, WhatsApp stage (driver `none`), atomic launch quota, refund, `orders:reconcile`, Filament "Pesanan"
- [x] Forgot password (`/lupa-sandi`, `/atur-sandi`), one-time set-password links (60 min / 48 h), admin and support excluded, `admin:reset-password` and `admin:reset-mfa`
- [x] Filament manual flow switched to one-time links (no password anywhere)
- [x] Prices and promo events editable in Filament ("Harga", "Promo"), audited, `Offer` is the single source
- [x] Garansi claim removed from `/`, `/fitur`, series mail 4 (mechanism kept); lifetime wording on those pages
- [x] `/syarat`, `/kontak`, `/privasi` researched and rewritten 2026-10-18 (refund policy, lifetime clause, Xendit and Brevo disclosures, seller block), see [[legal-review-notes]]
- [x] First set-password link has no time limit; dies when used or when the password changes (decision 2026-10-18)
- [ ] Researcher: read the rewritten `/syarat`, `/kontak`, `/privasi` in another session and confirm the refund conditions and time limits; ask a lawyer about the lifetime and shutdown clauses
- [ ] Researcher: fill `SITE_SELLER_NAME` and `SITE_SELLER_CITY` (seller identity on `/kontak`)
- [ ] Researcher, `~/.arti` repo: bring the EULA `LICENSE.txt` in line with `/syarat` (it still says refund "sesuai garansi" and a period from key issue)
- [ ] Researcher: confirm an already activated machine keeps working offline if the Worker is ever shut down (the `/syarat` lifetime clause says "kami berusaha")
- [ ] Researcher: confirm the lifetime prices (kept Rp99.000 then Rp199.000, one-time) and read the new copy on `/`, `/fitur`, series mail 4 and the purchase mail
- [ ] WhatsApp provider: brainstorm in a separate session (Cloud API with a dedicated number, or a third-party gateway); then a driver replaces `none`
- [x] Midtrans driver built and tested with `Http::fake` (gateway interface, Snap, Status API, signature check); Xendit kept as a second driver
- [x] Midtrans sandbox server key and notification URL set (sandbox run 2026-10-18)
- [ ] Researcher: ask Midtrans support whether an individual account may sell an e-course and software licences and whether NPWP is needed for QRIS/VA/e-wallet; production activation needs KTP and NPWP ([[purchase-production-checklist]])
- [x] Midtrans sandbox end to end ran 2026-10-18 and Snap accepts the redirect `callbacks`; the replay, reconcile and real forgot-password steps are the open item at the top of this section
- [ ] Researcher, Worker repo: silent `resend`, check `months: 1000`, optional idempotent `extend`; client shows "Lifetime" ([[worker-changes-spec]])
- [ ] Researcher, production: migrate, `permissions:sync`, env variables, Xendit callback URL, Cloudflare skip rule, `ADMIN_MFA_REQUIRED=true` ([[purchase-production-checklist]])
- [ ] Look at the new pages in a real browser: `/beli`, `/beli/selesai/...`, `/lupa-sandi`, `/atur-sandi/...`, Filament Pesanan, Harga, Promo (only HTTP/Livewire tested)
- [ ] Review `LicenseIssuanceResource` row action labels after the switch to links; `README` mentions
- [x] Learner change-password page for signed-in users (`/app/password`) still not built; "Lupa password?" covers the need — built 2026-10-18 ([[learner-app-ux]])
- [x] "Download" section in `resources/content/arti-framework` (`download-installer`, behind login and course access, link to the installer on the public CDN); deployed by the researcher 2026-10-18; its version, link, size and SHA-256 are updated per release

### Landing page (arti.ai.id), see [[landing-page-concept]]

- [ ] Listen to the explainer's music and watch it through once (only levels and 8 frames were checked) [[landing-explainer-storyboard]]
- [ ] Move or delete the unredacted `arti-dashboard.png` in the repo root (gitignored, but it shows the unpublished manuscript)
- [ ] Decide whether the dashboard screenshot should also hide the project name and file counts
- [ ] Media: record the real 1-2 min demo, make its poster, set `SITE_DEMO_VIDEO_EMBED_URL`
- [ ] Media: real hero screenshot (VS Code + Claude Code)
- [ ] Media: author photo / "Tentang penulis" section (undecided)
- [ ] Media: lift the staircase in `ad-story` (bottom ~250 px is Facebook UI)- [ ] Check at 375px the sections not yet checked (free chapter pages `/baca`, explainer video, dashboard image, icons); the solution, memory pair, ARTi PDF card and `/fitur` were checked
- [x] Rewrite `/fitur` per the agreed concept (stages, all tools grouped, honest "what ARTi does not do"); animated `model-skill-alat.mp4` placed there [[landing-page-concept]]
- [ ] Review the Indonesian example commands and the new copy on `/fitur`, and confirm the autoplay loop in a real browser
- [x] Top menu: Fitur, Harga (to `/#harga`), Baca bab 1 gratis, Kontak; `/harga` and `/unduh` removed with 301 redirects
- [x] ARTi PDF card in the free section (Windows only), screenshot and MIT licence prepared in the artipdf repo
- [ ] In the artipdf repo: commit and push `LICENSE`, `THIRD-PARTY-NOTICES.md`, screenshots and readme; release v1.0.7 so the landing's screenshot matches the download; delete `C:/arti-demo`
- [x] Page speed pass (self-hosted fonts, small logo and favicon, content-visibility with anchor safeguard, caching rules) [[page-speed]]
- [ ] Confirm gzip/brotli and cache headers on the real production host (local Apache lacks the modules), and test on a real phone
- [x] Redis wired for local dev: cache, session, queue (Predis, DBs 10/11) [[redis-setup]]
- [ ] Redis for production (own instance or keep database drivers), and check `/jamrud` and LMS login under Redis sessions
- [ ] `composer audit`: 4 advisories on laravel/framework 11.x; plan an upgrade
- [ ] Decide whether to hide `/artikel`, its sitemap entry and the footer "Umpan artikel" until the first article exists
- [x] Model + skill + tool illustration: 3 variants built, variant 1 placed in the Solution section with HTML legend and test
- [x] WhatsApp form on `/kontak`, admin-editable number (Filament Pengaturan situs), floating button [[contact-whatsapp]]
- [ ] Enter the WhatsApp number in the admin (or `SITE_CONTACT_WHATSAPP`) and run `php artisan migrate` in production
- [x] Floating button colour: kept the original `#25d366` with a white icon (contrast fixes were tried and rejected by the researcher); WhatsApp panel replaces direct links, `/kontak` kept as fallback [[contact-whatsapp]]
- [x] Default messages (purchase, general chat) editable in Filament Pengaturan situs [[contact-whatsapp]]
- [x] Rithah image replaced by the "benang terurai" illustration (`benang-terurai.webp`, source in `resources/media-src/landing/`) [[landing-page-concept]]
- [ ] On a real phone: open the panel from "Beli", send, and confirm the WhatsApp app opens with the text (only headless-checked)
- [ ] Fill `SITE_LAUNCH_SOLD` in the prod `.env` (`APP_DOMAIN_MAIN=arti.ai.id` is already set)
- [ ] Remove or disable the unused trial path (`/trial`, `arti-license trial`, `trial` tier) in the licensing Worker before deploy (see `~/.arti` licensing plan)
- [ ] Legal review of `/syarat` and `/privasi` including the 7-day refund line; fix README "nine skills" to ten
- [ ] Revise ebook chapter 3 wording ("not a product, not a tool to install") to match the paid-bundle story
- [ ] Decide: name "ARTi Workflow" vs "ARTi Framework" outside the ebook; offline grace after activation
- [x] Landing, `/harga`, `/fitur`, mobile header, free chapter 1 (`/baca`), icons, example cards, OG image, 600px column, tests
- [x] 40 s explainer (storyboard approved, rendered, lazy `<video>` in the demo section) and the dashboard screenshot with the manuscript pane pixelated, in the licence card

### Free-tool pages, see [[landing-scipeek-artipdf]]

- [x] Keyword audit and concept for `/scipeek` and `/artipdf`; installer ARTi PDF 1.0.7 built (62.9 MB)
- [x] Build `/scipeek` (page, media, tests)
- [ ] Look at `/scipeek` in a real browser (video playback, phone) and decide the copy
- [x] ARTi PDF v1.0.7 released; `/artipdf` built (tests green, images and layout still to look at in the real dev site)
- [ ] Decide whether the "Summarize with AI" right-click (needs the ARTi dashboard) should ship in the public installer

### List building, see [[list-building]]

- [x] Subscribers table, double opt-in sign-up, confirmation mail, unsubscribe, privacy section, tests
- [x] ARTi PDF download route (counted, 302 to R2 or GitHub) and thank-you dialog with sign-up
- [x] Sign-up card on every free-chapter page (`baca-1..4`)
- [x] ARTi PDF 1.0.7 installer uploaded to the public `arti` bucket; URL and SHA-256 are the config defaults (checked against the CDN ETag)
- [x] Tests re-run green after the queue change (MySQL restored: `lib` folder and port 3306 exclusion fixed, see [[list-building]])
- [ ] Connect SMTP (Brevo/Resend), start Redis and `php artisan queue:work`, send one real confirmation and click it; in production also `php artisan migrate`, `npm run build` and a queue worker (supervisor/cron)
- [x] Browser-checked the dialog and cards at 375 and 1100 px, plus confirm/unsubscribe through the Redis worker (2026-10-15); bucket-sharing throttle bug found and fixed [[list-building]]
- [ ] Look at `/artipdf` and a `/baca` page once on a real phone, and do one real download of the installer from the CDN
- [ ] Rotate the R2 `arti-private` token (key id and secret prefix were shown in a tool result)
- [x] Filament subscribers resource (list, filters, CSV export, widgets, resend, delete) and buyer marking on license issue [[list-building]]
- [ ] Run `php artisan permissions:sync` in production (adds `subscriber.manage`, admin only)
- [x] Welcome series (4 emails, hourly scheduler, off by default), broadcasts in Filament, one-click unsubscribe headers and the two-step unsubscribe page, all tested [[list-building]]
- [ ] Read the four series emails in `resources/views/mail/series/` (or send yourself a test) and edit the copy; only then set `MAIL_SERIES_ENABLED=true` in production
- [ ] Production: run `php artisan migrate` (two new migrations), make sure `php artisan schedule:run` is on cron (hourly series, daily license sync) and a queue worker is supervised
- [x] Dev Redis writes work again (checked 2026-10-18, no `-MISCONF`)
- [ ] `php artisan queue:clear redis` (2 `SeriesMail` jobs waiting), then re-test the series and a broadcast through the Redis queue and Brevo
- [ ] Send a real test of a series email and a broadcast through Brevo and look at it in Gmail (and a phone)

### Landing finalisation (next session), prompt in [[landing-finalisation-prompt]]

- [x] Commit the large uncommitted set in logical groups (landing and /fitur, media, menu and redirects, speed, Redis and Predis, tests, memory); nothing from the last sessions is committed
- [x] Audit every landing-related page for copy, consistency and facts: `/`, `/fitur`, `/kontak`, `/privasi`, `/syarat`, `/baca` pages, `/artikel`, header, footer, 404
- [x] Per-page SEO: unique title and description, canonical, OG image, sitemap and robots, `lang`; then accessibility and keyboard checks
- [x] Sync `landing-page-copy` to the final Blade copy, and retire or update stale notes in `landing-page-info`
- [ ] Real-browser checks that were only emulated: autoplay loop on `/fitur`, anchors, phone widths, speed on a real phone (375, 768, 1100 px were checked with puppeteer)
- [ ] Legal review of `/syarat` and `/privasi` using [[legal-review-notes]]; confirm the licensing Worker sends only the key and a hashed machine code
- [ ] Decide whether to hide `/artikel` and the footer feed link (recommended: hide until the first article; empty index is already noindex)
- [ ] Decide the app name: the learner app still says "ARTi LMS", the site "ARTi Framework"
- [ ] artipdf repo (not touched): push LICENSE, notices and screenshots, release v1.0.7, delete `C:/arti-demo`

### Content authoring progress (arti-framework, 18 chapters) — dedicated, most-active section

Rules for drafting: see [[content-authoring-workflow]]. Narrative/status detail (why a chapter is
blocked, style decisions made while drafting it, image notes): see [[content-authoring-progress]].
One line per chapter here; sub-bullets are the subchapters actually in `manifest.yaml` for that
chapter (not the full imagined scope).

#### Pendahuluan
- [x] 1. Kenapa AI Belum Memuaskan (Halusinasi & Memori)
  - [x] 2 Masalah: Halusinasi dan Memori (folder renamed `01-2-masalah-utama`, matches manifest)
  - [x] Solusinya (folder renamed `02-solusi-parsial`, matches manifest)
  - [x] Halusinasi = Meruntuhkan Kredibilitas Akademik (`03-halusinasi-meruntuhkan-kredibilitas-akademik`, new, inserted 2026-09-11)
  - [x] Hilang Memori: Pekerjaan jadi Tak Berarti (folder `04-hilang-memori-tak-berarti`, renumbered from `03-`; pemintal-benang parable from `resources/inbox/pemintal-benang.md`, image is the user's own)
- [ ] 2. Claude dan Claude Code (source outline: Ch.2 in `abstraksi-konten-dan-outline.md`, several `##` stubs still empty there)
  - [x] Taaruf (`05-taaruf`, renumbered from `04-`)
  - [x] Claude: Model dengan Branding Logika (`06-claude-model-dengan-branding-logika`, renumbered from `05-`; includes research-model benchmark table)
  - [x] Masih Ada yang Kurang (`07-masih-ada-yang-kurang`, renumbered from `06-`)
  - [x] Claude Code (`08-claude-code`, renumbered from `07-`; includes Claude Code vs Claude AI vs NotebookLM vs LLM-chat-lain comparison table)
  - [ ] Claude bisa pakai skill agar hasil lebih maksimal (outline stub, no bullets yet)
- [x] 3. Why ARTi Framework (source outline: Ch.3 in `abstraksi-konten-dan-outline.md`, all 11 subchapters drafted)
  - [x] Solusi dari Masalah Saya Selama Ini (Mungkin Anda Juga) (`09-solusi-dari-masalah-saya`, renumbered from `08-`)
  - [x] 3 Filosofi (`10-3-filosofi`, renumbered from `09-`)
  - [x] Menjadikan Pekerjaan Berarti (`11-menjadikan-pekerjaan-berarti`, renumbered from `10-`)
  - [x] Arsitektur (`12-arsitektur`, renumbered from `11-`; includes ASCII-art architecture diagram)
  - [x] Sistem Memori (`13-sistem-memori`, renumbered from `12-`)
  - [x] Sistem Referensi (`14-sistem-referensi`, renumbered from `13-`)
  - [x] Scratchbook: File Sakti Anti Halusinasi (`15-scratchbook`, renumbered from `14-`)
  - [x] Fitur-fitur ARTi (`16-fitur-fitur-arti`, new)
  - [x] Perbandingan ARTi Framework dengan yang Lain (`17-perbandingan-arti-framework`, new)
  - [x] dan Lainnya (`18-dan-lainnya`, renumbered from `16-`)
  - [x] Cara Instalasi ARTi Framework (`19-cara-instalasi-arti-framework`, new)
- [x] Fix the dangling wiki-link to the old "Solusinya" title in `07-masih-ada-yang-kurang/index.md` (now points at "Solusi Parsial", fixed 2026-09-11)
- [ ] Verify all renumbered folders render + manifest resolves once MySQL is running again (DB was down this session, `php artisan cache:clear` failed with a connection-refused error — manifest.yaml is cached 1hr, so the sidebar may still show pre-edit slugs until either the cache expires or clear succeeds)

#### Amati
- [ ] 4. The Researcher Profile
- [ ] 5. Mapping the Gap, Not Guessing It
- [ ] 6. Reading a Journal Tactically

#### Replikasi
- [ ] 7. Feasibility Before Ambition
- [ ] 8. The Scratchbook Discipline

#### Tambah Inovasi
- [ ] 9. Scoring Novelty Honestly
- [ ] 10. Building the Manuscript Blueprint
- [ ] 11. Matching Voice and Technical Depth
- [ ] 12. The Iteration Log and the Novelty Floor
- [ ] 13. Cover Letters and the Submission Checklist
- [ ] 14. Answering Reviewers Without Losing the Paper
- [ ] 15. After Acceptance: Feeding the Next Amati

#### BONUS
- [ ] 16. Toolkit Quick Reference
- [ ] 17. Panduan Instalasi
- [ ] 18. Prompt Guide

### ARTi App (see [[arti-app-architecture]])

- [x] Phase 1: public routes on the main domain (landing, fitur, harga, unduh, kontak, privasi, syarat), flat-file articles + tag/feed/sitemap/robots, `Route::domain()` via `config('app.domains')`, shared `tokens.css`, `/app` + `/dashboard` 301
- [x] Feature/unit tests for every new route (27 DB-free tests pass)
- [x] Idempotent dev `DatabaseSeeder` (admin + learner, refuses production) with `DatabaseSeederTest`
- [x] Start MySQL, run `php artisan test` — full suite green after phase 1 (2026-10-13)
- [ ] Run `php artisan db:seed` on the dev DB (seeder verified on the testing DB only; admin@example.com / test@example.com, both `password`)
- [ ] Browser-check an authenticated flow after phase 1 and the Filament 4 upgrade (login → `/app` → a `learn/*` page; `/jamrud` visual look under v4)
- [x] Upgrade Filament 3 → 4 (v4.15.2, done 2026-10-14; resource + relation manager converted by hand, covered by `FilamentCourseResourceTest`)
- [x] Filament license page stage 1: issue, encrypted key, WhatsApp message, sync, tests (Http::fake, suite 140 green) [[license-admin]]
- [x] Look at `/jamrud/license-issuances` in a browser against the real Worker (2026-10-15, local dev pointed at production)
- [ ] `php tests/manual/verify_license.php` needs a local `wrangler dev` (RAM); optional now that the production smoke test passed
- [x] Deploy the licensing Worker to Cloudflare: production keys, D1, secrets, `https://lisensi.arti.ai.id/v1 (moved from lisensi-arti.invait.id)`, smoke test passed [[license-worker-setup]]
- [x] Python clients send their own User-Agent (Cloudflare 403s `Python-urllib`): `arti_admin.py`, `~/.arti/tools/arti-license/cli.py`; client config and `pubkeys.json` are production [[license-worker-setup]]
- [ ] Empty the `LICENSE_*` lines in the local `.env` (researcher edits it), revoke the test licenses issued from dev, delete `%TEMP%\arti-prod-client`
- [ ] Production: cron `schedule:run` for the daily `licenses:sync`; restrict `/v1/admin/*` to the VPS IP; D1 export + restore drill [[license-worker-setup]]
- [ ] Run `php artisan migrate` (dev, then production) and set `LICENSE_API_URL=https://lisensi.arti.ai.id/v1`, `LICENSE_ADMIN_TOKEN`, `LICENSE_INSTALLER_URL` in the production `.env`; decide where the paid installer is hosted
- [x] Decided 2026-10-15: revoke does not stop an already-activated machine; accepted for launch [[license-admin-roadmap]]
- [x] License admin phase A built 2026-10-15: roles admin/support + permissions, default-deny for courses/settings, "Diterbitkan oleh", History, Edit contact, quick filters, drop tier, compact issue modal [[license-admin-roadmap]]
- [ ] Run `php artisan permissions:sync` in production after deploy (creates roles and permissions, idempotent); give support staff the `support` role
- [ ] Phase A leftovers: "Perlu ditinjau" filter waits for phase B; the "Kelola" menu only holds Ubah kontak and Riwayat until C/D add the rest; revoke decision taken: accept that it does not stop activated machines, add one sentence to `/syarat`
- [ ] Paid installer hosting decided (not built): Cloudflare R2 with short-lived download links issued by Laravel, then set `LICENSE_INSTALLER_URL`
- [x] License admin phase B built 2026-10-15: payment proof, verification, review queue, `/privasi` sentences [[license-admin-roadmap]]
- [x] `R2_BUCKET` switched to `arti-private`, connection test passed 2026-10-15 [[r2-storage]]
- [ ] Delete the old test proof object from the public `arti` bucket (`license-proofs/8HAL2SOt...jpg`) and re-attach or ignore the two dev rows whose proofs are not in `arti-private` [[r2-storage]]
- [ ] Payment proofs on Cloudflare R2 (code ready and connection tested 2026-10-15; default stays `local`): researcher creates a private bucket and an API token limited to it, runs `composer require league/flysystem-aws-s3-v3 "^3.0"`, sets `PROOFS_DISK=r2`, `R2_ACCESS_KEY_ID`, `R2_SECRET_ACCESS_KEY`, `R2_BUCKET`, `R2_ENDPOINT` (`https://<account>.r2.cloudflarestorage.com`) in the production `.env`; move any existing files from `storage/app/private/license-proofs/` into the bucket under the same keys; R2 then also covers backup of proofs [[license-admin-roadmap]]
- [x] All license migrations (plus the MFA columns and the `account_password` drop) applied on dev 2026-10-15; production still needs `php artisan migrate`
- [x] License admin phase C built 2026-10-15: `LicenseApi` extend/revoke/deactivate, Release device, Extend, Rotate key, Revoke/Undo, Delete pending, Tandai ditinjau [[license-admin-roadmap]]
- [ ] Browser-check phases A to D on the dev site (needs free RAM; the reset-password key modal fix was only test-verified); then one real round against the Worker with a test license (extend, release, rotate, revoke, undo), revoked afterwards
- [ ] Add a sentence to `/syarat`: revoking stops renewals and new activations but not machines already activated [[license-admin-roadmap]]
- [x] License admin phase D built 2026-10-15: Reset account password, `account_password` dropped, template fixed [[license-admin-roadmap]]
- [ ] `composer require bacon/bacon-qr-code:^3.1` to replace the `*` constraint in composer.json (researcher runs it)
- [ ] MFA postponed by the researcher: set `ADMIN_MFA_REQUIRED=true` in the production `.env` before the panel (especially extend, revoke, rotate) is used there; set up the authenticator and save the recovery codes first [[license-admin-roadmap]]
- [ ] Worker repo (needs the researcher's OK): `resend` without email, `extend` idempotent via `order_ref`; `arti_admin.py` should show the HTTP code on a non-JSON error body
- [x] MFA code built (authenticator app and recovery codes), switched off by decision; turning it on is the `ADMIN_MFA_REQUIRED` item above
- [ ] Backups for the license record: daily MySQL off the VPS, back up `APP_KEY`, nightly encrypted export of `license_issuances`, one restore drill
- [ ] Automatic purchases (gateway webhook in Laravel, `FulfillOrder`, one email sender, idempotent Worker `extend`, atomic first-100 count, set-password link): prerequisites in [[license-admin]]
- [ ] Add one sentence to `/privasi`: buyer email and name are kept at both the licensing Worker and the app ([[legal-review-notes]])
- [ ] Write the first real articles in `resources/articles/` (site shows the empty state now)
    // ... 180 lines omitted
  from `resources/inbox/260906_OK_narasi-ai-bar-bar-dan-analogi-alzheimer.md`. Renumbered every
// ... 179 more lines (total: 272)
- [x] Learner change-password page (`/app/password`: current, new, confirm; link on the dashboard; one sentence in the WhatsApp template); proposed 2026-10-15, researcher has not answered — built 2026-10-18 ([[learner-app-ux]])

### Domain migration arti.invait.id → arti.ai.id [[domain-migration-arti-ai-id]]
- [x] Steps 1-8 done 2026-10-15/16: DNS, shared VPS vhost, Cloudflare rules, `cdn.arti.ai.id`, `lisensi.arti.ai.id`, Brevo, prod `.env`, end-to-end tests, 301 redirect, old Worker route removed, OG image and explainer re-rendered
- [ ] SciPeek 1.0.1 release (separate session, in the SciPeek repo): bump version, ZIP, push, submit to the Web Store
- [ ] Remove the old R2 custom domain `cdn-arti.invait.id` from bucket `arti` once nothing links to it
- [ ] Enable 2FA (authenticator) on the admin account of `/jamrud`; consider a Cloudflare rate limit on `/jamrud/login`
- [ ] Re-enable Bot Fight Mode on arti.ai.id only after a WAF skip rule for `lisensi.arti.ai.id` (it can block the Python client)
- [ ] Move arti-lms to a dedicated VPS at the first paying buyer or when the shared one hurts (1.9 GB RAM, swap in use) [[domain-migration-arti-ai-id]]
- [ ] `DomainRoutingTest::test_generated_urls_use_the_main_domain` fails even on a clean stash (pre-existing, unrelated to the domain move)
