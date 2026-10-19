---
name: vps-oom-incident-2026-10-19
description: The 2026-10-19 outage of the shared production VPS (Cloudflare 522, SSH dead, OOM kills incl. mysqld), root cause (Netdata 894 MB), what was fixed, what is still pending, and the decision to stay on the shared VPS for now
metadata:
  type: project
  created: 2026-10-19 14:18
  updated: 2026-10-19 14:18
---

Day-to-day procedures that came out of this live in [[vps-maintenance-guide]]. Server layout and why it is shared: [[domain-migration-arti-ai-id]].

## What happened
- Browser showed **Cloudflare 522** on `arti.ai.id` (origin timed out); SSH did not answer. The provider console showed kernel `Out of memory: Killed process ... (php)`, `Free swap = 160kB` of 2 GB, and `journald buffer overrun`.
- The VM was thrashing (swap full), so sshd could not get CPU or memory. A **reboot from the provider panel** brought it back (soft reboot first; hard reset only if the console is dead).
- After the reboot the site returned a Laravel **500**: `Connection refused` to MySQL in `storage/logs/laravel.log` (session handler reads `sessions` from the DB). MySQL had been killed earlier and was up again from the new boot (`systemctl status mysql`); `curl -I https://arti.ai.id` returned 200 and `artisan tinker` `select 1` worked.
- **Clock gotcha:** the server clock is CST (UTC+8), Laravel logs are WIB (UTC+7, `APP_TIMEZONE=Asia/Jakarta`), one hour apart. MySQL died at 10:41:23 CST = 09:41 WIB (first `Connection refused`), came back 11:49:50 CST = 10:49 WIB (last error). So the database was **down about 1 hour 8 minutes**; orders, sign-ups and Midtrans notifications in that window may have failed to save.

## Root cause (from `journalctl -k -b -1 | grep -i "killed process"`)
OOM killer at 10:41 CST killed, in order: **`go.d.plugin` (Netdata, anon-rss 894 MB)**, dbus-daemon, **`mysqld`**, the user systemd, `YDService` (provider monitoring agent). The VPS has 1.9 GB RAM for 7 sites, so Netdata alone was about half of it. PHP was not the main hog (`pm = dynamic`, `max_children = 5`).

Contributing: a bot scanner flooded the box through Cloudflare (about 5.7 req/s of `GET /xxx.php` webshell probes with `Host: mail.invait.id`, "Primary script unknown" in the nginx error log). There was no `default_server`, so unknown hosts fell into the first server block (`arti.ai.id`) and started PHP-FPM for each probe. Harmless to security (files do not exist) but it burned workers.

## Fixed (confirmed from pasted output)
- Netdata fully purged (18 `netdata*` apt packages, including `netdata-repo-edge`); no process and nothing on port 19999; RAM used dropped from 977 MB to about 530 MB, available 990 MB to 1.4 GB.
- nginx default server added (`/etc/nginx/conf.d/00-default.conf`: port 80 `return 444`, port 443 `ssl_reject_handshake on`); `curl -H "Host: mail.invait.id" http://127.0.0.1` now returns nothing and `https://arti.ai.id` still 200.
- Backup to R2 built, run once and restore-tested (see [[vps-maintenance-guide]]). Before this there was **no backup at all**; the first manual dump was `~/dump-2026-10-19.sql` (3.7 MB, 185 tables, databases `mysql`, `arti_lms`, `invait`, `sitekim`, `tin_competition`, `tokoemas`).

## Applied per the researcher but not individually confirmed (verify)
`pm = ondemand` with `pm.process_idle_timeout = 10s`, `flock` on the cron lines, `artisan config:cache`, `route:cache`, `event:cache`, removing the `sites-enabled/netdata` link (the config was moved to `~/netdata-nginx.bak`). Listed in [[todo-list]].

Also done by the researcher (reported 2026-10-19): the backup passphrase is saved in a password manager, the first dump was copied to the laptop, an uptime monitor was set up. **Impact:** the product had not launched yet, so no order or payment was lost in the outage.

## Not done yet
MySQL low-memory config (`zz-lowmem.cnf`), 2 GB extra swap (swap was still 800-1000 MB used after the fix), deleting the DNS record `monitor` in the `invait.id` zone, deleting the unencrypted dump on the VPS.

## Decisions and discussion
- **Stay on the shared VPS for now**, with conditions: backup running and restore-tested, uptime alert, MySQL and FPM tuned, more swap. Move to a dedicated VPS (about 2 GB, Singapore or an Indonesian provider) at the first paying buyer with real volume, swap above 1.5 GB again, or another OOM. Cloudflare does not sell VPS (Workers, Containers, D1, R2 are not a fit for Laravel + MySQL), so the VPS stays with a normal provider; this host's `VM-1-23-ubuntu` / `YDService` look like Tencent Cloud.
- **Payment webhook stays in Laravel** (`POST /webhooks/pembayaran`). Moving it to a Cloudflare Worker would survive a VPS outage but fulfilment (learner account, mail, quota) needs Laravel anyway, doubles the source of truth and means rewriting tested logic. Safety nets already exist: Midtrans retries, and `orders:reconcile` every 10 minutes ([[automatic-purchase-plan]]). If volume grows, a thin Worker **buffer** (verify signature, queue, forward with retry) is the cheaper option. Deferred.
- Encryption of the backup: `.env` must be encrypted (APP_KEY, Midtrans and Brevo keys); the script encrypts both files with gpg. The passphrase lives only on the VPS and must also be in a password manager (Bitwarden suggested).

## Lessons
- Never run a heavy monitoring agent on a 2 GB shared box; measure with `ps aux --sort=-%mem` before adding anything.
- Know the failure before you see it: no alert existed, the outage was found by the researcher.
- A cron queue worker and `schedule:run` without `flock` can pile up; always `flock -n`.
- Check the time zone of each log before comparing times.

## Change log
- 2026-10-19 14:18 created after the incident session.
