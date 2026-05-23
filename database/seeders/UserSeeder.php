<?php

namespace Database\Seeders;

use App\Models\Permission;
use App\Models\Role;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $user = User::updateOrCreate([
            'email' => 'admin@acn.com',
        ], [
            'name' => 'Admin User',
            'phone' => '0248000000',
            'password' => Hash::make('password'),
            'access_level' => 'Super_User',
        ]);

        $role = Role::firstOrCreate([
            'name' => 'Administrator',
            'guard_name' => 'web',
        ]);

        $role->givePermissionTo(Permission::all());

        $user->assignRole($role);
    }
}
