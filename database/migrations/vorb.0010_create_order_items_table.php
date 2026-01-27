<?php

// Placeholder migration.
// Purpose: Create order_items table.
// Fields: id, order_id, product_id, name_snapshot, qty, unit_net_minor, tax_rate_percent, tax_minor, total_gross_minor, timestamps.
// Indexes: index(order_id), index(product_id).
// FKs: order_id -> orders.id, product_id -> products.id.
// TODO: Replace with artisan-generated migration.
