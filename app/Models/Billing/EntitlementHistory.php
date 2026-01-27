<?php

namespace App\Models\Billing;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class EntitlementHistory extends Model
{
    use HasFactory;

    public $timestamps = false;

    protected $fillable = [
        'entitlement_id',
        'change',
        'reason',
        'reference_type',
        'reference_id',
        'changed_by_user_id',
        'meta',
        'created_at',
    ];

    protected $casts = [
        'change' => 'integer',
        'reference_id' => 'integer',
        'meta' => 'array',
        'created_at' => 'datetime',
    ];

    public function entitlement(): BelongsTo
    {
        return $this->belongsTo(Entitlement::class);
    }

    public function changedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'changed_by_user_id');
    }
}
