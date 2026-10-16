---
name: domain-migration-arti-ai-id
description: The 2026-10-15/16 move of every ARTi hostname from invait.id to arti.ai.id (landing, CDN, license Worker, email), the production VPS setup, gotchas found, and what is still open
metadata:
  type: project
  created: 2026-10-16 00:11
  updated: 2026-10-16 00:11
---

## Decision
Product not launched yet, so the domain was switched before any buyer existed: `arti.invait.id` → **`arti.ai.id`**. Everything moved with it (researcher's choice, "pindah semua ke ai.id"). Plan file: `~/.claude/plans/jadi-begini-saya-ingin-giggly-sloth.md`.

| Role | Now | Was |
|---|---|---|
| Landing + Filament (`/jamrud`) | `arti.ai.id` | arti.invait.id |
| Public R2 bucket `arti` | `cdn.arti.ai.id` | cdn-arti.invait.id |
| License Worker | `lisensi.arti.ai.id/v1` | lisensi-arti.invait.id |
| Mail sender (Brevo) | `noreply@arti.ai.id` | noreply@invait.id |

`arti.invait.id/*` 301-redirects to `arti.ai.id/$1` (Cloudflare Redirect Rule in the `invait.id` zone, proxied dummy A record `arti` → `192.0.2.1`). The `invait.id` zone stays: staging, sitekim, tokoemas and monitor still live there. SciPeek's link change is a separate release: see the SciPeek repo's `domain-migration-arti-ai-id`.

## Production setup (shared VPS, Ubuntu, nginx, PHP 8.4)
- VPS hosts 7 sites on **1.9 GB RAM with swap in use**; arti-lms is the newest. Move to a dedicated VPS at the first paying buyer or when it hurts (migration: new box, clone, `.env`, dump/restore MySQL, `migrate --force`, cron, point the Cloudflare A record; media is on R2).
- Folder `/var/www/arti-lms`, owned by `deploy` (run git, composer, artisan as `deploy`; nginx, crontab, `/etc` with `sudo` as `ubuntu`; never artisan as root). `storage` and `bootstrap/cache` get ACL `g:www-data:rwX` (package `acl`, also the default ACL).
- Cloudflare **Origin Certificate** in `/etc/ssl/cloudflare/arti.ai.id.{pem,key}` (the other sites use Certbot); the `arti.ai.id` record must stay proxied. SSL mode Full (strict), Always HTTPS, www→root redirect rule, cache bypass for `/jamrud*` and `/livewire*`.
- No Vite on the VPS (RAM): run `npm run build` on the laptop and `scp` `public/build`.
- Cron as `www-data`: `schedule:run` every minute and `queue:work --stop-when-empty --max-time=55` every minute (no permanent worker). Queue driver is `database`; confirmation mails therefore arrive within 1-2 minutes.
- `APP_TIMEZONE=Asia/Jakarta` in production (daily download counts follow WIB).
- `bootstrap/app.php` now has `$middleware->trustProxies(at: '*')`, otherwise every visitor shares a Cloudflare IP and per-IP throttling hits everyone.

## Gotchas found
- **Brevo "authenticate automatically" fails** for `ai.id` (Cloudflare refuses; the integration treats `ai.id` as the zone). Do it manually. Brevo also lists hosts relative to `ai.id` (`arti`, `brevo1._domainkey.arti`, `_dmarc.arti`): in Cloudflare strip the trailing `.arti` (`@`, `brevo1._domainkey`, `_dmarc`), all DNS only.
- `make:filament-user` creates a user **without a role**; `canAccessPanel()` needs `admin` or `support`, and production never runs the seeder. Create the role in tinker (`Role::firstOrCreate(['name'=>'admin'])`, `syncRoles('admin')`). The symptom was a misleading "credentials do not match".
- A new Worker `custom_domain` creates its own DNS record; a manual `lisensi` record would block the deploy. The researcher's own resolver did not see the new name for a while (`curl --doh-url` proved the Worker was fine).
- Bot Fight Mode was switched off for the Worker tests (it can 403 the Python client); not re-enabled yet.
- `grep -r` does not follow the symlinks in `sites-enabled`; use `sudo nginx -T`.
- Revoking a license does not stop an already-activated machine (known, see [[license-admin-roadmap]]); `status --refresh` printed `[key_revoked]` and the local file stayed VALID.

## Verified (2026-10-16)
Public pages, `/jamrud` login, download 302 to `cdn.arti.ai.id` (62,863,855 bytes), mailing-list double opt-in (SPF/DKIM/DMARC pass), license issue/activate/revoke through `lisensi.arti.ai.id`, clean logs. Test suite: only `DomainRoutingTest::test_generated_urls_use_the_main_domain` fails, also on a clean stash.

## Still open
See `todo-list.md` section "Domain migration": SciPeek release, remove old R2 domain `cdn-arti.invait.id`, 2FA for the admin, Bot Fight Mode with a WAF skip for `lisensi.arti.ai.id`, dedicated VPS trigger. Related: [[license-worker-setup]], [[r2-storage]], [[list-building]].

## Change log
- 2026-10-16 00:11: created after steps 1-8 finished.
