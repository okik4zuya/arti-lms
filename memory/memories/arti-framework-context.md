---
name: arti-framework-context
description: What the ARTi Framework/ebook is, and how it relates to this LMS project — background needed to understand what content will eventually be loaded
metadata:
  type: project
  created: 2026-09-08 23:22
  updated: 2026-09-08 23:22
---

**ARTi Workflow / ARTi Framework** is a research-writing methodology and Claude-skill package for
academic researchers, built around three meanings of the name: **ART + i** ("state of the art" +
the researcher's innovation on top of it), **Amati, Replikasi, Tambah inovasi** ("Observe,
Replicate, Add innovation" — the literal operating loop), and Indonesian *arti* ("meaning"). The
package's flagship product is an Indonesian-language **ebook** (4 parts, 13 chapters + appendix)
teaching this loop, alongside Claude skills (`ARTi-idea`, `ARTi-writing`) that operationalize it.

**Relationship to arti-lms:** the ebook is being drafted in a separate project (not this repo) and
is not yet finished. This repo (`arti-lms`) is the **delivery platform** — an LMS — that the
finished ebook content will be loaded into once it exists. The LMS build is independent of the
ebook's writing progress and can proceed in parallel; content migration (pushing the ebook's
Markdown chapters into whichever content-storage mechanism the LMS ends up using — see
[[tech-stack-options]]) is a later lifecycle phase, not something blocking the LMS build itself.

**Business model context:** public-course scale (hundreds–thousands of learners), sold via
Facebook ads → landing page → WhatsApp → manual payment, MVP uses admin-manual learner account
creation (no self-registration); an automated Scalev checkout integration is a documented but
deferred later phase.

## Change log
- 2026-09-08 — captured ARTi Framework background context for arti-lms
