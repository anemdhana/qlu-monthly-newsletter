# QLU Monthly Magazine — Technical Specification

| Field | Value |
|--------|--------|
| **Product** | QLU Monthly Magazine (classic WordPress) |
| **Languages (v1)** | Telugu (`te`), English (`en`) |
| **Status** | Draft |
| **Related** | [README](./README.md) · [Requirements](./REQUIREMENTS.md) · [Look & feel](./LOOK-AND-FEEL.md) |

---

## 1. Decisions log

Fill when chosen (see Requirements §11).

| ID | Decision | Choice | Date |
|----|----------|--------|------|
| D1 | Language delivery | _TBD — A / B / C_ | |
| D2 | Theme / builder | _TBD — Kadence / Elementor / other_ | |
| D3 | Hosting | _TBD_ | |
| D4 | Holiday slip | _TBD_ | |
| D5 | PDF parallel | _TBD_ | |

**Recommended defaults (if undecided):** D1 = **A (parallel Te/En issues)** · D2 = **Kadence** · D3 = **managed WordPress (Cloudways or SiteGround)** · D5 = **web only for v1**.

---

## 2. Target stack

| Layer | Choice | Notes |
|-------|--------|-------|
| **CMS** | WordPress 6.x (latest stable) | Classic WP, not headless |
| **PHP** | 8.2+ (host default) | Match host support window |
| **Database** | MySQL 8 / MariaDB 10.6+ | Host-provided |
| **Web** | Nginx or Apache + HTTPS | Let’s Encrypt or host SSL |
| **Editor** | Gutenberg + **Kadence** blocks **or** **Elementor** | Rich layout; image placement; paste content |
| **Theme** | Kadence / GeneratePress / Blocksy (lightweight) | Child theme for QLU colours/fonts |
| **Multilingual** | Polylang **or** TranslatePress **or** manual parallel posts | Depends on D1 |
| **Cache** | Host cache + optional Cloudflare | Keep simple in v1 |
| **Backups** | Host daily + monthly off-host copy | Before plugin/theme updates |
| **Email** | Host SMTP or transactional (Resend/Mailgun) later | Forms not required day one |

**Local theme work:** this repo ships `docker-compose.yml` (WordPress + MySQL). Production remains managed hosting.

---

## 3. Information architecture

### 3.1 Suggested URL space

| Path | Purpose |
|------|---------|
| `/` | Home: latest issue + short intro + language entry |
| `/issues/` | Archive index |
| `/issues/YYYY-MM/` | Issue hub (language picker if D1=A) |
| `/issues/YYYY-MM/te/` | Telugu issue (D1=A) |
| `/issues/YYYY-MM/en/` | English issue (D1=A) |
| `/about/` | Short about QLU magazine |

If D1=B (single bilingual page), use `/issues/YYYY-MM/` only with Te/En sections on one page.

Custom post type **recommended:** `qlu_issue` (title, issue month, featured image) with content in the builder; category/tag optional.

### 3.2 Content model (logical)

```
Issue (YYYY-MM)
  ├── meta: publish_date, status, cover_image
  ├── te: sections[1..9]  (HTML / blocks)
  └── en: sections[1..9]  (HTML / blocks)
Shared media: images reused across te/en with language-specific captions when needed
```

Section titles (English keys, stable):

1. `themes` — Summary of the themes covered in that month  
2. `highlight_classes` — Highlight classes of the month  
3. `experiences` — Few Experiences from Participants  
4. `visits` — Dr. Newton's visits and classes in other places  
5. `vanya` — Vanya Maitreya Highlights  
6. `campus` — Goloka, Sahasrara and QLU Updates  
7. `upcoming` — Upcoming Programs/Announcements  

Masthead and Closing are part of the template, not separate assets.

---

## 4. Theme & design implementation

| Task | Spec |
|------|------|
| Colours / type | Implement CSS variables from [Look & feel](./LOOK-AND-FEEL.md) |
| Fonts | **Noto Sans Telugu** + Latin companion (Source Sans 3 or theme equivalent); self-host or Google Fonts per privacy preference |
| Templates | Home, Issue archive, Single issue (Te), Single issue (En), optional bilingual |
| Patterns | Reusable block pattern: “QLU Issue section” (heading + body + image row) |
| Logo | Text wordmark or provided SVG in child theme / Customizer |

Do not ship purple-gradient “AI default” themes. Stick to soft paper `#fffdf8`, gold `#a17b43`, ink `#352d26`.

---

## 5. Plugins (v1 allowlist)

Keep the plugin set small.

| Plugin type | Example | Required? |
|-------------|---------|-----------|
| Theme companion | Kadence blocks / Elementor | Yes (one builder family) |
| Multilingual | Polylang or TranslatePress | If D1=C; optional if D1=A/B manual |
| SEO | Rank Math or Yoast | Optional |
| Security | Host WAF + Wordfence light **or** host tools only | Prefer host-native |
| Backup | Host + UpdraftPlus off-site | Yes |
| Forms | Not required v1 | Later for subscribe |
| PDF | Not required v1 | Later |

Avoid page-builder stacking (Elementor + heavy extras + another builder).

---

## 6. Roles & access

| WP role | Use |
|---------|-----|
| Administrator | Host owner / technical; plugins, theme, users |
| Editor | Publish issues Te/En |
| Author | Draft only (optional) |
| Subscriber | Unused in v1 (unless membership later) |

Enforce 2FA for Administrator (and Editor if host supports). Strong unique passwords; no shared “qlu123” accounts.

---

## 7. Operations

| Area | Practice |
|------|----------|
| **Updates** | Monthly: WP core + theme + plugins on staging or backup-first |
| **Backups** | Daily automated; test restore once before launch |
| **Uptime** | Host monitoring; optional UptimeRobot |
| **Media** | Compress large photos before upload (long edge ~1600–2000 px typical) |
| **Telugu** | Verify fonts on Android Chrome + iOS Safari before first publish |
| **Staging** | Prefer host staging for template changes |

---

## 8. Local / Docker

Scaffold in this repo:

- `docker-compose.yml` — `wordpress:6.7-php8.2-apache` + `mysql:8.0`  
- Volumes map `wp-content/themes/qlu-magazine` and `wp-content/mu-plugins`  
- Default URL: `http://localhost:8080` (see `.env.example`)  
- Scripts: `scripts/up.cmd`, `scripts/down.cmd`  

Never point local at production DB. Production remains **managed hosting** unless ops standardizes on a VPS.

---

## 9. Security & privacy checklist

- [ ] HTTPS only; force SSL  
- [ ] Limit login attempts / host firewall  
- [ ] Disallow file edit in WP admin (`DISALLOW_FILE_EDIT`)  
- [ ] Principle of least privilege for users  
- [ ] No directory listing of uploads  
- [ ] Consent-sensitive images not in public marketing outside the issue without review  
- [ ] Hide WP version in generators where practical  

---

## 10. Launch checklist (engineering)

- [ ] Domain/subdomain DNS → host  
- [ ] WP install + SSL  
- [ ] Child theme with QLU tokens + Telugu font  
- [ ] Builder + issue template / patterns  
- [ ] Language approach implemented (D1)  
- [ ] Sample issue Te + En published privately then public  
- [ ] Backups verified  
- [ ] Admin/editor accounts handed over  
- [ ] Docs D1–D5 filled  

---

*Canonical path: `docs/TECHNICAL.md`.*
