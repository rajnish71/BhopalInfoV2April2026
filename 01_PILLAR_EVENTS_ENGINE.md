# PILLAR 1 — EVENTS ENGINE
**Scope:** This conversation covers the Events Engine only.
**Do not:** Redesign the News Module, Payment Engine internals, or global RBAC in this conversation.
**Depends on:** Anchor Doc (shared tables: cities, areas, wards, users, roles, payments, notifications)

---

## CURRENT PHASE

| Phase | Status |
|---|---|
| Phase A — Event CRUD, RSVP, capacity, admin workflow, homepage listing | **Active — build this** |
| Phase B — Ticket types structure, seat-lock skeleton, organizer dashboard, registration exports | Planned |
| Phase C — Payment enable switch, QR entry validation, commission engine, advanced analytics | Future |

---

## FROZEN DECISIONS

- Events Engine is a core module inside the Laravel modular monolith — not a plugin
- Payments are disabled now. Architecture exists. Switch is off. Do not remove the structure.
- `seat_lock_enabled = false` in platform_settings — skeleton exists, behavior inactive
- `qr_checkin_enabled = false` — architecture exists, activation is Phase C
- Event can only be published if `verification_status = verified` — no exceptions
- Published events cannot be hard deleted — archive only
- Every event must carry `city_id` and `area_id` — multi-city ready from Day 1
- No political rallies, private parties, MLM seminars, or unverified events — civic events only
- Auto-creates a linked `news_post` of type "Event Announcement" on publish

---

## DATABASE TABLES (EVENTS ENGINE)

### `events` (core spine)
| Field | Type | Notes |
|---|---|---|
| id | BIGINT PK | |
| city_id | FK → cities | Mandatory |
| area_id | FK → areas | Mandatory |
| ward_id | FK → wards | Nullable |
| organizer_id | FK → organizers | Not directly users |
| title | VARCHAR(255) | |
| slug | VARCHAR(255) UNIQUE | |
| summary | VARCHAR(300) | |
| description | LONGTEXT | |
| venue_name | VARCHAR(255) | |
| venue_address | TEXT | |
| start_datetime | DATETIME | |
| end_datetime | DATETIME | |
| capacity_total | INT | |
| capacity_remaining | INT | Auto-calculated, never manual |
| event_type | ENUM(free, paid) | paid disabled via config |
| verification_status | ENUM(pending, verified, rejected) | default: pending |
| publish_status | ENUM(draft, review, published, archived) | default: draft |
| commission_percent | DECIMAL(5,2) | Event-level, not global |
| social_distribution_status | ENUM(pending, published, skipped) | |
| seo_title | VARCHAR | |
| seo_description | VARCHAR | |
| created_by | FK → users | |
| approved_by | FK → users | Nullable |

**Indexes:** (city_id, area_id, publish_status), (start_datetime, end_datetime)

### `event_ticket_types`
| Field | Type | Notes |
|---|---|---|
| id | BIGINT PK | |
| event_id | FK → events | |
| name | VARCHAR | "RSVP" for free events |
| price | DECIMAL(10,2) | 0 for free |
| quantity_total | INT | |
| quantity_remaining | INT | |
| sale_start | DATETIME | Nullable |
| sale_end | DATETIME | Nullable |
| is_active | BOOLEAN | |

**Logic:** If `event_type = free` → auto-create one RSVP ticket type (price = 0)

### `event_registrations`
| Field | Type | Notes |
|---|---|---|
| id | BIGINT PK | |
| event_id | FK | |
| user_id | FK | |
| ticket_type_id | FK | |
| quantity | INT | default 1 |
| registration_status | ENUM(confirmed, cancelled, waitlist) | |
| payment_status | ENUM(disabled, pending, paid, refunded) | Always disabled now |
| seat_lock_expires_at | DATETIME | Nullable |
| ticket_uuid | UUID UNIQUE | Generated on registration |
| qr_hash | VARCHAR(128) | HMAC of ticket_uuid |
| entry_status | ENUM(not_checked_in, checked_in) | default: not_checked_in |
| checked_in_at | DATETIME | Nullable |
| checked_in_by | FK → users | Nullable |
| ticket_price | DECIMAL(10,2) | default 0 |
| platform_commission_amount | DECIMAL(10,2) | default 0 |
| organizer_amount | DECIMAL(10,2) | default 0 |

**Unique:** (event_id, user_id, ticket_type_id)

### `event_seat_locks` (skeleton only — inactive)
| Field | Type |
|---|---|
| id | BIGINT PK |
| event_id | FK |
| ticket_type_id | FK |
| user_id | FK |
| quantity | INT |
| locked_until | DATETIME |
| status | ENUM(active, expired, converted) |

**Controlled by:** `seat_lock_enabled = false` in platform_settings

