---
name: status
description: Living current-state pointer for arti-lms
metadata:
  type: project
  tier: T0
  created: 2026-09-08 21:36
  updated: 2026-10-19 14:18
---

## Current state

**2026-10-19 14:18.** The shared production VPS went down from an out-of-memory spiral (Netdata at 894 MB, `mysqld` killed, about 1 hour 8 minutes without a database); it is back, Netdata is gone, unknown hosts are rejected by nginx, and a daily encrypted MySQL + `.env` backup to the private R2 bucket `arti-backup` is installed and restore-tested. The passphrase is in a password manager and an uptime monitor is set; no orders were lost (not launched yet). Still pending: MySQL low-memory config, extra swap, deleting the old unencrypted dump on the VPS ([[vps-oom-incident-2026-10-19]], runbook in [[vps-maintenance-guide]]). Earlier today: course-package brainstorm (discussion only, nothing built): Paper Riset stays inside ARTi Framework, Workflow Paper SLR is the second course, BIMA is on hold; price architecture, copy and legal impact, and work order are still to discuss ([[course-packages-decisions]], [[landing-slr-workflow]], [[landing-page-keywords]]). Landing now has the buy form and the four-problems copy, all uncommitted and not yet seen on a phone or in a sandbox payment ([[landing-checkout-form]], [[landing-copy-four-problems]]). Production runs on the shared VPS at arti.ai.id ([[domain-migration-arti-ai-id]]); open work is in [[todo-list]].
