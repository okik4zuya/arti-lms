---
name: ses-migration-plan
description: Brainstorm plan to move outgoing mail from Brevo (300/day free) to Amazon SES when the quota runs out: code findings, risks, option B (SES primary, Brevo failover), phases, open decisions
metadata:
  type: project
  created: 2026-10-19 22:24
  updated: 2026-10-19 22:24
---

Brainstorm only, nothing built. Trigger: Brevo free plan (300 mails a day, shared by purchase mail, reset, welcome series, broadcasts and Gmail send-as) stops being enough. Related: [[custom-domain-email]], [[purchase-production-checklist]], [[list-building]], [[domain-migration-arti-ai-id]].

## Code findings
- All mail goes through one `smtp` mailer from `MAIL_*` env (`config/mail.php`); no Brevo API calls in the code.
- `ses` mailer already declared in `config/mail.php`, `services.ses` in `config/services.php`, `aws/aws-sdk-php` installed (used for R2). Switching = env change plus a `failover` mailer.
- `ListMailable` already has queue, retry, `List-Unsubscribe` one-click headers.
- Check before use: `AWS_*` env names must not clash with the R2 credentials in `config/filesystems.php` (not verified).

## Risks and facts
- **Bounce and complaint handling is ours**: SES can suspend above about 5% bounce or 0.1% complaint. Needs SNS to a Laravel webhook (verify signature, confirm subscription), marking subscribers `bounced`/`unsubscribed`; the series command and `SendBroadcast` must skip them. This is the only real code work.
- New SES account starts in sandbox (200/day, verified recipients only); production access is a manual request (about a day, can be refused). Argue with double opt-in, unsubscribe, bounce handling.
- DNS in the `ai.id` Cloudflare zone: Easy DKIM (3 CNAME, DNS only), custom MAIL FROM (MX + SPF TXT) for DMARC alignment; keep one merged SPF record while Brevo is also used.
- Domain reputation does not move from Brevo; ramp volume gradually.
- Cost about US$0.10 per 1,000 mails (300/day is under US$1 a month); compare with Brevo's paid plan before deciding. Region: Singapore `ap-southeast-1`; whether Jakarta has SES is unchecked.
- Use the `ses` API driver, IAM user limited to `ses:SendRawEmail`, MFA and budget alert on the AWS account.

## Recommended option B
`MAIL_MAILER=failover` with `ses` first, `smtp` (Brevo) second. Brevo stays alive for Gmail `hi@` send-as and as instant rollback (`MAIL_MAILER=smtp`).

## Phases
0. Define the trigger (for example Brevo above 70% for 3 days in a row).
1. AWS prep, free, can be done now: account, identity `arti.ai.id`, DKIM, MAIL FROM, production-access request, IAM user.
2. Code: `services.ses` + env, failover mailer, configuration set to SNS, webhook, feature tests.
3. Test in sandbox with the SES simulator addresses (`success@`, `bounce@`, `complaint@simulator.amazonses.com`); check SPF, DKIM, DMARC PASS and List-Unsubscribe in Gmail "Show original".
4. Cutover: transactional mail first, then raise `MAIL_SERIES_BATCH`, first broadcast to a small segment; watch bounce (target under 2%).
5. Rollback: `MAIL_MAILER=smtp`, restart the queue worker.

## Open decisions (asked, not yet answered)
1. Option B or full switch?
2. Do phase 1 now so production access is approved early (recommended)?
3. Build the bounce webhook now or only when the trigger is hit?

## Change log
- 2026-10-19 22:24 — created from the brainstorm.
