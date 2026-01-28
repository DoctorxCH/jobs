# Kurka Jobportal – Funktions- und Seitenübersicht (für ChatGPT-Strategie)

> Ziel: Diese Datei dient als kompakte „System-Notiz“ für ChatGPT, um schnell zu verstehen,
> wie die Website strukturiert ist, welche Funktionen existieren und wo die relevanten Dateien liegen.

## 1) Überblick

- **Frontend**: klassische Blade-Seiten (Home, Jobs-Demo, Login/Register, Dashboard mit Billing/Profil/Security).
- **Backend/Admin**: Filament Admin (über verstecktes Login-Gate erreichbar).
- **API**: kleine JSON-API für Unternehmens-Lookup (IČO-Suche).

## 2) Funktionen & Wie sie funktionieren

### 2.1 Admin-Gate (Filament Login-Proxy)
- **Route**: `/365gate` (GET/POST) und `/365gate/logout`.
- **Ablauf**:
  - Wenn bereits authentifiziert, Weiterleitung nach `/admin`.
  - Login validiert E-Mail/Passwort, regeneriert Session und prüft optional `platform.*`-Rollen.
  - Erfolgreiche Auth setzt `admin_gate` in der Session und leitet ins Filament-Adminpanel.
  - Logout invalidiert Session und leitet zurück zum Gate.

### 2.2 Frontend-Authentifizierung (Login/Registrierung)
- **Login**:
  - Validierung der Credentials, Session-Refresh, Redirect ins Dashboard.
- **Registrierung**:
  - Erstellt User + Company, erzeugt eindeutigen Company-Slug und weist Rolle `company.owner` zu.
  - Verknüpft User mit Company (`companies()` / Pivot) und loggt sofort ein.
- **Logout**:
  - Logout + Session-Reset, Redirect zur Login-Seite.

### 2.3 Dashboard
- **Startseite**:
  - Zeigt eingeloggten User und (falls vorhanden) seine Company.
- **Profil**:
  - Bearbeitet User-Name und Company-Daten.
  - Besitzer-Logik: Nicht-Owner dürfen Identitätsfelder (z. B. IČO) nicht ändern.
  - Erstellt Company nur, wenn der User noch keiner Company zugeordnet ist.
- **Security (Passwort ändern)**:
  - Zentrale Passwort-Policy (min 10 Zeichen, Groß-/Kleinbuchstaben, Zahl, Symbol).
  - Validiert aktuelles Passwort, speichert neues Hash-Passwort.

### 2.4 Billing (Produkte, Bestellungen, Rechnungen, Zahlungen)
- **Company-Kontext**:
  - Alle Billing-Views benötigen eine verknüpfte Company (sonst leere Info-Seite).
- **Produkte**:
  - Listet aktive Produkte, ermittelt Steuerregeln, berechnet Brutto/Steuern.
  - Detail/Checkout zeigen Preis, Steuerregel und Mengenberechnung.
- **Checkout/Bestellung**:
  - Erzeugt Order (Status `submitted`) und OrderItems.
  - Erstellt Invoice + InvoiceItems + Status-History-Einträge.
- **Orders**:
  - Listet und zeigt Orders mit Items und Status-History.
- **Invoices**:
  - Listet und zeigt Invoices, inkl. PDF-Download (Storage oder externer Link).
- **Payments**:
  - Listet Zahlungen und zeigt Zahlungsdetails inklusive Status-History.

### 2.5 Team-Einladungen (Company Invitations)
- **Einladung senden**:
  - Nur Company-Owner/Manager dürfen einladen.
  - Erstellt Invite mit Token + Ablaufdatum und sendet E-Mail.
- **Einladung annehmen**:
  - Prüft Token + Ablaufdatum.
  - Erstellt User oder aktualisiert bestehendes Passwort.
  - Legt Company-Membership an (Pivot) und setzt passende Rolle `company.*`.
  - Loggt den User ein und leitet ins Dashboard.

### 2.6 Cookie-Consent
- Speichert Consent-Level (`essential`/`stats`) als Cookie inkl. Version/Datum.
- Redirect zurück zur ursprünglichen Seite.

### 2.7 Jobs-Demo-Seiten
- `/jobs` und `/jobs/{slug}` sind aktuell Demo-/Placeholder-Seiten mit statischen Arrays.

### 2.8 API – Company Lookup
- JSON-Endpunkt `/api/company-lookup`.
- Prüft IČO-Format, verhindert doppelte IČO-Registrierungen, ruft externen SK-RPO Service.
- Cacht Ergebnisse für 7 Tage.

