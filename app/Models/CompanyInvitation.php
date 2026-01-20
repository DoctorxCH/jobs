<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CompanyInvitation extends Model
{
    protected $fillable = [
        'company_id','email','role','token','accepted_at','expires_at'
    ];

    protected $casts = [
        'accepted_at' => 'datetime',
        'expires_at' => 'datetime',
    ];

    public function company()
    {
        return $this->belongsTo(Company::class);
    }
}
