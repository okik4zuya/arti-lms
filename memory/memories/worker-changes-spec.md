---
name: worker-changes-spec
description: Hand-over spec and test list for the licensing Worker repo (other repo, researcher changes and deploys it): silent resend, lifetime months, optional idempotent extend, and the client "Lifetime" label
metadata:
  type: reference
  created: 2026-10-18 15:28
  updated: 2026-10-18 17:10
---

For `C:\Users\user\arti-services\licensing-worker` and the shipped client in `~/.arti`. **Not touched from this repo** (hard rule); the researcher changes, tests and deploys. Context: [[automatic-purchase-plan]], [[license-admin]]. Laravel is already written against this spec and degrades safely if the Worker is older (see "What Laravel does today").

## 1. `POST /v1/admin/resend` accepts `silent` (required before the first automatic purchase is relied on)
Why: Laravel is the only email sender. `IssueLicense` calls `resend` to recover a lost issue response and "Ganti key" rotates keys; the Worker must not mail the key itself.
- Body gains `silent: true` (boolean, default false). With `silent`, rotate the key as today (same `key_id`, activations kept), return the key when `show: true`, and **write nothing to `email_outbox` and send no email**. Response `emailed` is `"no"` (or omit).
- The event log keeps `key_resent`, detail may say `silent`; never the key.
- Tests:
  1. `silent:true, show:true` returns a new key, old key stops validating, activations unchanged, `email_outbox` row count unchanged.
  2. `silent` absent behaves exactly as before (one outbox row).
  3. `silent:true, show:false` returns no `key` field.
  4. Unknown `key_id` is 404; missing or wrong admin token is 401; neither writes an outbox row.
  5. `silent:"yes"` (non-boolean) is a 400 or is treated as false, but never as true by accident.

## 2. Lifetime = `months: 1000` (verify, probably no change)
Laravel issues purchases with `months = 1000` (config `site.offer.license_months`), about 83 years.
- Check the issue handler has no cap below 1000 on `months` (validation or `seats`/`months` maxima). If there is one, raise it to at least 1200.
- Tests: issue with `months:1000` returns `expires` and `updates_until` about 83 years out, both valid ISO dates; the signed license verifies in the Python client; `extend` on such a license still works; D1 and JS date maths do not overflow (year 2109 is far inside range).
- Client check (shipped repo `~/.arti`): `tools/_shared/license.py` accepts an expiry in 2109 and the grace maths does not overflow.

## 3. Client shows "Lifetime" (researcher's decision 2026-10-18, lightest change)
In the dashboard License panel and in `arti-license status`, when `expires` is more than 50 years away, print **Lifetime** instead of the date. Same rule as `LicenseIssuance::validityLabel()` in Laravel. Test: a license with `expires` 83 years out prints "Lifetime"; one with 12 months still prints the date.

## 4. Optional now: `extend` idempotent on `order_ref`
No longer needed for purchases (lifetime removed renewals). Still useful for admin compensation extensions, which Laravel guards with a 30 s lock.
- Body gains `order_ref` (string). A second call with the same `order_ref` for the same license returns the first result with `replayed: true` and extends nothing; the same ref on another license is 409.
- Tests: replay returns the same `expires`; two concurrent calls extend once; absent `order_ref` keeps the old behaviour.

## What Laravel does today
- `LicenseApi::resend` already sends `silent: true`. An older Worker ignores the unknown key and keeps emailing (the outbox provider currently sends nothing), so nothing breaks before the Worker change, but **do not enable a real Worker email provider** until item 1 is deployed.
- Purchases send `months: 1000`, `order` = the order's public id (idempotency), `amount_idr`, `provider` = the order's gateway (`midtrans` or `xendit`).

## Change log
- 2026-10-18 15:28 — created.
