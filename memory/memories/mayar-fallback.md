---
name: mayar-fallback
description: Mayar.id researched 2026-10-18 as a possible fallback gateway next to Duitku; fees vs Midtrans, individual requirements (NPWP optional), API gaps, risks, support questions. Not built, not pursued this session.
metadata:
  type: reference
  created: 2026-10-18 20:20
  updated: 2026-10-18 20:20
---

Researcher decision 2026-10-18: save the research, **do not pursue it now**. Midtrans stays the gateway ([[automatic-purchase-plan]]); other fallback: [[duitku-fallback]]. Sources are undated pages on docs.mayar.id and docs.midtrans.com; confirm everything with the vendors before relying on it.

## What Mayar is
A commerce platform that calls itself merchant of record, built on PSPs (Xendit and/or Doku). Higher fees than a plain gateway (its own docs say MoRs charge more). Sales tax is the merchant's own job.

## Individual account
KTP, selfie with KTP, bank book page; **NPWP optional** (Midtrans and Duitku both ask for it). Review often done in hours. Software, SaaS and online classes are named as accepted; prohibited: IP-infringing goods, mailing lists, crypto/NFT, adult, and similar. Payouts pause during periodic reviews; chargeback threshold 0.4%, chargebacks possible up to 120 days; violations can block the account without notice.

## Fees (docs.mayar.id/mor/fees)
- Channel: QRIS 0.7%; e-wallet 1.5% (Jenius 2%); VA Rp4.000; card 2.6% + Rp2.000 (excl. tax); minimart Rp5.000-7.500.
- Admin (platform) fee: Invoice/Payment Link 1.5% Starter, 1% Business; Digital Download/E-Book/Online Class/Subscription 4% Starter, 3% Business.
- Withdrawal Rp2.775 flat. Fees can be charged to the buyer (Settings, Payment Methods). Whether VAT is included is not stated.
- Example, QRIS via invoice (1.5%): Rp99.000 costs Rp2.178 and Rp199.000 costs Rp4.378; Midtrans (0.7% + 11% VAT) about Rp769 and Rp1.546. VA: Mayar Rp5.485 / Rp6.985, Midtrans Rp4.440. Midtrans per-method rates are not published beyond doc examples (VA Rp4.000, card 2.9%, QRIS 0.7%; BCA/Indomaret negotiated).

## API facts and gaps (v2)
- `POST https://api.mayar.id/hl/v2/invoices/create` (sandbox `api.mayar.io`), `Authorization: Bearer <key>`. Fields: name, email, mobile, items[{quantity, rate, description}], description, expiredAt, tax, paymentMethod, extraData. Response: id, transactionId, link, expiredAt, extraData.
- **No documented `redirectUrl` and no own order-id field** (only `extraData`); status lookup `GET /hl/v2/invoices/{id}` by invoice id only; statuses seen: paid, unpaid, closed (no `expired`).
- Webhook: POST JSON, events `payment.received`, `payment.reminder` (+ membership/shipper); **no signature, retry policy or IP list documented**; unclear whether `data.id` is the invoice or the transaction id. Our pattern (re-read status before trusting) would still apply. Sandbox dashboard `web.mayar.io`.
- Unofficial PHP and Laravel packages exist. A driver would need a new `PaymentGateway` implementation, `gateway_ref` = invoice id, and a way back to `/beli/selesai/...`.

## Assessment
Midtrans first (about 3x cheaper on QRIS, code done). Mayar is the only one of the three where NPWP is optional, so it matters if Midtrans refuses the account for lacking NPWP.

## Questions for Mayar support (if this path is taken)
1. Invoice API redirect after payment? 2. Webhook verification, retries, and which id `data.id` is. 3. Fees incl. VAT? 4. How long funds are held before withdrawal. 5. Is a software licence sold through the invoice API charged 1.5% or the 4% digital-product rate?

## Change log
- 2026-10-18 20:20 — created from the comparison chat; not pursued this session.
