<?php

// Placeholder migration.
// Purpose: Create invoices table.
// Fields: id, company_id, order_id, status, currency, issued_at, due_at, customer snapshots,
// tax snapshots, totals snapshots, payment_reference (unique), pdf_path, pdf_url, cancelled_invoice_id, created_by_admin_id, timestamps.
// Indexes: unique(payment_reference), index(company_id), index(order_id), index(status), index(issued_at).
// FKs: company_id -> companies.id, order_id -> orders.id, cancelled_invoice_id -> invoices.id, created_by_admin_id -> users.id.
// TODO: Replace with artisan-generated migration.
