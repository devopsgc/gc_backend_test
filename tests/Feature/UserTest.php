<?php

namespace Tests\Feature;

use App\Models\Country;
use App\Models\Role;
use App\Models\User;
use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;

class UserTest extends TestCase
{
    use RefreshDatabase;

    public function test_super_admin_dont_have_restricted_countries()
    {
        $this->seed('RoleSeeder');

        $user = factory(User::class)->create();

        $role = Role::where('type', 'super_admin')->first();

        $user->role_id = $role->id;
        $user->save();

        $this->assertEquals($user->getRestrictedCountries()->count(), 0);
    }

    public function test_sales_have_no_restricted_countries()
    {
        $this->seed('RoleSeeder');
        $this->seed('CountriesSeeder');

        $user = factory(User::class)->create();

        $role = Role::where('type', 'sales')->first();

        $user->role_id = $role->id;
        $user->save();

        $this->assertEquals($user->getRestrictedCountries()->count(), 0);
    }

    public function test_sales_have_one_restricted_countries()
    {
        $this->seed('RoleSeeder');
        $this->seed('CountriesSeeder');

        $user = factory(User::class)->create();

        $role = Role::where('type', 'sales')->first();
        $country = Country::where('iso_3166_2', 'SG')->first();

        $user->role_id = $role->id;
        $user->save();

        $user->countries()->attach($country);

        $this->assertEquals($user->getRestrictedCountries()->count(), 1);
    }
}
