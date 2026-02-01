<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ContactRequest extends Model
{
    protected $fillable = [
        'company_id',
        'user_id',
        'form_id',
        'name',
        'email',
        'subject',
        'message',
        'status',
        'assigned_to_user_id',
        'internal_notes',
        'reply_body',
        'replied_at',
        'reply_sent_by',
        'payload',
    ];

    protected $casts = [
        'company_id' => 'integer',
        'user_id' => 'integer',
        'form_id' => 'integer',
        'assigned_to_user_id' => 'integer',
        'reply_sent_by' => 'integer',
        'replied_at' => 'datetime',
        'payload' => 'array',
    ];

    public function form(): BelongsTo
    {
        return $this->belongsTo(ContactForm::class, 'form_id');
    }
}
