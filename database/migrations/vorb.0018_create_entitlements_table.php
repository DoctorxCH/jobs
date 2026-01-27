<?php

// Placeholder migration.
// Purpose: Create entitlements table.
// Fields: id, company_id, type, quantity_total, quantity_remaining, starts_at, ends_at, source_invoice_item_id, timestamps.
// Indexes: index(company_id), index(type), index(source_invoice_item_id).
// FKs: company_id -> companies.id, source_invoice_item_id -> invoice_items.id.
// TODO: Replace with artisan-generated migration.
