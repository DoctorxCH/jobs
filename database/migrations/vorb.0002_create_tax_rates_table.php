<?php

// Placeholder migration.
// Purpose: Create tax_rates table.
// Fields: id, tax_class_id, country_code, rate_percent, active, valid_from, valid_to, timestamps.
// Indexes: index(tax_class_id), index(country_code), index(active).
// FKs: tax_class_id -> tax_classes.id.
// TODO: Replace with artisan-generated migration.
