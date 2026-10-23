---
name: custom-domain-email
description: Gmail with the arti.ai.id custom domain via Cloudflare: receiving works (Email Routing), sending as hi@arti.ai.id still open; steps, SPF merge, gotchas
metadata:
  type: project
  created: 2026-10-19 16:00
  updated: 2026-10-19 16:00
---

Goal: use `hi@arti.ai.id` in Gmail for both receiving and sending. Brevo already sends the app's mail from `noreply@arti.ai.id` ([[domain-migration-arti-ai-id]], [[purchase-production-checklist]]).

## State (2026-10-19)
- **Receiving: done and working.** Cloudflare Email Routing forwards to the researcher's Gmail.
- **Sending: not done.** Replies still go out from the Gmail address. Stopped here on purpose; continue later.

## Remaining steps (sending as `hi@arti.ai.id`)
1. Brevo, SMTP & API, SMTP: server `smtp-relay.brevo.com`, port 587, login, SMTP key (a separate key for Gmail can be revoked on its own).
2. Brevo, Senders: add `hi@arti.ai.id` (domain already authenticated).
3. Gmail, Settings, Accounts and Import, Send mail as, Add another email address: untick "Treat as an alias", SMTP above, TLS.
4. Gmail sends a verification code to `hi@arti.ai.id`; Email Routing forwards it back to Gmail.
5. Merge SPF into one TXT record: `v=spf1 include:_spf.mx.cloudflare.net include:spf.brevo.com ~all` (two SPF records break SPF). Keep DKIM `brevo1._domainkey` and `_dmarc`.
6. Test: send from Gmail with From `hi@arti.ai.id`, "Show original" must say SPF, DKIM, DMARC PASS; reply to an incoming mail keeps that From.

## Gotchas
- Cloudflare treats `ai.id` as the zone and `arti.ai.id` as a subdomain; if routing on `@arti.ai.id` ever stops, Email Routing, Settings, Add subdomain.
- Brevo free plan: 300 mails a day shared with the app (purchase mails, series), Gmail sends eat into it.

## Change log
- 2026-10-19 16:00 — created: receiving done, sending open.
