<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ResourcePermission extends Model
{
    protected $fillable = [
        'resource',
        'role_name',
        'can_view',
        'can_create',
        'can_edit',
        'can_delete',
    ];

    protected $casts = [
        'can_view' => 'bool',
        'can_create' => 'bool',
        'can_edit' => 'bool',
        'can_delete' => 'bool',
    ];
}
