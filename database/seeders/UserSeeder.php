<?php

namespace Database\Seeders;

use App\Models\Permission;
use App\Models\Role;
use App\Models\User;
use App\Support\OlivesCardNumber;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $existingUser = User::where('email', 'admin@acn.com')->first();

        $user = User::updateOrCreate([
            'email' => 'admin@acn.com',
        ], [
            'name' => 'Admin User',
            'phone' => '0248000000',
            'password' => Hash::make('password'),
            'access_level' => 'Super_User',
            'olives_card_number' => $existingUser?->olives_card_number ?: OlivesCardNumber::generate('sky'),
        ]);

        $role = Role::firstOrCreate([
            'name' => 'Administrator',
            'guard_name' => 'web',
        ]);

        $role->givePermissionTo(Permission::all());

        $user->assignRole($role);
    }
}
