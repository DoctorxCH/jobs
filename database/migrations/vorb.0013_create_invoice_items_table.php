<?php

// Placeholder migration.
// Purpose: Create invoice_items table.
// Fields: id, invoice_id, product_id, name_snapshot, qty, unit_net_minor, tax_rate_percent, tax_minor, total_gross_minor, timestamps.
// Indexes: index(invoice_id), index(product_id).
// FKs: invoice_id -> invoices.id, product_id -> products.id.
// TODO: Replace with artisan-generated migration.
