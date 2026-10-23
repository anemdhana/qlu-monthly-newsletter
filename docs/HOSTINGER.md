# Deploy on Hostinger — theme, Devi readings, sample magazine

Guide to put **qlu-monthly-newsletter** live on [Hostinger](https://www.hostinger.com/pricing) and seed:

- **Jnanodayam** — Devi Navaratrula Jnanam (10 days × Te + En)  
- **Magazine** — sample month from `content/magazine/YYYY-MM/`  
- Soft-paper theme + CPTs  

Local rebuild (Docker): [SHARE.md](./SHARE.md). Magazine folder rules: [MAGAZINE-SEED.md](./MAGAZINE-SEED.md).

**Recommended plan for outreach:** **Unlimited** (daily backups, 50 GB, room to grow). Premium is only if you stay tiny.

---

## What you will have when done

| URL (examples) | Content |
|----------------|---------|
| `https://YOUR-DOMAIN/` | Programme hub — Days 1–10 |
| `https://YOUR-DOMAIN/jnanodayam/devi-navaratri-day-01-en/` | Full Day 1 English reading |
| `https://YOUR-DOMAIN/jnanodayam/…-te/` | Telugu twin |
| `https://YOUR-DOMAIN/issues/2026-10-te/` | Sample magazine Te |
| `https://YOUR-DOMAIN/issues/2026-10-en/` | Sample magazine En |

---

## Path A — Easiest: migrate from local Docker (recommended first launch)

Build everything locally, then upload one package. Best for first go-live.

### A1. Finish content on your PC

```powershell
cd c:\Users\dhana\GitHub\qlu-monthly-newsletter
copy .env.example .env
.\scripts\up.cmd
```

1. Open http://localhost:8080 → complete WordPress install.  
2. Seed Devi + base content:

```powershell
python scripts\bootstrap_content.py
```

3. Seed sample magazine from folders:

```powershell
python scripts\seed_magazine_from_folder.py --month 2026-10
```

4. Spot-check Programme, Day 1, magazine Te/En locally.

### A2. Create WordPress on Hostinger

1. hPanel → **Websites** → **Add website** → **WordPress**.  
2. Claim free domain (or use subdomain).  
3. Note admin URL / user / password.  
4. Confirm **SSL** is on.

### A3. Export local → import Hostinger

**On local WordPress**

1. Plugins → Add **All-in-One WP Migration** (or Duplicator).  
2. Export → download `.wpress` (or Duplicator package).  
3. If export is huge: increase limits in the plugin’s “How to” / Hostinger PHP limits, or export without unused media.

**On Hostinger WordPress**

1. Install the **same** migration plugin.  
2. Import the file.  
3. Log in again if asked.  
4. **Settings → Permalinks → Post name → Save** (twice if needed).  
5. **Appearance → Themes** — confirm **QLU Magazine** is active.

### A4. Verify live

- `/` lists Days 1–10 with English · తెలుగు  
- One Te + one En reading opens full text  
- `/issues/2026-10-te/` and `-en/` show photos + sections  
- Phone: Telugu glyphs OK  

Done for launch. Later months: edit on Hostinger **or** seed from folders via Path B §B5.

---

## Path B — Fresh Hostinger install + seed from this git repo

Use when you want a clean WP on Hostinger and rebuild from `content/` + `scripts/` (no local export).

### B1. Create WordPress (same as A2)

hPanel → Add website → WordPress → SSL on.

### B2. Upload code from git

Clone or download zip of https://github.com/anemdhana/qlu-monthly-newsletter  

Upload via **File Manager** or **FTP/SFTP**:

| From repo | To Hostinger (typical) |
|-----------|-------------------------|
| `wp-content/themes/qlu-magazine/` | `public_html/wp-content/themes/qlu-magazine/` |
| `wp-content/mu-plugins/*.php` | `public_html/wp-content/mu-plugins/` (create folder) |

Optional seed tree (keep **outside** public web if possible):

```
/home/USER/qlu-seed/
  content/jnanodayam-devi/     ← Devi HTML
  content/magazine/            ← magazine packs
  assets/import/               ← optional
  scripts/                     ← PHP import helpers
```

If you only have access under `public_html`, use e.g. `public_html/_qlu-seed/` and delete or protect it after seeding (do not leave import scripts world-writable/public long-term).

### B3. Activate theme + permalinks

1. WP Admin → **Appearance → Themes → Activate QLU Magazine**  
2. **Settings → Permalinks → Post name → Save**

CPTs (`Jnanodayam`, `Issues`) appear in the left menu after mu-plugins are in place.

### B4. Seed Devi (Jnanodayam) — needs SSH

Hostinger **Unlimited** usually includes **SSH**. In hPanel → **Advanced → SSH Access**, enable and connect (PuTTY / Windows Terminal).

```bash
# Example paths — adjust USER and home layout
WP=~/domains/YOUR-DOMAIN/public_html
# or: WP=~/public_html

SEED=~/qlu-seed
cd "$SEED"

# Paths expected by the import script
rm -rf /tmp/jnanodayam-devi
cp -a content/jnanodayam-devi /tmp/jnanodayam-devi

cd "$WP"
php "$SEED/scripts/import-jnanodayam-devi.php"
```

If `php` is not in PATH, use Hostinger’s PHP binary (hPanel often shows version), e.g. `/opt/alt/php82/usr/bin/php`.

**Activate theme / permalinks helpers (optional):**

```bash
cd "$WP"
php "$SEED/scripts/activate-theme.php"
php "$SEED/scripts/fix-htaccess.php"
```

Check: `https://YOUR-DOMAIN/` — Days 1–10 listed.

> `import-jnanodayam-devi.php` loads `wp-load.php` from `/var/www/html/...` in Docker.  
> On Hostinger the script must run with **cwd = WordPress root** *or* you must edit the `require` line to:
>
> `require __DIR__ . '/../public_html/wp-load.php';`  
>
> (adjust relative path). Prefer running from `public_html` after patching require to:
>
> ```php
> require dirname(__DIR__) . '/public_html/wp-load.php'; // if scripts live in ~/qlu-seed/scripts
> ```
>
> **Simplest Hostinger fix:** copy the three import scripts into `public_html/wp-content/mu-plugins/` temporarily is wrong. Better: at top of each import script on the server, set:
>
> ```php
> require '/home/USER/domains/YOUR-DOMAIN/public_html/wp-load.php';
> ```
>
> Replace `USER` / domain path with your real path from `pwd` after `cd` into WordPress.

### B5. Seed sample magazine from folder

```bash
SEED=~/qlu-seed
WP=~/domains/YOUR-DOMAIN/public_html   # adjust

MONTH=2026-10
rm -rf /tmp/magazine-issue
cp -a "$SEED/content/magazine/$MONTH" /tmp/magazine-issue

cd "$WP"
php "$SEED/scripts/import-magazine-from-folder.php" "$MONTH"
```

One section only (same as local):

```bash
php "$SEED/scripts/import-magazine-from-folder.php" "$MONTH" "themes"
# or: visits,campus
```

Check: `/issues/2026-10-te/` and `/issues/2026-10-en/`.

### B6. No SSH?

Use **Path A** (migration plugin), or ask Hostinger support to enable SSH / use their “PHP” cron with a one-off script. File Manager alone cannot easily run these PHP CLI importers.

---

## After go-live — new magazine month

1. On your PC, create pack:

```powershell
python scripts\seed_magazine_from_folder.py --month 2026-11 --init-from-template
# edit content/magazine/2026-11/... then either:
```

2. **Option 1 — seed on Hostinger (SSH):** upload the new `content/magazine/2026-11/` folder → run `import-magazine-from-folder.php 2026-11`.  

3. **Option 2 — edit in WP Admin:** Issues → add/edit Te + En posts (no seed).  

4. **Option 3 — partial update:**

```bash
php import-magazine-from-folder.php 2026-11 "visits"
```

---

## Checklist

- [ ] Hostinger **Unlimited** (or upgrade path) + SSL  
- [ ] Theme **QLU Magazine** active  
- [ ] Permalinks = **Post name**  
- [ ] mu-plugins: `qlu-jnanodayam-cpt.php`, `qlu-issue-cpt.php`  
- [ ] Programme `/` shows 10 days  
- [ ] Sample magazine Te + En with images  
- [ ] Telugu OK on mobile  
- [ ] Strong admin password + 2FA if available  
- [ ] Know how to restore from **daily backups**  

---

## Security notes

- Do not leave seed scripts or `_qlu-seed` publicly listable.  
- Do not commit Hostinger passwords.  
- Keep WP core updated; re-upload theme/mu-plugins from git when you change code.  

---

## Quick decision

| Situation | Do this |
|-----------|---------|
| First time live, local already seeded | **Path A** (All-in-One Migration) |
| Clean server, have SSH | **Path B** |
| Only File Manager, no SSH | **Path A** only |
| New month later | Folder seed on SSH or WP Admin |

---

*Canonical path: `docs/HOSTINGER.md`.*
