# Magazine issue seed — folder convention

Seed a monthly magazine from disk: **one folder per issue month**, **one subfolder per section**, with text + photos linked by filename.

Import: `python scripts/seed_magazine_from_folder.py --month 2026-10`  
(or PHP inside Docker — see script help).

---

## Layout

```
content/magazine/
  YYYY-MM/                          ← issue id (e.g. 2026-10)
    issue.json                      ← optional meta
    00-masthead/
    01-themes/
    02-highlight-classes/
    03-experiences/
    04-visits/
    05-vanya/
    06-campus/
    07-upcoming/
    08-closing/
```

Only folders that exist are imported. Missing sections get one quiet empty line (Te/En). Order is fixed by the `NN-` prefix (do not rename the slug part).

---

## Section folder names (fixed)

| Folder | WP heading (English) |
|--------|----------------------|
| `00-masthead` | Cover / intro (no H2; opens the issue) |
| `01-themes` | Summary of the themes covered in that month |
| `02-highlight-classes` | Highlight classes of the month |
| `03-experiences` | Few Experiences from Participants |
| `04-visits` | Dr. Newton's visits and classes in other places |
| `05-vanya` | Vanya Maitreya Highlights |
| `06-campus` | Goloka, Sahasrara and QLU Updates |
| `07-upcoming` | Upcoming Programs/Announcements |
| `08-closing` | Closing |

---

## Files inside a section folder

### Body text (required for a useful section)

| File | Role |
|------|------|
| `content.te.txt` | Telugu body (plain text or simple markdown: paragraphs, `- ` bullets) |
| `content.en.txt` | English body |

UTF-8 encoding. Blank lines → new paragraphs. Lines starting with `- ` or `* ` → bullet list.

### Photos (optional, 0–N)

Link image ↔ caption by **same stem**:

| File | Role |
|------|------|
| `01.jpg` / `01.png` / `01.webp` | Photo (order by stem: `01`, `02`, … or `photo-01`) |
| `01.caption.te.txt` | Telugu caption (optional) |
| `01.caption.en.txt` | English caption (optional) |
| `01.alt.te.txt` / `01.alt.en.txt` | Alt text (optional; else caption or stem) |

Supported image extensions: `.jpg` `.jpeg` `.png` `.webp` `.gif`

### Optional

| File | Role |
|------|------|
| `instruction.txt` | Editor/layout note — **not** printed in the issue |
| `featured.jpg` | Only in `00-masthead` — used as WP featured image for both langs if present |

### `issue.json` (optional, issue root)

```json
{
  "title_te": "నెలవారీ పత్రిక — అక్టోబర్ 2026",
  "title_en": "Monthly Magazine — October 2026",
  "slug_te": "october-2026-te",
  "slug_en": "october-2026-en"
}
```

Defaults: titles from month; slugs `YYYY-MM-te` / `YYYY-MM-en`.

---

## Example: one section

```
content/magazine/2026-10/01-themes/
  content.te.txt
  content.en.txt
  01.png
  01.caption.te.txt
  01.caption.en.txt
  02.jpg
  02.caption.en.txt
```

Import builds a themes section with English/Telugu body and photos `01` then `02` (captions per language).

---

## Empty section

Omit the folder, **or** leave folder with no `content.*.txt` and no images → importer inserts:

- Te: `ఈ నెలలో ప్రత్యేక నివేదిక లేదు.`  
- En: `No special report this month.`

---

## Workflow

1. Copy `content/magazine/_template/` → `content/magazine/YYYY-MM/`  
2. Drop photos + fill `content.te.txt` / `content.en.txt`  
3. `python scripts/seed_magazine_from_folder.py --month YYYY-MM`  
4. Open `/issues/YYYY-MM-te/` and `/issues/YYYY-MM-en/`

Re-run updates the same issue month (upsert by `qlu_issue_month` + lang).

---

*Canonical: `docs/MAGAZINE-SEED.md`.*
