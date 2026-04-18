# BHOPAL.INFO — CONTEXT ANCHOR DOCUMENT
**Version:** 1.0 | **Status:** Frozen | **Apply to:** Every project conversation

---

## 1. PLATFORM IDENTITY (FROZEN)

- **Platform name:** Bhopal.info
- **Founded:** 2010
- **Tagline:** What Bhopal Needs to Know – ज़रूरी है जानना
- **Nature:** Civic digital infrastructure. Not a blog. Not a news portal. Not a social page.
- **Master rule:** If a post does not help, inform, or orient a Bhopali — it does not belong.

---

## 2. BRAND RULES (NON-NEGOTIABLE)

| Element | Value |
|---|---|
| Background | #FFFFFF (white only) |
| Primary Red | #B71C1C — wordmark, primary buttons, critical alerts |
| Primary Orange | #F57C00 — sun rays & arc in logo only |
| Accent Green | #8FB339 — verified badges and accent dots only |
| Black | #000000 |

**Hard rules:**
- No new colors ever introduced
- No gradients anywhere
- No drop shadows
- No glow effects
- No rounded corners on single-sided borders
- Green is accent-only — never used as primary action color
- All UI must feel flat, calm, civic, and authoritative

---

## 3. TECHNOLOGY STACK (LOCKED)

- **Framework:** Laravel (latest stable LTS) — modular monolith architecture
- **Database:** MySQL with Redis for caching and queues
- **Auth:** Laravel Sanctum (API tokens, scoped abilities)
- **Payment gateway:** Razorpay (centralized)
- **Hosting:** Cloud VPS (AWS / DigitalOcean / Linode), Nginx, Ubuntu LTS
- **Queue monitoring:** Laravel Horizon
- **Architecture style:** Modular Monolith — single Laravel app, independent internal engines

---

## 4. CORE ENGINE LIST

| Engine | Purpose |
|---|---|
| Content Engine | Civic updates, alerts, guides, pillar posts |
| News Engine | Structured civic news publishing |
| Events Engine | Event CRUD, RSVP, ticketing skeleton |
| User Identity Engine | Auth, roles, geo preferences |
| Notification Engine | Email, WhatsApp, push — geo-filtered |
| Payment Engine | Razorpay integration, currently disabled |
| Business Directory Engine | Tiered listings, sponsored content |
| Contest Engine | Mini contests, BCC photo contests |
| Analytics Engine | Per-pillar, per-area metrics |

---

## 5. DATABASE: SHARED FOUNDATION TABLES

These tables are shared across ALL engines. Every engine depends on them.

| Table | Key Fields | Notes |
|---|---|---|
| `cities` | id, name, slug, is_active | Multi-city ready from Day 1 |
| `areas` | id, city_id, name, slug, is_active | FK → cities |
| `wards` | id, city_id, ward_number, name | Optional, future civic heatmap |
| `users` | id, name, email, city_id, area_id, role_id | Geo-linked |
| `roles` | id, name, permissions_json | RBAC foundation |
| `payments` | Central payment engine table | Shared across all engines |
| `notifications` | Central notification log | Shared across all engines |

**Critical rule:** Every entity (post, event, business, user) must carry `city_id`. No exception.

---

## 6. RBAC ROLES (FROZEN)

| Role | Scope |
|---|---|
| Super Admin | System-level infrastructure control |
| Director | Governance + financial authority |
| Admin | Content + event moderation |
| Finance | Settlement & reconciliation only |
| Editorial Lead | News verification, critical alert approval |
| Content Editor | Draft creation only |
| Moderator | Verification review support |
| Organizer | Event submission & operational access |
| Event Staff | QR check-in only |
| Revenue Lead | Business listings, partnerships |
| BCC Coordinator | BCC vertical only |
| Support | Read-only monitoring |

**Separation rules (non-negotiable):**
- No role can both verify AND settle money alone (except Director)
- Organizer cannot self-verify their own event
- Finance cannot publish content
- Editor cannot access financial data

---

## 7. GLOBAL STATE MACHINES (FROZEN)

**Events:**
- `verification_status`: pending → verified → rejected (verified cannot silently revert)
- `publish_status`: draft → review → published → archived (no deletion of published)

**News Posts:**
- `verification_status`: pending → verified → rejected
- `publish_status`: draft → review → published → archived

**Payments:**
- `payment_status`: disabled → pending → paid → refunded (no backward edits)

**Settlement:**
- `settlement_status`: pending → locked → simulated → settled (or failed)

**Trust:**
- `verification_level`: unverified → basic → verified → trusted → restricted

**Rule:** Every state transition must be logged, record actor, and timestamp. No silent changes.

---

## 8. CONTENT PILLARS (LOCKED — 7 PILLARS)

| # | Pillar | Tone | Frequency |
|---|---|---|---|
| 1 | Civic & Utility Updates | Neutral | As required |
| 2 | Local Alerts & Safety | Source-based | As required |
| 3 | City Services & How-To | Instructional | 2/week |
| 4 | Local Businesses (curated) | Neutral | 1/week |
| 5 | Events & What's Happening | Informational | 1/week |
| 6 | History, Heritage & Context | Archival | 1/week |
| 7 | Community Notices (controlled) | Verified | Controlled |

**Posting ratio per 10 posts:** 4 Civic, 2 City Services, 1 Alert, 1 Business, 1 Events, 1 History.

---

## 9. GLOBAL CONFIG FLAGS (platform_settings table)

All feature toggles live in `platform_settings` table — never in `.env` for business logic.

| Key | Current Value | Who Can Change |
|---|---|---|
| `payments_mode` | disabled | Director only |
| `settlement_mode` | simulation | Director only |
| `seat_lock_enabled` | false | Director only |
| `qr_checkin_enabled` | false | Director only |
| `auto_settlement_enabled` | false | Director only |
| `multi_city_enabled` | false | Director only |
| `settlement_delay_days` | 3 | Director only |
| `max_event_emails_per_user_per_day` | 2 | Admin |

---

## 10. ARCHITECTURAL PRINCIPLES (NEVER VIOLATE)

- Controllers contain ZERO business logic — all logic in Service classes
- All financial mutations inside `DB::transaction()` with `lockForUpdate()`
- All state transitions go through Service Layer only
- No hard delete of published content, financial records, or audit logs — archive only
- Every engine must carry `city_id` and `area_id` for multi-city readiness
- Payments are disabled now but architecture exists — do not rebuild later
- Civic tone: neutral, factual, calm — no sensationalism, no politics, no drama

---

## 11. BCC SUB-ENTITY (FEDERAL MODEL)

- **URL:** bhopal.info/bhopal-camera-club
- **Structure:** Separate society, separate logo, separate accounting export
- **Shared:** Infrastructure, Razorpay credentials, authentication system
- **Role:** BCC Coordinator (reports to Director)
- **Principle:** Central infrastructure control + vertical autonomy

---

*This anchor document is read-only context. Do not redesign anything in it during pillar conversations. If a truly global decision changes, update this file and propagate to affected pillar files.*
