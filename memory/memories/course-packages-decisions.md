---
name: course-packages-decisions
description: Decisions and reasoning from the 2026-10-19 course-package brainstorm: product map, licence rule, package/OTO design, pre-launch rules, existing-buyer flow, system impact estimate, what is still undecided
metadata:
  type: project
  created: 2026-10-19 08:39
  updated: 2026-10-19 08:39
---

Brainstorm session started from [[course-packages-prompt]]. Discussion only: no code, page or copy was changed, nothing committed. Builds on [[automatic-purchase-plan]], [[landing-checkout-form]], [[landing-page-concept]]. Demand data: [[landing-page-keywords]]. SLR page material: [[landing-slr-workflow]].

## Finding that shaped it
The unwritten e-course chapters Amati, Replikasi, Tambah Inovasi (12 chapters) already are a generic "Workflow Paper Riset". `manifest.yaml` has 6 sections (Download added); only Pendahuluan (19 subchapters) is written; the others are placeholders with English titles. No buyer exists yet (not launched), so restructuring costs nothing in refunds or grandfathering.

## Product map (agreed 2026-10-19)
- **ARTi Framework** (main landing product): licence + Pendahuluan + Download + **Workflow Paper Riset** (Amati, Replikasi, Tambah Inovasi) + BONUS. The only product that includes the licence. Paper Riset stays inside it because the landing promises "dari ide sampai publish"; making it an add-on would make the base product feel incomplete.
- **Workflow Paper SLR**: second course, no licence. Reason: proven demand, free Claude Code + PRISMA angle, real material ([[landing-slr-workflow]]).
- **Workflow Proposal BIMA** (or other grants): on hold. Demand is for portal form-filling (TKT, RAB, upload), not AI-written substance; yearly rules make a course go stale. Open: does ARTi have a grant-proposal skill (none among the 10 seen), and will the researcher maintain it yearly. Cheap test before writing: one YouTube video on AI for the BIMA proposal substance.
- Writing order: Paper Riset first (already promised), then SLR, BIMA only after evidence.

## Rules agreed
1. A workflow course is sold only to licence holders or inside a package with ARTi Framework (without the tool it cannot be practised; no dead-end purchases).
2. The licence comes only from ARTi Framework or from the ARTi item of a package.
3. Discount is on the course side. In a package the tool is priced at the current tier (promo > perintis > normal) and takes a launch place; otherwise someone could buy the course to get the tool cheaper than the perintis price.
4. Honest claim: "Diskon paket hanya berlaku bila keduanya dibeli bersamaan." Not "sekali saja" (a package is not a true OTO when the tool is required). No countdown, no re-opening of the same price elsewhere; the product itself stays on sale at normal price, only the discount disappears.
5. Existing buyers buy further courses inside `/app` while logged in; a public form keyed on email can end in a course-only account without a licence.
6. Placement: a pre-payment package or add-on at the checkout (one Midtrans transaction). A post-payment OTO needs a second transaction (VA/QRIS has no one-click, fees twice), so it is not recommended; if ever used, the deadline is a real timestamp shown on the page and the offer is tied to the parent order.
7. Pre-launch is allowed: visible chapter status (Terbit / Belum), no "complete" claim, price ends by a real criterion, dates only if keepable, about a third of chapters published before selling (proposal), refund wording for unpublished promised chapters to a lawyer.

## Terms (for later copy)
Order bump = tick box in the form before paying. Upsell = bigger version before paying. Bundle = fixed combination, always sold. Post-purchase offer = placement after paying. OTO = availability condition (price exists only at that moment); usually post-purchase. The agreed design is a bundle/package, not a pure OTO.

## System impact (estimate only, nothing built; medium, about one big session plus tests)
- `Order` has no items and `site.offer.course_slug` / `Offer` know one product and one price; Snap gets one fixed item.
- `CreateCheckout` refuses an email with an active licence (`AlreadyPurchasedException`): must allow buying an add-on course.
- `FulfillPaidOrder` always grants one course and issues a licence: must issue a licence only if the order has a licence-bearing item, and grant access per item.
- `RefundOrder` removes one course: must work per item; the licence is revoked only if the licence-bearing item is refunded.
- Needed: product catalogue with a "includes licence" flag, order items, per-course access (`course_access` has `granted_at` only, no expiry), purchase from `/app`, per-course pages with their own checkout, package price calculation shown in numbers.
- **Do not build before SLR has enough chapters to be sold**; today only one product can be sold and the single-product flow is enough for launch.

## Still to discuss (agenda items 3, 5, 6 of the prompt)
- Price architecture: price of SLR (normal, pre-launch), size of the package discount and how the page shows the arithmetic, interaction with the promo hierarchy.
- Copy and legal impact: landing, `/syarat` (refund per item, pre-launch), `/privasi`, comparison table, what a lawyer must read.
- Work order and the smallest first step.

## Dropped
- Asking Midtrans support whether an individual account may sell e-courses and software licences: the researcher judged it unnecessary (similar products already sell through Midtrans), 2026-10-19. Production activation still needs KTP and NPWP ([[purchase-production-checklist]]).

## Change log
- 2026-10-19 08:39 — created from the brainstorm (product map, rules, system impact, open items).
