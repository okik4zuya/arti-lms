---
name: purchase-production-checklist
description: Everything the researcher must set or check before automatic purchases run in production (env variable names, cron, queue, Xendit dashboard, Brevo, Cloudflare, migrations) and the sandbox end-to-end test steps
metadata:
  type: reference
  created: 2026-10-18 15:28
  updated: 2026-10-18 19:41
---

Names only; values are the researcher's (`.env` is never opened by Claude). Design in [[automatic-purchase-plan]]; Worker side in [[worker-changes-spec]]; server layout in [[domain-migration-arti-ai-id]].

## 1. `.env` variables
| Variable | Value / note |
|---|---|
| `PAYMENT_GATEWAY` | `midtrans` (default, the researcher's choice 2026-10-18) or `xendit`; an order keeps the gateway it was created with |
| `MIDTRANS_SERVER_KEY` | sandbox key (starts with `SB-Mid-server-`) while testing, live server key in production (Midtrans dashboard, Settings, Access Keys). The client key is not used: the buyer is redirected to the Snap page |
| `MIDTRANS_IS_PRODUCTION` | `false` for sandbox (default), `true` with the live key; picks `app.sandbox.midtrans.com` / `api.sandbox.midtrans.com` or the production hosts (`MIDTRANS_SNAP_URL`, `MIDTRANS_API_URL` override) |
| `XENDIT_SECRET_KEY`, `XENDIT_CALLBACK_TOKEN`, `XENDIT_API_URL` | only if `PAYMENT_GATEWAY=xendit` (Xendit needs a NIB, so unused for now) |
| `LICENSE_API_URL`, `LICENSE_ADMIN_TOKEN` | already set; production is `https://lisensi.arti.ai.id/v1` |
| `LICENSE_INSTALLER_URL` | **no longer used by mails or WhatsApp (2026-10-18)**: they point to the course Download page instead; the installer link lives only in `download-installer/index.md` |
| `MAIL_*` (Brevo) | **`MAIL_SCHEME` must be empty on port 587** (`smtps` there fails with "ssl://smtp-relay.brevo.com:587 SSL operation failed"; `smtps` belongs to port 465). `MAIL_FROM_ADDRESS` must be on `arti.ai.id` (was still `noreply@invait.id` in the sandbox run). **`MAIL_MAILER` must not be `log` in production: the purchase mail contains the key and the log would keep it** |
| `APP_URL` | the real `https://arti.ai.id` (links in mails are built from it) |
| `WHATSAPP_DRIVER` | leave `none` until a provider is chosen; `log` only records that a send would have happened |
| `SITE_SELLER_NAME`, `SITE_SELLER_CITY` | who sells ARTi, shown on `/kontak`; fill both before launch (electronic-trade rules ask for the seller's identity) |
| `ADMIN_MFA_REQUIRED` | set `true` before the panel is used in production |
| `SITE_LAUNCH_SOLD` | no longer needed: prices, quota and the manual count are on **Harga** in Filament |

## 2. Server
1. `php artisan migrate --force` (new: `orders`, `order_events`, `promotions`, `setting_changes`), then `php artisan permissions:sync` (new permissions `order.view`, `order.resend`, `order.refund`, `order.resolve`).
2. Cron as `www-data` already runs `schedule:run` every minute: it now also runs `orders:reconcile` every 10 minutes, `queue:prune-failed` daily and `orders:prune` daily (deletes unpaid orders older than a year, as the privacy page promises). The queue runs once a minute (`queue:work --stop-when-empty --max-time=55`), so the purchase mail arrives about a minute after payment; a permanent supervised worker is optional.
3. Create the course row `arti-framework` in Filament if it does not exist (orders still fulfil without it but flag `course_missing`).
4. Make sure at least one user has the `admin` role: failure and mismatch alerts go to admins.
5. Recovery commands live on the server: `php artisan admin:reset-password <email>` and `admin:reset-mfa <email>`.
6. `public/build`: run `npm run build` on the laptop and copy it (new CSS for the reset pages).

## 3. Gateway dashboard
- **Midtrans dashboard** (**Settings, Payment**, section "Payment notification URL"; the old "Configuration" menu no longer exists; leave "Recurring" and "Account linking" empty; the sandbox and production environments have separate settings): Payment Notification URL `https://arti.ai.id/webhooks/midtrans` (POST); the sandbox dashboard has its own setting, point it at a tunnel URL while testing. Finish, Unfinish and Error redirect URLs are sent by the app with every order, so they need no dashboard setting. Notifications are verified by `signature_key` and a re-read of the Status API, there is no token to copy.
- Individual accounts: BCA VA, Indomaret, BCA KlikPay, OCTO Clicks, Danamon, BRImo and UOB EZ Pay are **not** available (table in the Midtrans docs, 2026-10-18); cards, BNI/BRI/Permata VA, Mandiri bill, GoPay, ShopeePay, QRIS, Alfamart, Akulaku and Kredivo are. Snap shows only the methods activated on the account.
- Xendit only: Callbacks "Invoices paid" and "Invoices expired" to `https://arti.ai.id/webhooks/pembayaran` with `XENDIT_CALLBACK_TOKEN`.
- Cloudflare: do not challenge `/webhooks/midtrans` (or `/webhooks/pembayaran`): WAF skip rule or Bot Fight Mode exception, same as for `lisensi.arti.ai.id`.
- Refunds are done in the gateway dashboard; then use "Kembalikan dana" on the order in Filament to revoke the key and course access.

## 4. Brevo
DKIM, SPF and DMARC pass for `arti.ai.id` (done 2026-10-16). Send a real reset mail and a real purchase mail and look at them in Gmail and on a phone.

## 5. Sandbox end-to-end (researcher)
1. Put the Midtrans **sandbox** server key in the local `.env` (`MIDTRANS_IS_PRODUCTION=false`), `MAIL_MAILER=smtp` (Brevo), `QUEUE_CONNECTION=database`, expose the local site with a tunnel (ngrok is in Laragon) and set `<tunnel>/webhooks/midtrans` as the Payment Notification URL in the sandbox dashboard. Pay with the sandbox simulators (Midtrans docs, "Testing Payment on Sandbox").
2. Open `/beli`, pay with a test QRIS/VA, watch the order go `pending` to `paid` to `fulfilled` in Filament "Pesanan"; the mail arrives with the set-password link, key and download link.
3. Click the link, set a password, log in, open `/learn/arti-framework/download-installer`.
4. Send the notification again (Midtrans sandbox dashboard, transaction, resend notification): nothing new is issued.
5. Stop the queue and pay again: the order stays `paid`/`fulfilling` and `php artisan orders:reconcile` restarts it.
6. Real mail check of forgot-password: `/lupa-sandi` to your own address, click, set, log in.
7. Revoke the test license (Filament "Kembalikan dana") and delete test orders/users.

## 5b. Sandbox run 2026-10-18 (what happened, how to repeat it)
- **Tunnel:** ngrok sits in `C:\laragon6\bin\ngrok\ngrok.exe` (not on PATH; version 3.1.0 was too old, `ngrok update` gave 3.39.11). Free accounts get one auto-assigned static domain (a chosen name needs an upgrade). Run `ngrok http --url=<domain> --host-header=rewrite arti-lms.local:80` (Laragon vhost is `arti-lms.local`; `--host-header=rewrite` is needed for Apache). `APP_URL` = the tunnel URL, `APP_DOMAIN_MAIN` empty (routes answer only on that host when set).
- **Result:** Snap accepted the `callbacks` we send; the notification arrived and the order went `paid` then `fulfilled`; a card test payment worked (an earlier failed attempt shows as an `expired` event before `paid`, probably a denied card, not verified). The Snap page shows the merchant name from the Midtrans dashboard, not from our code.
- **Queue:** `queue:work` loads config once. A worker started before `.env` changed keeps the old values (mail scheme, From address); restart it (`queue:restart` with a supervised worker). Without any worker the order stays `paid` with a waiting `FulfillOrder` job. The production cron worker (`--stop-when-empty`) does not have this problem.
- **Mail:** a resent purchase mail has the same subject, so Gmail folds the repeated body behind the "•••" button; click it. A different subject for resends was proposed, not built.
- **Not yet run:** resend-notification idempotency, stopped-queue plus `orders:reconcile`, real forgot-password, download page after login, "Kembalikan dana" cleanup of the test licences (they hit the production Worker, `LICENSE_API_URL`).

## 6. Known limits
- A queued WhatsApp job carries the one-time link in its payload until it runs; failed jobs are pruned after a week.
- The status page `/beli/selesai/{id}` is reachable by anyone holding the id; it shows a masked email only.
- Midtrans' `challenge` fraud status on a card payment stays pending until the merchant accepts it in the Midtrans dashboard; the notification that follows settles the order.
- A pending order holds its launch place for 60 minutes; many abandoned checkouts can briefly hide places. Watch Filament "Pesanan".
- Timing: "known email" requests do slightly more work than unknown ones; the answer text and status are identical.

## Change log
- 2026-10-18 19:41 — Midtrans menu fixed (Settings, Payment), mail scheme/From notes, installer URL retired, sandbox run recorded (5b).
- 2026-10-18 15:28 — created.