## 3) Seitenübersicht (Routen → Views)

### Öffentliche Seiten
- `/` → `resources/views/home.blade.php`
- `/login` (GET/POST) → `resources/views/auth/login.blade.php`
- `/register` (GET/POST) → `resources/views/auth/register.blade.php`
- `/jobs` → `resources/views/jobs/index.blade.php` (Demo)
- `/jobs/{slug}` → `resources/views/jobs/show.blade.php` (Demo)
- `/company/dashboard` → `resources/views/company/dashboard.blade.php`

### Authentifizierter Bereich
- `/dashboard` → `resources/views/dashboard/index.blade.php`
- `/dashboard/profile` → `resources/views/dashboard/profile.blade.php`
- `/dashboard/security` → `resources/views/dashboard/security.blade.php`
- `/dashboard/team/invite` → `resources/views/dashboard/team.blade.php`

### Billing (Dashboard)
- `/dashboard/billing/products` → `resources/views/dashboard/billing/products/index.blade.php`
- `/dashboard/billing/products/{product}` → `resources/views/dashboard/billing/products/show.blade.php`
- `/dashboard/billing/products/{product}/checkout` → `resources/views/dashboard/billing/products/checkout.blade.php`
- `/dashboard/billing/orders` → `resources/views/dashboard/billing/orders/index.blade.php`
- `/dashboard/billing/orders/{order}` → `resources/views/dashboard/billing/orders/show.blade.php`
- `/dashboard/billing/invoices` → `resources/views/dashboard/billing/invoices/index.blade.php`
- `/dashboard/billing/invoices/{invoice}` → `resources/views/dashboard/billing/invoices/show.blade.php`
- `/dashboard/billing/invoices/{invoice}/download` → (Download / Redirect)
- `/dashboard/billing/payments` → `resources/views/dashboard/billing/payments/index.blade.php`
- `/dashboard/billing/payments/{payment}` → `resources/views/dashboard/billing/payments/show.blade.php`
- Fallback ohne Company: `resources/views/dashboard/billing/empty.blade.php`

### Admin (Filament)
- `/365gate` → `resources/views/auth/admin-gate.blade.php` (Login-Gate)
- `/admin` → Filament Panel (Ressourcen siehe Abschnitt „Wichtige Files“)
- `/admin/login` → HTTP 410 (absichtlich deaktiviert)

### Einladungen
- `/company-invite/{token}` → `resources/views/auth/company-invite-accept.blade.php`

## 4) Wichtige Dateien (Struktur/Quellen)

### Routing
- `routes/web.php` – Frontend + Admin Gate + Demo Jobs + Team Invite.
- `routes/api.php` – Company Lookup API.

### Controller (Frontend + Admin Gate)
- `app/Http/Controllers/AdminGateController.php`
- `app/Http/Controllers/Frontend/AuthController.php`
- `app/Http/Controllers/Frontend/DashboardController.php`
- `app/Http/Controllers/Frontend/ProfileController.php`
- `app/Http/Controllers/Frontend/SecurityController.php`
- `app/Http/Controllers/Frontend/Billing/*`
- `app/Http/Controllers/CompanyInvitationController.php`
- `app/Http/Controllers/CookieConsentController.php`
- `app/Http/Controllers/Api/CompanyLookupController.php`

### Middleware
- `app/Http/Middleware/FrontendAuthenticate.php` – schützt Dashboard und Billing.

### Services (Business-Logik)
- `app/Services/Billing/OrderService.php` – Order/Invoice Erzeugung.
- `app/Services/Billing/TaxRuleService.php` – Steuerregeln.
- `app/Services/CompanyLookup/*` – RPO Lookup & Cache.

### Views / Blade Templates
- `resources/views/home.blade.php`
- `resources/views/auth/*`
- `resources/views/dashboard/*` und `resources/views/dashboard/billing/*`
- `resources/views/jobs/*`
- `resources/views/components/*` (Layouts + Header/Footer + Dashboard Layout)
- `resources/views/filament/auth/login-custom.blade.php`

### Filament Admin (Ressourcen)
- `app/Filament/Resources/*` – z. B. Users, Companies, Billing (Orders, Invoices, Products, Payments, Coupons, Settings, Tax Rates), Company Invitations.
- `app/Filament/Pages/Auth/Login.php` – Filament Login-Page (wird über `/365gate` genutzt).

## 5) Migrationen (Datenbankstruktur)

