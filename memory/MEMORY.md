---
name: memory-index
description: Index of arti-lms project memory files, pointers only
metadata:
  type: project
  tier: T0
  created: 2026-09-08 21:36
  updated: 2026-10-14 23:58
---

# arti-lms — Memory Index

- [ARTi Framework context](memories/arti-framework-context.md) — what the ebook/framework is (finalized 5-section/18-chapter outline), drafted in this repo's own resources/inbox/
- [Subchapter hierarchy](memories/subchapter-hierarchy.md) — Section>Chapter>Subchapter content level, per-subchapter progress; sidebar is a `<details>` accordion with per-level type scale and badges numbered continuously per section
- [Tech stack options](memories/tech-stack-options.md) — 3 candidate stacks; Option 3 (git-backed Markdown, server-rendered) + relative-path image workflow chosen
- [Option 3 implementation plan](memories/option3-implementation-plan.md) — full build checklist + Laravel setup commands
- [Testing DB isolation](memories/testing-db-isolation.md) — phpunit.xml must point at arti_lms_testing, not the dev DB; RefreshDatabase wiped dev data once
- [Course layout responsive](memories/course-layout-responsive.md) — off-canvas sidebar + sections/chapters manifest grouping; clear the manifest cache when re-verifying; headless Chrome/Edge work directly; mobile drawer QA still open
- [Branding: login page](memories/branding-login.md) — login redesign, `/` redirects to login/dashboard, primary blue from the logo dot (#2563eb)
- [Feedback: check memory before env probes](memories/feedback-check-memory-before-env-probes.md) — don't re-run discovery for facts memory already records
- [Content authoring workflow](memories/content-authoring-workflow.md) — rules for drafting arti-framework subchapters, plus the Markdown rendering pipeline (GFM tables, wiki-link resolution, per-subchapter `<style>`)
- [Content authoring progress](memories/content-authoring-progress.md) — narrative status per chapter; the checklist itself lives in todo-list.md
- [ARTi App architecture](memories/arti-app-architecture.md) — this repo grows into the ARTi App; licensing stays on its Cloudflare Worker; phase-1 public routes built, risks and next steps
- [Landing page info](memories/landing-page-info.md) — sources, assets, proof, contradictions and open decisions for the landing page
- [Landing page concept](memories/landing-page-concept.md) — positioning, copy formula, outline, offer rules and the build record, including media placed (explainer, dashboard, e-course screenshots)
- [Landing page keywords](memories/landing-page-keywords.md) — YouTube keyword run on pain wording; pain words have little demand, so the page must teach the problem
- [Landing page copy](memories/landing-page-copy.md) — draft v1 of the Indonesian copy, section by section, with a fact-check list
- [Landing explainer storyboard](memories/landing-explainer-storyboard.md) — the 40 s explainer: 7 scenes, build and render recipe, installed on the landing page
