---
name: duitku-fallback
description: Duitku researched 2026-10-18 as a fallback payment gateway if Midtrans rejects the account or the e-course/licence category; requirements, fees, API quirks, questions for support. Not built.
metadata:
  type: reference
  created: 2026-10-18 16:44
  updated: 2026-10-18 16:44
---

Decision 2026-10-18 (researcher): **Midtrans stays the gateway; Duitku is only the fallback.** Nothing is built for Duitku. Context: [[automatic-purchase-plan]] (gateway abstraction), [[purchase-production-checklist]]. Sources are undated pages from duitku.com and docs.duitku.com; confirm everything with Duitku before relying on it.

## Individual account
- KTP, NPWP and bank account in the same name (students may use a student card instead of NPWP). **No NIB mentioned** for individuals; NPWP is still asked.
- Website must be live and not a marketplace, social or website-builder subdomain. Verification by phone within 5 working days. A test account can be made without a website.
- Prohibited: adult, gambling, arms, drugs, illegal goods, high-risk telemarketing, replicas. E-courses and software are not named either way (the FAQ mentions online courses as digital products).
- Available to individuals: QRIS, OVO/ShopeePay/DANA/LinkAja, Alfamart, VA (BCA is company-only), cards (default cap Rp1 million). Indomaret is company-only.

## Fees (undated page, VAT included)
QRIS 0.7%; e-wallet about 1.67-2% (digital-goods tier: OVO 3.03%, ShopeePay 4%); VA Rp1.500-4.000 (Rp3.000 for most banks); Alfamart Rp2.500; cards 2.9% + Rp2.500. Payout to bank: BI-Fast Rp2.500, online transfer Rp5.000. QRIS and e-wallet settle D+2.

## API facts
- `POST /api/merchant/createInvoice` (sandbox `api-sandbox.duitku.com`, production `api-prod.duitku.com`), headers `x-duitku-signature` (HMAC-SHA256 of merchant code + timestamp, keyed by API key), `x-duitku-timestamp`, `x-duitku-merchantcode`; returns `paymentUrl` and `reference`.
- Callback: form-urlencoded POST, signature = HMAC-SHA256 over `merchantCode + amount + merchantOrderId`; `resultCode` 00 success, 01 failed; 5 retries; must answer HTTP 200; resend from the dashboard.
- Status check `transactionStatus` (sandbox `sandbox.duitku.com/webapi/...`, production `passport.duitku.com/webapi/...`), signature = HMAC-SHA256 of `merchantCode + merchantOrderId`; statusCode 00/01/02. **The docs forbid repeated or cron polling (rate limit blocks about an hour)**, which clashes with `orders:reconcile` every 10 minutes and the webhook's re-read: a Duitku driver would need throttled, targeted status calls.
- No refund API; refunds are manual (dashboard or disbursement). Docs are thin and contradict themselves on the status-check content type and the signature replacement; test in the sandbox.

## Questions for Duitku support (if this path is taken)
1. Are an e-course and software licences accepted for an individual account? 2. Fees for digital goods on QRIS and e-wallets. 3. The `transactionStatus` rate limit. 4. Is NPWP required? 5. Which methods are active for an individual account.

## Change log
- 2026-10-18 16:44 — created from the research chat; Midtrans kept as the gateway.
