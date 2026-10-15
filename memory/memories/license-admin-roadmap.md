---
name: license-admin-roadmap
description: Agreed design for the next stage of /jamrud/license-issuances — admin and support roles with permissions, payment proof and review queue, tier removed from the UI, compact issue modal, row actions, phases A-D; not implemented yet
metadata:
  type: project
  created: 2026-10-15 13:43
  updated: 2026-10-15 16:59
---

Decided with the researcher on 2026-10-15 after a brainstorm; **phase A is implemented (see "Phase A built"), B to D are not**. Builds on [[license-admin]] (what exists) and [[license-worker-setup]] (the Worker is deployed). Full plan in Indonesian with 9 ASCII wireframes: `~/.claude/plans/sebelum-lanjut-coba-analisis-majestic-swan.md` (read it before building; it is the detailed spec). The researcher will implement in a separate session.

## Why
The table only has Key/WhatsApp, Sync and Issue. The Worker already offers `extend` (accepts negative months, not idempotent), `revoke`/`undo`, `deactivate` (free a device), `resend` (rotates the key, keeps activations, emails from the Worker) and `show` (returns activations and events, which `SyncLicense` throws away). Without buttons, everyday cases (new laptop, renewal, refund) need the terminal with `ADMIN_TOKEN`, which is riskier. Today any `admin` can do everything in `/jamrud`, including site settings (the WhatsApp number) and courses.

## Decisions
- **Roles:** `admin` (owner, everything via `Gate::before`) and `support` (serves buyers). `learner` stays without panel access. `viewer`/`editor` postponed until someone needs them.
- **Principle:** support acts, admin reviews afterwards. Hard blocks only for punitive/irreversible actions and configuration.
- **Support may:** view, sync, **issue**, reveal key / WhatsApp, edit contact, release a device (reason, max 3 per key per year), **extend**, **rotate key** (reason), attach payment proof, reset a learner password (never for admin/support accounts).
- **Admin only:** verify payment, revoke / undo revoke, delete a pending row, edit the amount after issue, site settings, courses, users and roles. Default-deny these for support (the panel opens to support, so every other resource and page needs `canAccess`).
- **Permissions (spatie):** `license.view`, `license.view_amount`, `license.sync`, `license.issue`, `license.reveal_key`, `license.edit_contact`, `license.release_device`, `license.extend`, `license.rotate_key`, `payment.attach`, `account.reset_password`, `payment.verify`, `license.revoke`, `license.delete_pending`, `license.edit_amount`, `site.manage`, `course.manage`, `user.manage`. Seed them with an idempotent artisan command (`DatabaseSeeder` refuses production). Every action checks server-side, hiding a button is not security.
- **Payment proof (maker-checker):** columns `payment_ref`, `payment_proof_path` (private disk, jpg/png/pdf, 5 MB), `payment_verified_by`, `payment_verified_at`; `issued_by` already exists. Proof may be added after issue but a red "Bukti belum ada" badge shows, and the row enters a "Perlu ditinjau" filter with a navigation badge for admin. Amount 0 = complimentary (reason required, always reviewed). Extend has two kinds: paid (needs reference) and compensation (max 1 month, reason, reviewed). Proof files are served through a signed route only to permission holders; add one sentence to `/privasi` (proof contains bank data). Gateway orders later count as verified.
- **Tier removed from the admin UI** (form, table, WhatsApp template `{tier}`); "Jumlah perangkat" is the only knob (default 1). Reasons: in the Worker `TIERS` only sets default seats (`individual` 1, `lab` 5, `academic` 1, `site` 1000, `trial` 1), `site` is the only one that changes behaviour (no machine binding), and there is one offer. Do **not** touch the Worker or client: tier is in the signed license and the client rejects unknown tiers (`bad_tier`). Laravel stops sending `tier`, so the Worker defaults to `individual`.
- **Issue modal:** width `Width::TwoExtraLarge` (42rem), no scroll when "Opsi lain" is closed. Visible: Nama · Email / WhatsApp · Referensi pembayaran / Bulan (12) · Harga (suggested launch/normal) · toggle "Buat akun belajar". Collapsed "Opsi lain": seats, order ref (auto `WA-yymmdd-XXXX`), notes, courses, proof upload. The temporary-password field is removed. Other modals: width md/lg, reasons as a preset select plus optional text. Look at it in headless Chrome (1366x768 and 375 px).
- **Row actions:** Key/WhatsApp and Sync visible; "Kelola" menu (Extend, Release device, Rotate key, Reset account password, Edit contact, Attach proof, History, Verify payment for admin); red "Bahaya" group (Revoke, Delete pending). Quick filters: needs review, expiring in 30 days, stale key, WhatsApp not sent, sync failed. New column "Diterbitkan oleh".
- **Password storage:** do not keep it. The `account_password` column and the `{account}` block in the WhatsApp message were built earlier in this session (uncommitted, migration `2026_10_15_130000_...` still Pending on dev) but will be dropped in phase D: the password reaches the message only through the modal that opens right after issuing, afterwards use "Reset password akun". The template line "Segera ganti password" is wrong until learners have a change-password flow (none found in the app).

