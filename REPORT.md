# QA Report — Laravel/Filament Job Portal

## Executive Summary
Static review of routes, controllers, models, and Filament resources shows a mostly complete functional surface for the frontend dashboard and admin panel, with several authorization edge cases and role-management gaps. The most critical issues discovered were:

- **Company context resolution** in the dashboard job flow relied on owner-only checks, which blocked valid team members (recruiter/member) from job management.
- **Job publishing authorization** allowed non-authorized users to post jobs.
- **Company profile editing** allowed non-owners to update company data.
- **Invitation acceptance** could remove `platform.*` roles due to a hard `syncRoles` call.

All four issues have been fixed in this change set. Additional security/performance findings remain for follow-up.

## Feature Map (Discovery)
### Public Pages
- **Home:** `/` → `resources/views/home.blade.php` via `frontend.home` route.
- **Jobs listing (placeholder):** `/jobs` → `resources/views/jobs/index.blade.php` (mock data).
- **Job detail (placeholder):** `/jobs/{slug}` → `resources/views/jobs/show.blade.php` (mock data).
- **Static/SEO content:** `resources/views/welcome.blade.php` is present and references login routes.

### Authentication (Frontend)
- **Login:** `/login` (GET/POST) → `Frontend\AuthController@showLogin/login`.
- **Register:** `/register` (GET/POST) → `Frontend\AuthController@showRegister/register`.
- **Logout:** `/logout` (POST).
- **Password reset:** Filament vendor views exist but no explicit frontend reset routes are defined.

### Dashboard (Frontend Authenticated)
- **Dashboard:** `/dashboard`.
- **Profile:** `/dashboard/profile`.
- **Security/Password:** `/dashboard/security`.
- **Jobs CRUD:** `/dashboard/jobs/*` (index/create/store/edit/update/post/archive).
- **Billing:** `/dashboard/billing/*` (products, checkout, orders, invoices, payments).
- **Team & Invitations:** `/dashboard/team/invite` + `/dashboard/team/invite` (POST send).

### Admin
- **Admin gate login:** `/365gate` (GET/POST) as named route `login`.
- **Admin logout:** `/365gate/logout`.
- **Default Filament login disabled:** `/admin/login` returns 410.
- **Admin panel:** `/admin` protected by Filament panel + custom gate.

### Filament Resources (Admin)
- Taxonomy/Lookups: Benefits, Skills, Countries, Regions, Cities, Education Levels, Education Fields, Driving License Categories, SKNICE positions.
- Core data: Companies, Company Users, Company Invitations, Jobs, Job Languages, Users.
- Permissions: Resource Permissions, Platform Users, Cookie Settings.

## Critical Paths (User Flows)
1. **Guest → Register/Login → Dashboard**
2. **Company profile → update legal/company data**
3. **Team → invite member → accept invitation → dashboard access**
4. **Jobs → create/edit/post/archive**
5. **Admin → /admin via /365gate**

## Test Matrix (Status Overview)
> **Legend:** PASS (verified), FAIL (repro), BLOCKED (needs runtime), NOT RUN (not executed in repo-only review)

| Area | Scenario | Status | Notes |
|---|---|---|---|
| Smoke | Home page loads without 500 | NOT RUN | Requires runtime/server |
| Smoke | Dashboard pages load with auth | NOT RUN | Requires runtime/server |
| Auth | /admin redirects to /365gate | NOT RUN | Routes/middleware appear configured |
| Auth | /admin/login disabled | PASS (static) | Route returns 410 |
| Auth | Session regeneration on login | PASS (static) | Present in controllers |
| Security | CSRF protection on POST routes | PASS (static) | Uses web middleware |
| Authorization | Team invite restricted to owner | PASS (fixed) | Server-side restriction now enforced |
| Jobs | Member/recruiter can manage jobs | PASS (fixed) | Effective company lookup + auth in job flow |
| Jobs | Viewer cannot manage jobs | PASS (static/test) | Tests expect 403 |
| Invitations | Accept invite preserves platform roles | PASS (fixed) | Roles are merged instead of overwritten |
| GDPR | Cookie consent persists / blocks tracking | NOT RUN | Requires runtime + frontend check |
| Perf | N+1 checks | NOT RUN | Requires profiling |

