# Sharing this project — rebuild content from scripts

The git repo has **theme + plugins + scripts + sample assets**. It does **not** include the WordPress database.

Anyone who clones and runs Docker gets an empty site until they run the seed/import steps below. After that they should see the same **Jnanodayam Devi readings** and **sample magazine** (layout may differ slightly from your machine if you edited posts by hand later).

---

## What is in git vs what is rebuilt

| In the repo | Rebuilt by scripts (into Docker WP DB + uploads) |
|-------------|--------------------------------------------------|
| `wp-content/themes/qlu-magazine/` | 10 days × Te + En Jnanodayam posts |
| `wp-content/mu-plugins/` (CPTs) | Programme front page + permalinks |
| `assets/import/` (magazine photos + logo) | Magazine Oct 2026 Te + En issues + Media Library |
| `scripts/*.php` | Theme activation, `.htaccess`, custom logo |
| `content/jnanodayam-devi/` (Devi day HTML — see §2) | Full reading body text |

**Not shared via git:** MySQL volume, `wp-content/uploads/` inside the container, your admin password.

---

## Prerequisites

- Docker Desktop running  
- Git clone of **this** repo  
- Python 3.9+ (`python` or `py -3` on Windows)  
- PowerShell (Windows) or bash  

---

## Automated (recommended)

One script runs theme activate, `.htaccess`, Jnanodayam import, magazine enrich, and link fix.

```powershell
cd <path-to>\qlu-monthly-newsletter
copy .env.example .env          # first time only
.\scripts\up.cmd                # or: python scripts\bootstrap_content.py --start
```

Open http://localhost:8080 and finish the **WordPress installer** (once). Then:

```powershell
python scripts\bootstrap_content.py
# or:
.\scripts\bootstrap-content.cmd
```

Options:

| Flag | Effect |
|------|--------|
| `--start` | `docker compose up -d` first |
| `--skip-magazine` | Only Jnanodayam readings |
| `--skip-devi-copy` | Do not copy from sibling `quantum-life-university` folder |

The script will:

1. Use `content/jnanodayam-devi/` if present, else copy from sibling  
   `../quantum-life-university/Swadhyaya/9-Day-Devi-Navaratri-Dr-Newton/`  
2. Require `assets/import/` for the magazine step  
3. `docker compose cp` + run the PHP helpers in order  

Manual steps below remain as a fallback.

---

## 1. Start WordPress (manual)

```powershell
cd <path-to>\qlu-monthly-newsletter
copy .env.example .env
.\scripts\up.cmd
```

Open http://localhost:8080 and finish the **5-minute install** (site title, admin user, password).

Container name is usually `qlu-monthly-newsletter-wordpress-1` (check with `docker compose ps`).

---

## 2. Devi HTML source (required for Jnanodayam import)

Put the day HTML folder on disk as **`content/jnanodayam-devi/`** inside this repo (recommended for sharing):

```
content/jnanodayam-devi/
  day-01-en.html … day-10-te.html
  index.html
  assets/devi-navaratrula-jnanam-banner.png
```

**If you already have** the sibling folder  
`..\quantum-life-university\Swadhyaya\9-Day-Devi-Navaratri-Dr-Newton\`  
copy it once:

```powershell
New-Item -ItemType Directory -Force -Path content | Out-Null
Copy-Item -Recurse "..\quantum-life-university\Swadhyaya\9-Day-Devi-Navaratri-Dr-Newton\*" content\jnanodayam-devi\
```

Commit `content/jnanodayam-devi/` so others do not need the sibling repo.

---

## 3. Activate theme + pretty permalinks

```powershell
docker cp scripts\activate-theme.php qlu-monthly-newsletter-wordpress-1:/tmp/activate-theme.php
docker compose exec -T wordpress php /tmp/activate-theme.php

docker cp scripts\fix-htaccess.php qlu-monthly-newsletter-wordpress-1:/tmp/fix-htaccess.php
docker compose exec -T wordpress php /tmp/fix-htaccess.php
```

You should see theme **QLU Magazine** and working rewrite rules.

---

## 4. Import Jnanodayam (Devi days 1–10, Te + En)

```powershell
docker cp content\jnanodayam-devi qlu-monthly-newsletter-wordpress-1:/tmp/jnanodayam-devi
docker cp scripts\import-jnanodayam-devi.php qlu-monthly-newsletter-wordpress-1:/tmp/import-jnanodayam-devi.php
docker compose exec -T wordpress php /tmp/import-jnanodayam-devi.php
```

Expect ~20 readings and home set to the Programme page.

- Hub: http://localhost:8080/  
- Example: http://localhost:8080/jnanodayam/devi-navaratri-day-01-en/

Re-running the import **updates** the same series/day/lang posts (safe to repeat).

---

## 5. Sample magazine (preferred: folder pack)

Section folders + photos + txt — see **[MAGAZINE-SEED.md](./MAGAZINE-SEED.md)**.

```powershell
python scripts\seed_magazine_from_folder.py --month 2026-10
```

- Te: http://localhost:8080/issues/october-2026-te/  
- En: http://localhost:8080/issues/october-2026-en/  
- Archive: http://localhost:8080/issues/

**Legacy:** `scripts/enrich-sample-from-assets.php` + `assets/import/` still works if you prefer a flat image dump.

---

## 6. Checklist for the person receiving the repo

- [ ] `docker compose up -d` and WP install done  
- [ ] `python scripts\bootstrap_content.py` succeeds (or manual §3–§5)  
- [ ] Programme lists Days 1–10; magazine issues open with images  
- [ ] Magazine links go to `/` and `/jnanodayam/…`, not GitHub Pages  

---

## 7. What maintainers should commit before sharing

So others can run the bootstrap without hunting files:

| Commit | Why |
|--------|-----|
| `assets/import/*` | Magazine images + logo |
| `content/jnanodayam-devi/**` | Full Devi day HTML + banner |
| `scripts/bootstrap_content.py` (+ `bootstrap-content.cmd`) | One-shot seed |
| `scripts/import-jnanodayam-devi.php` | Readings import |
| `scripts/enrich-sample-from-assets.php` | Magazine seed |
| `scripts/activate-theme.php`, `fix-htaccess.php`, `fix-magazine-jnanodayam-links.php` | Bootstrap helpers |
| Theme + mu-plugins | Runtime |

Do **not** commit `.env` or DB dumps unless you intentionally choose a different share model.

Optional: omit huge duplicate images under `assets/import` if hashes match (keep unique files only).

---

## 8. Reset / start over

```powershell
.\scripts\down.cmd
docker compose down -v
```

`-v` deletes MySQL + WP volumes. Then repeat from §1.

---

## 9. Limits of this approach

- Hand-edits in WP Admin after seeding are **not** in git — re-import overwrites seeded posts.  
- New magazine months must be authored in WP or added to seed scripts.  
- For a pixel-identical clone of a live edited site, use a DB + uploads dump instead (not covered here).

---

*Canonical path: `docs/SHARE.md`.*
