<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable
{
    use HasFactory;
    use Notifiable;
    use HasRoles;

    protected $fillable = [
        'name',
        'email',
        'password',

        // company binding
        'company_id',
        'is_company_owner',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',

            'company_id' => 'integer',
            'is_company_owner' => 'boolean',
        ];
    }

    /**
     * Primary company (where the user is currently operating from).
     */
    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }

    // Hinweis: Ein User ist aktuell genau einer Company zugeordnet (company_id).
}
