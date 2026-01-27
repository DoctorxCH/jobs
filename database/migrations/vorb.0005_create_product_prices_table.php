<?php

// Placeholder migration.
// Purpose: Create product_prices table.
// Fields: id, product_id, currency, unit_net_amount_minor, tax_class_id, valid_from, valid_to, active, timestamps.
// Indexes: index(product_id), index(tax_class_id), index(currency), index(active), index(valid_from).
// FKs: product_id -> products.id, tax_class_id -> tax_classes.id.
// TODO: Replace with artisan-generated migration.
