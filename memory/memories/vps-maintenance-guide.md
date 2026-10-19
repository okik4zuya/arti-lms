---
name: vps-maintenance-guide
description: Runbook for the shared production VPS (arti-lms and 6 other sites): layout, daily and weekly checks, monitoring commands, the R2 backup system and how to restore from it, outage recovery steps, hardening settings, gotchas
metadata:
  type: reference
  created: 2026-10-19 14:18
  updated: 2026-10-19 14:18
---

Why this exists: the 2026-10-19 OOM outage ([[vps-oom-incident-2026-10-19]]). Setup history: [[domain-migration-arti-ai-id]]. No secret values here: passwords and keys live in the password manager and root-only files named below.

## 1. Server facts
- Ubuntu 24.04, hostname `VM-1-23-ubuntu`, **1.9 GB RAM, 2 GB swap**, 39 GB disk, MySQL 8.0, nginx, PHP 8.4-FPM. Login user `ubuntu` (sudo).
- Sites in `/var/www/`: `arti-lms`, `invait`, `sitekim`, `tahukotak` (API at `apps/api`), `tin_competition`, `tokoemas`, `html`. MySQL databases: `arti_lms`, `invait`, `sitekim`, `tin_competition`, `tokoemas` (plus the `mysql` system schema).
- **Server clock is CST (UTC+8); Laravel logs are WIB (UTC+7).** Convert before comparing.
- arti-lms cron (crontab of `www-data`): `schedule:run` and `queue:work --stop-when-empty --max-time=55` every minute, queue driver `database`. Use `flock -n /tmp/<name>.lock` in front of each.
- Cloudflare in front of everything (proxied). nginx server blocks are in `sites-enabled`; `grep -r` skips symlinks, use `sudo nginx -T`.
- `/etc/nginx/conf.d/00-default.conf` rejects unknown hosts (port 80 `return 444`, port 443 `ssl_reject_handshake on`). Keep it.
- Do not install heavy agents (Netdata used 894 MB and caused the outage). The provider agent `YDService` is small; leave it.

## 2. Quick health check (2 minutes, weekly or after any alert)
```bash
free -h                                  # want available > 600 MB; swap used < 1.5 GB
uptime                                   # load below 2
df -h /                                  # disk below 80 %
ps aux --sort=-%mem | head -8            # who holds RAM
systemctl is-active nginx php8.4-fpm mysql
sudo journalctl -k --since "7 days ago" | grep -i "killed process"   # any OOM kill = investigate
sudo tail -5 /var/log/backup-r2.log
sudo rclone ls r2:arti-backup/ | tail -4        # newest files dated today or yesterday
sudo tail -20 /var/www/arti-lms/storage/logs/laravel.log | cut -c1-200
sudo tail -20 /var/log/nginx/error.log | cut -c1-200
curl -sI https://arti.ai.id | head -1                                # HTTP/2 200
```
Thresholds that mean "act now": swap used above 1.5 GB, available RAM under 300 MB, any `killed process`, backup log stale for more than a day.

## 3. Outage runbook
**522 / site unreachable, SSH not answering**
1. Open the provider console (works without SSH). Look for `Out of memory` lines.
2. Soft reboot from the provider panel, wait 3-5 minutes; if the console is frozen, hard reset (MySQL does crash recovery on start, normally fine).
3. After SSH is back: `free -h`, `systemctl status mysql php8.4-fpm nginx --no-pager`, `sudo journalctl -k -b -1 | grep -i "killed process"` for the culprit.
4. **Take a dump before changing anything**: `mysqldump --all-databases --single-transaction -u root -p > ~/dump-$(date +%F).sql`, copy it to the laptop with `scp` **run from the laptop's PowerShell** (not inside the SSH session), then delete the file on the VPS (it is unencrypted).

**500 with `SQLSTATE[HY000] [2002] Connection refused` in `laravel.log`:** MySQL is down. `sudo systemctl start mysql`, read `journalctl -u mysql -b --no-pager | tail -40`, then `sudo -u www-data php /var/www/arti-lms/artisan tinker --execute="echo DB::select('select 1 as ok')[0]->ok;"`.

**Other 500:** read `laravel.log` and the nginx error log; check ownership of `storage` and `bootstrap/cache` (`www-data`), then `sudo -u www-data php artisan optimize:clear`.

**After any database downtime (once the product has launched):** check the Midtrans dashboard for payments in the window and resend the notifications; `orders:reconcile` also catches stragglers (`php artisan schedule:list` should show it).

## 4. Monitoring
- **Uptime monitor (set up by the researcher 2026-10-19; service and alert channel not recorded here):** 5-minute checks on `https://arti.ai.id`, ideally also `https://lisensi.arti.ai.id`, alerts by email or Telegram. Test the alert once by stopping nothing: use the monitor's own "test notification".
- Manual: the weekly check in section 2. Consider a cron that mails when swap exceeds a limit only if the manual routine proves too easy to forget.
- Cloudflare: Bot Fight Mode is off on purpose until a WAF skip rule exists for `lisensi.arti.ai.id`; a rate limit on `/jamrud/login` is optional.

## 5. Backup to Cloudflare R2
**What runs:** root cron `0 3 * * *` (server time) → `flock -n /tmp/backup-r2.lock /usr/local/sbin/backup-to-r2.sh >> /var/log/backup-r2.log 2>&1`. The script:
1. `mysqldump --defaults-extra-file=/root/.backup-my.cnf --no-tablespaces --all-databases --single-transaction --quick --routines --triggers | gzip` → `/var/backups/r2/mysql-<date>_<hhmm>.sql.gz`
2. `tar` of every site's `.env` (contains `APP_KEY`; without it encrypted columns such as licence keys are unreadable) → `env-<stamp>.tar.gz`
3. gpg symmetric AES256 with `/root/.backup-pass` → `*.gpg`, raw files deleted
4. `rclone copy` to bucket `r2:arti-backup/`, `rclone delete --min-age 14d` there, local `.gpg` older than 2 days removed

