<?php

// Placeholder migration.
// Purpose: Create invoice_external table.
// Fields: id, invoice_id (unique), provider, external_invoice_id, external_invoice_number,
// external_pdf_url, external_pdf_path, sync_status, last_synced_at, last_error, timestamps.
// Indexes: unique(invoice_id), index(provider), index(sync_status).
// FKs: invoice_id -> invoices.id.
// TODO: Replace with artisan-generated migration.
