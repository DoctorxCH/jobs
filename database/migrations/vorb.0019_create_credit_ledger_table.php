<?php

// Placeholder migration.
// Purpose: Create credit_ledger table.
// Fields: id, company_id, change, reason, reference_type, reference_id, created_by_admin_id, created_at.
// Indexes: index(company_id), index(reason), index(reference_type), index(reference_id).
// FKs: company_id -> companies.id, created_by_admin_id -> users.id.
// TODO: Replace with artisan-generated migration.
