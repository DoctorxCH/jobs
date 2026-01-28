<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Company extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $fillable = [
        'owner_user_id',
        'category_id',
        'legal_name',
        'slug',
        'ico',
        'dic',
        'ic_dph',
        'website_url',
        'general_email',
        'phone',
        'logo_path',
        'description_short',
        'bio',
        'social_links',
        'country_code',
        'region',
        'city',
        'postal_code',
        'street',
        'contact_first_name',
        'contact_last_name',
        'contact_email',
        'contact_phone',
        'team_size',
        'founded_year',
        'status',
        'verified_at',
        'active',
        'notes_internal',
        'seats_purchased',
        'seats_locked',
    ];

    protected $casts = [
        'social_links' => 'array',
        'verified_at' => 'datetime',
        'active' => 'boolean',
        'seats_purchased' => 'integer',
        'seats_locked' => 'integer',
        'team_size' => 'integer',
        'founded_year' => 'integer',
        'is_top_partner' => 'boolean',
        'is_top_partner_active' => 'boolean',
        'is_top_partner_from' => 'date',
        'is_top_partner_until' => 'date',
        'top_partner_activated_at' => 'datetime',
        'social_links' => 'array',
    ];

    /* -----------------
     | Relationships
     |-----------------*/

    public function owner()
    {
        return $this->belongsTo(User::class, 'owner_user_id');
    }

    public function users()
    {
        return $this->belongsToMany(User::class)
            ->using(CompanyUser::class)
            ->withPivot([
                'role',
                'status',
                'invited_at',
                'accepted_at',
            ])
            ->withTimestamps();
    }

    public function members()
    {
        return $this->users()->wherePivot('status', 'active');
    }

    public function category()
    {
        return $this->belongsTo(CompanyCategory::class);
    }

    /* -----------------
     | Helpers
     |-----------------*/

    public function hasFreeSeats(): bool
    {
        $purchased = (int) ($this->seats_purchased ?? 1);
        $locked = (int) ($this->seats_locked ?? 0);

        return $this->members()->count() < max($purchased - $locked, 0);
    }

        public function scopeActiveTopPartners($query)
    {
        $today = now()->toDateString();

        return $query->where('is_top_partner', true)
            ->where('is_top_partner_active', true)
            ->where(function ($q) use ($today) {
                $q->whereNull('is_top_partner_from')
                  ->orWhere('is_top_partner_from', '<=', $today);
            })
            ->where(function ($q) use ($today) {
                $q->whereNull('is_top_partner_until')
                  ->orWhere('is_top_partner_until', '>=', $today);
            })
            ->orderBy('top_partner_sort');
    }
}
