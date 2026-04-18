# BHOPAL.INFO — PROJECT MASTER CONTEXT
# ================================================
# READ THIS FILE COMPLETELY before doing anything.
# This is the single source of truth. It overrides
# all previous decisions made in any chat session.
# ================================================

## Project identity
- Platform: Bhopal.info — Bhopal's civic digital infrastructure
- Domain: bhopal.info
- Founded: 2010
- Tagline: "What Bhopal Needs to Know – ज़रूरी है जानना"
- GitHub: https://github.com/rajnish71/BhopalInfo-Ver1
- Staging server: AWS EC2 at 52.66.167.85 (NO localhost ever)

## Development workflow (non-negotiable order)
1. Write or edit code in Antigravity IDE
2. Commit and push to GitHub
3. SSH into EC2 and run: git pull origin main
4. Test on staging server at 52.66.167.85
5. Owner confirms step is working before next step begins
6. NEVER edit files directly on EC2 — always via GitHub

## Stack (locked)
- Backend: Laravel LTS (latest stable)
- Database: MySQL + Redis (cache + queues)
- Web server: Nginx + PHP latest stable
- Payment: Razorpay (centralised gateway)
- Architecture: Modular Monolith
  → One Laravel app, 8 independent internal engines
  → Theme layer sits above all blade views
- Version control: Git + GitHub
- Hosting: AWS EC2 (staging and production)

## Brand (never change — zero exceptions)
- Primary Red:    #B71C1C  (wordmark, favicon ring)
- Primary Orange: #F57C00  (sun rays, arc)
- Accent Green:   #8FB339  (O dot, accent only)
- Background:     #FFFFFF / #000000
- Rules: Flat design only. No gradients. No shadows.
         No glow. No new colors. Green is accent-only.

## Theme system (NEW — built in Phase 1)
Architecture: ThemeService class in Laravel
- Themes are blade template sets stored in:
  resources/views/themes/{theme-name}/
- Active theme set via config/theme.php or DB setting
- Switching themes = zero backend changes needed
- CSS variables handle color/spacing per theme
- Admin panel allows theme switching without code
- Default theme: "civic-default" (brand-compliant)
- Phase 1 delivers: civic-default theme only
- Phase 2+ may add: dark-mode, high-contrast, seasonal

## Database rules (non-negotiable)
- Every single table must have city_id (FK to cities)
- This enables multi-city expansion without rebuilding
- city_id = 1 for Bhopal during all Phase 1 and 2 work

## ER diagram — locked table list
users, cities, areas, wards, organizers,
events, event_ticket_types, event_registrations,
event_seat_locks, event_transactions,
payments, news_posts

## The 8 engines — phased build order

### PHASE 1 — Foundation (build in this exact order)
Dependencies must exist before next engine starts.
Owner confirms each engine before proceeding.

  Engine 1: Theme Engine
  - ThemeService class
  - Blade theme folder structure
  - config/theme.php
  - Basic admin theme switcher
  Status: [ ] Not started

  Engine 2: User Identity Engine
  - Registration, login, logout
  - Role-based access control (RBAC)
  - Roles: Platform Director, Ops Head, Editorial Lead,
           Tech Lead, Revenue Lead, BCC Coordinator,
           Content Moderator, Contest Manager
  - Area tagging per user (links to areas table)
  - Activity log table
  Status: [ ] Not started

  Engine 3: Content Engine
  - Post creation with pillar tagging
  - 7 pillars enforced via enum/relation
  - Geo-tagging (city_id + area_id on every post)
  - Editorial workflow: Draft → Review → Approved → Published
  - SEO metadata fields (slug, meta_title, meta_desc)
  - Source reference field (for civic alerts)
  - Auto-distribution trigger hook (fires on publish)
  Status: [ ] Not started

  Engine 4: Business Directory Engine
  - 3-tier listings: Free / Standard / Premium
  - Subscription expiry tracking
  - Category hierarchy with geo filter
  - Click tracking per listing
  - Review moderation queue
  Status: [ ] Not started

