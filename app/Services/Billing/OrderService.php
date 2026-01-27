<?php

namespace App\Services\Billing;

use App\Models\Billing\Order;
use App\Models\Billing\OrderStatusHistory;
use App\Models\User;

class OrderService
{
    public function markStatus(Order $order, string $toStatus, ?User $actor = null, ?string $note = null, array $meta = []): Order
    {
        $fromStatus = $order->status;
        $order->status = $toStatus;
        $order->save();

        OrderStatusHistory::query()->create([
            'order_id' => $order->id,
            'from_status' => $fromStatus,
            'to_status' => $toStatus,
            'changed_by_user_id' => $actor?->id,
            'note' => $note,
            'meta' => $meta,
            'changed_at' => now(),
        ]);

        return $order;
    }
}
