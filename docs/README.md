# QLU Monthly Magazine (WordPress)

Public **Telugu + English** web magazine for Quantum Life University. Editors design layout, place images, and paste content in a rich visual editor.

**Companions:** [Requirements](./REQUIREMENTS.md) · [Technical](./TECHNICAL.md) · [Look & feel](./LOOK-AND-FEEL.md) · [Share / rebuild](./SHARE.md) · [Magazine folder seed](./MAGAZINE-SEED.md)

---

## Why this exists

| Need | Approach |
|------|----------|
| Design layout on the page | Classic WordPress + block editor (Kadence) or Elementor |
| Place images, paste Te/En copy | Media library + rich text / blocks |
| Share by URL | Public issue posts |

PDF may remain optional later. **v1 deliverable is the live web issue.**

---

## Documents

| Doc | Audience | Covers |
|-----|----------|--------|
| **[Requirements](./REQUIREMENTS.md)** | Product / editorial | Purpose, bilingual rules, sections, cadence, consent, acceptance |
| **[Technical](./TECHNICAL.md)** | Engineering / hosting | Stack, hosting, theme, plugins, languages, backups |
| **[Look & feel](./LOOK-AND-FEEL.md)** | Editors + designers | Soft-paper palette, typography, web layout rules |

---

## Product snapshot

| Item | Value |
|------|--------|
| Publisher | Quantum Life University (QLU) |
| Product | నెలవారీ పత్రిక / Monthly Magazine |
| Repo | `qlu-monthly-newsletter` |
| Languages (v1) | **Telugu** and **English** |
| Stack | WordPress + MySQL + Kadence child theme (local Docker for theme work) |
| Suggested URL | `magazine.<qlu-domain>` or `news.<qlu-domain>` |
| Cadence | Publish by **1st of each month** (covers previous calendar month) |

---

## Fixed section order

1. Cover / masthead  
2. Summary of the themes covered in that month  
3. Highlight classes of the month  
4. Few Experiences from Participants  
5. Dr. Newton's visits and classes in other places  
6. Vanya Maitreya Highlights  
7. Goloka, Sahasrara and QLU Updates  
8. Upcoming Programs/Announcements  
9. Closing  

Empty sections: one quiet line (Te + En) or hide that block — do not invent content.

---

## Editorial workflow (v1)

```
All month          Collect photos + captions
                   ↓
Near month-end     WordPress: duplicate last issue template
                   Place images · paste Telugu · paste English · adjust layout
                   ↓
Freeze T−4 days    Proof Te + En · consent check · links
                   ↓
1st of month       Publish · share URL (email / WhatsApp)
```

---

## Status

| Phase | Intent |
|-------|--------|
| **Scaffold** | This repo: Docker WP, child theme, CPT, docs |
| **Next** | Choose host; activate Kadence parent; build Issue template |
| **Later** | Archive polish, subscriber form, optional PDF |

Owner: QLU Communications / Editorial. Engineering supports hosting and theme setup.
