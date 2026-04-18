# PILLAR 7 — INFRASTRUCTURE, CI/CD, SECURITY & DISASTER RECOVERY
**Version:** 2.0 | **Updated:** April 2026
**Scope:** Server setup, deployment workflow, security hardening, backup strategy, disaster recovery.
**Do not:** Redesign application business logic — that belongs in the respective engine pillars.
**Depends on:** Anchor Doc (tech stack decisions, platform_settings table, RBAC roles)

---

## CURRENT ACTUAL STATE (April 2026)

| Component | Status | Detail |
|---|---|---|
| Hosting | Active | AWS EC2 |
| Web server | Active | Nginx |
| Live URL | Active | http://52.66.167.85/ |
| Framework | Active | Laravel |
| Git repo | Active | BhopalInfoV2April2026 (GitHub) |
| Active branch | dev | Working branch |
| Staging environment | None | Intentional — Phase 2 |
| CI/CD pipeline | None | Intentional — Phase 2 |
| Release-based deployment | Inactive | Structure exists, not yet used |
| HTTPS / SSL | Not confirmed | Currently HTTP — needs immediate attention |

---

## SERVER DIRECTORY STRUCTURE

```
/var/www/bhopal-admin-core/
├── current/                    ← Active Laravel app (git-tracked)
│   ├── app/
│   ├── routes/
│   ├── resources/
│   │   └── views/
│   │       └── themes/         ← Theme Engine location
│   │           └── modern2026/ ← Active theme
│   ├── public/
│   └── scripts/
│       ├── dev.sh              ← Git workflow automation
│       └── backup_db.sh        ← Manual DB backup
├── releases/                   ← Reserved for future release-based deploy
└── shared/                     ← Reserved for future shared assets
```

**Active root served by Nginx:** `/var/www/bhopal-admin-core/current/public`

---

## GIT ARCHITECTURE

| Item | Value |
|---|---|
| Repository | BhopalInfoV2April2026 |
| Platform | GitHub |
| Git location | `/var/www/bhopal-admin-core/current` |
| Main branch | `main` — stable version |
| Dev branch | `dev` — active development |

**Tracking rules:**
- Git tracks application code only
- `releases/` and `shared/` folders NOT tracked
- Sensitive + heavy files excluded via `.gitignore`

---

## DAILY DEVELOPMENT WORKFLOW

```
1. Work inside dev branch on server directly
2. Make code changes
3. Run: ./scripts/dev.sh
4. Script performs:
   → git pull
   → git status
   → confirmation prompt (prevents blind commits)
   → git add
   → commit with manual message
   → git push
```

**Current mode:** Development directly on live server. Intentional for Phase 1 speed. Risk accepted. Mitigate by committing frequently and scheduling `backup_db.sh`.

---

## FROZEN DECISIONS

- Framework: Laravel (latest stable LTS) — no switching
- Database: MySQL with Redis for caching and queues
- Hosting: AWS EC2 with Nginx — locked
- Razorpay is the single payment gateway — centralized
- Business logic never in `.env` — always in `platform_settings` table
- Financial config toggles require Director approval + dual confirmation
- No direct commits to `main` — all work in `dev`, merge when stable

---

## CURRENT RISKS AND ACTIONS NEEDED

| Risk | Severity | Current Mitigation | Action Needed |
|---|---|---|---|
| No HTTPS | High | None | Install SSL certificate — urgent |
| Dev on live server | Medium | dev.sh prevents blind commits | Staging env in Phase 2 |
| No automated DB backup | High | backup_db.sh exists (manual) | Schedule via cron — urgent |
| No rollback system | Medium | Git history available | Release-based deploy in Phase 2 |
| No staging environment | Medium | Frequent commits | Phase 2 milestone |

---

## ENVIRONMENT STRATIFICATION (PLANNED — PHASE 2)

| Environment | Payments | Settlement | QR |
|---|---|---|---|
| Local dev (future) | disabled | simulation | off |
| Staging (future) | simulation | simulation | optional |
| Production | disabled (now) | simulation | off |

---

## PLATFORM_SETTINGS TABLE (CONFIG CONTROL LAYER)

