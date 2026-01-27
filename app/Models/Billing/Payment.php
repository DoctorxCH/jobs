<?php

namespace App\Models\Billing;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Payment extends Model
{
    protected $fillable = [
        'invoice_id',
        'method',
        'status',
        'amount_minor',
        'currency',
        'bank_reference',
        'received_at',
        'created_by_admin_id',
    ];

    protected $casts = [
        'amount_minor' => 'integer',
        'received_at' => 'datetime',
    ];

    public function invoice(): BelongsTo
    {
        return $this->belongsTo(Invoice::class);
    }

    public function statusHistory(): HasMany
    {
        return $this->hasMany(PaymentStatusHistory::class);
    }

    public function createdByAdmin(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by_admin_id');
    }
}
