<?php

namespace App\Models\Billing;

use App\Models\Company;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CreditReservation extends Model
{
    use HasFactory;

    protected $fillable = [
        'company_id',
        'amount',
        'purpose',
        'reference_type',
        'reference_id',
        'status',
        'expires_at',
    ];

    protected $casts = [
        'amount' => 'integer',
        'reference_id' => 'integer',
        'expires_at' => 'datetime',
    ];

    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }
}
