# PILLAR 6 — QR VALIDATION & CHECK-IN SYSTEM
**Scope:** QR code generation, gate-level ticket validation, check-in audit, offline fallback strategy.
**Do not:** Redesign event CRUD (Pillar 1), payment logic (Pillar 3), or trust scoring (Pillar 5).
**Depends on:** Anchor Doc + event_registrations table (ticket_uuid, qr_hash, entry_status fields)

---

## CURRENT STATUS

| Component | Status |
|---|---|
| `qr_checkin_enabled` | `false` (platform_settings) |
| QR schema | EXISTS — fields in event_registrations |
| Validation service | Architecture built — dormant |
| Check-in tables | Need to be created (Phase B/C) |
| Offline mode | Phase C+ only |

---

## FROZEN DECISIONS

- QR validation is SERVER-SIDE only — never client-trust, never browser-only validation
- QR code contains ONLY `ticket_uuid` — nothing else (no user email, no amount, no event metadata)
- `ticket_uuid` is UUID v4 — non-sequential, cannot be guessed or enumerated
- `lockForUpdate()` is mandatory during check-in — prevents double scan
- Duplicate scan must be rejected with exact failure reason and timestamp
- Every scan attempt logged — including failed/fraudulent attempts
- QR code content format: `https://bhopal.info/checkin/{ticket_uuid}` or just the UUID

---

## QR FIELDS (IN event_registrations — ALREADY EXIST)

| Field | Type | Notes |
|---|---|---|
| ticket_uuid | UUID UNIQUE | Generated on registration via `Str::uuid()` |
| qr_hash | VARCHAR(128) | `hash_hmac('sha256', ticket_uuid, APP_KEY)` — optional extra security |
| entry_status | ENUM(not_checked_in, checked_in) | default: not_checked_in |
| checked_in_at | DATETIME | Nullable |
| checked_in_by | FK → users | Nullable |

---

## DATABASE TABLES (QR + CHECK-IN LAYER)

### `event_checkins` (audit trail — separate from registrations)
| Field | Type | Notes |
|---|---|---|
| id | BIGINT PK | |
| event_id | FK → events | |
| registration_id | FK → event_registrations | |
| checked_in_by | FK → users | Staff user |
| checked_in_at | DATETIME | |
| scan_device | VARCHAR | Nullable |
| ip_address | VARCHAR | Nullable |

**Index:** (event_id, checked_in_at)

**Why separate table?**
- Multi-day events support
- Re-entry scenarios
- Security audit
- Refund dispute evidence

### `qr_scan_logs` (all attempts including failed)
| Field | Type | Notes |
|---|---|---|
| id | BIGINT PK | |
| ticket_uuid | VARCHAR | Scanned value |
| scan_status | ENUM(valid, already_checked_in, invalid, cancelled, unpaid) | |
| event_id | FK → events | Nullable |
| scanned_by | FK → users | Nullable |
| device_info | VARCHAR | Nullable |
| ip_address | VARCHAR | Nullable |
| created_at | TIMESTAMP | |

**Index:** (ticket_uuid, created_at)
**Purpose:** Fraud protection, staff misuse detection, gate dispute resolution

### `event_gate_staff` (multi-gate deployment)
| Field | Type | Notes |
|---|---|---|
| id | BIGINT PK | |
| event_id | FK → events | |
| user_id | FK → users | Staff member |
| gate_name | VARCHAR | "Gate A", "VIP Gate", etc. |

---

## MIGRATION ORDER (QR LAYER)

1. modify_event_registrations_table (add ticket_uuid, qr_hash, entry_status fields)
2. create_event_checkins_table
3. create_qr_scan_logs_table
4. create_event_gate_staff_table

---

## QR VALIDATION SERVICE

**Class:** `QRValidationService`
**Transaction:** YES — with `lockForUpdate()`
**API route:** `POST /api/v1/organizer/checkin`
**Middleware:** `auth:sanctum + role:organizer + ability:organizer.checkin + throttle:120/min`

