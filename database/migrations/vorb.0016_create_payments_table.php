<?php

// Placeholder migration.
// Purpose: Create payments table.
// Fields: id, invoice_id, method, status, amount_minor, currency, received_at, bank_reference, created_by_admin_id, timestamps.
// Indexes: index(invoice_id), index(status), index(method), index(received_at).
// FKs: invoice_id -> invoices.id, created_by_admin_id -> users.id.
// TODO: Replace with artisan-generated migration.
