<?php

namespace App\Models;

use App\Models\Model;
use Illuminate\Auth\Authenticatable;
use Illuminate\Auth\MustVerifyEmail;
use Illuminate\Notifications\Notifiable;
use Illuminate\Auth\Passwords\CanResetPassword;
use Illuminate\Foundation\Auth\Access\Authorizable;
use Illuminate\Contracts\Auth\Access\Authorizable as AuthorizableContract;
use Illuminate\Contracts\Auth\Authenticatable as AuthenticatableContract;
use Illuminate\Contracts\Auth\CanResetPassword as CanResetPasswordContract;
use Illuminate\Contracts\Auth\MustVerifyEmail as MustVerifyEmailContract;

class User extends Model implements
    AuthenticatableContract,
    AuthorizableContract,
    CanResetPasswordContract
{
    use Authenticatable, Authorizable, CanResetPassword, Notifiable;

    protected $fillable = [
        'first_name', 'last_name', 'email', 'password',
    ];

    protected $hidden = [
        'password', 'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    public function countries()
    {
        return $this->belongsToMany('App\Models\Country');
    }

    public function role()
    {
        return $this->belongsTo('App\Models\Role');
    }

    public function isSuperAdmin()
    {
        return $this->role && $this->role->type === 'super_admin';
    }

    public function isAdmin()
    {
        return $this->role && $this->role->type === 'admin';
    }

    public function isManager()
    {
        return $this->role && $this->role->type === 'manager';
    }

    public function isOperations()
    {
        return $this->role && $this->role->type === 'operations';
    }

    public function isSales()
    {
        return $this->role && $this->role->type === 'sales';
    }

    public function getFullNameAttribute()
    {
        return $this->first_name . ' ' . $this->last_name;
    }

    public function getRestrictedCountries()
    {
        return $this->countries;
    }
}
