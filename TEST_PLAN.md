# Test Plan — Job Portal (Laravel + Filament)

## 1. Discovery / Inventory
1.1 **Routes inventory**
- **Given** the app routes are loaded
- **When** checking web routes
- **Then** routes for `/365gate`, `/admin/login`, `/dashboard/*`, `/jobs/*` exist and map to controllers/views

1.2 **Filament resources inventory**
- **Given** the admin panel is enabled
- **When** listing Filament resources
- **Then** resources for Jobs, Companies, Users, Company Invitations, Lookups are available

## 2. Smoke Tests
2.1 **Homepage load**
- **Given** a guest user
- **When** visiting `/`
- **Then** the page returns HTTP 200 and renders without 500 errors

2.2 **Jobs listing load**
- **Given** a guest user
- **When** visiting `/jobs`
- **Then** HTTP 200 and job cards render

2.3 **Dashboard load (authenticated)**
- **Given** a logged-in company owner
- **When** visiting `/dashboard`
- **Then** HTTP 200 and company summary renders

## 3. Auth & Security
3.1 **Admin redirect**
- **Given** a guest user
- **When** visiting `/admin`
- **Then** redirect to `/365gate`

3.2 **Admin login disabled**
- **Given** a guest user
- **When** visiting `/admin/login`
- **Then** HTTP 410 (gone)

3.3 **Session regeneration**
- **Given** a guest user
- **When** logging in via `/365gate`
- **Then** session ID is regenerated

3.4 **Rate limiting**
- **Given** multiple failed login attempts
- **When** posting invalid credentials to `/365gate`
- **Then** login attempts are throttled (expected enhancement)

## 4. Company/Profile Flow
4.1 **Owner can edit company**
- **Given** a company owner
- **When** updating company profile fields
- **Then** changes persist and success message is shown

4.2 **Member cannot edit company**
- **Given** a company member
- **When** posting updates to company fields
- **Then** company data remains unchanged; only user name can update

4.3 **IČO validation**
- **Given** invalid IČO format
- **When** submitting profile with non-8-digit IČO
- **Then** validation error is displayed

## 5. Jobs (Core)
5.1 **Create job**
- **Given** a company owner/recruiter
- **When** submitting job create form with valid data
- **Then** job is created, relations (benefits, skills, languages) persist

5.2 **Edit job**
- **Given** an existing job
- **When** editing the job and submitting changes
- **Then** pivot relations update without duplicates

5.3 **Viewer cannot manage jobs**
- **Given** a viewer member
- **When** visiting `/dashboard/jobs`
- **Then** HTTP 403

5.4 **Publish job (credits)**
- **Given** available credits
- **When** posting `/dashboard/jobs/{job}/post` with days
- **Then** job status becomes published and credits are deducted

5.5 **Publish job without credits**
- **Given** insufficient credits
- **When** posting `/dashboard/jobs/{job}/post`
- **Then** validation error is returned

## 6. Job Languages
6.1 **Add multiple languages**
- **Given** a job edit form
- **When** adding multiple language rows and saving
- **Then** job_languages records persist with language_code + level

6.2 **Remove languages**
- **Given** an existing job with languages
- **When** removing rows and saving
- **Then** removed rows are deleted

## 7. Team & Invitations
7.1 **Send invitation**
- **Given** company owner
- **When** sending invite
- **Then** invitation is created and email is sent

7.2 **Invite acceptance**
- **Given** a valid token
- **When** user accepts and sets password
- **Then** membership is created, roles are assigned, and user is logged in

7.3 **Expired invitation**
- **Given** expired token
- **When** visiting invite URL
- **Then** HTTP 410 with expiration message

## 8. Billing (Smoke)
8.1 **Products list**
- **Given** authenticated user
- **When** visiting `/dashboard/billing/products`
- **Then** HTTP 200 and products render

8.2 **Invoices list**
- **Given** authenticated user
- **When** visiting `/dashboard/billing/invoices`
- **Then** HTTP 200 and invoice list renders

## 9. Cookies/DSGVO
9.1 **Consent persistence**
- **Given** user accepts cookies
- **When** reloading the page
- **Then** consent is persisted (cookie or storage)

9.2 **No consent = no tracking**
- **Given** user declines or has no consent
- **When** visiting pages
- **Then** no tracking requests are fired

## 10. Performance & Security Headers
10.1 **N+1 checks**
- **Given** jobs index with relations
- **When** profiling DB queries
- **Then** no N+1 patterns on relations

10.2 **Security headers**
- **Given** any public page
- **When** inspecting response headers
- **Then** X-Frame-Options, X-Content-Type-Options, and other headers are present

