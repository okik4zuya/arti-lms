---
name: status
description: Living current-state pointer for arti-lms
metadata:
  type: project
  tier: T0
  created: 2026-09-08 21:36
  updated: 2026-10-23 11:34
---

## Current state

**2026-10-24 17:58.** Planned the free tools hub at `/tools` (hub, composite, diagram, plot, journal-profile Gem; client-only, Vite-bundled, kill switch `SITE_TOOLS_ENABLED`): full build plan in [[tools-hub-plan]], nothing built yet. Earlier state below.

**2026-10-23 11:34.** Payments moved from Midtrans to **Mayar** (hosted Mayar page; the Midtrans driver is kept): the driver, webhook (secret in the URL plus Mayar's Webhook Token) and 12 tests are built, and a sandbox payment ran end to end (redirect, webhook, order `paid`, purchase mail, set-password page) with the unfinished checks and the go-live list in [[mayar-gateway]] and [[purchase-production-checklist]]. Landing and legal pages no longer name a gateway ([[landing-checkout-form]]); dev MySQL (Windows port reservation) and Redis (`MISCONF` on low RAM) problems and fixes are in [[php-binary-and-shell]] and [[redis-setup]]. Everything is uncommitted; open work is in [[todo-list]].