### PHASE 2 — Revenue (unlock after Phase 1 confirmed stable)
Do not begin Phase 2 until owner confirms Phase 1 is live
and functioning correctly on staging.

  Engine 5: Payment & Transaction Engine
  - Razorpay integration (centralised)
  - Payments table linked to: user, module, vertical
  - Separate accounting export per vertical (BCC vs core)
  - Transaction log with full audit trail
  Status: [ ] Locked until Phase 1 complete

  Engine 6: Event & Ticketing Engine
  - Event creation with capacity management
  - Ticket categories (event_ticket_types)
  - Seat locking (event_seat_locks)
  - QR hash generation per registration
  - Commission calculation per event
  - Organiser dashboard
  - Attendee export
  Status: [ ] Locked until Engine 5 complete

  Engine 7: Notification Engine
  - Email (SMTP via Laravel Mail)
  - WhatsApp (via WhatsApp Business API)
  - In-app notifications
  - Geo-filter logic: send to area subscribers only
  - Interest category filtering
  Status: [ ] Locked until Phase 1 complete

  Engine 8: Contest Engine
  - Mini civic contests + BCC photo contests
  - Entry fee logic (connects to Engine 5)
  - File upload for photo entries
  - Jury panel access role
  - Certificate generation
  - Winner declaration workflow
  - Public result display
  Status: [ ] Locked until Engine 5 complete

### PHASE 3 — Scale (unlock after Phase 2 revenue confirmed)
  - Mobile app (conditional: only if 10K+ emails + 5K WhatsApp)
  - Multi-city expansion (city_id already in all tables)
  - Data subscription products
  - Institutional partnerships
  - Analytics and civic data reports

## BCC Vertical (Bhopal Camera Club)
URL: bhopal.info/bhopal-camera-club
Model: Federal — autonomous operations, shared infrastructure
- Separate contest dashboard
- Separate revenue accounting export
- Separate BCC Coordinator role
- Shared: auth system, payment gateway, notification engine
- Built as part of Contest Engine in Phase 2

## 7 Content pillars (locked — enforced in DB as enum)
1. Civic & Utility Updates   (target: 40% of posts)
2. Local Alerts & Safety     (target: 10%)
3. City Services & How-To    (target: 20%)
4. Local Businesses          (target: 10%)
5. Events & What's Happening (target: 10%)
6. History, Heritage & Context (target: 10%)
7. Community Notices         (admin-approval required)

## Governance tiers
Tier 1 — Platform Director (owner): brand, strategy, revenue approval
Tier 2 — Ops Head, Editorial Lead, Tech Lead, Revenue Lead
Tier 3 — Content Moderators, Field Volunteers,
          Contest Team, BCC Coordinator

## Revenue model by phase
Phase 1: Business listings (paid tiers), limited sponsored posts,
         mini contests (low entry fee)
Phase 2: Event ticketing commissions (5-15%), directory scaling,
         newsletter sponsorship, contest sponsorship
Phase 3: Data subscriptions, institutional partnerships,
         multi-city licensing

## AI coding rules (for Antigravity / Claude sessions)
These rules apply in every session, no exceptions:

1. Read this file completely before writing a single line of code
2. Run: ls -la, php artisan migrate:status, php artisan route:list
   Report what exists vs what blueprint requires
3. Before editing any file:
   - State the exact filename
   - Summarise current content
   - Describe planned change
   - Wait for owner confirmation
4. Never delete any file or table without explicit owner approval
5. Tag every task with: Engine name + Phase number
6. After every session, list:
   - Files created
   - Files modified
   - Migrations run
   - Current status per engine
   - Next recommended step
7. If codebase contradicts this document: FLAG IT, do not work around it silently
8. Follow Laravel conventions strictly — no shortcuts, no hacks
9. Explain every technical decision in plain English first
10. If a better approach exists than what is planned:
    SUGGEST IT clearly with pros and cons, then await decision
    Do NOT implement it unilaterally
11. Step-by-step only — one confirmed working step at a time

## Session start checklist (run every single session)
[ ] Read BHOPAL_PROJECT_CONTEXT.md
[ ] Run: php artisan migrate:status
[ ] Run: ls -la app/Services/ app/Http/Controllers/ resources/views/themes/
[ ] Report: which engines exist, which are incomplete
[ ] Ask owner: what to work on today
[ ] Confirm task before writing any code

## Current build status (update after every session)
Last updated: [DATE]
Phase 1 progress:
  - Theme Engine:      [ ] Not started
  - User Identity:     [ ] Not started
  - Content Engine:    [ ] Not started
  - Business Directory:[ ] Not started
Phase 2 progress:   LOCKED
Phase 3 progress:   LOCKED
Known issues: none yet
Next priority: Audit existing EC2 files against this blueprint
GitHub last commit: [COMMIT HASH]
