<?php

namespace App\Models;

use App\Models\Billing\Invoice;
use App\Models\Billing\Payment;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CompanyVerificationRequest extends Model
{
    protected $fillable = [
        'company_id',
        'method',
        'status',
        'requested_by_user_id',
        'requested_by_email',
        'code_sent_to_email',
        'code_hash',
        'code_expires_at',
        'auto_verified_at',
        'attempts',
        'last_sent_at',
        'invoice_id',
        'payment_id',
        'bank_reference',
        'ack_status',
        'ack_at',
        'ack_by',
        'admin_note',
    ];

    protected $casts = [
        'code_expires_at' => 'datetime',
        'auto_verified_at' => 'datetime',
        'last_sent_at' => 'datetime',
        'ack_at' => 'datetime',
    ];

    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }

    public function requestedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'requested_by_user_id');
    }

    public function ackBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'ack_by');
    }

    public function invoice(): BelongsTo
    {
        return $this->belongsTo(Invoice::class);
    }

    public function payment(): BelongsTo
    {
        return $this->belongsTo(Payment::class);
    }
}
