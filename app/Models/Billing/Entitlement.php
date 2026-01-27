<?php

namespace App\Models\Billing;

use App\Models\Company;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Entitlement extends Model
{
    protected $fillable = [
        'company_id',
        'type',
        'quantity_total',
        'quantity_remaining',
        'starts_at',
        'ends_at',
        'source_invoice_item_id',
    ];

    protected $casts = [
        'quantity_total' => 'integer',
        'quantity_remaining' => 'integer',
        'starts_at' => 'datetime',
        'ends_at' => 'datetime',
    ];

    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }

    public function sourceInvoiceItem(): BelongsTo
    {
        return $this->belongsTo(InvoiceItem::class, 'source_invoice_item_id');
    }

    public function history(): HasMany
    {
        return $this->hasMany(EntitlementHistory::class);
    }
}
