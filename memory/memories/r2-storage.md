---
name: r2-storage
description: Cloudflare R2 buckets (arti public, arti-member, arti-private), which data goes where, how the app reaches R2, and the gotchas found while wiring payment proofs
metadata:
  type: reference
  created: 2026-10-15 15:17
  updated: 2026-10-16 00:11
---

Context: payment proofs of [[license-admin-roadmap]] moved to Cloudflare R2 on 2026-10-15; the installer hosting decision lives in the same roadmap.

## Buckets (set up by the researcher on 2026-10-15)
| Bucket | Access | Purpose |
|---|---|---|
| `arti` | **public**, custom domain `cdn.arti.ai.id` | public media: large videos, images, free installers |
| `arti-member` | not stated; assumed members-only (paid) | presumably the paid installer and member downloads, served through short-lived links; confirm before building |
| `arti-private` | private | **payment proofs** (`license-proofs/`) and anything with personal or bank data |

Rule: never put personal or bank data in `arti` (anyone who knows or guesses the path can read it). One API token per bucket, scoped to that bucket, with Client IP filtering to the VPS (plus the dev machine's IP for a dev token).

## How the app uses R2
- `config/filesystems.php`: disk `r2` (S3 driver, `visibility` private, path style) reads `R2_ACCESS_KEY_ID`, `R2_SECRET_ACCESS_KEY`, `R2_BUCKET`, `R2_ENDPOINT`; `proofs_disk` reads `PROOFS_DISK` (default `local`, `r2` in production). Package `league/flysystem-aws-s3-v3` is installed.
- Proofs are streamed through Laravel (`LicenseProofController`, signed 5-minute route, `license.view`), never via a bucket URL. One bucket per `R2_BUCKET`; today the `r2` disk is the proofs bucket, so a second purpose (member downloads) needs its own disk entry and token.
- `R2_ENDPOINT` must be `https://<account-id>.r2.cloudflarestorage.com`. A custom domain (here `cdn.arti.ai.id`) is read-only public delivery and fails on write with a non-XML error.
- Cloudflare UI names: "Public Development URL" (r2.dev) and "Custom Domains" under bucket Settings control public access; there is no "Public access" section anymore.
- Never open `.env`; the researcher edits the variables. Connection test used a standalone PHP script that boots Laravel (write, exists, read, delete a `_connection-test.txt` key): worked on 2026-10-15 against `arti`.

## State at 2026-10-15 15:30 (end-to-end check passed: upload from the panel and "Lihat bukti" work against `arti-private`, confirmed by the researcher)
- `R2_BUCKET` now resolves to **`arti-private`** and the write/read/delete test passed. Decision: keep the single `r2` disk for now (proofs only); a disk per purpose (`r2_private`, `r2_member`, token per bucket, `PROOFS_DISK`/`MEMBERS_DISK`) is the agreed pattern for when `arti-member` gets a feature.
- Two dev license rows still point at proofs that are not in `arti-private`: `license-proofs/8HAL2SOt...jpg` (object still sits in the public `arti` bucket, delete it there) and `license-proofs/VFIe3lcB...jpg` (local disk only). Both are test uploads: re-attach or ignore ("Lihat bukti" answers 404 for them).

## Change log
- 2026-10-15 15:17 — created from the bucket setup and the proofs wiring.
