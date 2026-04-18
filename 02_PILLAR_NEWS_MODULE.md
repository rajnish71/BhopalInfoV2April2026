# PILLAR 2 — NEWS MODULE
**Scope:** This conversation covers the News Publishing System only.
**Do not:** Redesign the Events Engine, Payment Engine, or global RBAC in this conversation.
**Depends on:** Anchor Doc (shared tables: cities, areas, wards, users, roles, notifications)

---

## CURRENT PHASE

| Phase | Status |
|---|---|
| Phase A — News CRUD, editorial workflow, geo tagging, homepage integration | **Active — build this** |
| Phase B — Notification triggers, auto social push, SEO optimization, RSS feed | Planned |
| Phase C — Area subscription filtering, alert analytics, crisis mode switch, multi-city | Future |

---

## FROZEN DECISIONS

- This is NOT a blog — it is a structured civic news engine
- No post can be published without `verification_status = verified`
- Every civic post must have `area_id` — except city-wide alerts
- No hard deletion of published posts — archive only
- All edits after publish logged in `news_updates` table
- Every update creates an audit entry in `news_status_logs`
- Crime/accident coverage allowed only with: public safety relevance, no sensational headlines, source mentioned, no speculation
- Content categories are locked to 7 pillars — cannot add random categories
- Only Editorial Lead can mark a post as "Critical Alert"
- Critical Alert triggers: WhatsApp Channel + Email Alert + X real-time post

---

## DATABASE TABLES (NEWS MODULE)

### `news_posts` (primary spine)
| Field | Type | Notes |
|---|---|---|
| id | BIGINT PK | |
| city_id | FK → cities | Mandatory |
| area_id | FK → areas | Mandatory (except city-wide) |
| ward_id | FK → wards | Nullable |
| category_id | FK → categories | Pillar-linked |
| source_id | FK → sources | Nullable |
| event_id | FK → events | Nullable — auto-linked for event coverage |
| title | VARCHAR(255) | |
| slug | VARCHAR(255) UNIQUE | |
| summary | VARCHAR(300) | |
| content | LONGTEXT | |
| featured_image | VARCHAR(255) | Nullable |
| urgency_level | ENUM(normal, important, critical) | default: normal |
| verification_status | ENUM(pending, verified, rejected) | default: pending |
| publish_status | ENUM(draft, review, published, archived) | default: draft |
| scheduled_at | DATETIME | Nullable |
| published_at | DATETIME | Auto-set on publish |
| created_by | FK → users | |
| approved_by | FK → users | Nullable |
| seo_title | VARCHAR(255) | |
| seo_description | VARCHAR(255) | |
| view_count | BIGINT | default 0 |

**Indexes:** (city_id, area_id, publish_status), (urgency_level, publish_status), (published_at)
**Note:** `event_id` column enables auto-link when Events Engine publishes an event

### `categories`
| Field | Type | Notes |
|---|---|---|
| id | BIGINT PK | |
| name | VARCHAR(150) | |
| slug | VARCHAR(150) UNIQUE | |
| pillar_type | TINYINT UNSIGNED | 1–7, maps to locked pillar system |
| description | TEXT | |
| is_active | BOOLEAN | |

**Constraint:** `pillar_type` must be validated in model (range 1–7 only)

### `sources`
| Field | Type | Notes |
|---|---|---|
| id | BIGINT PK | |
| name | VARCHAR(255) | |
| type | ENUM(government, police, utility, press_release, verified_report) | |
| reference_url | VARCHAR | Nullable |
| contact_info | VARCHAR | Nullable |

**Rule:** No anonymous source entries allowed

### `news_updates` (version history — no silent edits)
| Field | Type |
|---|---|
| id | BIGINT PK |
| news_post_id | FK → news_posts |
| previous_content | LONGTEXT |
| updated_content | LONGTEXT |
| updated_by | FK → users |
| update_reason | TEXT |
| created_at | TIMESTAMP |

### `news_status_logs` (audit trail)
| Field | Type |
|---|---|
| id | BIGINT PK |
| news_post_id | FK |
| action | ENUM(created, updated, approved, published, archived) |
| performed_by | FK → users |
| notes | TEXT |
| created_at | TIMESTAMP |

### `news_notifications`
| Field | Type |
|---|---|
| id | BIGINT PK |
| news_post_id | FK |
| channel | ENUM(email, whatsapp, push, x) |
| status | ENUM(queued, sent, failed) |
| sent_at | DATETIME |

**Trigger:** Auto-insert when `urgency_level = critical` AND `publish_status = published`

### `tags`
| Field | Type |
|---|---|
| id | BIGINT PK |
| name | VARCHAR(100) |
| slug | VARCHAR(100) UNIQUE |

### `news_tag_map` (pivot)
| Field | Type |
|---|---|
| news_post_id | FK |
| tag_id | FK |

Composite PK: (news_post_id, tag_id)

### `attachments` (Phase B)
| Field | Type |
|---|---|
| id | BIGINT PK |
| news_post_id | FK |
| file_path | VARCHAR |
| file_type | VARCHAR |

---

## MIGRATION ORDER (STRICT)

