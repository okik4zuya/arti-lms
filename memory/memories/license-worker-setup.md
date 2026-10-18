---
name: license-worker-setup
description: Runbook for the next session — verify the Laravel license page against a real local Worker, then deploy the licensing Worker to Cloudflare, wire Laravel production, and finish the prerequisites (MFA, backups, scheduler, privacy line)
metadata:
  type: project
  created: 2026-10-15 11:57
  updated: 2026-10-16 00:11
---

Written at the end of the 2026-10-15 session so a new session can pick up without re-deriving. Context: [[license-admin]] (what was built and why), `~/.arti/memory/memories/licensing-update-plan.md` (Worker plan). Nothing is deployed; the Worker lives in `C:\Users\user\arti-services\licensing-worker` (own git repo, no commits yet); run its commands in PowerShell there. **Do not commit anything unless asked** (working-preferences).

## Progress, 2026-10-15 afternoon (the Worker is deployed)
Done by the researcher in their own terminal, skipping Step 0 (RAM ~900 MB free): keys generated and stored (password manager plus offline copy), D1 `arti-licensing` created, migration `0001_init.sql` applied remotely, the four secrets set, `wrangler deploy` to **`https://lisensi-arti.invait.id/v1`**. Checked: `/v1/validate` GET gives 405, `/v1/admin/outbox` without a token gives 401 `not_entitled`. Production smoke test passed (issue, activate, second machine refused with `seat_limit`, revoke, refresh gives `key_revoked`). `~/.arti/tools/arti-license/config.json` `api_base` and `pubkeys.json` (now identical to `pubkeys.prod.json`) are production; the dev public keys are kept at `C:\Users\user\arti-secrets\dev\pubkeys.dev.json`. A throwaway client copy with production keys is in `%TEMP%\arti-prod-client` (delete when done). The smoke license stays in D1 as revoked.

**Gotcha found:** Cloudflare answers 403 with an empty body to the default `Python-urllib` User-Agent, so `arti_admin.py` and the buyer-facing `~/.arti/tools/arti-license/cli.py` failed (JSON decode error). Both now send their own User-Agent (`arti-admin/1.0`, `arti-license/1.0`); Guzzle and curl were never blocked. Any new Python client needs a custom User-Agent, and `arti_admin.py` still crashes with a traceback on a non-JSON error body. Revoke does not stop an already-activated machine (see [[license-admin-roadmap]]).

Local Filament check: the researcher pointed the local `.env` at the production Worker and the page worked (issue, key modal). Empty the three `LICENSE_*` lines in the local `.env` afterwards so dev is not tied to production, and revoke the test licenses made that way. Never open `.env` files (working-preferences); ask the researcher to edit them.