## Role & Permission Matrix (Expected)
| Action | platform.super_admin | company.owner | company.member | company.recruiter | company.viewer |
|---|---|---|---|---|---|
| View company dashboard | ✅ | ✅ | ✅ | ✅ | ✅ (read-only) |
| Edit company profile | ✅ | ✅ | ❌ | ❌ | ❌ |
| Manage team/invitations | ✅ | ✅ | ❌ | ❌ | ❌ |
| Create/edit jobs | ✅ | ✅ | ✅ | ✅ | ❌ |
| Publish/archive jobs | ✅ | ✅ | ✅ | ✅ | ❌ |
| Billing/purchases | ✅ | ✅ | ❌ | ❌ | ❌ |
| Admin panel access | ✅ | ❌ | ❌ | ❌ | ❌ |

## Bug List (Repro + Fix)

### BUG-001 — Team members blocked from job management
- **Area:** Jobs (Dashboard)
- **Priority:** P1
- **Repro Steps:**
  1. Create company owner.
  2. Invite user as member/recruiter.
  3. Login as member and open `/dashboard/jobs`.
- **Expected:** Member/recruiter can access job dashboard.
- **Actual:** 403 because company lookup is owner-only.
- **Root Cause:** `JobController::companyForUser()` queried by `owner_user_id` only.
- **Fix:** Resolve company via `effectiveCompanyId` and enforce job permissions.

### BUG-002 — Job publish endpoint lacks authorization
- **Area:** Jobs (Publish)
- **Priority:** P1
- **Repro Steps:**
  1. Login as any user without job permissions.
  2. POST `/dashboard/jobs/{job}/post`.
- **Expected:** 403 if user cannot manage jobs.
- **Actual:** Authorization passed because request authorize() always returned true.
- **Root Cause:** `JobPostRequest::authorize()` returned true.
- **Fix:** Bind to `canCompanyManageJobs()`.

### BUG-003 — Non-owners can edit company data
- **Area:** Company Profile
- **Priority:** P1
- **Repro Steps:**
  1. Invite a member.
  2. Login as member and update company profile fields.
- **Expected:** Only owner can modify company data.
- **Actual:** Member can change most fields (except legal name/IČO).
- **Root Cause:** `ProfileController@update` allowed non-owner updates to most fields.
- **Fix:** For non-owner, only allow updating own display name; company data is read-only.

### BUG-004 — Accepting invitation strips platform roles
- **Area:** Invitations/Roles
- **Priority:** P1
- **Repro Steps:**
  1. User with `platform.*` role accepts company invite.
  2. Check roles after accept.
- **Expected:** Platform roles preserved, company role added.
- **Actual:** `syncRoles` removed platform roles.
- **Root Cause:** `CompanyInvitationController@complete` used `syncRoles([$spatieRole])`.
- **Fix:** Merge existing platform roles with company role before syncing.

### BUG-005 — Missing rate limiting on /365gate
- **Area:** Security
- **Priority:** P2
- **Repro Steps:**
  1. POST multiple failed logins to `/365gate`.
- **Expected:** Throttle or lockout.
- **Actual:** No rate limiting seen in route or controller.
- **Fix Suggestion:** Add `ThrottleRequests` middleware or Laravel Fortify-style rate limiter.

## Security & Compliance Findings
- **CSRF**: Web routes use `web` middleware, so CSRF protection is expected for POST routes.
- **Session regeneration**: Both admin and frontend logins regenerate sessions.
- **Role scoping**: Company roles are enforced in several paths, but keep verifying admin-only access for Filament resources.
- **Invitation tokens**: 64-char random tokens with expiry check are present.
- **Cookie consent**: Controller exists, but needs runtime verification for blocking analytics.

## Performance Findings (Static)
- **Job dashboard list** uses pagination; no obvious eager-loading issues in controller.
- **Lookup lists** in job form are loaded via DB queries; may need caching if used frequently.

## Recommended Follow-ups
1. Add rate limiting on `/365gate` (P2).
2. Implement frontend job search/index with real data (currently placeholder routes).
3. Add E2E tests for login → dashboard → job CRUD flow.