**Pieces and where they live (all root-only):** `/usr/local/sbin/backup-to-r2.sh` (700), `/root/.config/rclone/rclone.conf` (600, remote `r2`, S3 endpoint `https://<ACCOUNT_ID>.r2.cloudflarestorage.com`), `/root/.backup-my.cnf` (600, MySQL user `backup`), `/root/.backup-pass` (600, gpg passphrase). Bucket `arti-backup` is **private** (no public access, no custom domain), separate from the public CDN bucket ([[r2-storage]]). The MySQL user `backup`@`localhost` has `SELECT, SHOW VIEW, TRIGGER, EVENT, LOCK TABLES, SHOW_ROUTINE` on `*.*` only.

**The passphrase must also be in a password manager** (Bitwarden suggested, 2FA on, recovery codes on paper). If it is lost together with the VPS, every backup is unreadable.

**Run by hand and check**
```bash
sudo /usr/local/sbin/backup-to-r2.sh && echo OK
sudo rclone ls r2:arti-backup/
```
First run 2026-10-19 15:12 CST: `mysql-...sql.gz.gpg` 737 KB, `env-...tar.gz.gpg` 4 KB.

**Restore drill (done once 2026-10-19; repeat monthly)**
```bash
sudo rclone copy r2:arti-backup/ /tmp/restore-test
sudo gpg --batch --pinentry-mode loopback --passphrase-file /root/.backup-pass -d /tmp/restore-test/mysql-*.gpg | gunzip | head -20   # SQL header = OK ("Broken pipe" from head is harmless)
sudo gpg --batch --pinentry-mode loopback --passphrase-file /root/.backup-pass -d /tmp/restore-test/env-*.gpg | tar -tz               # lists 6 .env files
sudo rm -rf /tmp/restore-test
```
**Real restore on a new box:** install rclone, recreate `rclone.conf` with the R2 token, then
```bash
rclone copy r2:arti-backup/ ./restore --include "mysql-<stamp>.sql.gz.gpg" --include "env-<stamp>.tar.gz.gpg"
gpg -d restore/mysql-<stamp>.sql.gz.gpg | gunzip > dump.sql        # asks for the passphrase from the password manager
mysql -u root -p < dump.sql                                          # every database
mysql -u root -p --one-database arti_lms < dump.sql                  # or one database only
mkdir env && gpg -d restore/env-<stamp>.tar.gz.gpg | tar -xz -C env  # paths are var/www/<site>/.env; copy the one needed
```
Then `php artisan migrate --force`, caches, cron, point the Cloudflare A record (media is on R2, licences on the Worker + D1 and are not in this backup).

**Not covered by this backup:** the Cloudflare D1 licence database (nightly encrypted export of `license_issuances` still open, [[license-admin]]), the R2 media buckets, the `tahukotak` data if it uses a database not in the dump list, uploaded files in `storage/app` (check whether any are not on R2).

**Rotation:** to rotate the R2 token, create a new token scoped to `arti-backup`, edit `rclone.conf`, test with `rclone ls r2:arti-backup`, delete the old token. To change the gpg passphrase, new backups use the new one but old files still need the old one, so keep both.

## 6. Hardening settings in place or pending
- Done: Netdata removed; nginx default server; backup system; `flock` and `pm = ondemand` reported applied (verify).
- Pending: `/etc/mysql/mysql.conf.d/zz-lowmem.cnf` with `performance_schema = OFF`, `innodb_buffer_pool_size = 128M`, `max_connections = 40`, then `sudo systemctl restart mysql` (do it after a verified backup, in a quiet hour); extra swap (`fallocate -l 2G /swapfile2`, `chmod 600`, `mkswap`, `swapon`, add to `/etc/fstab`); Laravel `config:cache`, `route:cache`, `event:cache` as `www-data` (re-run after each deploy or `.env` change, then restart the queue).
- FPM pool file `/etc/php/8.4/fpm/pool.d/www.conf`: `pm = ondemand`, `pm.max_children = 5`, `pm.process_idle_timeout = 10s`; test with `sudo php-fpm8.4 -t` before restarting.
- Run `npm run build` on the laptop and `scp` `public/build`; never build on the VPS.

## 7. Gotchas collected
- `rclone lsd r2:` returns 403 for a bucket-scoped token; test with `rclone lsd r2:arti-backup`.
- apt's rclone 1.60.1 gave intermittent `501 NotImplemented` against R2; install the current one with the official `rclone.org/install.sh` and the errors stopped. `rclone rcat` (streaming) is not reliable on R2; the script uses `rclone copy` of files.
- `mysqldump` as Linux root fails with `Access denied ... (using password: NO)` because MySQL root has a password; hence the dedicated `backup` user. `--defaults-extra-file` must be the first option.
- `scp` from inside the VPS to `C:\...` fails with "Could not resolve hostname c"; run it from the laptop.
- `grep` on a root-only script needs `sudo`; a leftover `<placeholder>` pasted literally into a path is a common slip.
- gpg prints `directory '/root/.gnupg' created` on the first run; harmless.

## Change log
- 2026-10-19 14:18 created after the OOM incident and the R2 backup setup.
