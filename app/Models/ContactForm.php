<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ContactForm extends Model
{
    protected $fillable = [
        'name',
        'slug',
        'description',
        'fields',
        'active',
    ];

    protected $casts = [
        'fields' => 'array',
        'active' => 'boolean',
    ];

    public function requests(): HasMany
    {
        return $this->hasMany(ContactRequest::class, 'form_id');
    }
}
