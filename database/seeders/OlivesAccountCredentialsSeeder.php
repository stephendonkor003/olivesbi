<?php

namespace Database\Seeders;

use App\Models\Role;
use App\Models\Assembly;
use App\Models\User;
use App\Support\OlivesCardNumber;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\PermissionRegistrar;

class OlivesAccountCredentialsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        app(PermissionRegistrar::class)->forgetCachedPermissions();

        $password = Hash::make('Olives@2026');
        $defaultAssemblyCode = Assembly::query()->value('assembly_code');

        $accounts = [
            [
                'name' => 'Olives BI Subscriber',
                'email' => 'subscriber@olivesbi.local',
                'phone' => '0248000101',
                'access_level' => 'Subscriber',
                'role' => 'Subscriber',
                'tier' => 'subscriber',
                'erms_id' => 'OLV-SUB-0001',
            ],
            [
                'name' => 'Olives Account Holder',
                'email' => 'account@olivesbi.local',
                'phone' => '0248000102',
                'access_level' => 'customer',
                'role' => 'Olives Account',
                'tier' => 'account',
                'erms_id' => 'OLV-ACC-0001',
            ],
            [
                'name' => 'Olives Pro Municipality',
                'email' => 'pro@olivesbi.local',
                'phone' => '0248000103',
                'access_level' => 'Olives_Pro_Applicant',
                'role' => 'Olives Pro',
                'tier' => 'pro',
                'erms_id' => 'OLV-PRO-0001',
                'assembly_code' => $defaultAssemblyCode,
            ],
            [
                'name' => 'Olives Sky National Account',
                'email' => 'sky@olivesbi.local',
                'phone' => '0248000104',
                'access_level' => 'Olives_Sky_Applicant',
                'role' => 'Olives Sky',
                'tier' => 'sky',
                'erms_id' => 'OLV-SKY-0001',
            ],
            [
                'name' => 'Olives Cloud Regional Account',
                'email' => 'cloud@olivesbi.local',
                'phone' => '0248000105',
                'access_level' => 'Olives_Cloud_Applicant',
                'role' => 'Olives Cloud',
                'tier' => 'cloud',
                'erms_id' => 'OLV-CLD-0001',
            ],
            [
                'name' => 'Olives Shell Continental Account',
                'email' => 'shell@olivesbi.local',
                'phone' => '0248000106',
                'access_level' => 'Olives_Shell_Applicant',
                'role' => 'Olives Shell',
                'tier' => 'shell',
                'erms_id' => 'OLV-SHL-0001',
            ],
        ];

        foreach ($accounts as $account) {
            $role = Role::firstOrCreate(
                ['name' => $account['role'], 'guard_name' => 'web'],
                ['active' => true]
            );

            $existingUser = User::where('email', $account['email'])->first();

            $user = User::updateOrCreate(
                ['email' => $account['email']],
                [
                    'name' => $account['name'],
                    'phone' => $account['phone'],
                    'password' => $password,
                    'access_level' => $account['access_level'],
                    'status' => 'Active',
                    'erms_id' => $account['erms_id'],
                    'assembly_code' => $account['assembly_code'] ?? $existingUser?->assembly_code,
                    'olives_card_number' => $existingUser?->olives_card_number ?: OlivesCardNumber::generate($account['tier']),
                    'role_access' => $role->id,
                    'change_password' => false,
                ]
            );

            $user->syncRoles([$role]);
        }
    }
}
