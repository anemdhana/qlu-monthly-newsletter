# QLU Monthly Magazine — Requirements Specification

| Field | Value |
|--------|--------|
| **Product** | Quantum Life University (QLU) Monthly Magazine (web) |
| **Format** | Public WordPress site (HTML pages / posts) |
| **Cadence** | 1st of every month |
| **Languages (v1)** | **Telugu** and **English** |
| **Status** | Draft — specification for setup |
| **Owner** | QLU Communications / Editorial |
| **Related** | [README](./README.md) · [Technical](./TECHNICAL.md) · [Look & feel](./LOOK-AND-FEEL.md) |

---

## 1. Purpose

A monthly **web magazine** that summarizes QLU’s teaching, community life, and institutional updates for the previous calendar month, published as a shareable URL on the **1st of each month**.

**Goals**

- Let editors **design layout**, place images, and paste Telugu + English in a rich visual editor, without depending on ChatGPT PDF.
- Serve both Telugu-preferring and English-preferring readers from one property.
- Keep sadhakas connected to themes, experiences, visits, campus updates, and upcoming programs.
- Stay visually consistent with QLU soft-paper / gold identity ([Look & feel](./LOOK-AND-FEEL.md)).

**Non-goals (v1)**

- Full class transcripts or video libraries inside WordPress.
- Automated machine translation as the source of truth (editors own both languages).
- Mandatory PDF (optional later).
- Headless WordPress.

---

## 2. Audience

| Segment | Need |
|---------|------|
| Telugu readers | Full issue in Telugu; familiar names in Latin/Sanskrit as used in QLU |
| English readers | Full issue in English; same facts and section map |
| Remote / occasional participants | Catch-up via URL on phone |
| Donors / well-wishers | Campus and program updates |
| Editors | Visual layout, media library, paste from docs, duplicate-last-month template |
| Newcomers (forwarded link) | Clear masthead; calm first screen; no invented jargon |

**Tone:** Warm, clear, guru-respectful; knowledge-forward; not salesy.

---

## 3. Cadence & coverage

| Item | Rule |
|------|------|
| **Publish window** | Live by **00:00–09:00 IST on the 1st** (or previous business day if 1st is a holiday — confirm in §11 open decisions) |
| **Content window** | Previous calendar month (e.g. April issue covers 1–31 March) |
| **Editorial freeze** | **T−4 days** before publish (e.g. 28th for a 1st issue) |
| **Late additions** | Only critical announcements after freeze, with editor approval |
| **Issue id** | `YYYY-MM` (e.g. `2026-04`) |

---

## 4. Language (v1 — Telugu and English)

### 4.1 Scope

- **v1 supports Telugu and English only.** No other locales until a new requirements revision.
- Both languages are **first-class**: same section order, same photos where shared, same factual claims.
- Proper names (Dr. Newton, Goloka, Sahasrara, Vanya Maitreya, Jnanodayam, Quantum Life University) keep established forms in both languages.

### 4.2 Delivery model (choose one at setup; document choice in Technical)

| Option | Description | Prefer when |
|--------|-------------|-------------|
| **A — Parallel issues** | Two posts/pages per month: `…/te/2026-04` and `…/en/2026-04`, with a language switcher | Cleaner layout control; different Te/En length |
| **B — Bilingual page** | One page; each section has Te block then En block (or side-by-side on wide screens) | Single URL to share |
| **C — Language as WP locale** | Polylang / TranslatePress: one “issue” translated | Editors already use WP multilingual plugins |

**Requirement:** Readers must reach **complete Telugu** and **complete English** content for every published issue without missing sections in one language.

### 4.3 Editorial rules

- Do **not** invent teachings. Copy only from approved notes, class themes, and consented sharings.
- If a section has no content for the month: quiet line — Te: *ఈ నెలలో ప్రత్యేక నివేదిక లేదు.* · En: *No special report this month.*
- Empty or unfinished drafts must not go live (no “Add summary…” placeholders).
- Machine translation may assist a first draft; **editor must proof** before publish.

### 4.4 Out of scope until approved

- Hindi or other Indian languages.
- Automated sync from external databases.

---

## 5. Delivery & channels

| Requirement | Detail |
|-------------|--------|
| **Primary artifact** | Published WordPress URL(s) for the month |
| **URL pattern (suggested)** | `/issues/YYYY-MM/` plus language path or query per §4.2 |
| **Share** | Email + WhatsApp / Telegram with short blurb + link |
| **Optional PDF** | Later; same content; naming `QLU-Magazine-YYYY-MM-te.pdf` / `-en.pdf` if added |
| **Archive** | Index page listing past issues (Te/En) |