## Phases
- **A:** roles and permissions, default-deny for courses/settings/users, "Diterbitkan oleh", History (use `show.events`), Edit contact, quick filters, drop tier, compact issue modal.
- **B:** payment proof (migration, field at issue, attach, verify, review queue and badge, `/privasi` sentence).
- **C:** `LicenseApi` wrappers (`extend`, `revoke`, `deactivate`, events), then Release device, Extend, Rotate key, Revoke/Undo, Delete pending. Extend only positive months (max 12 per action) and lock the button while it runs (Worker is not idempotent).
- **D:** Reset account password, drop `account_password`, fix the template.

MFA for support must be on before phase C reaches production. Suggested Worker changes (other repo, needs the researcher's OK): `resend` without email (`silent` flag) and `extend` taking `order_ref`; required before automatic purchases, not before phases A-D.

## Verify
Per-role permission matrix tests through Livewire `callAction`; guard-rail tests (negative months, reason required, device cap, odd amount flagged, admin password reset refused, proof file inaccessible without permission); key and password never in table, JSON or logs. Run tests per file or with `--filter`: the full suite ran out of PHP memory on this machine on 2026-10-15 (about 900 MB free RAM).

## Open product question
Revoke does not kill machines that are already active: the client keeps the signed license until it expires (plus grace) and only prints "could not refresh" on `key_revoked`. Accept it, or make the client delete the local license on `key_revoked`.

## Phase A built (2026-10-15)
- Roles and permissions: `App\Support\Permissions` (SUPPORT and ADMIN_ONLY lists), command `permissions:sync` (idempotent, production-safe), `Gate::before` for admin in `AppServiceProvider`, `User::canAccessPanel` = `hasAnyRole(['admin','support'])`. Dev DB was synced.
- Default-deny: `canAccess()` on `CourseResource` (`course.manage`) and `SiteSettings` (`site.manage`); no user-management resource exists yet, so `user.manage` guards nothing today. License resource: `canViewAny` = `license.view`; every action uses `->authorize(...)` (server-side), amount column needs `license.view_amount`.
- Added: "Diterbitkan oleh" column, `editContact` action (email locked, logs `contact_edited`), `history` slide-over (`LicenseHistory` + `filament/license-history.blade.php`: live Worker activations/events merged with local events, still renders when the Worker is down), four quick filters (expiring <30 days, stale key, WhatsApp not sent, sync failed; applied instantly).
- Tier removed: `IssueRequest` has no tier, Laravel sends none (Worker defaults to `individual`), local `tier` column kept (NOT NULL, placeholder `individual`, replaced by the Worker's value on sync), table column and `{tier}` gone. Message now reads "(1 perangkat, berlaku sampai ...)". `{account}` untouched.
- Issue modal: `Width::TwoExtraLarge`, sticky header, name/email, WhatsApp, months/price/"Buat akun belajar" visible, collapsed "Opsi lain" (seats, order ref, notes, courses). Password field removed; server generates `Str::password(12)` and still stores it in `account_password` until phase D. Checked in headless Chrome: closed state has no scroll at 1366x768 (window 672x517) and 375 px (1 px rounding); open state scrolls a little, as intended.
- Not in A: "Perlu ditinjau" filter and the payment-reference field (phase B), the rest of the "Kelola" menu (C/D). Tests: `LicenseRolesTest` (new), `FilamentLicenseIssuanceTest` extended, 35 pass across the license files.
- Decisions: revoke does not stop activated machines (accepted for launch, add a sentence to `/syarat`); paid installer hosted on Cloudflare R2 with short-lived links from Laravel.

## Phase B built (2026-10-15 14:08)
- Migration `2026_10_15_140000_add_payment_proof_to_license_issuances_table` (`payment_ref`, `payment_proof_path`, `payment_verified_by`, `payment_verified_at`), **Pending on dev** like the `account_password` one (a plain `migrate` would run both; the researcher said not to run the latter).
- `LicenseIssuance::paymentState()` (verified / waiting / missing / none) and scope `needsReview()` (not pending, source `manual`, not verified). `IssueLicense` marks non-manual sources (gateway) verified at once.
- Issue modal: "Referensi pembayaran" beside WhatsApp (row 2), proof upload in "Opsi lain"; amount 0 without a reason in Catatan is refused with a notification (it also lands in the queue).
- Row actions in "Kelola": Lampirkan bukti (`payment.attach`), Lihat bukti (signed 5-minute link, route `license.proof`, `LicenseProofController`, checks `license.view`, private `local` disk, `no-store`), Verifikasi pembayaran (`payment.verify`, admin only). Column "Pembayaran" badge (days without proof after 3), filter "Perlu ditinjau", navigation badge for admins.
- `/privasi` has two new sentences (buyer name/email at Worker and app; payment proofs private). Tests: `LicensePaymentProofTest`; 76 pass across license and public-page files.
- Not built: compensation-extend and "Tandai ditinjau" for amount 0 beyond the verify action (compensation belongs to phase C), image preview inside the panel (opens in a new tab).

## Phase C built (2026-10-15 15:35)
- `LicenseApi`: `extend`, `revoke` (with `undo`), `deactivate`. Action classes in `app/Actions/Licensing/`: `ExtendLicense`, `ReleaseDevice`, `RotateKey`, `RevokeLicense` (each re-checks the permission, so a hidden button is not the only guard), reasons in `App\Support\LicenseReasons` ("Lainnya" needs text).
- Extend: months 1-12 only, compensation max 1 month with a reason, paid without a reference allowed but flagged; a 30 s cache lock refuses a double click (Worker `extend` is not idempotent); on an unreachable Worker the row is untouched and the notification says to Sinkronkan and check before retrying.
- Release device: machine list read live from the Worker (`show`), reason required, cap 3 per key per year counted from local `device_released` events (the Worker's admin deactivate does not write to its reissue counter); only admin (`license.revoke`) may exceed it.
- Rotate key: reason required, new key stored encrypted, stale flag and `wa_sent_at` cleared, then the key/WhatsApp modal opens; the event stores the reason, never the key. Note the Worker `resend` still emails from the Worker (outbox provider, so nothing is sent today); the planned `silent` flag is a Worker-repo change.
- Revoke / Undo / Delete pending: admin only; revoke asks for the email typed back; delete only for a pending row without `key_id`; "Bahaya" group is an icon button. Revoke does not stop activated machines (accepted).
- New column `review_note` (migration `2026_10_15_150000_...`, **Pending on dev**): compensation, unpaid extension and amount-0 licenses set it; `needsReview()` now includes rows with a note; admin action "Tandai ditinjau" clears it (event `reviewed`).
- `phpunit.xml` now forces `PROOFS_DISK=local`: with `PROOFS_DISK=r2` in the dev `.env`, the proof tests wrote a real object to `arti-private` (one leftover was found and deleted; only the referenced proof remains).
- Tests: `LicenseActionsTest` (new); 83 pass across the license files. Not checked in a browser (the dev DB lacks the pending migrations). Production use needs MFA on `/jamrud` first.

## Phase D built and MFA coded but off (2026-10-15)
- **Account password no longer stored.** Migration `2026_10_15_170000_drop_account_password_...` drops the column (guarded by `hasColumn`; the earlier add migration stays in the tree because dev ran it). `LicenseMessage::for($license, ?$accountPassword)`: the password exists only as an argument of the key/WhatsApp modal that `replaceMountedTableAction` opens right after issuing a license with a new account or after a reset; opened later from the table, the message has no password (only the email and "password lama tetap berlaku"). The Buka WhatsApp footer link reads the same argument from `mountedActions[0]`.
- **Reset password akun** (`ResetAccountPassword`, permission `account.reset_password`): random 12-char password, sessions cut (`remember_token` rotated), event `account_password_reset` without the password; refuses admin/support accounts and licenses without an account. If the license has no usable key, the password appears once in a persistent notification instead.
- Template: "Segera ganti password setelah masuk." replaced by "Simpan password ini baik-baik." (learners still have no change-password flow).
- **MFA (authenticator app + recovery codes) is coded but OFF by decision (2026-10-15).** `User` implements Filament's `HasAppAuthentication` and `HasAppAuthenticationRecovery` (encrypted `app_authentication_secret` and `app_authentication_recovery_codes`, migration `2026_10_15_160000_...`), panel `->multiFactorAuthentication([AppAuthentication::make()->recoverable()], isRequired: ...)` plus `->profile()`; the requirement reads `config('site.admin.mfa_required')` = `ADMIN_MFA_REQUIRED` (default **false**). Filament attaches the "required" middleware when routes register, so the flag must be set before boot (tests set the env before creating the app, see `AdminMfaRequiredTest`). **Set `ADMIN_MFA_REQUIRED=true` before the panel is used in production.** The correct package name is `bacon/bacon-qr-code` (installed v3.1.1; `composer.json` constraint was `*`, tighten to `^3.1`).
- **Filament gotcha (fixed 2026-10-15):** `replaceMountedTableAction` only stacks a table action on a non-table (header) action; called from inside a table action (Reset password akun, Ganti key) the key modal silently failed to open and only the toast showed. Use `$livewire->replaceMountedAction('sendKey', $args, ['table' => true, 'recordKey' => $id])` (clears the stack, then mounts) via `LicenseIssuanceResource::openKeyModal()`. `callAction` in tests unmounts everything, so regression tests use `mountAction()` + `callMountedAction()` and read `mountedActions`.
- Tests: `LicenseActionsTest` extended, `AdminMfaTest`, `AdminMfaRequiredTest`; 100 pass across the license and MFA files.

## Change log
- 2026-10-15 16:06 — phase D built, MFA coded and left off.
- 2026-10-15 15:35 — phase C built and recorded.
- 2026-10-15 14:08 — phase B built and recorded.
- 2026-10-15 13:56 — phase A built and recorded; revoke and installer-hosting decisions noted.
- 2026-10-15 13:43 — created from the roles, proof, tier and modal brainstorm.
