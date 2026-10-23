# qlu-monthly-newsletter

WordPress **Jnanodayam reader / blog** for Quantum Life University — full Te + English readings. Primary series: **Devi Navaratrula Jnanam** (10 days). Optional sample **monthly magazine** issues.

Specs: [docs/README.md](./docs/README.md) · **Share / rebuild content:** [docs/SHARE.md](./docs/SHARE.md)

---

## What’s in this repo

| Path | Purpose |
|------|---------|
| `docker-compose.yml` | Local WordPress 6.7 + MySQL 8 |
| `wp-content/themes/qlu-magazine/` | Soft-paper reader theme |
| `wp-content/mu-plugins/qlu-jnanodayam-cpt.php` | `qlu_jnanodayam` readings (series / day / lang) |
| `wp-content/mu-plugins/qlu-issue-cpt.php` | Optional monthly `qlu_issue` CPT |
| `assets/import/` | Loose sample images (optional; magazine prefers folder packs) |
| `content/magazine/YYYY-MM/` | **Magazine seed packs** (section folders + photos + txt) — [docs/MAGAZINE-SEED.md](./docs/MAGAZINE-SEED.md) |
| `content/jnanodayam-devi/` | Devi day HTML source (commit before sharing) |
| `scripts/` | Import / bootstrap / magazine seed helpers |
| `docs/` | Product notes + [SHARE.md](./docs/SHARE.md) |

**Git does not include the WordPress database.** After clone, follow [docs/SHARE.md](./docs/SHARE.md) so others get the same readings and sample magazine.

---

## Quick start (local)

```powershell
cd c:\Users\dhana\GitHub\qlu-monthly-newsletter
copy .env.example .env
.\scripts\up.cmd
```

1. Open **http://localhost:8080** and finish the WordPress installer.  
2. Seed content (theme + Devi readings + sample magazine):

```powershell
python scripts\bootstrap_content.py
# or: .\scripts\bootstrap-content.cmd
```

Details / manual fallback: **[docs/SHARE.md](./docs/SHARE.md)**.

**Magazine from folders** (section-wise photos + `content.te.txt` / `content.en.txt`):

```powershell
python scripts\seed_magazine_from_folder.py --month 2026-10
# new month:  ... --month 2026-11 --init-from-template
```

Convention: **[docs/MAGAZINE-SEED.md](./docs/MAGAZINE-SEED.md)**.

Then:

| | |
|--|--|
| Programme | http://localhost:8080/ |
| Day 1 EN | http://localhost:8080/jnanodayam/devi-navaratri-day-01-en/ |
| Magazine Te | http://localhost:8080/issues/october-2026-te/ |

---

## Languages (v1)

- **Telugu** and **English** only.  
- One reading post per day per language.  
- Te | En switch + Previous / Next day on each reading.
