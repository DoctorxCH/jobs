<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\Pivot;

class CompanyUser extends Pivot
{
    protected $table = 'company_user';

    public $incrementing = true;

    /**
     * @var list<string>
     */
    protected $fillable = [
        'company_id',
        'user_id',
        'role',
        'status',
    ];

    /**
     * @return BelongsTo<Company, CompanyUser>
     */
    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }

    /**
     * @return BelongsTo<User, CompanyUser>
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
