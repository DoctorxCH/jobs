<?php

namespace App\Models\Billing;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class InvoiceStatusHistory extends Model
{
    public $timestamps = false;

    protected $fillable = [
        'invoice_id',
        'from_status',
        'to_status',
        'changed_by_user_id',
        'note',
        'meta',
        'changed_at',
    ];

    protected $casts = [
        'meta' => 'array',
        'changed_at' => 'datetime',
    ];

    public function invoice(): BelongsTo
    {
        return $this->belongsTo(Invoice::class);
    }

    public function changedByUser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'changed_by_user_id');
    }
}
