# BHOPAL.INFO — CONTEXT MANAGEMENT SYSTEM
## How to Use These Files

---

## FILE INDEX

| File | Purpose | Upload To |
|---|---|---|
| `00_ANCHOR_DOC.md` | Global frozen decisions — paste into EVERY project | All 7 projects |
| `01_PILLAR_EVENTS_ENGINE.md` | Events CRUD, RSVP, organizer dashboard | Project: Events Engine |
| `02_PILLAR_NEWS_MODULE.md` | News publishing, editorial workflow, geo alerts | Project: News Module |
| `03_PILLAR_PAYMENTS_COMMISSION.md` | Commission logic, webhook handling, payment flow | Project: Payments |
| `04_PILLAR_SETTLEMENT_REFUNDS_MONITORING.md` | Settlement cron, refunds, reconciliation, alerts | Project: Settlement |
| `05_PILLAR_TRUST_GOVERNANCE.md` | Trust scoring, badges, RBAC, governance charter | Project: Trust & Governance |
| `06_PILLAR_QR_CHECKIN.md` | QR validation, gate check-in, scan logs | Project: QR & Check-in |
| `07_PILLAR_INFRASTRUCTURE_SECURITY.md` | CI/CD, security, backups, disaster recovery | Project: Infrastructure |

---

## HOW TO SET UP EACH PROJECT

1. Create a new Claude Project for each pillar
2. Upload `00_ANCHOR_DOC.md` to every project
3. Upload the pillar-specific `.md` file to that project
4. Start conversations inside the project — context is auto-loaded

**Starting prompt template for each conversation:**
```
Context: Anchor doc and [pillar name] context file are uploaded.
Task: [Your specific question or build task]
Scope: Stay within this pillar only. Do not redesign decisions from the anchor doc.
```

---

## CROSS-PILLAR WORK

When a decision touches two pillars (e.g., how Events Engine links to News Module):

1. Open a temporary conversation (not a project)
2. Paste both relevant pillar files + anchor doc
3. Decide and document the outcome
4. Update the "Frozen Decisions" or "Open Questions" section in BOTH affected pillar files
5. Close the temporary conversation

---

## MAINTENANCE RULES

**Update a pillar file when:**
- A decision gets made (move from Open Questions → Frozen Decisions)
- A new table or field is added
- A service class is created or renamed
- A phase moves from Planned → Active

**Update the anchor doc when:**
- A truly global decision changes (very rare)
- A new shared table is added
- A new RBAC role is formalized

**Never:**
- Redesign anchor doc decisions inside a pillar conversation
- Keep decisions only in chat history — always write them back to the files
- Start a new conversation without loading both anchor + pillar files

---

## AUDIT STATUS

| Pillar | Source Documents Audited | Status |
|---|---|---|
| Anchor Doc | Events Engine + News Module + Infrastructure 2026 | ✅ Complete |
| Events Engine | Events Engine PDF | ✅ Complete |
| News Module | News Module PDF | ✅ Complete |
| Payments & Commission | Events Engine PDF (commission sections) | ✅ Complete |
| Settlement & Monitoring | Events Engine PDF (settlement sections) | ✅ Complete |
| Trust & Governance | Events Engine PDF (trust sections) | ✅ Complete |
| QR & Check-in | Events Engine PDF (QR sections) | ✅ Complete |
| Infrastructure | Infrastructure 2026 PDF | ✅ Complete |

**Not yet incorporated (visual files — require manual review):**
- BHOPAL_INFO_ERD_v4_0_COMPLETE_RELATIONAL_ARCHITECTURE.pdf — visual ERD
- BHOPAL_INFO_ERD_v4_0_VISUAL_DIAGRAM_INVESTOR_READY.pdf — visual diagram
- Event_ER_Diagram.pdf — event-specific ERD
- ChatGPT_Image_Feb_22_2026_11_19_36_AM.pdf — image file

**Action needed:** Review visual ERD files manually and check if any table fields or relationships are missing from the pillar context files. Update affected pillar files if gaps are found.

---

## WHAT THESE FILES DO NOT COVER YET

The following modules exist in the infrastructure blueprint but do not have their own pillar files yet. Create them when ready to build:

| Module | Blueprint Reference |
|---|---|
| Business Directory Engine | Infrastructure 2026 doc |
| Contest Engine | Infrastructure 2026 doc |
| BCC Vertical | Infrastructure 2026 doc |
| Distribution Engine (social push, newsletter, RSS) | Infrastructure 2026 + News Module |
| Analytics & Reporting Engine | Infrastructure 2026 doc |
| Mobile App | Year 3 roadmap |

---

*Last updated: April 2026 — Initial audit complete*

---

## UPDATE — April 2026

**Pillar 7 updated** — Reflects actual AWS EC2 dev environment, dev.sh workflow, current risks (no SSL, no automated backup), and Phase 2 upgrade path.

**Pillar 8 added** — Theme Engine & Theme Management CMS. Includes full architectural review of current `modern2026` theme folder, identified gaps, correct long-term architecture with ThemeService, fallback system, themes DB table, and Theme Management Admin CMS vision.

**New files:**
| File | Purpose |
|---|---|
| `07_PILLAR_INFRASTRUCTURE_SECURITY.md` | Updated v2 with actual server state |
| `08_PILLAR_THEME_ENGINE.md` | New — Theme architecture + CMS |

**Immediate actions flagged:**
- Install SSL certificate on EC2 (urgent — currently HTTP)
- Schedule backup_db.sh as cron job (urgent)
- Audit existing modern2026 theme folder before building Theme Engine
