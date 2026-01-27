<?php

namespace App\Services\Billing;

use App\Models\Billing\EntitlementHistory;
use App\Models\Billing\OrderStatusHistory;
use App\Models\Billing\PaymentStatusHistory;
use App\Models\User;

class HistoryService
{
    public function recordOrderStatus(int $orderId, ?string $fromStatus, string $toStatus, ?User $actor = null, ?string $note = null, array $meta = []): OrderStatusHistory
    {
        return OrderStatusHistory::query()->create([
            'order_id' => $orderId,
            'from_status' => $fromStatus,
            'to_status' => $toStatus,
            'changed_by_user_id' => $actor?->id,
            'note' => $note,
            'meta' => $meta,
            'changed_at' => now(),
        ]);
    }

    public function recordPaymentStatus(int $paymentId, ?string $fromStatus, string $toStatus, ?User $actor = null, ?string $note = null, array $meta = []): PaymentStatusHistory
    {
        return PaymentStatusHistory::query()->create([
            'payment_id' => $paymentId,
            'from_status' => $fromStatus,
            'to_status' => $toStatus,
            'changed_by_user_id' => $actor?->id,
            'note' => $note,
            'meta' => $meta,
            'changed_at' => now(),
        ]);
    }

    public function recordEntitlementHistory(int $entitlementId, int $change, string $reason, string $referenceType, int $referenceId, ?User $actor = null, array $meta = []): EntitlementHistory
    {
        return EntitlementHistory::query()->create([
            'entitlement_id' => $entitlementId,
            'change' => $change,
            'reason' => $reason,
            'reference_type' => $referenceType,
            'reference_id' => $referenceId,
            'changed_by_user_id' => $actor?->id,
            'meta' => $meta,
            'created_at' => now(),
        ]);
    }
}
