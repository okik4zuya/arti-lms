---
name: list-building
description: Mailing-list capture built 2026-10-15 (double opt-in, ARTi PDF download dialog with R2 installer, free-chapter sign-up cards on every page); decisions, files, what is unverified, owner steps
metadata:
  type: project
  created: 2026-10-15 17:35
  updated: 2026-10-16 00:11
---

Goal: collect emails without breaking the public promises ("gratis, offline, tanpa akun" for ARTi PDF; chapter 1 readable without an email). Context: [[landing-page-concept]], [[landing-scipeek-artipdf]], [[r2-storage]].

## Decisions (researcher, 2026-10-15)
- **Not gated.** Chapter 1 and the ARTi PDF download stay open; the sign-up is an offer next to them, never a wall. A hard gate was rejected: it contradicts the "no account" claim, the GitHub release is public anyway, and it would hurt `/artipdf` SEO.
- **Double opt-in**, confirmation email carries the `/baca` link (and SmartScreen tips when the source is `artipdf`).
- **Download counted by our own route** `/artipdf/unduh` (daily total only, no visitor data), then 302 to the installer on the **public R2 bucket `arti`** (`cdn.arti.ai.id`). Empty `ARTIPDF_DOWNLOAD_URL` falls back to the GitHub release page (button then opens a new tab).
- **Free-chapter capture on every page**, not only the last, because some readers stop at page 2. Source tag `baca-1..4` shows where people sign up. Pages 1-3: "Kirim tautannya" card with a "Tidak sekarang" dismiss; page 2 has its own hook line; page 4: "Kabari saya" before the buy card.
- Only the email is collected (no name, no IP). `source` is whitelisted in `Subscriber::SOURCES`.

## What exists
- Tables `subscribers` (email unique, source, consented_at, confirmation_sent_at, confirmed_at, unsubscribed_at, buyer_at) and `download_counts` (name, day, downloads; atomic upsert). Both migrated on dev.
- `SubscriptionController` (`POST /daftar` throttle 6/min, signed `GET /daftar/konfirmasi/{subscriber}` 7 days, signed `GET /berhenti/{subscriber}` no expiry). The answer is identical for new, pending and confirmed addresses; honeypot field `website`; resend at most once per 10 min; a mail failure is reported but never breaks the sign-up.
- `x-subscribe-form` component (works as a plain POST, `resources/js/subscribe.js` upgrades it to fetch and remembers subscribed/dismissed in localStorage), `ConfirmSubscription` mailable (HTML + text), `site/subscription.blade.php` (noindex status page), `/privasi` section "Daftar email".
- `/artipdf`: both buttons go through `artipdf.unduh`, `<dialog id="download-thanks">` (thanks, SmartScreen tips, form, close; once per session; the click is not prevented so the download always starts), GitHub link kept, optional SHA-256 line (`ARTIPDF_SHA256`).
- Tests: `SubscriptionTest`, `ArtiPdfDownloadTest`, additions to `FreeChapterTest`; related page tests still green (run per file).

## Update 2026-10-15 (later)
- Installer uploaded by the researcher to `https://cdn.arti.ai.id/downloads/artipdf/ArtiPDFSetup-1.0.7.exe`. Checked: HTTP 200, 62,863,855 bytes, ETag equals the local file's MD5. `config/site.php` now defaults `artipdf_download_url` and `artipdf_sha256` (337e15a2...f9b) to that file; the `.env` variables only override. On a new release change version, URL and hash together after uploading.
- `ConfirmSubscription` now `implements ShouldQueue` (3 tries, backoff 60/300 s) and the controller calls `->queue()`. Local queue is Redis ([[redis-setup]]), so testing it needs Redis running and `php artisan queue:work`. Tests run the queue synchronously (`assertQueued`, plus one test that executes the job into the array mailer).
- **Tests re-run and green** after the queue change (`SubscriptionTest` 15, `ArtiPdfDownloadTest` 7, `FreeChapterTest` 15, `ArtiPdfPageTest` 4; only the old PDO deprecation notices). Getting there needed two local fixes by the researcher: Laragon's MySQL 8.0.30 had lost its whole `lib` folder (`component_reference_cache.dll` missing, errno 126; restored from a fresh 8.0.30 package, data dir untouched), and port 3306 sat inside a Windows excluded range (3281-3380, same cause as the Vite port incident), cleared with an elevated `net stop winnat` / `net start winnat`. If MySQL refuses to start again: read `C:\laragon6\data\mysql-8\mysqld.log` first, then `netsh int ipv4 show excludedportrange protocol=tcp`.

