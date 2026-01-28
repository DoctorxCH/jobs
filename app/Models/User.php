<?php

namespace App\Models;

use App\Models\Company;
use App\Models\CompanyUser;
use Filament\Models\Contracts\FilamentUser;
use Filament\Panel;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable implements FilamentUser
{
    use HasFactory;
    use Notifiable;
    use HasRoles;

    protected $fillable = [
        'name',
        'email',
        'password',

        // company binding (legacy/primary context)
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
     * NOTE: do NOT use this alone for authorization.
     */
    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }

    /**
     * Companies the user is a member of (pivot: company_user).
     */
    public function companies(): BelongsToMany
    {
        return $this->belongsToMany(Company::class)
            ->using(CompanyUser::class)
            ->withPivot([
                'role',
                'status',
                'invited_at',
                'accepted_at',
            ])
            ->withTimestamps();
    }

    /**
     * Company owned by this user (DB-truth).
     */
    public function ownedCompany(): HasOne
    {
        return $this->hasOne(Company::class, 'owner_user_id');
    }

    /**
     * Returns the company_user row (if any). DB enforces UNIQUE(user_id) => max 1 row.
     */
    public function companyMembership(): ?CompanyUser
    {
        return CompanyUser::query()
            ->where('user_id', $this->id)
            ->first();
    }

    /**
     * Effective company id for business context.
     * Priority: owned company -> membership company -> users.company_id (legacy).
     */
    public function effectiveCompanyId(): ?int
    {
        $ownedId = $this->ownedCompany()->value('id');
        if ($ownedId) {
            return (int) $ownedId;
        }

        $membership = $this->companyMembership();
        if ($membership) {
            return (int) $membership->company_id;
        }

        return $this->company_id ? (int) $this->company_id : null;
    }

    /**
     * Effective company role: owner|member|recruiter|viewer|null
     * Owner is determined by companies.owner_user_id (DB-truth).
     * Team role comes from company_user.role (only if status=active).
     */
    public function companyRole(): ?string
    {
        // DB-truth owner
        $ownedId = $this->ownedCompany()->value('id');
        if ($ownedId) {
            return 'owner';
        }

        $m = $this->companyMembership();
        if (! $m) {
            return null;
        }

        if (($m->status ?? 'inactive') !== 'active') {
            return null;
        }

        $role = $m->role ?? null;

        return in_array($role, ['member', 'recruiter', 'viewer'], true)
            ? $role
            : null;
    }

    /* -----------------
     | Capability helpers
     |-----------------*/

    public function canCompanyView(): bool
    {
        return $this->companyRole() !== null;
    }

    public function canCompanyManageJobs(): bool
    {
        return in_array($this->companyRole(), ['owner', 'member', 'recruiter'], true);
    }

    public function canCompanyManageTeam(): bool
    {
        return in_array($this->companyRole(), ['owner', 'member'], true);
    }

    public function canCompanyBilling(): bool
    {
        return in_array($this->companyRole(), ['owner', 'member'], true);
    }

    public function canCompanyTransferOwnership(): bool
    {
        return $this->companyRole() === 'owner';
    }

    /**
     * Filament Admin Panel access control.
     * IMPORTANT: must match AdminGateController's allowed platform.* roles.
     */
    public function canAccessPanel(Panel $panel): bool
    {
        if ($panel->getId() !== 'admin') {
            return false;
        }

        // Only platform.* roles may access /admin
        return $this->hasAnyRole([
            'platform.super_admin',
            'platform.admin',
            'platform.manager',
            'platform.editor',
            'platform.moderator',
            'platform.finance',
            'platform.support',
        ]);
    }
}
