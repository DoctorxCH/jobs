# Frontend Billing Strategie (Company-User Dashboard)

## 1. Scope & Nicht-Scope

### Scope (MVP)
- Sichtbare Bereiche im Company-Dashboard: Invoices, Payments, Orders, Products zum Kaufen.
- Read-only Listen + Detailseiten für Invoices, Payments, Orders.
- Kauf-Flow mit Order-Erstellung, Invoice-Erstellung, Zahlungsanweisung (Banküberweisung), PDF-Download.
- Statusdarstellung inkl. Overdue, Export-Status (read-only).
- Company-User sieht ausschließlich Daten der eigenen Company (company_id Scope).

### Nicht-Scope (Admin/Filament-only)
- Produktpflege, Preis- und Steuer-Setup.
- Manuelle Statusänderungen an Invoices/Payments/Orders.
- Externe Integrationen konfigurieren (z. B. SuperFaktura Credentials, Webhooks).
- Refunds/Stornos erstellen (nur Anzeige im Frontend).

### Später (Phase 2/3)
- Self-serve VAT-ID Validierung mit Live-Check.
- Wiederkehrende Bestellungen/Subscriptions.
- Automatisierte Zahlungseingangsprüfung (Bank-Import).
- Benachrichtigungen/Reminder bei Overdue.

## 2. Informationsarchitektur / Menüstruktur

### Menüstruktur
- Billing
  - Invoices
  - Payments
  - Orders
  - Products

### Rollen & Sichtbarkeit (Vorschlag)
- Owner: alle Billing-Bereiche sichtbar (voller Zugriff, read-only + Kauf).
- Member: Invoices + Orders sichtbar; Payments optional (read-only) zur Transparenz.
- Recruiter/Viewer: nur Invoices (read-only) oder gar kein Billing, abhängig von Rollenstrategie.
- Begründung: Owner benötigt volle Übersicht + Kauf. Members brauchen Budgettransparenz. Recruiter/Viewer typischerweise keine Finanzrolle.

### Zustände ausblenden
- Wenn keine Company vorhanden: Billing-Menü ausblenden, stattdessen Hinweis/CTA zur Company-Erstellung.
- Wenn Company deaktiviert/gesperrt: Billing komplett ausblenden, stattdessen Kontakt-Hinweis.

## 3. User Journeys (4 Flows)

### Flow A: Invoices ansehen
- Entry point: Billing → Invoices.
- Screens:
  - Liste: alle Invoices der Company.
  - Detail: einzelne Invoice inkl. Positionen und Status.
- Datenfelder (Liste): Invoice-Nummer, Datum, Fälligkeitsdatum, Status, Betrag (netto/brutto), Währung, Export-Status.
- Datenfelder (Detail): alle Listendaten + Rechnungsempfänger, Zahlungsreferenz, Bankdaten, Positionen (invoice_items), Steuerinformationen.
- Call-to-actions: PDF herunterladen, Referenz kopieren, Bankdaten kopieren.

### Flow B: Payments ansehen (read-only)
- Entry point: Billing → Payments.
- Screens:
  - Liste: alle Payments der Company.
  - Detail: einzelnes Payment.
- Datenfelder (Liste): Payment-Referenz, Datum, Betrag, Währung, Status, verknüpfte Invoice.
- Datenfelder (Detail): Betrag, Valutadatum, Statusverlauf (payment_status_history), Zahlungsreferenz, verknüpfte Invoice/Order.
- Call-to-actions: Referenz kopieren, verknüpfte Invoice öffnen.

### Flow C: Orders ansehen (read-only + Detail)
- Entry point: Billing → Orders.
- Screens:
  - Liste: alle Orders der Company.
  - Detail: einzelne Order inkl. Positionen.
- Datenfelder (Liste): Order-Nummer, Datum, Status, Betrag, Währung, verknüpfte Invoice.
- Datenfelder (Detail): Positionen (order_items), Statusverlauf (order_status_history), Invoice-Link, Payment-Status (wenn vorhanden).
- Call-to-actions: Invoice öffnen, Statusverlauf anzeigen.

### Flow D: Products kaufen (Order → Invoice → Zahlungsanweisung)
- Entry point: Billing → Products.
- Screens:
  - Produktliste: verfügbare Produkte/Packages.
  - Produktdetail: Preis, Steuerinfo, Gültigkeit/Leistungsumfang.
  - Checkout: Zusammenfassung, Company-Daten, Steuerdetails, AGB/Bestätigung.
  - Bestellbestätigung: Order erstellt, Invoice erstellt, Banküberweisungsanweisung, PDF-Link.
- Datenfelder (Checkout): Produkt, Preis, Steuer (reverse charge/standard), Company-Adresse, VAT-ID, Zahlungsreferenz, Bankdaten.
- Call-to-actions: Bestellung abschließen, Invoice PDF herunterladen, Referenz kopieren.

## 4. Zugriffskontrolle & Daten-Scope