1. create_cities_table *(shared — may already exist)*
2. create_areas_table *(shared)*
3. create_wards_table *(shared)*
4. create_roles_table *(shared)*
5. modify_users_table — add city_id, area_id, role_id
6. create_categories_table
7. create_sources_table
8. create_tags_table
9. create_news_posts_table
10. create_news_updates_table
11. create_news_status_logs_table
12. create_news_tag_map_table
13. create_news_notifications_table
14. create_attachments_table *(Phase B)*

---

## EDITORIAL WORKFLOW

```
Draft created (Content Editor)
→ Source entry (mandatory)
→ Geo tagging (area_id required)
→ Submit for review
→ Editorial Lead review
→ Verified + Approved
→ Published
→ Auto-distribution triggered (news_notifications insert)
→ If urgency = critical: WhatsApp + Email + X triggered
```

**Role-based publish permissions:**
| Role | Draft | Verify | Publish | Trigger Critical | View Analytics |
|---|---|---|---|---|---|
| Content Editor | ✔ | ✗ | ✗ | ✗ | ✗ |
| Editorial Lead | ✔ | ✔ | ✔ | ✔ | Limited |
| Admin | ✔ | ✔ | ✔ | ✔ | ✔ |
| Director | ✔ | ✔ | ✔ | ✔ | Full |
| Moderator | Limited | ✗ | ✗ | ✗ | ✗ |

---

## NEWS POST FORMAT (STANDARDIZED STRUCTURE)

Every civic news article must follow this template:
1. What happened
2. Who is affected
3. Duration (if applicable)
4. Official source statement
5. What citizens should do
6. Contact numbers (if relevant)

Footer: Source citation, last updated time, share buttons, correction link

---

## NEWS TYPES (SYSTEM FLAGS)

Controls distribution behavior, notification behavior, and homepage placement:
- Routine Civic Update
- Emergency Alert
- Service Guide
- Event Coverage
- Advisory
- Public Notice
- Informational Context

---

## CRITICAL ALERT LOGIC

```
IF urgency_level = critical AND publish_status = published
→ Insert row in news_notifications (channel: email, whatsapp, x)
→ Notification Engine handles distribution
```
**Implementation:** Use Laravel event listeners — not controller-level logic.

---

## CRISIS MODE

When `crisis_mode = true` (platform_settings):
- Homepage auto-switches layout
- Only verified updates visible
- Comments locked
- Right sidebar disabled
- Requires Editorial Lead or Director approval to toggle

---

## HOMEPAGE INTEGRATION

**Section layout:**
1. Top Strip — Critical Alert (conditional, red #B71C1C)
2. Header — Logo, area selector, search, login
3. Area Filter Bar — dynamic geo filtering
4. Primary Grid (70/30):
   - Left: Featured civic update + 5 latest civic updates
   - Right: Quick city services, upcoming events, business spotlight
5. Category Blocks — pillar-organized rows
6. Newsletter subscription
7. Footer

---

## SEO STRATEGY

Title format: `[Area] + [Issue] + [Date]`
Example: "Water Supply Disruption in Kolar – 22 Feb 2026"

Each post must include:
- Structured SEO title and description
- FAQ block (for guides)
- Schema markup (NewsArticle type)

Target keywords: "Bhopal water cut today", "Bhopal power outage schedule", etc.

---

## ADMIN UI STRUCTURE (NEWS MODULE)

**Left sidebar:**
Dashboard → News (All / Add New / Drafts / Pending Review / Published / Critical Alerts / Archived) → Categories → Sources → Tags → Areas → Analytics → Crisis Mode → Audit Logs

**Add/Edit screen sections:**
- A: Basic Info (Title, Slug, Summary, Image, News Type)
- B: Classification (City, Area, Ward, Category — pillar auto-mapped, cannot override)
- C: Content (structured template blocks, not free-form)
- D: Source Verification (Source name, URL, verification status — cannot publish without verified)
- E: Urgency Control (urgency level, schedule, publish status — critical requires confirmation modal)
- F: SEO (collapsible)
- G: Tags
- H: Attachments (Phase B)
- I: Action buttons (Save Draft / Submit for Review / Publish / Archive)

---

## DATA INTEGRITY RULES

- Cannot publish if `verification_status ≠ verified`
- Cannot publish without `area_id` (except city-wide alerts)
- Cannot delete published post — archive only
- Every edit after publish logged in `news_updates`
- Source required for Pillar 1 & 2 categories
- Pillar type auto-mapped from category — cannot be manually overridden

---

## EVENTS ↔ NEWS AUTO-LINK

When event `publish_status = published` and `verification_status = verified`:
1. Auto-create `news_post`
2. Set `category_id` = "Public Events Coverage" (Pillar 5)
3. Attach `event_id` to the news post

When event completes:
- Can auto-generate "Event Coverage" news draft

This keeps archive unified. No duplication.

---

## OPEN QUESTIONS FOR THIS PILLAR

*(Update this section as decisions are made)*
- [ ] Language: should posts support Hindi body content in the same content field, or separate field?
- [ ] Auto social push — which Laravel package for Facebook/X API integration?
- [ ] RSS feed format — standard RSS 2.0 or Atom?
- [ ] Correction request flow — who receives and processes correction reports?
- [ ] Crisis mode toggle — single global switch or per-city in future?
