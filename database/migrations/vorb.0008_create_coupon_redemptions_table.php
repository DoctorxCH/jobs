<?php

// Placeholder migration.
// Purpose: Create coupon_redemptions table.
// Fields: id, coupon_id, order_id, invoice_id, company_id, user_id, discount_minor, currency, redeemed_at, timestamps.
// Indexes: index(coupon_id), index(order_id), index(invoice_id), index(company_id), index(user_id).
// FKs: coupon_id -> coupons.id, order_id -> orders.id, invoice_id -> invoices.id, company_id -> companies.id, user_id -> users.id.
// TODO: Replace with artisan-generated migration.
