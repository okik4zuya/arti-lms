---
name: mayar-gateway
description: Mayar chosen as the payment gateway 2026-10-23 (hosted Mayar page, Midtrans driver kept): API facts, verified webhook payload, what was built and tested in the sandbox, the problems met and their fixes, production steps and open questions for Mayar support
metadata:
  type: project
  created: 2026-10-23 11:34
  updated: 2026-10-23 11:34
---

Replaces `mayar-fallback` (research of 2026-10-18, deleted). Researcher decision 2026-10-23: the Midtrans application was complicated, so **Mayar becomes the gateway, hosted "Mayar page" only** (no embedded or native checkout); **the Midtrans driver stays** (switch back with `PAYMENT_GATEWAY=midtrans`); **fees are borne by the seller**; nobody has bought yet. Order and fulfilment design: [[automatic-purchase-plan]]; production steps: [[purchase-production-checklist]]; fallback of last resort: [[duitku-fallback]]. Sources are docs.mayar.id (`llms.txt` lists every page) and the researcher's dashboard; the dashboard page `headless-checkout` needs a login and was not readable.

## What Mayar is (account and fees)
- Commerce platform (merchant-of-record claim); individual account needs KTP, selfie with KTP, bank book page; **NPWP optional**. Prohibited: IP-infringing goods, mailing lists, crypto/NFT, adult. Payouts can pause during reviews; chargeback threshold 0.4%; accounts can be blocked without notice (from the old research, confirm with support).
- Fees (docs.mayar.id/mor/fees): channel QRIS 0.7%, e-wallet 1.5% (Jenius 2%), VA Rp4.000, card 2.6% + Rp2.000, minimart Rp5.000-7.500; **platform fee for Payment Request 1.5% (Starter) / 1% (Business)**; withdrawal Rp2.775 flat. The "fee borne by buyer" setting does **not** apply to the Payment Request endpoint (or Dynamic QR), so the seller pays. Rp199.000 by QRIS about Rp4.378, by VA about Rp6.985 (Midtrans about Rp1.546 / Rp4.440). VAT included or not, and settlement/hold time: **not stated**.
- Dashboard: sandbox `web.mayar.io`, production `web.mayar.id`; menu Integrasi, "API Keys & Token" (key type **Read & Write** needed, Read Only is GET only; Webhook Token shown there), "Webhook" (Url Webhook, TEST URL, Notifikasi Event), "Headless Custom Checkout". **Changing the domain or subdomain requires a new API key**, so fix the subdomain and shop name first. Sandbox shows the shop as `arti.myr.lat` and merchant "Oki".

## API facts (v2; v1 died 2026-10-01)
- Base `https://api.mayar.id/hl/v2` (production), `https://api.mayar.io/hl/v2` (sandbox); keys only work in their own environment; `Authorization: Bearer <key>`; response envelope `statusCode`, `messages`, `data` (the body `statusCode` is authoritative, some endpoints answer HTTP 200 with another code).
- **Used:** `POST /payments/create` (Single Payment Request): `name`, `amount`, `email`, `mobile`, `description`, `redirectUrl`, `expiredAt` (ISO UTC); returns `data.id` (payment request id = our `gateway_ref`), `transactionId`, `link`. `GET /payments/{id}`: `status` `paid`/`unpaid`/`closed` (no `expired`), `amount`, `transactions` (undocumented). There is **no customer-name field and no order-id field**; Mayar then shows the request title as the customer name (webhook `customerName` was "ARTi Framework"), so its own emails greet the wrong name.
- `paymentMethod` (`qris`, `va/bni` ...) only picks the code returned; **the hosted page still shows every enabled method**, so the landing method cards cannot narrow it. A bad `redirectUrl` returns 400; 409 duplicate; 429 duplicate within a minute.
- **Not used, but known:** `GET /transactions/{id}` (statuses `paid`, `unpaid`, `created`, `expired`; `paymentMethod`; `paymentLink.id`; no VA number or code); `GET /payment-channels`; `POST /qr-codes/create` (dynamic QRIS, returns `qrString`/`url`, but no id, expiry or webhook, so unusable for reconciliation); Invoice endpoint (no `redirectUrl` documented); sandbox-only `POST /payments/simulate` (body undocumented; the sandbox pay page has a **"simulate payment" button**, which is what was used).
- Dashboard "headless checkout" offers three routes: Hosted Mayar page (used), Embedded Checkout (overlay modal, only for static payment links set up in the dashboard, so not per-order), Custom Native Checkout (own UI for QRIS/VA/e-wallet choice and status; endpoints not in public docs, fees/plan unknown, "Upgrade ke Mayar Pro" is shown in the sidebar). Revisit only if a native UI is wanted; ask for the prompt text under that card.

