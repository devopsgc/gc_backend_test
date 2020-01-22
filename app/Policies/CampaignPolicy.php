<?php

namespace App\Policies;

use Illuminate\Auth\Access\HandlesAuthorization;
use App\Models\Campaign;
use App\Models\User;

class CampaignPolicy
{
    use HandlesAuthorization;

    public function index(User $auth_user)
    {
        return true;
    }

    public function view(User $auth_user, Campaign $campaign)
    {
        return (
            $auth_user->isSuperAdmin() ||
            $auth_user->isAdmin() ||
            $auth_user->id == $campaign->created_by_user_id
        );
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

    public function update(User $auth_user, Campaign $campaign)
    {
        return (
            $auth_user->isSuperAdmin() ||
            $auth_user->isAdmin() ||
            $auth_user->id == $campaign->created_by_user_id
        );
    }

    public function delete(User $auth_user, Campaign $campaign)
    {
        return (
            $auth_user->isSuperAdmin() ||
            $auth_user->isAdmin() ||
            $auth_user->id == $campaign->created_by_user_id
        );
    }
}
