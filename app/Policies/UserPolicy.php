<?php

namespace App\Policies;

use Illuminate\Auth\Access\HandlesAuthorization;
use App\Models\User;

class UserPolicy
{
    use HandlesAuthorization;

    public function index(User $auth_user)
    {
        return (
            $auth_user->isSuperAdmin() ||
            $auth_user->isAdmin()
        );
    }

    public function view(User $auth_user, User $user)
    {
        return (
            $auth_user->isSuperAdmin() ||
            $auth_user->isAdmin()
        );
    }

    public function create(User $auth_user)
    {
        return (
            $auth_user->isSuperAdmin() ||
            $auth_user->isAdmin()
        );
    }

    public function update(User $auth_user, User $user)
    {
        return (
            $auth_user->isSuperAdmin() ||
            $auth_user->isAdmin()
        );
    }

    public function suspend(User $auth_user, User $user)
    {
        return ! $user->isSuperAdmin();
    }

    public function restore(User $auth_user, User $user)
    {
        return ! $user->isSuperAdmin();
    }
}
