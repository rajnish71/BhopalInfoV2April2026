# PILLAR 5 — TRUST FRAMEWORK & GOVERNANCE
**Scope:** Organizer trust scoring, verification levels, badge system, civic governance charter, public-facing policies.
**Do not:** Redesign settlement logic (Pillar 4), event CRUD (Pillar 1), or payment internals (Pillar 3).
**Depends on:** Anchor Doc + organizers table, events table, event_registrations

---

## FROZEN DECISIONS

- Trust is measured — not granted for popularity
- Organizer verification levels upgraded by evidence only — not by request
- Auto-restriction triggers are system-driven — not emotional/manual
- No public leaderboards, no gamification, no gold stars — authority over popularity
- Governance charter is public — available as /verification page on bhopal.info
- All trust level changes are logged — no silent upgrades or downgrades
- Director override is allowed but must be logged with reason + IP + timestamp

---

## ORGANIZER VERIFICATION LEVELS (FROZEN)

| Level | Meaning |
|---|---|
| unverified | Profile created, no KYC |
| basic | Email + phone verified |
| verified | Documents validated (GST / ID) — eligible to publish events |
| trusted | Strong event performance history — earned, not granted |
| restricted | Flagged due to complaints, refund abuse, or fraud attempt |

**Transitions:**
```
unverified → basic (email/phone validation)
basic → verified (document validation by Admin/Director)
verified → trusted (performance metrics — auto + manual review)
any → restricted (fraud / complaints / refund spike — system-triggered)
restricted → verified (Director review + clearance)
```

**Rule:** No auto-upgrade to `trusted` without performance logic confirmation. Director/Admin approves.

---

## DATABASE TABLES (TRUST + GOVERNANCE)

### `event_trust_metrics`
| Field | Type | Notes |
|---|---|---|
| id | BIGINT PK | |
| event_id | FK → events | |
| fill_rate_percent | DECIMAL | |
| refund_percent | DECIMAL | |
| checkin_percent | DECIMAL | |
| complaints_count | INT | |
| anomaly_flags | INT | |
| calculated_score | INT | 0–100 |
| calculated_at | TIMESTAMP | |

### `organizer_trust_logs`
| Field | Type | Notes |
|---|---|---|
| id | BIGINT PK | |
| organizer_id | FK → organizers | |
| previous_level | VARCHAR | |
| new_level | VARCHAR | |
| reason | TEXT | |
| changed_by | FK → users | |
| created_at | TIMESTAMP | |

**Rule:** No silent changes. Every level change has a log entry.

### `event_incidents`
| Field | Type | Notes |
|---|---|---|
| id | BIGINT PK | |
| event_id | FK → events | |
| reported_by | FK → users | |
| type | ENUM(no_show, venue_issue, refund_delay, misconduct) | |
| severity | ENUM(low, medium, high) | |
| status | ENUM(open, resolved, escalated) | |
| notes | TEXT | |
| created_at | TIMESTAMP | |

---

## TRUST SCORE CALCULATION (AUTOMATED)

**Base score:** 50

| Condition | Adjustment |
|---|---|
| Organizer level = verified | +10 |
| fill_rate > 60% | +10 |
| refund_percent < 5% | +10 |
| No anomalies | +10 |
| refund_percent > 20% | -15 |
| Complaint spike | -20 |
| Fraud attempt detected | -30 |

**Score range:** 0–100. Recalculated after each event completion.

**Service:** `TrustScoreService`
- Runs after event end_datetime passed
- Reads fill rate, refund %, check-in %, complaint count from completed event
- Updates `event_trust_metrics`
- Re-evaluates organizer cumulative trust index
- If thresholds crossed → triggers `OrganizerRestricted` event

---

## AUTO-RESTRICTION RULES

System auto-flags organizer (`verification_level = restricted`) if:
- 3 high-severity complaints in 30 days
- Refund ratio > 40% of total transactions
- Fraud attempt detected (duplicate payout, QR abuse)
- Duplicate settlement anomaly

**When restricted:**
- Cannot publish new events
- Existing events remain but marked "under review"
- Settlement paused
- `OrganizerRestricted` event dispatched → EventPublishBlockListener + AdminNotificationListener

---

## BADGE HIERARCHY (UI)

### Organizer Badges

| Badge | Background | Text | Border | Usage |
|---|---|---|---|---|
| VERIFIED | #8FB339 | White | None | verification_level = verified |
| TRUSTED | #8FB339 | White | 1px Black | verification_level = trusted |
| BASIC | White | Black | 1px Black | verification_level = basic |
| UNDER REVIEW | White | Black | 2px #F57C00 | Under investigation |
| RESTRICTED | #B71C1C | White | None | verification_level = restricted |

