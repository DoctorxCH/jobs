<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Job extends Model
{
    use HasFactory;

    /**
     * @var list<string>
     */
    protected $fillable = [
        'company_id',
        'created_by_user_id',
        'status',
        'occupation',
        'category',
        'title',
        'short_description',
        'description',
        'location',
        'is_remote',
        'workload',
        'languages',
        'education_level',
        'requirements',
        'contact_name',
        'contact_email',
        'attachments',
        'is_featured',
        'is_top',
        'expires_at',
    ];

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'languages' => 'array',
            'requirements' => 'array',
            'attachments' => 'array',
            'is_remote' => 'boolean',
            'is_featured' => 'boolean',
            'is_top' => 'boolean',
            'expires_at' => 'datetime',
        ];
    }

    /**
     * @return BelongsTo<Company, Job>
     */
    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }

    /**
     * @return BelongsTo<User, Job>
     */
    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by_user_id');
    }
}