## Brevo + Redis queue check (2026-10-15, standalone script, `.env` never opened)
- The researcher set up Brevo in `.env` (host `smtp-relay.brevo.com`, port 587, from `noreply@invait.id`, moved to `noreply@arti.ai.id` on 2026-10-16). Preflight showed two problems: `MAIL_SCHEME=smtps` on port 587 (Brevo's 587 is STARTTLS, so the scheme must be empty/`null` or `smtp`) and `QUEUE_CONNECTION=database` (not `redis`).
- With the scheme forced to null and the queue to redis (in-memory overrides), a confirmation job was queued on Redis, the worker ran it once and it finished with no failure; with `smtps` on 587 the job did not complete (released for retry). Inbox arrival of the first mail is for the researcher to confirm. The mail link host is `APP_URL` (`http://localhost` locally; production needs the real URL).
- Script pattern lives only in the session scratchpad (queue + `queue:work --once` + cleanup of its own Redis keys and the test subscriber).

## Browser end-to-end check (2026-10-15, puppeteer-core + Edge, 375 and 1100 px)
Setup that worked (nothing in `.env` touched): Vite dev on 5600, `C:\php\php.exe -S 127.0.0.1:8099 ../vendor/.../server.php` from `public`, `queue:work redis`, with process env overrides `MAIL_MAILER=log QUEUE_CONNECTION=redis APP_URL=http://127.0.0.1:8099` so mail goes to `storage/logs/laravel.log` instead of Brevo; the CDN request was stubbed as an attachment so nothing was downloaded. Everything was stopped and the test rows (`*@example.test`, `download_counts`) deleted afterwards.
- Verified: the download click opens the dialog and still requests the CDN file; invalid email shows the message, a valid one shows the answer; Esc closes, no reopen in the same session, no dialog for someone already subscribed; sign-up cards on pages 1-4, subscribed state hides them after reload, "Tidak sekarang" hides pages 1-3 cards but not the closing offer on page 4; the no-JS POST redirects back with the answer; no horizontal overflow; no JS errors. Through the worker: confirmation mails were produced, the signed confirm link confirms, an altered or unsigned link gives 403, unsubscribe works, a later confirm link does not revive an unsubscribed address.
- Found and fixed: `throttle:N,M` keys only on domain and IP, so `/artipdf/unduh`, `/daftar` and `/kontak` shared one bucket (downloads ate the sign-up allowance, and a campus NAT would hit it fast). Now named limiters `subscribe` (20/min per IP) and `download` (60/min per IP) in `AppServiceProvider`; `kontak.send` keeps `throttle:10,1`. Also fixed: the email field stayed visible after success (`display:grid` beat `hidden`), and "Tidak sekarang" no longer removes the page-4 offer. Tests: `SubscriptionTest` 16, plus a test that downloads do not use up the sign-up allowance.
- Dev-only noise, not ours: in Vite dev mode the self-hosted font URLs resolve against port 5600 and 404 (also on `/`); the production build is unaffected.

## Not verified
- A real phone and a real browser download of the 63 MB installer from the CDN (the browser run used a stubbed attachment). Real Brevo delivery was confirmed by the researcher (one mail arrived), not the double opt-in click path through production.
- Production: `npm run build` is needed (`public/hot` exists locally; `subscribe.js` is a new Vite entry), plus `php artisan migrate`, a queue worker under a supervisor, `APP_URL` set to the real site, and the Brevo domain (DKIM/SPF) verified for the sender.

## Owner steps
1. Upload `C:\python_tools\artipdf\dist\ArtiPDFSetup-1.0.7.exe` to bucket `arti` (e.g. `downloads/artipdf/`), then set `ARTIPDF_DOWNLOAD_URL` (and `ARTIPDF_SHA256`) in `.env`. The app's R2 token only reaches `arti-private`.
2. Connect SMTP (Brevo or Resend) via `MAIL_*`, run `php artisan migrate` in production, `npm run build`.
3. Consider rotating the R2 `arti-private` token: its key id and a secret prefix were printed in a tool result this session by a grep on `.env` (a slip; do not open `.env`).

## Filament "Daftar email" (built 2026-10-15, admin only)
- `SubscriberResource` at `/jamrud/subscribers`, gated by the new permission **`subscriber.manage`** (in `Permissions::ADMIN_ONLY`; support and learners get 403; run `php artisan permissions:sync` in production). List only, no create/edit. Columns: email (copyable, searchable), status badge (Aktif / Menunggu konfirmasi / Berhenti, computed by `Subscriber::status()` with scopes `active`, `pending`, `unsubscribed`), source label (`Subscriber::SOURCES` is now key => label), buyer tick, dates. Filters: status, source (multiple), buyer. Row actions: resend confirmation (pending only, respects the 10-minute window, uses the shared `App\Actions\Subscribers\SendConfirmation`, which the public sign-up now also uses) and delete (permanent, for erasure requests); bulk delete.
- Header actions: **Ekspor CSV (aktif)** (`SubscriberCsv`: only confirmed and not unsubscribed, columns email/sumber/dikonfirmasi/pembeli, cells starting with `= + - @` get a leading quote against formula injection) and **Cocokkan dengan pembeli** (`Subscriber::syncBuyers()`, matches case-insensitively against licenses that are not pending).
- Header widgets (in `Resources/SubscriberResource/Widgets`, so they do not land on the dashboard): stats (active, pending, unsubscribed, ARTi PDF downloads over 7 days with a sparkline) and a bar chart of active vs pending per source.
- **Buyers**: `IssueLicense` now calls `Subscriber::markBuyer($email)` after a successful issue (and after recovering a lost response), so buyers stop being sales-mail targets; a failed issue marks nobody.
- Tests: `FilamentSubscriberTest` (12), two additions in `IssueLicenseActionTest`; license and role tests still green. Checked in a real browser at 1280 and 390 px with throwaway data (a temporary admin user and `*@example.test` rows, all deleted): stats, chart, filters, badges, no JS errors, no horizontal overflow.

## Welcome series, broadcasts, unsubscribe (built 2026-10-16, uncommitted)
- **Series** (`App\Support\EmailSeries`, command `subscribers:send-series`, scheduled hourly in `routes/console.php`): 4 emails at 1, 3, 6 and 10 days after confirmation, never closer than 20 h apart, only for confirmed, not-unsubscribed, non-buyers. **Off by default**: `MAIL_SERIES_ENABLED=false`; `MAIL_SERIES_BATCH=100` caps one run (Brevo free = 300/day). Columns `subscribers.series_step` and `series_sent_at`; a step is claimed with one atomic update, so overlapping runs cannot double-send, and a queue outage gives the step back. Copy lives in `resources/views/mail/series/1..4.blade.php` (the researcher must read it before switching the series on): 1 hallucination (links to free page 3), 2 lost memory / Rithah (page 4), 3 what ARTi is + honest requirements (`/fitur`), 4 the offer, built from `App\Support\Offer` (launch price and "tersisa N dari 100" only when the sold counter is set, otherwise normal price; 7-day guarantee; honest limits; WhatsApp CTA). Only email 4 sells.
- **Mail classes**: `ListMailable` (queued, 3 tries, HTML + generated text twin via `MailText`, footer with the reason and the unsubscribe link, `List-Unsubscribe` + `List-Unsubscribe-Post` one-click headers), `SeriesMail`, `BroadcastMail` (Markdown body through `MarkdownRenderer`; a test copy has `[UJI]` in the subject and no working unsubscribe link). Dates in the footer are Indonesian.
- **Broadcasts**: Filament "Siaran email" (`BroadcastResource`, admin only, permission `subscriber.manage`). Tables `broadcasts` and `broadcast_deliveries` (unique per broadcast and person). Audience "Belum membeli" (default, for offers) or "Semua yang aktif"; both only confirmed, not-unsubscribed. Row actions: send test to my own address, send / continue sending (modal states the recipient count, "tidak bisa dibatalkan"), edit and delete **only while draft** (hidden once sent). `SendBroadcast` job records a person before queueing their mail, so a rerun or a resume after an outage never mails anyone twice; an outage stops the job and leaves the broadcast "sending" with a "Lanjutkan pengiriman" button.
- **Unsubscribe**: the link in an email now opens a page with a button (GET shows the form, POST unsubscribes), so mail scanners that open links cannot unsubscribe anyone; POST is exempt from CSRF (`bootstrap/app.php`, `berhenti/*`) because a mail provider's one-click call has no session, and the signed URL authorises it.
- Copy rule applied: the site and these mails say "ARTi", not "kami"; earlier sign-up texts were corrected.
- **Verified**: tests (`EmailSeriesTest` 13, `BroadcastTest` 16, `SubscriptionTest` 18, plus the earlier files); a real worker on the **database** queue wrote the series mails and the broadcast mails to the log with correct headers and skipped the buyer; the Filament broadcast list, create form (Markdown editor), send modal and the continue-sending flow, and the unsubscribe GET/POST, were driven in a browser. Test users and rows were deleted afterwards.
- **Not verified**: the Redis queue for these jobs, because the dev Redis refuses writes (see below); a real Brevo delivery of the series/broadcast mails; how the HTML looks in Gmail/Outlook (checked only as a 600 px browser render and the text twin).

## Local Redis refuses writes (found 2026-10-16)
The dev Redis (Laragon, 5.0.14 Windows port, `dir = C:\laragon6`) reports `rdb_last_bgsave_status = err` and `stop-writes-on-bgsave-error = yes`, so every write fails with `-MISCONF` (the queue worker loops on it). Not a code problem: the snapshot to disk fails (likely the low page file / memory on this machine, or the dump path). Options for the researcher: restart Redis and check free RAM, or accept no persistence in dev with `CONFIG SET stop-writes-on-bgsave-error no` (not persistent, and it is a shared instance, so the researcher decides). A few test jobs may still sit in the Redis queue: run `php artisan queue:clear redis` once Redis is writable again.

## Later (not built)
Per-subscriber detail page if ever needed; resending a single series email by hand; open/click tracking (deliberately absent: the privacy page promises no third-party analytics).

## Change log
- 2026-10-15 17:35 — created after the build.
