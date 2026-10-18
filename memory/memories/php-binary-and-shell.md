---
name: php-binary-and-shell
description: Where php lives for artisan and tests on this machine, and the shell gotchas that cost time (Bash heredocs with quotes, CRLF views, sed and backslashes)
metadata:
  type: reference
  created: 2026-10-18 15:40
  updated: 2026-10-18 15:40
---

- **PHP for artisan and tests:** `C:\laragon6\bin\php\php-8.4.12-nts-Win32-vs17-x64\php.exe` (Bash path `/c/laragon6/bin/php/php-8.4.12-nts-Win32-vs17-x64/php.exe`). A bare `php` is not on the Bash PATH (`php.bat: command not found`); the researcher pointed to Laragon's `bin` folder. Run tests per file: `php artisan test tests/Feature/<File>.php`. MySQL (Laragon) must be running first; if it is not, every DB test fails with "actively refused" (port 3306 excluded range, see [[list-building]]).
- **Writing files:** a Bash heredoc that contains an apostrophe or certain quote mixes can fail with "unexpected EOF while looking for matching" and write nothing. Use the Write tool for files; for multi-line replacements write a small PHP script with the Write tool into the scratchpad and run it.
- **Line endings:** many Blade views and tests are CRLF. Replace text with a script that normalises `\r\n` to `\n`, edits, and writes back in the original ending; `sed` and the Edit tool both trip on this for multi-line blocks.
- **sed eats backslashes** in PHP namespaces ([[contact-whatsapp]]); never use it for lines containing `\`.
- **PowerShell `Set-Content -Encoding utf8` adds a BOM** that breaks PHP files ("Namespace declaration statement has to be the very first statement"); do not use it on PHP.
- PHPUnit reserves method names such as `callback()`; Filament and Livewire test helpers differ for modal bodies, so render plain Blade views directly when asserting content ([[license-admin-roadmap]] pattern).

## Change log
- 2026-10-18 15:40 — created.
