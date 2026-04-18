# PILLAR 4 — SETTLEMENT, REFUNDS & MONITORING
**Scope:** Settlement engine, refund reversal logic, cron automation, financial monitoring.
**Do not:** Redesign commission calculation (Pillar 3), event CRUD (Pillar 1), or RBAC (Anchor Doc).
**Depends on:** Anchor Doc + event_transactions, event_registrations, organizers tables

---

## CURRENT STATUS

| Component | Status |
|---|---|
| `settlement_mode` | `simulation` (platform_settings) |
| `auto_settlement_enabled` | `false` |
| Settlement logic | Built — runs in simulation |
| Real Razorpay payouts | OFF — not activated |
| Refund logic | Architecture built — dormant until payments live |
| Monitoring | Built — alerts watching simulation runs |

---

## FROZEN DECISIONS

- Settlement ≠ Payment. Payment is per-registration. Settlement is per-batch, per-organizer.
- Historical rows are NEVER edited — refunds create reversal rows, settlements append
- `settlement_status` on transactions: pending → locked → settled (or adjustment_pending)
- `lockForUpdate()` is mandatory on all settlement and refund operations
- Cron must use `withoutOverlapping()` + `onOneServer()`
- Manual settlement must go through Director or Finance role + audit log
- SETTLEMENT_DELAY_DAYS = 3 (configurable in platform_settings)
- Commission % cannot be modified after event start_datetime

---

## DATABASE TABLES (SETTLEMENT + REFUND + MONITORING)

### `event_settlements`
| Field | Type | Notes |
|---|---|---|
| id | BIGINT PK | |
| organizer_id | FK → organizers | |
| total_gross | DECIMAL(12,2) | |
| total_commission | DECIMAL(12,2) | |
| total_net | DECIMAL(12,2) | |
| settlement_status | ENUM(pending, locked, simulated, processing, settled, failed) | |
| locked_at | DATETIME | Nullable |
| processed_at | DATETIME | Nullable |
| settled_at | DATETIME | Nullable |
| simulation_reference | VARCHAR | Nullable |
| payout_reference | VARCHAR | Nullable — Razorpay payout ID |
| created_by | FK → users | Nullable (null = system/cron) |

### `settlement_alerts`
| Field | Type | Notes |
|---|---|---|
| id | BIGINT PK | |
| alert_type | VARCHAR | e.g. "locked_stuck", "payout_failed", "refund_spike" |
| reference_id | BIGINT UNSIGNED | Nullable — points to settlement/transaction |
| severity | ENUM(info, warning, critical) | |
| message | TEXT | |
| status | ENUM(open, acknowledged, resolved) | default: open |
| resolved_by | FK → users | Nullable |
| resolved_at | TIMESTAMP | Nullable |

**Rule:** Alerts are never deleted. State only: open → acknowledged → resolved

### `settlement_health_logs` (periodic health snapshot)
| Field | Type |
|---|---|
| id | BIGINT PK |
| pending_transactions | INT |
| pending_net_amount | DECIMAL(14,2) |
| locked_settlements | INT |
| failed_settlements | INT |
| logged_at | TIMESTAMP |

### `settlement_runs` (cron execution audit)
| Field | Type |
|---|---|
| id | BIGINT PK |
| total_organizers_processed | INT |
| total_amount_settled | DECIMAL(12,2) |
| mode | ENUM(simulated, live) |
| started_at | TIMESTAMP |
| completed_at | TIMESTAMP |
| status | ENUM(success, partial, failed) |
| error_message | TEXT |

---

## SETTLEMENT STATE MACHINE

**event_transactions.settlement_status:**
```
pending → locked (batch selected)
locked → settled (simulation or payout success)
locked → pending (payout failure — revert)
settled → adjustment_pending (post-settlement refund)
adjustment_pending → settled (next settlement batch)
refunded → [FINAL]
```

**event_settlements.settlement_status:**
```
pending → locked (batch selection)
locked → simulated (current mode)
locked → processing (real payout mode, future)
processing → settled (payout success)
processing → failed (payout failure)
simulated → settled (auto)
failed → pending (retry)
settled → [FINAL]
```

---

## SETTLEMENT EXECUTION FLOW (EXACT ORDER)

```
STEP 1 — Find eligible transactions
  WHERE payment_status = paid
    AND settlement_status = pending
    AND event.end_datetime <= now() - SETTLEMENT_DELAY_DAYS
    AND organizer.verification_status = verified

STEP 2 — Group by organizer_id

STEP 3 — For each organizer: DB::transaction
  → lockForUpdate() on eligible transactions
  → Calculate totals (gross, commission, net)
  → Create EventSettlement (status: locked)
  → Update transactions: settlement_status = locked
  → Commit

STEP 4 — Simulation mode:
  → Update settlement: status = simulated
  → Update transactions: status = settled, settled_at = now()
  → Insert settlement_runs log

STEP 4 (future live mode):
  → Call Razorpay Payout API
  → If success: status = settled
  → If failure: status = failed, transactions revert to pending
```

