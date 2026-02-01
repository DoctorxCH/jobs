<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ContactSetting extends Model
{
    protected $fillable = [
        'inbox_email',
        'outbox_email',
        'status_options',
        'default_form_id',
    ];

    protected $casts = [
        'status_options' => 'array',
        'default_form_id' => 'integer',
    ];
}