## Webhook (verified in the sandbox 2026-10-23)
- One URL per account, `POST` JSON, events `payment.received` (used), `payment.reminder`, membership/shipper events. **Body example (real):** `event: "payment.received"`, `data.id` = `data.transactionId` (transaction id), **`data.productId` = payment request id = our `gateway_ref`**, `data.status` `SUCCESS` but `data.transactionStatus` `created` (so the body is never trusted for status), `amount`, `paymentMethod` `QRIS`, `productDescription` (carries our order public id), `customerEmail`, `nettAmount`.
- Mayar has a dashboard **Webhook Token** ("verifies the webhook comes from Mayar"); the docs do not name the header. We therefore accept the token in any request header (bare or after `Bearer `) or a body `token`; a mismatch is logged as header names only. Leaving `MAYAR_WEBHOOK_TOKEN` empty skips it. Layer two is our own **secret in the URL path**.
- **The URL must be `https://<host>/webhooks/mayar/<MAYAR_WEBHOOK_SECRET>`**; the researcher first registered `/mayar/<secret>` (missing `webhooks/`), got 404 and a FAILED test, then fixed it. TEST URL sends `event: "testing"`: we answer 200 `ignored`.

## Built (all uncommitted at 2026-10-23)
- `app/Services/Payments/MayarClient.php` (+ `MayarException`): create, status read, `normalise` (paid → PAID, closed → EXPIRED, else PENDING; paid amount = the request's amount), `validSecret`, `validToken`; `getInvoice` finds the order's public id through `orders.gateway_ref` in our own table, never from Mayar.
- `app/Http/Controllers/MayarWebhookController.php`: secret, token, configured, event filter, order by `data.productId`, status re-read from the API, payment method from the body (informational only), `SettleInvoice`; 503 on an unreachable Mayar so Mayar retries.
- `PaymentGateways::DRIVERS['mayar']`, route `POST webhooks/mayar/{secret}` (throttle `webhook`), `config/services.php` block `mayar`: `MAYAR_API_KEY`, `MAYAR_API_URL` (default sandbox unless **`MAYAR_IS_PRODUCTION=true`**), `MAYAR_WEBHOOK_SECRET`, `MAYAR_WEBHOOK_TOKEN`.
- `tests/Feature/MayarGatewayTest.php` (12 tests, passing). **Lesson:** tests must pin every `.env`-dependent config (`services.mayar.webhook_token => ''`); six tests failed with 403 once the researcher's real token was in `.env`.
- Landing and legal pages no longer name any gateway ("penyedia pembayaran berlisensi di Indonesia"; product line "e-course ARTi Framework dengan tools pendukung"; method card "Virtual Account / e-wallet"; closed notice "Verifikasi akun pembayaran kami sedang berjalan"); `LegalPagesTest`, `MidtransGatewayTest` (now asserts the name is absent) and `CheckoutTest` updated. Name the provider again in `/privasi` once Mayar is live and the legal entity is verified.

## Sandbox run 2026-10-23 (what happened)
- Tunnel: ngrok static domain `uncreditably-tamest-torie.ngrok-free.dev` (`ngrok http --url=<domain> --host-header=rewrite arti-lms.local:80`). `/beli` redirected to `https://arti.myr.lat/invoices/<slug>`, then Pay, `select-channel`, QRIS, `pay/<transactionId>`, **simulate payment** button, back to `/beli/selesai/<public id>`.
- Webhook arrived and was accepted; order `paid` (Rp99.000, launch tier); the queue is **Redis** in dev, so the order stayed `paid` until `queue:work` ran; then the ARTi purchase mail arrived. **Mayar sent 4 emails** of its own (payment request, "Selesaikan Pembayaran", "Pembayaran Diterima", "Resi Pembayaran dan Akses Produk") next to our 1 (the subject "Akses Produk" may confuse buyers).
- Set-password page first failed with Redis `MISCONF` (low RAM, see [[redis-setup]]); after Redis recovered the link worked ("atur password aman").
- Machine notes: MySQL could not start because Windows reserved port 3306 ([[php-binary-and-shell]]).

## Not yet verified
- Webhook resend (retry in Mayar's webhook history) creates no second key or mail; login and the Download page after the set-password; the order reaching `fulfilled`; revoking the test licence ("Kembalikan dana") and deleting test orders/users (the licence is on the production Worker).
- The header name of the Webhook Token (check `storage/logs/laravel.log` for "Mayar webhook token mismatch" in production).
- A real payment in production; whether Mayar offers refunds from its dashboard (the "Kembalikan dana" flow assumes refunds happen in the gateway dashboard).

## Decisions and ideas pending
- Remove the VA/QRIS method cards and the `metode` validation (agreed, **not done yet**: Mayar ignores the choice).
- A sentence on the buy and finish pages: Mayar sends payment receipts, the ARTi mail with account, key and download comes separately (check spam). Alternatives: omit `email` from the request (Mayar's page may ask for it anyway), a dashboard notification setting, or ask support.
- Optional: store `transactionId` and read `GET /transactions/{id}` for a clear `expired` status and the payment method; `closed` on a payment request can also mean closed by hand.
- Production Redis: queue on `database`, session/cache on Redis only with safe settings ([[redis-setup]]).

## Questions for Mayar support (bantuan@mayar.id)
1. Hold time before withdrawal, and is VAT in the fees? 2. How are refunds done? 3. Can transactional emails be turned off or branded? 4. Header name of the Webhook Token. 5. Is a software licence/e-course sold through a Payment Request charged 1.5% or the 4% digital-product rate? 6. Can the VA number or QR be returned by API for our own page (Custom Native Checkout)?

## Change log
- 2026-10-23 11:34 — created from the Mayar session; supersedes `mayar-fallback`.
