# QLU Monthly Magazine — Look & Feel (Web)

> Design brief for the **WordPress magazine** (Telugu + English). Soft-paper / gold identity for screen.

**Companions:** [README](./README.md) · [Requirements](./REQUIREMENTS.md) · [Technical](./TECHNICAL.md)

---

## 1. Product identity

| Item | Value |
|------|--------|
| Publisher | Quantum Life University (QLU) |
| Product name | నెలవారీ పత్రిక / Monthly Magazine |
| Voice | Warm, clear, guru-respectful; knowledge-forward; not salesy |
| Languages (v1) | **Telugu** and **English** — both complete; proper names as used in QLU materials |

Do **not** invent teachings. Use only editor-approved copy and consented photos. Empty section line: Te *ఈ నెలలో ప్రత్యేక నివేదిక లేదు.* · En *No special report this month.*

---

## 2. Page & layout (web)

| Spec | Value |
|------|--------|
| Canvas | Full-bleed soft paper background; content column **~680–720 px** for long reading (wider for photo grids) |
| Margins | Comfortable padding; avoid edge-to-edge Telugu paragraphs on mobile |
| Length | One scrollable issue; clear section breaks (gold hairline or spacing) |
| First viewport | Brand + magazine title + month/year + language control — **calm**; no stats, schedules, or promo chips |
| Columns | Follow section needs (1 column default; 2–3 for photo grids) |
| Footer | `Quantum Life University` · issue id `YYYY-MM` |

**Layout priority**

1. Issue template / section pattern  
2. Per-block instructions editors leave in draft notes (not printed on the page)  
3. If unsure: single-column reading flow  

---

## 3. Colour palette (soft paper / gold)

| Token | Hex | Use |
|-------|-----|-----|
| Paper | `#fffdf8` | Page background |
| Soft panel | `#f6f1e8` | Masthead strip, pull-quotes |
| Ink | `#352d26` | Body text |
| Heading brown | `#6b4a25` | Section titles |
| Muted | `#7c6e5d` / `#6f6252` | Captions, meta, dates |
| Gold | `#a17b43` | Accents, rules, wordmark |
| Line | `#e8dfd0` | Hairlines, photo frames |

Avoid purple gradients, heavy glow, harsh black, and busy patterns behind body text.

---

## 4. Typography

| Role | Guidance |
|------|----------|
| Brand / masthead | Tracked Latin: `QUANTUM LIFE UNIVERSITY` · gold · small · letter-spacing ~0.12em |
| Document title | Telugu and/or English · `#6b4a25` · prominent but not louder than brand |
| Section headings | Exact English section titles **or** agreed Telugu equivalents · same **order** · `#6b4a25` |
| Body Te | Noto Sans Telugu (or equivalent) · `#352d26` · ≥ 1.05–1.125 rem · line-height ~1.55–1.7 |
| Body En | Source Sans 3 / theme sans · same ink · similar size/rhythm |
| Captions | Smaller · muted · optional italic for photo credit |

**Avoid:** Decorative scripts for long Telugu; all-caps Telugu; tiny body text on mobile.

---

## 5. Logos & masthead

| Element | Instruction |
|---------|-------------|
| Wordmark | Text gold tracked caps if no SVG yet |
| Logo file | Use provided brand file only; do not invent a mark |
| Banner | Soft hero band `#f6f1e8` with title + month only |
| Language | Clear Te | En control near the top |

---

## 6. Fixed heading order

Do not reorder. Use English keys as canonical; show Telugu heading labels when the page language is Te.

1. Summary of the themes covered in that month  
2. Highlight classes of the month  
3. Few Experiences from Participants  
4. Dr. Newton's visits and classes in other places  
5. Vanya Maitreya Highlights  
6. Goloka, Sahasrara and QLU Updates  
7. Upcoming Programs/Announcements  

Then **Closing**. Each section starts with a clear visual break. Do not merge sections.

---

## 7. Content sizing (per section)

| Section | Budget |
|---------|--------|
| Themes | 4–8 short bullets or mini-paragraphs |
| Highlight classes | 3–6 items; date + title + 2–4 sentences |
| Experiences | 2–5 sharings; ~80–180 words each (per language) |
| Visits | Chronological; 1–2 photos max unless visit-heavy |
| Vanya Maitreya | Short highlights; verified only |
| Campus / QLU | Factual dated updates |
| Upcoming | Next 4–8 weeks; deadlines first |

**Photos:** Prefer quality over quantity. Typical **1–3 images per section**, **≤ ~12** per issue unless intentionally photo-led. Soft corner radius optional (≤ 8 px); thin `#e8dfd0` frame OK. No stickers/emoji overlays on photos.

---

## 8. Bilingual presentation

| Mode | Look |
|------|------|
| Parallel pages (D1=A) | Each language page is monolingual body; switcher in header |
| Single bilingual (D1=B) | Within a section: Telugu block then English block (or two columns ≥900 px); shared images between |
| Plugin locales (D1=C) | Follow plugin UI; keep visual tokens identical across locales |

Never publish an issue where one language is a stub and the other is complete (except during private draft).

---

## 9. Web anti-patterns (do not ship)

| Avoid | Do instead |
|-------|------------|
| Card grids and dashboard chrome on the home/issue hero | One calm masthead composition |
| Purple / neon / glassmorphism theme demos | Soft paper + gold |
| Printing editor notes or “layout applied” on the page | Apply layout silently |
| Placeholder copy live | Keep draft until ready |
| Broken Telugu glyphs | Embed/load Noto Sans Telugu; test mobile |
| Dumping the full photo library into one section | Curate; reuse Media Library selectively |
| Autoplay video, popups, sticky promo bars | Quiet reading |

---

## 10. Checklist before publish

- [ ] Paper `#fffdf8`, ink `#352d26`, gold `#a17b43`  
- [ ] Sections in order; empty handled with quiet Te/En line or omitted cleanly  
- [ ] Telugu **and** English complete for v1 scope  
- [ ] Fonts OK on phone; images have alt text  
- [ ] Masthead calm; QLU identity present  
- [ ] No invented teachings; consent checked for people stories/photos  
- [ ] Footer brand + issue id  

---

*Canonical path: `docs/LOOK-AND-FEEL.md`.*
