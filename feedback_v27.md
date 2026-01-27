# Feedback v27 – Konsistenzprüfung (gesamtes Git)

## Vorgehen / Scope
- Alle getrackten Dateien laut `git ls-files` wurden in die Prüfung einbezogen.
- Fokus der Konsistenzprüfung: 
  - Abgleich Models ↔ Migrationen ↔ Filament Resources (Filament 3.3)
  - Routen/Controller/View-Alignment
  - Repo-Hygiene (unerwartete Dateien, generierte Assets, Beispiel-Configs)

## Findings (konkret, dateibezogen)

### 1) Model ↔ Migration Inkonsistenz: CompanyCategory
- **Migration:** `company_categories` hat `is_active` (boolean) und keine `active` oder `sort` Spalten.
- **Model:** `app/Models/CompanyCategory.php` castet `active` (bool) und `sort` (int) – diese Spalten existieren nicht.
- **Resource-Form:** nutzt korrekt `is_active` (passend zur Migration).
- **Zusätzlich:** `description` existiert in der Migration, ist aber im Resource-Form nicht abbildbar.

**Auswirkung:** Casts greifen ins Leere; `is_active` wird nicht gecastet. UI kann `description` nicht pflegen.

---

### 2) CompanyUser: Unique-Constraint vs. Modellbeziehung
- **Migration:** Unique auf `user_id` (per `2026_01_21_004207_alter_company_user_unique_user_id.php`).
- **Model:** `User::companies()` (belongsToMany) impliziert Mehrfachzuordnung zu Companies.

**Auswirkung:** Aktuelles DB-Constraint erlaubt nur eine Company pro User; Modell lässt theoretisch mehrere zu. Fachlich klären.

---

### 3) Company: Seats-Logik
- **Model:** `Company::hasFreeSeats()` zählt `members()->count() < seats_purchased`.
- **Migration:** `seats_locked` existiert, wird in der Logik nicht berücksichtigt.

**Auswirkung:** Seats können als verfügbar zählen, obwohl `seats_locked` reserviert sind.

---

### 4) Filament Resource Properties
- **CompanyResource** definiert `model`, `navigationLabel`, `navigationGroup`, `navigationIcon`, aber **keine** `modelLabel`/`pluralModelLabel` (im Gegensatz zu anderen Resources).

**Auswirkung:** UI-Benennung inkonsistent zwischen Resources (und Regel 4.1 nur teilweise erfüllt).

---

### 5) Route/View Demo-Daten (kein Persistenz-Abgleich)
- `routes/web.php` nutzt statische Arrays für Jobs/Company Dashboard.
- Views (`resources/views/jobs/*.blade.php`, `resources/views/company/dashboard.blade.php`) sind konsistent mit den statischen Payloads.

**Hinweis:** Falls echte Jobs/Companies geplant sind, müssen Routen/Views auf Models/DB umgestellt werden.

---

### 6) Repo-Hygiene / unerwartete Datei
- Datei **`Color::Amber,`** im Root ist leer und inhaltlich nicht zuordenbar.

**Auswirkung:** Wahrscheinlich Artefakt / versehentlich committet.

---

### 7) Assets & Build-Pipeline
- `public/css/filament/*` und `public/js/filament/*` sind im Repo enthalten (kompilierte Assets).
- `resources/css/app.css` / `resources/js/*` und `vite.config.js` sind vorhanden.

**Hinweis:** Falls die Assets generiert werden, sollte geprüft werden, ob `public/`-Builds bewusst versioniert werden oder via Build-Process entstehen.

---

## Zusammenfassung (kritischste Punkte)
1. **CompanyCategory-Model castet nicht existierende Spalten**, und `description` fehlt im UI.
2. **CompanyUser DB-Constraint vs. Model-Beziehung** (Unique user_id vs belongsToMany) ist fachlich inkonsistent.
3. **Seats-Logik** ignoriert `seats_locked`.
4. **Unerwartete Root-Datei** `Color::Amber,` (leer).

## Empfehlung (nächste Schritte)
1. CompanyCategory-Model-Casts auf `is_active` korrigieren und `description` im Form ergänzen.
2. Fachliche Entscheidung: 1 Company pro User oder Multi-Company? Danach Migration + Model anpassen.
3. Seats-Logik um `seats_locked` erweitern.
4. Unerwartete Root-Datei entfernen oder erklären.
