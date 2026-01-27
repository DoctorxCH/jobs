<?php

// Placeholder migration.
// Purpose: Create entitlement_history table.
// Fields: id, entitlement_id, change, reason, reference_type, reference_id, changed_by_user_id, meta (json), created_at.
// Indexes: index(entitlement_id), index(reason), index(reference_type), index(reference_id), index(changed_by_user_id).
// FKs: entitlement_id -> entitlements.id, changed_by_user_id -> users.id.
// TODO: Replace with artisan-generated migration.
