<?php

namespace App\Policies;

use Illuminate\Auth\Access\HandlesAuthorization;
use App\Models\Record;
use App\Models\User;

class RecordPolicy
{
    use HandlesAuthorization;

    public function index(User $auth_user)
    {
        return true;
    }

    public function view(User $auth_user, Record $record)
    {
        return true;
    }

    public function create(User $auth_user)
    {
        return (
            $auth_user->isSuperAdmin() ||
            $auth_user->isAdmin() ||
            $auth_user->isManager() ||
            $auth_user->isOperations() ||
            $auth_user->isSales()
        );
    }

    public function update(User $auth_user, Record $record)
    {
        return (
            $auth_user->isSuperAdmin() ||
            $auth_user->isAdmin() ||
            $auth_user->isManager() ||
            $auth_user->isOperations() ||
            $auth_user->isSales()
        );
    }

    public function delete(User $auth_user, Record $record)
    {
        return (
            $auth_user->isSuperAdmin() ||
            $auth_user->isAdmin() ||
            $auth_user->isManager() ||
            $auth_user->isOperations() ||
            $auth_user->isSales()
        );
    }

    public function download_excel(User $auth_user)
    {
        return (
            $auth_user->isSuperAdmin() ||
            $auth_user->isAdmin() ||
            $auth_user->isManager()
        );
    }

    public function create_campaign(User $auth_user)
    {
        return true;
    }

    public function socapi(User $auth_user, Record $record)
    {
        return (
            $auth_user->isSuperAdmin() ||
            $auth_user->isAdmin()
        );
    }
}