Still open from the runbook: Step 10 production Laravel (`.env`, `migrate`, cron `schedule:run`), Step 11 (restrict `/v1/admin/*` to the VPS IP, D1 export plus one restore drill), MFA on `/jamrud`, backups, the `/privasi` sentence, removing the trial path in the Worker (needs the researcher's OK), and the installer link for `LICENSE_INSTALLER_URL` (where the paid bundle installer will be hosted is undecided).

## State at the end of the earlier session (before deploy)
- Laravel stage 1 is built and tested with `Http::fake` (suite 140 green). Dev DB migrated, `.env` local uses `database` drivers (Redis was dropped after a MISCONF error; see [[redis-setup]]).
- **Never run against a real Worker.** `wrangler dev` crashed in the agent session (V8 out of memory, ~650 MB free of 8 GB; a second try outside the sandbox failed "cannot execute"). It has worked on the researcher's machine before (e2e 45 checks, 2026-10-13).
- Not done: MFA, backups, browser look, privacy sentence, trial removal in the Worker, production anything.

## Step 0 — verify locally first (next session starts here)
1. Free RAM (close heavy apps, aim for 1.5 GB+ free), then `npm run dev` in `licensing-worker` (port 8760, dev secrets and state under `C:\Users\user\arti-secrets\dev`). If it still dies, the researcher runs it in their own terminal and the agent only runs the script.
2. Run `php tests/manual/verify_license.php` (PowerShell; `php` is not on the bash PATH). It boots Laravel against **`arti_lms_testing` only** (aborts otherwise), issues through the real Worker, activates with the shipped `arti-license` CLI (isolated `ARTI_HOME`, never touches `~/.arti/license.json`), checks CLI revoke/undo picked up by sync, key flagged stale after a CLI rotation, lost-response recovery (409 rotates a working key), unreachable Worker then retry, and no key in `storage/logs`. It cleans its rows in the testing DB; Worker dev state keeps the test licenses (harmless).
3. Then open `/jamrud/license-issuances` in a browser: table, "Terbitkan lisensi" (needs `LICENSE_API_URL=http://127.0.0.1:8760/v1` and `LICENSE_ADMIN_TOKEN` = the `ADMIN_TOKEN` in `dev.vars`, local `.env` only), key modal, copy button, WhatsApp button.

## Step 1 — decisions needed from the researcher before deploying
- **Worker domain: first decided 2026-10-15 as `lisensi-arti.invait.id`, MOVED the same night to `lisensi.arti.ai.id`** ([[domain-migration-arti-ai-id]]) (final: it is baked into `tools/arti-license/config.json` shipped to every buyer; changing it later means changing installed clients). Use it wherever `<domain>` appears below. A Worker `custom_domain` needs the `invait.id` zone on Cloudflare nameservers and no existing DNS record for that hostname (wrangler creates the record and certificate on deploy; no manual DNS). Avoid `*.workers.dev` for a paid product.
- `wrangler.toml`: `routes = [{ pattern = "lisensi.arti.ai.id", custom_domain = true }]` goes at the top level, before the first the `d1_databases` table header (below a table header TOML makes it part of that table and wrangler ignores it). Keep `binding = "DB"` (the code reads `env.DB`); only replace `database_id`.
- A separate Cloudflare account with 2FA (Phase 0 item, researcher doing it).
- Permission to **remove the unused trial path** in the Worker (`/trial`, CLI `trial`, `trial` tier). It is a code change in the other repo; do not touch it unasked.

## Step 2 — production keys (offline, outside every repo)
`~/.arti/python/python.exe C:\Users\user\arti-services\release\gen_keys.py --out C:\Users\user\arti-secrets\prod --pubkeys C:\Users\user\arti-secrets\prod\pubkeys.prod.json --license-kid license-1 --release-kid release-1`. The script refuses repo paths, and **overwrites the `--pubkeys` file**, so never point it at the live `pubkeys.json`. It writes `dev.vars`: rename to `prod.vars`. Keep PEMs + `prod.vars` in a password manager plus an offline copy. Never change `PEPPER` after the first license is issued (all key HMACs would stop matching). Never print these values into chat.

## Step 3 to 7 — Cloudflare (researcher's terminal, in `licensing-worker`)
1. `npx wrangler login`; `npx wrangler d1 create arti-licensing`; put the returned `database_id` into the `d1_databases` table in `wrangler.toml`.
2. Add a route to `wrangler.toml`: `routes = [{ pattern = "<domain>", custom_domain = true }]`. Keep `EMAIL_PROVIDER = "outbox"`: stage 1 needs no email (`issue` sends nothing, Laravel reads the key from the response).
3. `npx wrangler d1 migrations apply DB --remote`.
4. `npx wrangler secret put ADMIN_TOKEN`, `PEPPER`, `LICENSE_SIGNING_KEY`, `LICENSE_KID` (values from `prod.vars`, pasted at the prompt).
5. `npx wrangler deploy`.

## Step 8 — smoke test against production
Env `ARTI_LICENSE_API=https://<domain>/v1`, `ARTI_ADMIN_TOKEN=<token>`: `arti_admin.py issue ... --order smoke-1`; activate with `arti-license activate` using a throwaway `ARTI_HOME` + `ARTI_MACHINE_ID`; `arti_admin.py revoke <key_id>`; confirm a second machine is refused. The smoke license stays in the production D1 as revoked.

## Step 9 — ship the client
In `~/.arti`: `tools/arti-license/config.json` `api_base` to `https://<domain>/v1`; `tools/arti-license/pubkeys.json` replaced by `pubkeys.prod.json`, with the `dev-*` kids removed before any release.

## Step 10 — Laravel production
`.env`: `LICENSE_API_URL=https://<domain>/v1`, `LICENSE_ADMIN_TOKEN=<same ADMIN_TOKEN>`, `LICENSE_INSTALLER_URL=<installer link>`; `php artisan migrate`; **cron `* * * * * php artisan schedule:run`** (without it the daily `licenses:sync` never runs); issue one test license from `/jamrud/license-issuances` and send it through the WhatsApp modal.

## Step 11 — hardening and recovery
- Restrict `/v1/admin/*` to the VPS IP (Cloudflare WAF rule or Access); today it is internet-facing behind a single bearer token.
- D1 Time Travel (check retention for the plan) plus periodic `npx wrangler d1 export arti-licensing --remote --output backup.sql`, and one restore drill before launch.
- Public `/v1/activations` is rate-limited per IP (20/min, `RATE_ACTIVATE_PER_MIN`); `licenses:sync --verify` defaults to 10 key checks per run to stay under it.

## Laravel-side leftovers (can be done in parallel)
- **MFA on `/jamrud`**: `composer require bacon/bacon-qr-code` (researcher runs it), `HasAppAuthentication` (+ recovery) on `User`, two nullable columns, panel `->multiFactorAuthentication([AppAuthentication::make()->recoverable()])`. Needed before the key reveal is used in production.
- Backups: daily MySQL off the VPS, a copy of `APP_KEY`, nightly encrypted export of `license_issuances`, one restore drill.
- One sentence in `/privasi`: buyer email and name are kept at the licensing Worker and in the app ([[legal-review-notes]]).
- Then the automatic-purchase path in [[license-admin]] (needs Phase 0: gateway, email sender; Worker `extend` must become idempotent on `order_ref`; `resend` must stop emailing from the Worker).

## Change log
- 2026-10-16 00:11 — Worker moved to `lisensi.arti.ai.id`, old route removed; see [[domain-migration-arti-ai-id]].
- 2026-10-15 13:43 — Worker deployed and smoke-tested; recorded the progress, the User-Agent gotcha and what is still open.
- 2026-10-15 12:28 — Worker domain decided (`lisensi-arti.invait.id`); researcher chose to skip local Step 0 (RAM ~900 MB free) and go straight to the Cloudflare steps; notes on `binding`, `routes` placement and DNS added.
- 2026-10-15 11:57 — created from the end-of-session planning; the verification script was copied from the session scratchpad to `tests/manual/verify_license.php`.
