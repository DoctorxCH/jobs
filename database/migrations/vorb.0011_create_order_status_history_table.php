<?php

// Placeholder migration.
// Purpose: Create order_status_history table.
// Fields: id, order_id, from_status, to_status, changed_by_user_id, note, meta (json), changed_at.
// Indexes: index(order_id), index(to_status), index(changed_by_user_id).
// FKs: order_id -> orders.id, changed_by_user_id -> users.id.
// TODO: Replace with artisan-generated migration.
