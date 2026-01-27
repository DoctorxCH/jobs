<?php

namespace App\Models\Billing;

use App\Models\Company;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Invoice extends Model
{
    use HasFactory;

    protected $fillable = [
        'company_id',
        'order_id',
        'status',
        'currency',
        'issued_at',
        'due_at',
        'customer_name_snapshot',
        'customer_address_snapshot',
        'customer_city_snapshot',
        'customer_postal_code_snapshot',
        'customer_country_code_snapshot',
        'customer_vat_id_snapshot',
        'customer_vat_id_valid',
        'tax_rule_snapshot',
        'reverse_charge_snapshot',
        'tax_rate_percent_snapshot',
        'subtotal_net_minor_snapshot',
        'discount_minor_snapshot',
        'company_discount_minor_snapshot',
        'tax_minor_snapshot',
        'total_gross_minor_snapshot',
        'payment_reference',
        'pdf_path',
        'pdf_url',
        'cancelled_invoice_id',
        'created_by_admin_id',
    ];

    protected $casts = [
        'issued_at' => 'datetime',
        'due_at' => 'datetime',
        'customer_vat_id_valid' => 'boolean',
        'reverse_charge_snapshot' => 'boolean',
        'tax_rate_percent_snapshot' => 'decimal:4',
        'subtotal_net_minor_snapshot' => 'integer',
        'discount_minor_snapshot' => 'integer',
        'company_discount_minor_snapshot' => 'integer',
        'tax_minor_snapshot' => 'integer',
        'total_gross_minor_snapshot' => 'integer',
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

    public function cancelledInvoice(): BelongsTo
    {
        return $this->belongsTo(self::class, 'cancelled_invoice_id');
    }

    public function createdByAdmin(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by_admin_id');
    }
}
