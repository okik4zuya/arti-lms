---
name: testing-db-isolation
description: phpunit.xml must point at a separate MySQL testing DB, never the dev DB — RefreshDatabase wiped dev data once already
metadata:
  type: feedback
  created: 2026-09-09 01:20
  updated: 2026-09-09 01:20
---

Before running `php artisan test` (or anything using `RefreshDatabase`/`DatabaseMigrations`),
confirm `phpunit.xml`'s `DB_CONNECTION`/`DB_DATABASE` env block points at an isolated database —
currently `mysql` / `arti_lms_testing` (dev DB is `arti_lms`, same MySQL server, no
`pdo_sqlite` driver available on this PHP install so in-memory sqlite isn't an option here).

**Why:** `phpunit.xml` shipped with the DB env lines commented out, so the first
`FilamentPanelAccessTest` run (`RefreshDatabase`) ran `migrate:fresh` against the real dev
`arti_lms` database and silently wiped all users/courses/course_access/progress rows. Caught only
because the user then couldn't log in. See [[status]] "Incident" entry for the full recovery.

**How to apply:** any time `phpunit.xml` is regenerated, reset, or a fresh clone is set up, verify
the `DB_CONNECTION`/`DB_DATABASE` lines are present and non-commented before trusting any test run
that touches the database — don't assume Laravel's testing defaults protect the dev DB.

## Change log
- 2026-09-09 01:20 — created after the incident
