<?php

namespace App\Models\Billing;

use App\Models\Company;
use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Invoice extends Model
{
    protected $fillable = [
        'company_id',
        'order_id',
        'status',
        'currency',
        'issued_at',
        'due_at',
        'payment_reference',
        'customer_name_snapshot',
        'customer_address_snapshot',
        'customer_country_snapshot',
        'customer_vat_id_snapshot',
        'tax_rule_applied',
        'reverse_charge',
        'tax_rate_percent_snapshot',
        'subtotal_net_minor',
        'discount_minor',
        'tax_minor',
        'total_gross_minor',
        'pdf_path',
        'pdf_hash',
        'cancelled_invoice_id',
        'created_by_admin_id',
    ];

    protected $casts = [
        'issued_at' => 'datetime',
        'due_at' => 'datetime',
        'reverse_charge' => 'boolean',
        'tax_rate_percent_snapshot' => 'decimal:2',
        'subtotal_net_minor' => 'integer',
        'discount_minor' => 'integer',
        'tax_minor' => 'integer',
        'total_gross_minor' => 'integer',
    ];

    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }

    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }

    public function items(): HasMany
    {
        return $this->hasMany(InvoiceItem::class);
    }

    public function statusHistory(): HasMany
    {
        return $this->hasMany(InvoiceStatusHistory::class);
    }

    public function external(): HasOne
    {
        return $this->hasOne(InvoiceExternal::class);
    }

    public function payments(): HasMany
    {
        return $this->hasMany(Payment::class);
    }

    public function createdByAdmin(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by_admin_id');
    }

    public function cancelledInvoice(): BelongsTo
    {
        return $this->belongsTo(self::class, 'cancelled_invoice_id');
    }
}
