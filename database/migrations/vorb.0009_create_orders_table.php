<?php

// Placeholder migration.
// Purpose: Create orders table.
// Fields: id, company_id, user_id, currency, status, subtotal_net_minor, discount_minor, company_discount_minor,
// tax_minor, total_gross_minor, tax_rule_applied, reverse_charge, tax_rate_percent_snapshot, coupon snapshots,
// expires_at, timestamps.
// Indexes: index(company_id), index(user_id), index(status), index(currency).
// FKs: company_id -> companies.id, user_id -> users.id.
// TODO: Replace with artisan-generated migration.
