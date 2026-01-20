<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\Pivot;

class CompanyUser extends Pivot
{
    protected $table = 'company_user';

    protected $fillable = [
        'company_id',
        'user_id',
        'role',
        'status',
        'invited_at',
        'accepted_at',
    ];

    protected $casts = [
        'invited_at' => 'datetime',
        'accepted_at' => 'datetime',
    ];

    public function company()
    {
        return $this->belongsTo(Company::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