### Validation sequence (strict order inside DB transaction):
```
1. Lookup registration WHERE ticket_uuid = ?  → lockForUpdate()
2. Validate event ownership:
   registration.event.organizer_id == auth()->organizer_id
3. Validate event state:
   publish_status = published
   verification_status = verified
   not archived
   current_time within event window
4. Validate registration state:
   registration_status = confirmed
   entry_status = not_checked_in
5. Validate payment (future paid mode):
   payment_status = paid
   (skipped for free/RSVP events currently)

If all valid → proceed to check-in
If any fail → log to qr_scan_logs + return structured error
```

### On success:
```
DB::transaction
  → Update entry_status = checked_in
  → Set checked_in_at = now()
  → Set checked_in_by = staff_id
  → Insert into event_checkins
  → Insert into qr_scan_logs (scan_status = valid)
  → Commit
```

---

## FAILURE RESPONSE MATRIX

| Condition | HTTP | scan_status | Message shown |
|---|---|---|---|
| UUID not found | 404 | invalid | Ticket not recognized |
| Already checked in | 409 | already_checked_in | "Already used at Gate B – 6:42 PM" |
| Registration cancelled | 403 | cancelled | Registration cancelled |
| Unpaid (future paid mode) | 402 | unpaid | Payment not confirmed |
| Organizer mismatch | 403 | invalid | Unauthorized scan |
| Event not active | 410 | invalid | "Event not active yet" or "Event has ended" |

**Response format:**
```json
{
  "success": false,
  "status": "already_checked_in",
  "message": "Ticket already used at 6:42 PM."
}
```

---

## GATE SCANNER UI (DESIGN RULES)

Follows brand rules — flat, no animations, civic tone:

- Background: #FFFFFF
- Valid entry: Green border (#8FB339 accent)
- Rejected: Red border (#B71C1C)
- No animations, no confetti, no celebration effects
- Full-screen result: ✅ VALID ENTRY or ❌ REJECTED
- Clear human-readable reason on rejection

---

## CONCURRENCY PROTECTION

**Problem:** Two gates scan same ticket simultaneously.

**Solution:**
```php
$registration = EventRegistration::where('ticket_uuid', $uuid)
  ->lockForUpdate()
  ->firstOrFail();
```

Inside `DB::transaction()`. First request succeeds. Second request sees `entry_status = checked_in` and is rejected. No duplicate entries possible.

---

## OFFLINE MODE (PHASE C+ ONLY — NOT NOW)

**Strategy:**
- Pre-download encrypted list of valid `ticket_uuid` values before event
- Store locally on device
- Mark locally scanned
- Sync on reconnect
- Server validates on sync, rejects duplicates, flags anomalies

**Constraints:**
- Restricted to short time window only
- Large events only (>500 attendees)
- Logged separately as offline scans
- Never default to offline-first

---

## FREE EVENT BEHAVIOR (CURRENT — ACTIVE)

Validation skips payment check:
- confirmed → ✔
- not cancelled → ✔
- not already checked_in → ✔

No payment_status check needed. Works now.

---

## PAID EVENT BEHAVIOR (FUTURE)

Add check: `payment_status = paid`
No schema change required — field already exists in `event_registrations`.

---

## SECURITY HARDENING

| Requirement | Implementation |
|---|---|
| HTTPS enforced | Server config |
| Sanctum token | Required for all check-in API calls |
| Ability scope | `organizer.checkin` |
| Rate limit | 120 requests/min for check-in endpoint |
| CSRF | Off (API token-based) |
| Organizer scoping | Policy check — organizer can only scan their own events |
| Suspicious scan alert | > 20 invalid attempts → trigger alert |

---

## POST-EVENT RECONCILIATION

After event end, auto-calculate:
- Total registrations
- Total check-ins
- Total failed scans
- Duplicate scan attempts
- Gate-wise activity

Store in `event_analytics`. Feed into trust score calculation via `TrustScoreService`.

---

## OPEN QUESTIONS FOR THIS PILLAR

*(Update this section as decisions are made)*
- [ ] QR generation: server-side image (PNG) or client generates from UUID string?
- [ ] Email delivery: QR image attached to confirmation email or link to download?
- [ ] Gate UI: PWA (web app) or native mobile app for scanning?
- [ ] Multi-gate: is event_gate_staff table needed for Phase B or only Phase C?
- [ ] QR expiry: should QR expire after event end + 12 hours, or same day midnight?
