<?php

namespace Tests;

use App\Models\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\TestCase as BaseTestCase;

abstract class TestCase extends BaseTestCase
{
    use CreatesApplication;

    protected function getSuperAdminUser()
    {
        $user = factory(User::class)->create();

        $role = new Role;
        $role->type = 'super_admin';
        $role->name = 'Super Admin';
        $role->save();

        $user->role_id = $role->id;
        $user->save();

        return $user;
    }

    protected function getSalesUser()
    {
        $user = factory(User::class)->create();

        $role = new Role();
        $role->type = 'sales';
        $role->name = 'Sales';
        $role->save();

        $user->role_id = $role->id;
        $user->save();

        return $user;
    }

    protected function createUserAsRole($role)
    {
        $user = factory(User::class)->create();
        $user->role_id = Role::where('type', $role)->first()->id;
        $user->save();

        return $user;
    }

    protected function getSampleSocialData($item)
    {
        return file_get_contents(storage_path() . '/test/sampleSocialData' . $item . '.json');
    }
}
