# PILLAR 3 — PAYMENTS & COMMISSION ENGINE
**Scope:** Payment integration, commission calculation, transaction recording.
**Do not:** Redesign settlement logic (Pillar 4), event CRUD (Pillar 1), or RBAC (Anchor Doc).
**Depends on:** Anchor Doc + events table (event_id, organizer_id, commission_percent)

---

## CURRENT STATUS

| Component | Status |
|---|---|
| Payment architecture | EXISTS — structure built, behavior OFF |
| `payments_mode` | `disabled` (platform_settings) |
| `settlement_mode` | `simulation` |
| Commission calculation | Dormant — runs only when `payment_status = paid` |
| Razorpay integration | Structured, sandbox testing when needed |

---

## FROZEN DECISIONS

- `payments_mode = disabled` — no checkout, no payment routes active, buttons hidden
- `payment_status` always = `disabled` until config switch flipped
- Commission % is EVENT-LEVEL configurable — never hardcoded globally
- Commission is STORED at transaction time — never recalculated later
- No partial state allowed — all payment mutations inside `DB::transaction()`
- Historical transaction rows are NEVER edited — reversals are new rows
- Idempotency: duplicate webhooks must be rejected (store gateway payment_id, check before insert)
- Razorpay is the single payment gateway — centralized across all engines

---

## DATABASE TABLES (PAYMENT + COMMISSION)

### `event_transactions` (revenue spine — never mix with generic payments)
| Field | Type | Notes |
|---|---|---|
| id | BIGINT PK | |
| event_id | FK → events | |
| registration_id | FK → event_registrations | |
| organizer_id | FK → organizers | |
| gross_amount | DECIMAL(10,2) | |
| commission_amount | DECIMAL(10,2) | Stored at time of payment |
| net_amount | DECIMAL(10,2) | gross - commission |
| payment_gateway_reference | VARCHAR | Nullable — Razorpay payment ID |
| payment_status | ENUM(disabled, pending, paid, failed, refunded) | default: disabled |
| settlement_status | ENUM(pending, locked, settled, adjustment_pending) | default: pending |
| settled_at | DATETIME | Nullable |

**Index:** (event_id, payment_status), (organizer_id, settlement_status)

### `revenue_ledger` (optional — investor-grade multi-vertical)
| Field | Type | Notes |
|---|---|---|
| id | BIGINT PK | |
| entity_type | VARCHAR | 'event', 'contest', 'directory' |
| entity_id | BIGINT UNSIGNED | |
| gross_amount | DECIMAL(12,2) | |
| commission_amount | DECIMAL(12,2) | |
| net_amount | DECIMAL(12,2) | |
| created_at | TIMESTAMP | |

**Index:** (entity_type, entity_id)

---

## COMMISSION CALCULATION LOGIC

**Trigger:** Payment gateway webhook (paid) — only when `payment_status = paid`

```
gross = ticket_price × quantity
commission = ROUND(gross × (event.commission_percent / 100), 2)
net = gross - commission
```

**Stored in:**
- `event_registrations`: ticket_price, platform_commission_amount, organizer_amount
- `event_transactions`: gross_amount, commission_amount, net_amount

**Event-level commission resolution:**
1. event.commission_percent (stored on event at creation)
2. Cannot be edited after event start_datetime

---

## COMMISSION SERVICE

**Class:** `CommissionService`
**Transaction:** YES — always inside `DB::transaction()`

```
Input: registration_id, payment_reference
Validates: payment not already processed (idempotency)
Calculates: gross, commission, net
Updates: event_registrations (payment_status, amounts)
Creates: event_transactions row
Creates: revenue_ledger row (if enabled)
Dispatches: PaymentRecorded, CommissionCalculated events
```

**Hard stops:**
- No commission if `payment_status ≠ paid`
- No calculation from request input — always from DB
- No negative net amounts
- No manual override without Director role + audit log

---

## PAYMENT STATE MACHINE

```
disabled (current) → pending (when payments enabled)
pending → paid (webhook success)
pending → failed (gateway failure)
paid → refunded (refund executed)
failed → pending (retry, optional)
refunded → [FINAL]
```

**Rules:**
- Cannot mark `paid` manually — webhook-driven only
- Refund must insert a reversal transaction row — never edit original

---

## WEBHOOK HANDLING

```
POST /webhooks/razorpay
→ Validate webhook signature
→ Match registration_id via gateway reference
→ Check idempotency (has this payment_id been processed?)
→ DB::transaction → CommissionService → Commit
→ Dispatch PaymentRecorded event
```

**Idempotency:** Store `payment_gateway_reference` → reject if already exists in `event_transactions`

---

## DOMAIN EVENTS

| Event | Listeners |
|---|---|
| `PaymentRecorded` | CommissionCalculatedListener |
| `CommissionCalculated` | RevenueLedgerInsertListener |

---

## DISABLED MODE BEHAVIOR (CURRENT)

```
checkout button: hidden
payment routes: blocked
payment_status: auto-set to "disabled"
commission: 0
organizer_amount: 0
event_transactions: can be created in disabled mode (for simulation)
```

---

## ACTIVATION CHECKLIST (DO NOT FLIP EARLY)

Before setting `payments_mode = live`:
- [ ] 30–60 days of simulation mode completed
- [ ] Reconciliation dashboard matches simulation math
- [ ] Refund-before-settlement tested
- [ ] Refund-after-settlement tested
- [ ] Duplicate webhook rejection tested
- [ ] Director + Tech Lead + Revenue Lead sign-off
- [ ] Refund policy page publicly visible
- [ ] Commission disclosure visible
- [ ] Terms of service updated

---

## OPEN QUESTIONS FOR THIS PILLAR

*(Update this section as decisions are made)*
- [ ] Which Razorpay payout method for organizer settlements? (bank transfer / UPI)
- [ ] GST handling — is commission amount inclusive or exclusive of GST?
- [ ] Invoice generation — auto PDF on payment, or manual?
- [ ] Contest engine payments — does this Pillar handle BCC entry fees too, or separate flow?
- [ ] Gateway sandbox setup timeline — when does Phase B begin?
