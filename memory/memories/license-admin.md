---
name: license-admin
description: Filament license page (/jamrud/license-issuances) — A+ architecture (Worker enforces, Laravel keeps commercial record + encrypted key), what is built, what is verified, prerequisites and the path to automatic purchases
metadata:
  type: project
  created: 2026-10-15 11:30
  updated: 2026-10-15 11:57
---

Built 2026-10-15 from the approved plan (`~/.claude/plans/mulai-mengembangkan-mekanisme-generasi-splendid-snail.md`). Builds on [[arti-app-architecture]]; the Worker side is `~/.arti/memory/memories/licensing-update-plan.md`.

## Architecture (A+), decided after comparing Worker-as-truth, "reverse hybrid" and domain-split
- **The Worker (Cloudflare + D1) stays the enforcer and source of truth for license state**: key minting, status, dates, seats, activations, signing. No Worker change was needed for stage 1.
- **Laravel keeps the commercial record and an encrypted copy of the key** (`license_issuances.key`, `encrypted` cast, `$hidden`), so the admin can resend a key. Laravel commands the Worker synchronously; the status columns here are a display copy and **the Worker wins** on any difference (`licenses:sync`, daily 03:00).
- Why not push/async (reverse hybrid): pending states, delayed revoke, email gating, split-brain with the CLI — all self-inflicted. Why a key copy at all: the admin asked to be able to resend; the Worker only holds an HMAC. Trade-off accepted: DB + `APP_KEY` leak together exposes keys.
- `arti-admin` CLI stays valid for emergencies; do not rotate keys from it (the local copy goes stale; sync flags `key_stale`).

## What exists
- `App\Services\Licensing\LicenseApi` (+ `LicenseApiException`): `issue`, `show`, `resend`, `verifyKey` (`/activations`). Config `services.licensing.{url,admin_token,installer_url}` from `LICENSE_API_URL`, `LICENSE_ADMIN_TOKEN`, `LICENSE_INSTALLER_URL` (token in `.env` only).
- `App\Actions\Licensing\IssueLicense` (+ `IssueRequest`): pending row first, then Worker `issue`; 409 `order_exists` without a local key rotates via `resend` automatically (so the future gateway job needs no human); unreachable keeps the row pending (retry with the same `order_ref`); a refusal deletes the row. `SyncLicense` refreshes a row (and retries a pending one), `CreateLearner` was extracted from `AccessRelationManager`.
- Filament resource `LicenseIssuanceResource` (`/jamrud/license-issuances`): "Terbitkan lisensi" header action (optionally creates a learner + course access), table with masked key state, "Key / WhatsApp" modal (key + ready message from `config('site.license.whatsapp_template')`, wa.me button, every opening logged in `license_issuance_events`), per-row sync / retry, bulk sync. `LicenseMessage`, `Offer::normalizeNumber()`.
- Command `licenses:sync {--verify=20}`; migration `2026_10_15_120000_create_license_issuances_table` (dates are `dateTime`, not `timestamp`: MySQL TIMESTAMP stops at 2038).
- Tests: `LicenseApiTest`, `IssueLicenseActionTest`, `SyncLicensesCommandTest`, `FilamentLicenseIssuanceTest` (Http::fake). Full suite 140 green (the deprecations are the old PDO constant).

## Verified vs not
- Verified: all of the above with `Http::fake` against the testing DB.
- **Not verified against the real Worker yet.** `wrangler dev` crashed here (V8 out of memory, about 650 MB free of 8 GB; second try outside the sandbox: "cannot execute"). A ready script exists in the session scratchpad (`verify_license.php`: issue → `arti-license activate` → CLI revoke/rotate → sync → lost-response recovery → unreachable/retry → no key in logs); run `npm run dev` in `C:\Users\user\arti-services\licensing-worker` with enough free RAM, then the script (copy it out of the scratchpad first if the temp dir was cleaned).
- Not looked at in a browser (table, key modal, copy button).

## Prerequisites before using it in production
- **MFA on `/jamrud` not done**: Filament 4 app authentication needs `bacon/qr-code` (not installed), `HasAppAuthentication` on `User`, two nullable columns and `->multiFactorAuthentication(...)` on the panel. Needs a `composer require`, which the researcher runs (composer had trouble before).
- Daily MySQL backup off the VPS, a backup of `APP_KEY` (without it the key column is unreadable), a nightly encrypted export of `license_issuances`, one restore drill.
- Run `php artisan migrate` (dev and production), set the three `LICENSE_*` vars. Worker still undeployed; Phase 0 (EULA, Worker domain, email sender, gateway) open.

The step-by-step for the next session (local verification, then Cloudflare deploy and production wiring) is in [[license-worker-setup]]. The verification script now lives at `tests/manual/verify_license.php` (not part of the PHPUnit suites).

## Path to automatic purchases (not built)
Gateway → `POST /webhooks/pembayaran` in **Laravel** (not the Worker: the email carries an account) → `orders` row → queued `FulfillOrder` = `CreateLearner` + `IssueLicense` + mailable. Needed first: (1) Phase 0 choices; (2) Laravel the only email sender, so the Worker's `resend` must stop emailing (`silent` flag, other repo); (3) Worker `extend` takes a month delta and is not idempotent: it must accept `order_ref` before automatic renewals; (4) atomic count for the first-100 price and amount check against the webhook; (5) password by one-time set-password link rather than plain text; (6) `orders:reconcile` for payments made while Laravel was down.

## Change log
- 2026-10-15 11:30 — created after building stage 1.
- 2026-10-15 11:57 — linked the Worker setup runbook; verification script moved to tests/manual.
