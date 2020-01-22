<?php

use Illuminate\Database\Seeder;
use App\Models\User;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        User::create([
            'first_name' => 'Super Admin',
            'last_name' => 'Gushcloud',
            'email' => 'superadmin@gushcloud.com',
            'password' => bcrypt('123123'),
            'role_id' => 1,
        ]);
        User::create([
            'first_name' => 'Admin',
            'last_name' => 'Gushcloud',
            'email' => 'admin@gushcloud.com',
            'password' => bcrypt('123123'),
            'role_id' => 2,
        ]);
        User::create([
            'first_name' => 'Manager',
            'last_name' => 'Gushcloud',
            'email' => 'manager@gushcloud.com',
            'password' => bcrypt('123123'),
            'role_id' => 3,
        ]);
        User::create([
            'first_name' => 'Operations',
            'last_name' => 'Gushcloud',
            'email' => 'operations@gushcloud.com',
            'password' => bcrypt('123123'),
            'role_id' => 4,
        ]);
        User::create([
            'first_name' => 'Sales',
            'last_name' => 'Gushcloud',
            'email' => 'sales@gushcloud.com',
            'password' => bcrypt('123123'),
            'role_id' => 5,
        ]);
    }
}
