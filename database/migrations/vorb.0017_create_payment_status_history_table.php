<?php

// Placeholder migration.
// Purpose: Create payment_status_history table.
// Fields: id, payment_id, from_status, to_status, changed_by_user_id, note, meta (json), changed_at.
// Indexes: index(payment_id), index(to_status), index(changed_by_user_id).
// FKs: payment_id -> payments.id, changed_by_user_id -> users.id.
// TODO: Replace with artisan-generated migration.