---

## REFUND SERVICE

**Class:** `RefundService`
**Transaction:** YES — with `lockForUpdate()`

### Scenario A — Refund Before Settlement
```
DB::transaction
  → lockForUpdate() on transaction
  → Block if settlement_status = locked
  → Mark original transaction: payment_status = refunded
  → Insert reversal row (negative amounts, payment_status = refunded)
  → Update registration: payment_status = refunded
```

### Scenario B — Refund After Settlement
```
DB::transaction
  → Insert reversal row (negative amounts)
  → Set reversal: settlement_status = adjustment_pending
  → Next cron batch picks it up and deducts
  → Original settlement record: NEVER modified
```

### Partial Refund
```
refund_commission = ROUND(refund_amount × (event.commission_percent / 100), 2)
Insert reversal:
  gross_amount = -refund_amount
  commission_amount = -refund_commission
  net_amount = -(refund_amount - refund_commission)
```

**Hard stops:**
- Cannot refund if `payment_status ≠ paid`
- Cannot refund if already refunded
- Cannot refund if `settlement_status = locked` (wait for settlement to complete)
- Cannot manually delete refund transactions

---

## CRON AUTOMATION

**Command:** `php artisan settlements:process`

**Schedule:**
```php
$schedule->command('settlements:process')
  ->dailyAt('02:00')
  ->withoutOverlapping()
  ->onOneServer();
```

**Monitoring command:** `php artisan settlements:monitor` → runs hourly

**Safety controls:**
- `withoutOverlapping()` — prevents duplicate runs
- `onOneServer()` — prevents multi-node collision
- Max settlement cap per run (configurable)
- Log every run in `settlement_runs`

---

## SETTLEMENT MONITORING — ALERT TRIGGERS

| Severity | Trigger Condition |
|---|---|
| CRITICAL | settlement_status = failed |
| CRITICAL | locked > 30 minutes without resolving |
| CRITICAL | cron last run > 26 hours ago |
| CRITICAL | duplicate settlement attempt detected |
| WARNING | single settlement > configurable threshold amount |
| WARNING | refund ratio > 20% in 24 hours |
| WARNING | transaction count mismatch (confirmed ≠ transactions) |
| INFO | settlement simulated successfully |

---

## RECONCILIATION DASHBOARD STRUCTURE

**Access:** Director, Finance role only

**Section A — Global Snapshot:** Total Registrations, Paid Registrations, Gross Revenue, Platform Commission, Net Payable (with time filters)

**Section B — Event-wise table:**
| Event | Organizer | Registrations | Paid | Gross | Commission | Net | Settlement Status |

**Section C — Discrepancy panel:** Flag if confirmed registrations ≠ transaction count

**Section D — Settlement panel (by organizer):**
| Organizer | Total Gross | Commission | Net Due | Settlement Status |

**Section E — City-wise view** (multi-city ready via event.city_id)

**Query pattern:** Always aggregate from stored `event_transactions` — never recalculate from ticket_price

---

## SETTLEMENT BADGE COLORS (UI)

| Status | Color |
|---|---|
| Pending | Orange border (#F57C00) |
| Locked | Grey |
| Simulated | Blue |
| Processing | Purple (not in brand — use neutral) |
| Settled | Green (#8FB339) |
| Failed | Red (#B71C1C) |

---

## DOMAIN EVENTS

| Event | Listeners |
|---|---|
| `SettlementLocked` | AuditLogListener |
| `SettlementSimulated` | SettlementHealthListener |
| `SettlementCompleted` | OrganizerNotificationListener, RevenueLedgerInsertListener |
| `SettlementFailed` | SettlementAlertListener |
| `RefundProcessed` | RefundAdjustmentListener, TrustImpactListener |

---

## FRAUD PROTECTION (SETTLEMENT LAYER)

- `lockForUpdate()` prevents double payout
- `settlement_status = locked` prevents re-selection
- Duplicate detection: check if locked settlement already exists before creating
- `withoutOverlapping()` prevents cron overlap
- All settlement overrides logged with actor, reason, IP, timestamp

---

## OPEN QUESTIONS FOR THIS PILLAR

*(Update this section as decisions are made)*
- [ ] What is the max settlement cap per batch run?
- [ ] Razorpay payout method: bank transfer or UPI? (needed for Phase C)
- [ ] High-value settlement alert threshold — what INR amount triggers a warning?
- [ ] Refund window — how many days after event end is refund allowed?
- [ ] Settlement email to organizer — what information to include in notification?