All feature toggles stored in DB — never in `.env` for business logic.

```php
function setting($key, $default = null) {
  return cache()->rememberForever("setting_$key", function() use ($key, $default) {
    $record = PlatformSetting::where('key', $key)->first();
    if (!$record) return $default;
    return match($record->type) {
      'boolean' => filter_var($record->value, FILTER_VALIDATE_BOOLEAN),
      'integer' => (int) $record->value,
      'decimal' => (float) $record->value,
      'json' => json_decode($record->value, true),
      default => $record->value,
    };
  });
}
```

On setting update: `cache()->forget("setting_$key")`

### Config lock matrix

| Config Key | Who Can Toggle | Dual Approval |
|---|---|---|
| payments_mode | Director only | Yes |
| settlement_mode | Director only | Yes |
| seat_lock_enabled | Director only | No |
| qr_checkin_enabled | Director only | No |
| active_theme | Super Admin | No |
| multi_city_enabled | Director only | No |

---

## BACKUP STRATEGY

**Current:** Manual `backup_db.sh` — requires human to run.

**Immediate action — schedule it:**
```bash
# Add to crontab: crontab -e
0 2 * * * /var/www/bhopal-admin-core/current/scripts/backup_db.sh >> /var/log/bhopal_backup.log 2>&1
```

**Target:**

| Type | Frequency | Retention |
|---|---|---|
| Full DB backup | Nightly 02:00 AM | 7 days rolling |
| Weekly snapshot | Weekly | 4 weeks |
| Monthly archive | Monthly | 6 months |

---

## SECURITY HARDENING

### Immediate (before any public user data collection)
- [ ] Install SSL — Let's Encrypt is free, can be done in under 10 minutes with certbot
- [ ] Set `APP_DEBUG=false` in production `.env`
- [ ] Confirm `.env` is not web-accessible
- [ ] Confirm `APP_KEY` is set and unique

### Admin panel (when built)
- 2FA mandatory for Director, Tech Admin, Editorial Lead
- Session idle timeout: 20 minutes
- No shared passwords, all activity logged

### API layer (when Organizer API is built)
- Laravel Sanctum with scoped ability tokens
- Rate limits: 60/min read, 20/min write, 120/min QR
- Token auto-revoke after 90 days inactivity

### Financial layer
- All mutations inside `DB::transaction()`
- `lockForUpdate()` on financial rows
- Idempotency keys for payment webhooks

---

## PHASE 2 UPGRADE PATH (NOT YET — PLANNED)

When feature stabilization is complete:

```
Step 1: Activate release-based deployment
  → Releases go into releases/
  → current/ symlinked to active release
  → Rollback = re-symlink to previous release

Step 2: Staging environment
  → Separate environment mirroring production
  → payments = simulation

Step 3: CI/CD
  → GitHub Actions: push to main → deploy to production
  → push to dev → deploy to staging

Step 4: Automated deploy script
  → Replaces manual dev.sh workflow for production
```

---

## DISASTER RECOVERY (CURRENT MINIMAL PROTOCOL)

**Bad code push:**
1. `git revert` the bad commit
2. `git push`, pull on server
3. `php artisan config:cache && php artisan view:clear`

**Financial data issue (when payments go live):**
1. Set `settlement_mode = manual` in platform_settings
2. Export transaction snapshot
3. Fix via reversal entries only — never edit historical rows

**Server loss:**
1. Clone repo to new EC2: `git clone [repo]`
2. Restore `.env` from secure offline backup
3. Restore DB from latest backup_db.sh output
4. `composer install --no-dev && php artisan migrate`
5. `php artisan config:cache && php artisan route:cache`

---

## OPEN QUESTIONS FOR THIS PILLAR

- [ ] SSL certificate — Let's Encrypt or paid? Needs to happen before launch.
- [ ] Is backup_db.sh scheduled as cron yet?
- [ ] Where is production .env backed up? (must NOT be in git)
- [ ] Is Redis installed and configured on EC2?
- [ ] Is Laravel queue worker running? Via supervisor?
- [ ] What triggers Phase 2 migration — which feature milestone?