---

## 6. Content structure (fixed section order)

Every issue includes these sections in this order. Do not reorder.

### 6.1 Cover / masthead

- Brand: Quantum Life University  
- Title: నెలవారీ పత్రిక / Monthly Magazine  
- Month + year; issue id `YYYY-MM`  
- Language control visible on first screen  

### 6.2 Summary of the themes covered in that month

- 4–8 short theme bullets or mini-paragraphs  
- Themes, not day-by-day logs  
- Optional “theme of the month” callout  

### 6.3 Highlight classes of the month

- 3–6 sessions: date, title, 2–4 sentences why it mattered  
- Pointer to where recordings live (channel / playlist name) — no required video embed  

### 6.4 Few experiences from participants

- 2–5 sharings; consent required (§9)  
- Guide: ~80–180 words per language version; light polish only  

### 6.5 Dr. Newton's visits and classes in other places

- Chronological; place + date + short note  
- Photos sparingly unless the month is visit-heavy  

### 6.6 Vanya Maitreya Highlights

- Short verified highlights only  

### 6.7 Goloka, Sahasrara and QLU Updates

- Factual dated updates; light photo use  

### 6.8 Upcoming Programs/Announcements

- Next 4–8 weeks; deadlines first  
- Critical schedule changes may update after freeze  

### 6.9 Closing

- Short blessing / gratitude  
- Contact / website / social links  

---

## 7. Editor experience (WordPress)

| Requirement | Detail |
|-------------|--------|
| **Rich editing** | Block editor and/or page builder: headings, paragraphs, lists, images, columns, pull-quotes |
| **Layout** | Editors can arrange sections and place images without writing code |
| **Template** | “Monthly issue” template or reusable pattern; **duplicate previous month** as starting point |
| **Media** | Upload JPG/PNG/WebP; captions; alt text required for meaningful images |
| **Preview** | Desktop + mobile preview before publish |
| **Roles** | Editor (publish), Author (draft), Admin (plugins/theme) — see Technical |
| **Draft safety** | Unpublished drafts not publicly guessable |

---

## 8. Visual & brand

- Soft-paper / gold palette and typography rules: **[Look & feel](./LOOK-AND-FEEL.md)**  
- First viewport: brand + title + month + language — calm; no promo sticker clutter  
- Prefer quality over quantity: typically **1–3 images per section**, **≤ ~12** per issue unless a section is intentionally photo-led  
- No invented logos; use provided QLU wordmark/files  

---

## 9. Consent, privacy, accuracy

| Topic | Rule |
|-------|------|
| **Participant stories** | Written or recorded consent before naming; anonymize on request |
| **Photos of people** | Prefer group/context shots; identifiable close-ups need consent |
| **Children** | Extra care; avoid naming minors; consent from guardian if shown |
| **Teachings** | No invented quotes attributed to Dr. Newton or teachers |
| **Corrections** | Fix live page promptly; note “Updated DD Mon YYYY” if material change |

---

## 10. Acceptance criteria (v1 launch)

- [ ] Managed WordPress live on agreed subdomain with HTTPS  
- [ ] Theme + editor allows layout, images, paste Te/En without code  
- [ ] Issue template exists; one sample month published in **Telugu and English** (§4.2 model documented)  
- [ ] Fixed section order respected; empty sections handled per §4.3  
- [ ] Soft-paper look applied (colours, fonts, masthead)  
- [ ] Mobile-readable; Telugu glyphs render correctly  
- [ ] Archive or “Latest issue” entry point from home  
- [ ] Consent checklist used for experiences/photos  
- [ ] Backups + updates process agreed (Technical §7)  

---

## 11. Open decisions

| ID | Question | Options |
|----|----------|---------|
| D1 | Language delivery | A parallel / B bilingual page / C Polylang-style |
| D2 | Builder | Kadence blocks vs Elementor |
| D3 | Host | Cloudways / SiteGround / WordPress.com Business / other managed |
| D4 | Holiday publish slip | Previous business day vs keep 1st |
| D5 | Keep generating PDF in parallel? | No (web only) / Yes optional |

Record decisions in [Technical](./TECHNICAL.md) §1 when chosen. Holiday slip is D4.

---

*Canonical path: `docs/REQUIREMENTS.md`.*
