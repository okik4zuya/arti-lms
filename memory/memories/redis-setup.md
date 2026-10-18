---
name: redis-setup
description: How Redis is wired for local dev (Laragon Redis, Predis, own DBs and prefix), what uses it, how it was verified, and what production still needs
metadata:
  type: project
  created: 2026-10-15 08:40
  updated: 2026-10-18 21:54
---

Set up 2026-10-15 08:40. Local only: `.env` is gitignored, so each environment needs its own.

## The environment
- **Laragon's Redis 5.0.14 (Windows port)** is already running on `127.0.0.1:6379` (`C:\laragon6\bin\redis\redis-x64-5.0.14.1`), no password, shared with other projects. db0 holds other apps' keys (`laravel:database:*`, `sitekim:horizon:*`, test keys): **never use db0 and never flush it.**
- PHP is `C:\php\php.exe` 8.5.4 ZTS with **no phpredis extension** (none in `C:\php\ext`), so the client is **Predis** (`predis/predis ^3.7`, added to `composer.json`). In bash `php` is not on PATH (`php.bat`); run PHP from PowerShell.

## Configuration (`.env`)
`CACHE_STORE=redis`, `SESSION_DRIVER=redis`, `QUEUE_CONNECTION=redis`, `REDIS_CLIENT=predis`, `REDIS_PREFIX=arti_lms_database_`, `CACHE_PREFIX=arti_lms_cache_`, **`REDIS_DB=10`** (default connection: sessions, queue) and **`REDIS_CACHE_DB=11`** (cache). `.env.example` documents the Redis block (Predis, prefix, DB numbers) but keeps the `database` drivers as defaults. `phpunit.xml` forces `array`/`sync`, so tests never touch Redis (checked: key counts unchanged after a test run).
- Switching the session driver logged the dev session out once. Nothing in the app dispatches jobs or mail yet, so the queue has no worker and none is needed until something is queued.

## Update 2026-10-18 (night)
The dev `.env` had fallen back to the `database` drivers; the researcher switched cache, session and queue to Redis again (`CACHE_STORE`, `SESSION_DRIVER`, `QUEUE_CONNECTION` = `redis`) and added `CONTENT_CACHE_STORE=redis` for the course content cache ([[learner-app-ux]]). Checked through a fresh PHP process: both connections (db10, db11) write and read, so the earlier `-MISCONF` is gone; the Redis queue held 2 waiting `SeriesMail` jobs (left from the 2026-10-15 tests; `php artisan queue:clear redis` before `queue:work` if they should not be sent); the `jobs` table was empty. `phpunit.xml` now also pins `CONTENT_CACHE_STORE=array`. Production Redis is still undecided (own instance, or leave `CONTENT_CACHE_STORE` empty and the drivers on `database`).

## Verified
Scripted check (standalone PHP booting Laravel): PING, a cache round trip into db11, queue size 0. A real HTTP request to `/login` created one session key in db10 with a 120-minute TTL, and the landing page rendered. Not verified: the Filament `/jamrud` login and the LMS pages under Redis sessions, and any behaviour under load.

## Benchmark: landing page with and without Redis (2026-10-15)
Only **session** is touched by the landing (no app cache on `/`). 160 sequential requests per cell, A/B interleaved over 4 rounds, `.env` toggled and restored. TTFB, mean / median in ms, Redis minus database:
| Setup | cold visitor `/` | returning `/` | cold `/fitur` | returning `/fitur` |
|---|---|---|---|---|
| **opcache on** (PHP 8.5 CLI built-in server) | -13.8 / -12.5 | -8.8 / -4.4 | -4.1 / -7.8 | -10.2 / -10.7 |
| **opcache off** (Laragon Apache, PHP 8.4 fcgid) | +276 / +31 | +103 / +15 | +203 / +32 | +58 / +24 |
- Opcache on: Redis is faster everywhere, roughly 5 to 17 % of a ~70 to 90 ms request. Opcache off: Redis (Predis) is slower by 15 to 32 ms at the median and has a much worse tail (p95 1.1 to 2.7 s vs 0.5 to 1.0 s); the tail cause is **not established** (RDB snapshots ruled out: fork 24 ms).
- Micro-benchmark, session write+read: database ~1.6 ms, Redis ~0.3 ms once connected, but the first operation costs more on Redis (~30 to 37 ms vs ~16 to 29 ms: Predis class loading plus connect). That first-call cost is what dominates without opcache.
- Conclusion: Redis helps only where opcache is on (production should have it); the Laragon Apache dev stack has it off, so there Redis sessions are slower. Absolute gains are small. Real-world value of Redis here is more about moving session/cache writes off MySQL than about landing latency.
- Method pitfalls: run the PHP built-in server from `public` with `-d opcache.enable=1 -d opcache.enable_cli=1` and `../vendor/laravel/framework/src/Illuminate/Foundation/resources/server.php`; tag benchmark requests with `User-Agent: perf-bench` so the rows in `sessions` can be deleted afterwards (656 were); clean db10 only, never db0.

## Still open
- **Production**: needs its own Redis (or keep the database drivers there) and the same env vars; the Laragon one is a dev convenience and is old (5.0.14, no Horizon-grade features).
- `composer audit` reports 4 advisories on `laravel/framework` (low, medium and high, e.g. temporary signed URL path confusion, debug-page XSS). Pre-existing, not from Predis; the app is on 11.x. Worth a framework upgrade pass.

## Change log
- 2026-10-18 21:54 — Redis for cache, session and queue back on in dev, content cache store, test pin.
- 2026-10-15 09:04 — added the with/without Redis landing benchmark.
- 2026-10-15 08:40 — created after wiring Redis for cache, session and queue.