### `event_status_logs`
| Field | Type |
|---|---|
| id | BIGINT PK |
| event_id | FK |
| action | ENUM(created, verified, published, updated, archived) |
| performed_by | FK → users |
| notes | TEXT |
| created_at | TIMESTAMP |

### `event_analytics`
| Field | Type |
|---|---|
| id | BIGINT PK |
| event_id | FK |
| views | INT |
| registrations_count | INT |
| waitlist_count | INT |
| capacity_fill_percent | DECIMAL(5,2) |

### `event_notifications` (bridge to Notification Engine)
| Field | Type |
|---|---|
| id | BIGINT PK |
| event_id | FK |
| channel | ENUM(email, whatsapp, push) |
| status | ENUM(queued, sent, failed) |
| sent_at | DATETIME |

### `event_geo_metrics`
| Field | Type |
|---|---|
| id | BIGINT PK |
| event_id | FK |
| city_id | FK |
| area_id | FK |
| registrations_count | INT |
| waitlist_count | INT |
| fill_percent | DECIMAL |

### `event_subscriptions` (geo subscription engine)
| Field | Type |
|---|---|
| id | BIGINT PK |
| user_id | FK |
| city_id | FK |
| area_id | FK (nullable) |
| category | VARCHAR (nullable) |
| is_active | BOOLEAN |

---

## ORGANIZER TABLES

### `organizers`
| Field | Type |
|---|---|
| id | BIGINT PK |
| user_id | FK UNIQUE |
| business_name | VARCHAR |
| gst_number | VARCHAR (nullable) |
| bank_account_name | VARCHAR (nullable) |
| bank_account_number | VARCHAR (nullable) |
| ifsc_code | VARCHAR (nullable) |
| verification_status | ENUM(pending, verified, rejected) |
| verification_level | ENUM(unverified, basic, verified, trusted, restricted) |

**Relationship:** User → hasOne Organizer → hasMany Events

---

## MIGRATION ORDER (STRICT)

1. create_events_table
2. create_event_ticket_types_table
3. create_event_registrations_table
4. create_event_seat_locks_table
5. create_event_status_logs_table
6. create_event_analytics_table
7. create_event_notifications_table
8. create_organizers_table
9. create_event_subscriptions_table
10. create_event_geo_metrics_table

**Pre-conditions:** cities, areas, wards, users, roles, payments table must already exist.

---

## SERVICE CLASSES (EVENTS ENGINE)

| Service | Responsibility | DB Transaction |
|---|---|---|
| `EventPublishService` | draft → review → published, logs, triggers EventPublished event | YES |
| `RSVPRegistrationService` | Capacity check, confirm or waitlist, decrement capacity_remaining | YES |
| `SeatLockService` | Seat reservation (dormant, config-gated) | YES |
| `QRValidationService` | Gate-level ticket validation with lockForUpdate | YES |

**RSVP logic (race-safe pattern):**
```
DB::transaction → $event->refresh() → check capacity_remaining
→ if > 0: create registration, decrement capacity
→ else: set waitlist
```

---

## DOMAIN EVENTS

| Event | Triggers |
|---|---|
| `EventPublished` | AutoCreateNewsPostListener, GeoNotificationListener, SocialDistributionListener |
| `RegistrationConfirmed` | SendConfirmationEmailListener, UpdateAnalyticsListener |
| `RegistrationWaitlisted` | SendWaitlistEmailListener |
| `TicketCheckedIn` | UpdateCheckinAnalyticsListener |
| `TicketCheckinRejected` | FraudMonitoringListener |

---

## APPLICATION RULES

- Cannot publish unless `verification_status = verified`
- Cannot delete published event — archive only
- Auto-calculate `capacity_remaining` on publish
- Auto-generate RSVP ticket for free events
- Prevent registration after `start_datetime`
- Prevent overbooking at DB transaction level
- Organizer cannot self-verify their own event
- Organizer cannot publish directly — Admin/Director only

---

## ADMIN UI STRUCTURE (EVENTS MODULE)

**Sidebar:**
Events → All Events / Add Event / Drafts / Pending Review / Published / Archived / Registrations / Analytics

**Add/Edit Event Sections:**
- Section A: Basic Info (Title, Slug, Summary, Description, Event Type)
- Section B: Geo (City, Area, Ward optional)
- Section C: Venue & Timing (Venue Name, Address, Start, End, Capacity)
- Section D: Ticket Types — shows banner: "Ticket payments currently disabled. RSVP mode active."
- Section E: Verification (status, approve, publish)

---

## OPEN QUESTIONS FOR THIS PILLAR

*(Update this section as decisions are made)*
- [ ] Organizer onboarding flow — how does a user become an organizer?
- [ ] Event categories — do we need a separate event_categories table or use the pillar system?
- [ ] Social distribution — manual flag or auto on publish for all events?
- [ ] Offline QR mode — define eligibility criteria when activating Phase C
