---
name: subchapter-hierarchy
description: Added 3rd content level (Section > Chapter > Subchapter); progress now tracked per-subchapter, chapters are pure grouping labels with no content file; sidebar numbering runs continuously per-section (fixed a restart-per-chapter bug)
metadata:
  type: project
  created: 2026-09-09 13:40
  updated: 2026-09-10 21:27
---

Implemented `~/.claude/plans/read-resources-inbox-task-md-for-this-whimsical-balloon.md` Part 2, on
top of [[course-layout-responsive]]'s Section→Chapter grouping. Reason: chapters were too long as
the unit of progress for the ARTi Framework ebook course.

**Design:** chapters became pure grouping labels (no content file of their own); every subchapter
gets its own content page. Subchapter slugs stay **flat and globally unique per course** (same
convention chapters used before) — content lives at
`resources/content/{course_slug}/{subchapter_slug}/index.md`. The existing route
(`learn/{course}/{slug}`) and `ContentController::image()` needed **zero path/regex changes** —
`slug` now just means "subchapter slug."

**Changed files:**
- `database/migrations/2026_09_08_170128_create_progress_table.php` — edited in place (no prod
  data existed): `chapter_slug` → `subchapter_slug` column + unique constraint.
- `app/Models/Progress.php` — `$fillable` renamed.
- `ContentController::show()` — progress query column renamed only; no path change.
- `ProgressController::store()` — writes `subchapter_slug` now.
- `DashboardController` — `flattenChapters()` → `flattenSubchapters()`, now flattens 3 levels
  (`sections[].chapters[].subchapters[]`); `firstChapterSlug`/`totalChapters`/`completedChapters` →
  `firstSubchapterSlug`/`totalSubchapters`/`completedSubchapters`.
- `resources/views/content/show.blade.php` — sidebar gained a non-clickable `.tree-subgroup` label
  (chapter) between `.tree-group` (section) and the subchapter `<a>` links.
- `resources/css/app.css` — added `.tree-subgroup` rule near `.tree-group`/`.tree-item`.
- `resources/views/dashboard.blade.php` — `firstChapterSlug` reference renamed.
- Manifest schema: chapters now carry a `subchapters: [{slug, title}]` list instead of being leaf
  content nodes. `example-course`/`second-course` manifests updated to wrap their existing
  `01-intro` content dir as a subchapter under a new label-only `introduction` chapter (no directory
  renames needed). `arti-framework/manifest.yaml` got the full 18-chapter/5-section ARTi ebook
  skeleton (see [[content-repo-pointer]] / `resources/inbox/ebook-outline-v2.md`) with **one
  placeholder subchapter slot per chapter** — no subchapter content actually drafted yet, per the
  "structural skeleton only" decision. Only `arti-framework/01-intro` has a real content file;
  the other 17 chapters' placeholder subchapter slugs have no content dir yet (404 if visited,
  expected until each chapter is drafted).

**Verification:** `php artisan migrate:fresh --seed` (dev DB had only 2 users/1 course/1
course_access/1 progress row, manually recreated after — `test@example.com`/`password` learner +
`admin@example.com`/`password` admin + `example-course` access, same as before). Manifest cache
cleared (`php artisan cache:clear`, known gotcha from [[course-layout-responsive]]). Verified via
`artisan tinker` simulated requests (no browser tool available, same limitation as before):
dashboard shows 3-level-flattened subchapter progress (0% → 100% after marking `01-intro`
complete), content page sidebar renders section → chapter-label → subchapter-link three levels
with the active-page highlight and completion checkmark, `progress` table row confirmed written
with `subchapter_slug = '01-intro'`. `php artisan test`: FilamentPanelAccessTest still passes;
the one failure (`Tests\Feature\ExampleTest` expecting `/` → 200) is a pre-existing stub-test issue
unrelated to this change (todo-list already flags replacing the stub tests).

