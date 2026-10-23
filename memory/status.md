---
name: status
description: Living current-state pointer for arti-lms
metadata:
  type: project
  tier: T0
  created: 2026-09-08 21:36
  updated: 2026-10-19 22:24
---

## Current state

**2026-10-19 22:24.** Brainstormed moving mail from Brevo to Amazon SES once 300 a day is not enough: plan, risks and open decisions are in [[ses-migration-plan]] (nothing built). Earlier today: Production deploy troubleshooting: the server build failed because the committed `vite.config.js` still listed the deleted `contact-panel.js` (fix is local, uncommitted); "Kenapa murah sekarang" was removed from the landing; a `SITE_CHECKOUT_ENABLED` kill switch now greys out the buy form with a "Segera dibuka" notice while the Midtrans application is under review ([[purchase-production-checklist]]). Custom-domain email: receiving at `hi@arti.ai.id` works through Cloudflare Email Routing, sending as that address from Gmail is still open ([[custom-domain-email]]). Earlier today the VPS outage and backups ([[vps-oom-incident-2026-10-19]], [[vps-maintenance-guide]]) and the course-package brainstorm ([[course-packages-decisions]]); production runs on the shared VPS at arti.ai.id ([[domain-migration-arti-ai-id]]); open work is in [[todo-list]].