- `0001_01_01_000000_create_users_table.php`
- `0001_01_01_000001_create_cache_table.php`
- `0001_01_01_000002_create_jobs_table.php`
- `2026_01_19_154553_create_permission_tables.php`
- `2026_01_19_154600_create_resource_permissions_table.php`
- `2026_01_19_224500_create_company_categories_table.php`
- `2026_01_19_224600_create_companies_table.php`
- `2026_01_19_224650_add_seats_fields_to_companies_table.php`
- `2026_01_19_224651_add_company_fields_to_users_table.php`
- `2026_01_19_224652_create_company_invitations_table.php`
- `2026_01_19_224652_create_company_user_table.php`
- `2026_01_21_003115_alter_company_invitations_constraints.php`
- `2026_01_21_004207_alter_company_user_unique_user_id.php`
- `2026_01_24_111507_create_cookie_settings_table.php`
- `2026_01_24_213226_top_partners_setup.php`
- `2026_01_27_000001_create_tax_classes_table.php`
- `2026_01_27_000002_create_tax_rates_table.php`
- `2026_01_27_000003_create_settings_table.php`
- `2026_01_27_000004_create_products_table.php`
- `2026_01_27_000005_create_product_prices_table.php`
- `2026_01_27_000006_create_coupons_table.php`
- `2026_01_27_000007_create_coupon_scopes_table.php`
- `2026_01_27_000008_create_orders_table.php`
- `2026_01_27_000009_create_order_items_table.php`
- `2026_01_27_000010_create_order_status_history_table.php`
- `2026_01_27_000011_create_invoices_table.php`
- `2026_01_27_000012_create_invoice_items_table.php`
- `2026_01_27_000013_create_invoice_status_history_table.php`
- `2026_01_27_000014_create_invoice_external_table.php`
- `2026_01_27_000015_create_payments_table.php`
- `2026_01_27_000016_create_payment_status_history_table.php`
- `2026_01_27_000017_create_entitlements_table.php`
- `2026_01_27_000018_create_credit_ledger_table.php`
- `2026_01_27_000019_create_credit_reservations_table.php`
- `2026_01_27_000020_create_entitlement_history_table.php`
- `2026_01_27_000021_create_coupon_redemptions_table.php`

## 6) Codex-Implementierungs-Notizen (verbindlich)

### 6.1 Credits-Mechanik (bestehender Stack verwenden)
- **Wichtig**: Es existiert bereits ein vollständiger Credits-/Entitlement-Stack. **Keine neuen Tabellen** anlegen.
- **1 Credit = 1 Tag** (beim Job-Posting).
- **Buchung/Logik**:
  - **Purchase → Credits**: `app/Services/Billing/FulfillmentService.php` erzeugt `Entitlement` + schreibt in `CreditLedger` (`reason: purchase`). 
  - **Job-Posting → Verbrauch**: `app/Services/Billing/CreditService.php` reserviert (`CreditReservation`) und bucht Verbrauch in `CreditLedger` (`change: -1`, `reason: job_post`).
- **Zentrale Models**: 
  - `app/Models/Billing/Entitlement.php`
  - `app/Models/Billing/EntitlementHistory.php`
  - `app/Models/Billing/CreditLedger.php`
  - `app/Models/Billing/CreditReservation.php`

### 6.2 Jobs DB (bestehende Tabelle)
- **Jobs-Tabelle existiert bereits**: `database/migrations/0001_01_01_000002_create_jobs_table.php`.
- **Regel**: Nur **ALTER/ADD**-Migrationen erstellen, niemals `create_jobs_table` neu.

### 6.3 TeamMember / HR-Kontakt (Quelle)
- **TeamMember-Quelle**: `CompanyUser` (Pivot Model).
- Pfad: `app/Models/CompanyUser.php`.
- `User::companies()` nutzt das Pivot-Modell; `company_user` hat u. a. `role`, `status`, `invited_at`, `accepted_at`.

### 6.4 Rollen & Permissions (Job-Management)
- **Verbindliche Matrix** basiert auf `User::canCompanyManageJobs()`:
  - **Dürfen Jobs erstellen/posten/editieren**: `owner`, `member`, `recruiter`.
  - **Nicht erlaubt**: `viewer`.
- Quelle: `app/Models/User.php`.

### 6.5 SKNICE (Import/Mapping)
- **Aktuell kein SKNICE-Import im Codebase** (keine Tabellen/Seeder/Services vorhanden).
- **Vorgabe**: Zunächst nur CRUD in Filament + FK in `jobs` (falls benötigt). Import/Mapping **später** hinzufügen.
