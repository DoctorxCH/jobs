<?php

namespace App\Models\Billing;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class InvoiceItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'invoice_id',
        'product_id',
        'name_snapshot',
        'qty',
        'unit_net_minor',
        'tax_rate_percent',
        'tax_minor',
        'total_gross_minor',
    ];

    protected $casts = [
        'qty' => 'integer',
        'unit_net_minor' => 'integer',
        'tax_rate_percent' => 'decimal:4',
        'tax_minor' => 'integer',
        'total_gross_minor' => 'integer',
    ];

    public function invoice(): BelongsTo
    {
        return $this->belongsTo(Invoice::class);
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }
}