**2026-09-10 follow-up — sidebar accordion:** real-browser use (screenshot) surfaced two UX bugs the
tinker-only verification missed: the chapter label (`.tree-subgroup`) wasn't clickable, and with
chapter/subchapter titles identical (placeholder content, [[subchapter-hierarchy]] §1 above) the
two levels read as one flat duplicated list rather than a hierarchy. User confirmed via
AskUserQuestion: wants an accordion (chapter toggles open/close), and confirmed the blue-link 404s
on undrafted chapters are expected, not a bug to fix here. Fix: `.tree-subgroup` div → native
`<details class="tree-chapter">`/`<summary>` (no JS needed), `open` attribute set when
`collect($chapter['subchapters'])->contains('slug', $slug)`; CSS added a rotating chevron
(`.tree-subgroup::after`) and extra left-padding (`--space-5`) on `.tree-item` so subchapters
visibly nest. Verified via tinker rendering `ContentController::show()` for `arti-framework`/
`01-intro`: only chapter 0's `<details>` carries `open`, the other 17 don't.

**2026-09-10 follow-up — readability/visual-hierarchy pass:** ran from a saved plan
(`~/.claude/plans/i-want-to-optimize-cozy-crystal.md`), independent of content drafting. Problem:
section/chapter/subchapter all rendered at nearly the same font size, so the tree still read flat
even after the accordion fix above. Changes, all in `.tree-group`/`.tree-subgroup`/`.tree-item`
(`resources/css/app.css`) + the subchapter `<a>` markup (`resources/views/content/show.blade.php`):
section labels dropped to `--font-size-xs` with a real `border-top` divider + `--space-6` margin
(skipped on `:first-child`); chapter labels bumped to `--font-size-base` (was tied with subchapter
size) with the open-chevron recolored to `--color-primary`; subchapters gained a 20px filled-circle
`.tree-item__index` badge showing `$loop->iteration` (numbering restarted per chapter — bug,
fixed 2026-09-10 21:27, see follow-up below), swapping to
`--done` (green fill + checkmark) via the existing `$completedSlugs` check — replaces the old
`tree-item__check` chip for this spot in the markup only. **`tree-item__check`/`--done` CSS was
deliberately kept, not deleted**, because `show.blade.php`'s separate "chapter marked complete"
banner (outside the sidebar loop) still uses that class — the plan's "replace the rules" wording
would have broken that if taken literally.

**2026-09-10 follow-up — numbering-restart bug fixed:** real content (chapter 2, three subchapters
after chapter 1's three) surfaced the bug flagged above as a real user-visible issue via screenshot:
`$loop->iteration` is scoped to the inner `@foreach ($chapter['subchapters'])` loop, so it reset to
1 at every chapter boundary instead of continuing — chapter 2's subchapters showed "1, 2, 3" again
instead of "4, 5, 6". Fixed in `content/show.blade.php`: a `$subchapterNumber` counter is now
initialized to 0 once per **section** (outer loop) and incremented inside the subchapter loop, so
numbering runs continuously across all chapters within a section and only resets at the next
section boundary — matching the section header's existing visual break. Not verified in a real
browser this pass (no browser tool available this session either); logic was read-verified only.

## Change log
- 2026-09-09 13:40 — Created. Subchapter level added end-to-end (migration, model, 3 controllers,
  2 views, CSS, 3 manifests), migrate:fresh + tinker-verified.
- 2026-09-10 03:43 — Added sidebar accordion fix (`<details>`/`<summary>` + chevron CSS) for the two
  UX bugs found in real-browser use; tinker-verified only-active-chapter expands.
- 2026-09-10 04:48 — Readability/visual-hierarchy pass (per-level type scale, section dividers,
  numbered badges); real headless-Chrome screenshot verified (see [[course-layout-responsive]] for
  the corrected browser-tooling fact this relied on).
- 2026-09-10 21:27 — Fixed the numbering-restart bug the 04:48 entry had flagged but not yet hit in
  practice: switched `$loop->iteration` to a per-section running counter so subchapter numbers
  continue across chapters instead of resetting at each one. Found via user screenshot, read-verified
  only (no browser tool available).
