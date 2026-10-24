---
name: status
description: Living current-state pointer for arti-lms
metadata:
  type: project
  tier: T0
  created: 2026-09-08 21:36
  updated: 2026-10-24 18:30
---

## Current state

**2026-10-24 18:30.** Built the free tools hub at `/tools` (hub, composite, diagram, plot, journal-profile Gem page, 4 Gem texts) behind `SITE_TOOLS_ENABLED=false`: JS unit tests (21) and 12 PHP tests pass and the tools were exercised in headless Chrome; unchecked items (real Gems, PDF print tab, draw.io, phone) and 3 unrelated failing tests are in [[tools-hub-plan]] and [[todo-list]]. Nothing is committed.

**2026-10-23 11:34.** Payments moved from Midtrans to **Mayar** (hosted Mayar page; the Midtrans driver is kept): the driver, webhook (secret in the URL plus Mayar's Webhook Token) and 12 tests are built, and a sandbox payment ran end to end (redirect, webhook, order `paid`, purchase mail, set-password page) with the unfinished checks and the go-live list in [[mayar-gateway]] and [[purchase-production-checklist]]. Landing and legal pages no longer name a gateway ([[landing-checkout-form]]); dev MySQL (Windows port reservation) and Redis (`MISCONF` on low RAM) problems and fixes are in [[php-binary-and-shell]] and [[redis-setup]]. Everything is uncommitted; open work is in [[todo-list]].
