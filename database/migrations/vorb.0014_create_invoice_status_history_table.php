<?php

// Placeholder migration.
// Purpose: Create invoice_status_history table.
// Fields: id, invoice_id, from_status, to_status, changed_by_user_id, note, meta (json), changed_at.
// Indexes: index(invoice_id), index(to_status), index(changed_by_user_id).
// FKs: invoice_id -> invoices.id, changed_by_user_id -> users.id.
// TODO: Replace with artisan-generated migration.
