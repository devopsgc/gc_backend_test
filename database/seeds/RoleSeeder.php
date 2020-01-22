<?php

use Illuminate\Database\Seeder;
use App\Models\Role;

class RoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Role::create([
            'type' => Role::SUPER_ADMIN,
            'name' => 'Super Admin',
        ]);
        Role::create([
            'type' => Role::ADMIN,
            'name' => 'Admin',
        ]);
        Role::create([
            'type' => Role::MANAGER,
            'name' => 'Manager',
        ]);
        Role::create([
            'type' => Role::OPERATIONS,
            'name' => 'Operations',
        ]);
        Role::create([
            'type' => Role::SALES,
            'name' => 'Sales',
        ]);
    }
}
