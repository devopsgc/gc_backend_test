<?php

namespace App\Models;

use App\Models\Model;

class Role extends Model
{
    const SUPER_ADMIN = 'super_admin';
    const ADMIN = 'admin';
    const MANAGER = 'manager';
    const OPERATIONS = 'operations';
    const SALES = 'sales';

    protected $fillable = [
        'type', 'name',
    ];

    public function users()
    {
        return $this->hasMany('App\Models\User');
    }
}