- company_id ermitteln über eingeloggten User → Company-Relation (z. B. user->company_id oder user->companies->active).
- Jede Query im Frontend mit company_id Scope (Policies/Global Scope) absichern.
- Route-Model-Binding so konfigurieren, dass nur Records der Company gebunden werden.
- Bei nicht passender company_id: 404 statt 403 (verhindert IDOR-Erkennung).
- Direkter Zugriff auf Invoice/Order/Payment IDs anderer Companies muss immer 404 liefern.

## 5. Datenmodell-Nutzung (Mapping)

- Invoices Screen
  - invoices, invoice_items, invoice_external
- Payments Screen
  - payments, payment_status_history
- Orders Screen
  - orders, order_items, order_status_history
- Products Screen
  - products, product_prices
- Entitlements/Credits (nur wenn MVP nötig)
  - entitlements, credit_ledger, credit_reservations

## 6. Status- und Lifecycle-Regeln im Frontend

- Invoice-Status Darstellung
  - Draft: neutral, nicht zahlbar
  - Open/Sent: zahlbar, Bankdaten anzeigen
  - Paid: grün, read-only
  - Overdue: rot, Hinweis „überfällig“
  - Canceled/Credit Note: grau, read-only
- Frontend-Aktionen
  - Erlaubt: View/Download, Copy Reference
  - Nicht erlaubt: Mark paid, Status ändern, Export triggern
- Nach SuperFaktura-Export
  - invoice_external.sync_status nur anzeigen (read-only)
- Overdue-Regel
  - Overdue, wenn due_date < today und Status != Paid/Canceled
  - Zusätzlich Flag in Listenübersicht

## 7. Checkout-Logik (präzise Schritte)

- Pricing & Tax
  - Produktpreis aus product_prices anhand Währung und Gültigkeit.
  - Steuerregel bestimmen über Company-Land + VAT-ID:
    - SK: Standard VAT
    - EU mit VAT-ID: Reverse Charge
    - EU ohne VAT-ID: blockieren
    - Non-EU: out of scope, blockieren oder nur netto (Entscheidung in offenen Fragen)
- Coupon (optional)
  - Validierung im Checkout vor Order-Erstellung.
  - Redemption wird bei erfolgreicher Order-Erstellung persistiert.
- Order-Erstellung
  - Order + order_items erstellen, Status „created“.
- Invoice-Erstellung
  - Invoice vor Zahlung erstellen, mit Zahlungsreferenz.
  - invoice_items aus order_items.
- Bankdaten/Payment Instructions
  - Bankdaten aus Settings/Company-Config (zentral gepflegt).
  - Zahlungsreferenz + Fälligkeitsdatum anzeigen.
- PDF-Link
  - Strategische Entscheidung: intern generiert oder extern (SuperFaktura) – siehe offene Fragen.

## 8. Fehlerfälle & Edge Cases

- EU ohne VAT-ID → Checkout blockieren, Hinweis zur VAT-ID.
- Währung/Preis fehlt → Produkt nicht kaufbar, Support-Hinweis.
- Invoice export failed (invoice_external.sync_status failed) → Hinweis „Export fehlgeschlagen“, Support-Hinweis.
- Duplicate invoice/payment → Frontend zeigt Hinweis, keine Aktion.
- Storno/Credit Note → Anzeige in Invoices, read-only.
- Company ohne Berechtigung / User ohne Company → Billing-Menü ausblenden, 404 auf direkte Links.

## 9. Schrittplan (Implementationsreihenfolge)

### Phase 1 (MVP) – 6–10 Schritte
1. Menüstruktur + Routes im Frontend-Dashboard.
2. Company-Scoping Middleware/Policies + Route-Model-Binding 404.
3. Invoices: Liste + Detail + PDF-Download + Copy-Reference.
4. Orders: Liste + Detail + Statusverlauf.
5. Payments: Liste + Detail + Statusverlauf.
6. Products: Listing + Detail.
7. Checkout: Order/Invoice-Erstellung + Zahlungsanweisung.
8. Overdue-Logik + Statusdarstellung.

### Phase 2 (nice-to-have)
- VAT-ID Live-Check.
- Benachrichtigungen bei Overdue.
- Payment-Import (read-only Matches).

### Phase 3 (Skalierung/Automation)
- Automatische Statusupdates über Bank-Import.
- Wiederkehrende Orders/Subscriptions.
- Erweiterte Reporting-Views (CSV Export).

## 10. Offene Fragen an Martin (max 8)

1. Sollen Recruiter/Viewer überhaupt Billing sehen oder komplett versteckt?
2. Welche Bankdaten sollen angezeigt werden (IBAN/BIC/Bankname/Empfängername)?
3. Soll Non-EU-Kunde blockiert werden oder netto kaufen dürfen?
4. Wo soll das Invoice-PDF gehostet werden (intern vs. SuperFaktura-Link)?
5. Soll Payment-Status im Order-Detail prominenter dargestellt werden?
6. Wird eine Kreditnote als eigenes Dokument benötigt oder nur als Invoice-Status?
7. Sollen Coupons schon in MVP aktiviert sein oder erst Phase 2?
8. Gibt es feste SLA/Reminder-Regeln für Overdue, die wir berücksichtigen müssen?
