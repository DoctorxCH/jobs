<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class DrivingLicenseCategory extends Model
{
    use HasFactory;

    protected $fillable = [
        'code',
        'label',
    ];

    public function jobs(): BelongsToMany
    {
        return $this->belongsToMany(Job::class, 'job_driving_license');
    }
}
