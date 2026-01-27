<?php

namespace App\Models\Billing;

use App\Models\Company;
use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CreditLedger extends Model
{
    public $timestamps = false;

    protected $table = 'credit_ledger';

    protected $fillable = [
        'company_id',
        'change',
        'reason',
        'reference_type',
        'reference_id',
        'created_by_admin_id',
        'created_at',
    ];

    protected $casts = [
        'change' => 'integer',
        'reference_id' => 'integer',
        'created_at' => 'datetime',
    ];

    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }

    public function createdByAdmin(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by_admin_id');
    }
}