### Event Badges

| Badge | Background | Text | Usage |
|---|---|---|---|
| VERIFIED EVENT | #8FB339 | White | verification_status = verified AND publish_status = published |
| DRAFT | White | Black | Internal only |
| PENDING REVIEW | White | Black | Orange border |
| ARCHIVED | White | Black | Black border |

**Placement rules:**
- Maximum 2 badges visible simultaneously per event card
- Badges appear right-aligned near title
- Never animated
- Never stacked with more than 2

---

## RBAC PERMISSION MATRIX (EVENTS + TRUST)

| Permission | Director | Admin | Finance | Organizer | Event Staff |
|---|---|---|---|---|---|
| event.create | ✔ | ✔ | ✗ | ✔ (draft only) | ✗ |
| event.verify | ✔ | ✔ | ✗ | ✗ | ✗ |
| event.publish | ✔ | ✔ | ✗ | ✗ | ✗ |
| event.archive | ✔ | ✔ | ✗ | ✗ | ✗ |
| organizer.verify | ✔ | ✔ | ✗ | ✗ | ✗ |
| organizer.restrict | ✔ | ✔ | ✗ | ✗ | ✗ |
| trust.modify | ✔ | ✗ | ✗ | ✗ | ✗ |
| revenue.view | ✔ | ✗ | ✔ | Limited | ✗ |
| commission.modify | ✔ | ✗ | ✗ | ✗ | ✗ |
| settlement.create | ✔ | ✗ | ✔ | ✗ | ✗ |
| settlement.override | ✔ | ✗ | ✗ | ✗ | ✗ |
| config.modify | ✔ | ✗ | ✗ | ✗ | ✗ |
| organizer.checkin | ✔ | ✔ | ✗ | ✔ | ✔ (scoped) |

---

## GOVERNANCE CHARTER (SUMMARY — FROZEN DOCTRINE)

**What Bhopal.info publishes:**
- Public cultural programs
- Exhibitions and fairs
- Educational sessions and workshops
- Civic-interest gatherings
- City-level community events

**What is explicitly excluded:**
- Political rallies
- Private celebrations
- Closed-door membership events
- Unverified commercial promotions
- MLM seminars

**Editorial neutrality rules:**
- No political propaganda
- No opinion-based attack posts
- No unverified allegations
- Source mandatory for all alerts
- Neutral tone in civic matters

**Financial governance:**
- Transparent bookkeeping
- Separate vertical reporting (BCC separate)
- No informal cash handling
- All commission stored at transaction time — never recalculated
- Settlement audit trail mandatory

**Crisis protocol:**
1. Freeze affected module
2. Raise internal alert
3. Document incident
4. Notify stakeholders if required
5. Publish public clarification if necessary

---

## PUBLIC-FACING PAGES (GOVERNANCE TRANSPARENCY)

### /verification (public)
Explains organizer verification levels to citizens:
- What Verified means
- What Trusted means
- What Under Review means
- What Restricted means
- How decisions are made (evidence-based, not popularity-based)

### Event detail page (quick verification box)
Compact box on every event page:
1. ✅ Public Interest Confirmed
2. 🏷 Organizer Status Verified (badge shown)
3. 📍 Location & Date Validated
4. 👥 Capacity Structured
5. 🔎 Review & Approval Logged
6. 🔐 Financial Architecture Ready (payments currently disabled)

**Box design:** White background, red section titles (#B71C1C), green only for "Verified" label, no shadows

---

## DIRECTOR DASHBOARD — TRUST VIEW

| Organizer | Events | Avg Fill | Refund % | Trust Score | Status |
|---|---|---|---|---|---|

**Color logic:**
- Score 80+ → Green
- Score 50–79 → Neutral
- Score < 50 → Warning
- Restricted → Red (#B71C1C)

---

## DOMAIN EVENTS

| Event | Listeners |
|---|---|
| `TrustScoreUpdated` | OrganizerStatusReevaluationListener |
| `OrganizerRestricted` | EventPublishBlockListener, AdminNotificationListener |

---

## OPEN QUESTIONS FOR THIS PILLAR

*(Update this section as decisions are made)*
- [ ] Trusted level — what exact metrics combination qualifies? (minimum events count, minimum fill rate, time period)
- [ ] Complaint submission — is this public-facing (from attendees) or internal admin only?
- [ ] Trust score recalculation — triggered on event completion only, or also on new complaint?
- [ ] Post-restriction review — what is the appeal process for organizer to challenge restriction?
- [ ] Public transparency report — quarterly cadence, who drafts, what format?
