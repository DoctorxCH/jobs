<?php

namespace App\Models\Billing;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class InvoiceExternal extends Model
{
    use HasFactory;

    protected $fillable = [
        'invoice_id',
        'provider',
        'external_invoice_id',
        'external_invoice_number',
        'external_pdf_url',
        'external_pdf_path',
        'sync_status',
        'last_synced_at',
        'last_error',
    ];

    protected $casts = [
        'last_synced_at' => 'datetime',
    ];

    public function invoice(): BelongsTo
    {
        return $this->belongsTo(Invoice::class);
    }
}
