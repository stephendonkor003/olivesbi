<?php

namespace Database\Seeders;

use App\Models\AccountPropertyRequest;
use App\Models\Assembly;
use App\Models\Bill;
use App\Models\Business;
use App\Models\Citizen;
use App\Models\Owner;
use App\Models\Payment;
use App\Models\Property;
use App\Models\Role;
use App\Models\User;
use App\Models\WasteCompany;
use App\Models\WastePickup;
use App\Models\WastePickupBooking;
use App\Models\WasteServicePlan;
use App\Support\OlivesCardNumber;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\PermissionRegistrar;

class OlivesAccountDemoSeeder extends Seeder
{
    public function run(): void
    {
        app(PermissionRegistrar::class)->forgetCachedPermissions();

        $assembly = Assembly::query()
            ->where('assembly_code', 'AMA')
            ->orWhere('name', 'like', '%Accra%')
            ->first()
            ?: Assembly::query()->first()
            ?: Assembly::create([
                'name' => 'Demo Metropolitan Assembly',
                'assembly_code' => 'DEMO',
                'regional_code' => 'GAR',
                'address' => 'Demo Civic Centre',
                'phone' => '0300000000',
                'status' => 'Active',
                'currency_code' => 'GHS',
                'currency_symbol' => 'GHS',
                'currency_name' => 'Ghanaian Cedi',
            ]);

        $assemblyCode = $assembly?->assembly_code;

        $role = Role::firstOrCreate(
            ['name' => 'Olives Account', 'guard_name' => 'web'],
            ['active' => true]
        );

        $existingUser = User::where('email', 'account@olivesbi.local')->first();

        $user = User::updateOrCreate(
            ['email' => 'account@olivesbi.local'],
            [
                'name' => 'Olives Account Holder',
                'phone' => '0248000102',
                'password' => Hash::make('Olives@2026'),
                'access_level' => 'customer',
                'status' => 'Active',
                'erms_id' => 'OLV-ACC-0001',
                'assembly_code' => $assemblyCode,
                'olives_card_number' => $existingUser?->olives_card_number ?: OlivesCardNumber::generate('account'),
                'role_access' => $role->id,
                'change_password' => false,
            ]
        );

        $user->syncRoles([$role]);

        $citizen = Citizen::updateOrCreate(
            ['user_id' => $user->id],
            [
                'first_name' => 'Ama',
                'last_name' => 'Mensah',
                'prefix' => 'Ms',
                'gender' => 'Female',
                'date_of_birth' => '1990-04-12',
                'marital_status' => 'Single',
                'account_number' => 'CIT-OLV-0001',
                'telephone_number' => $user->phone,
                'country_of_citizenship' => 'Ghana',
                'status' => 'Active',
                'Ghana_card_number' => 'GHA-000000000-0',
                'id_type' => 'Ghana Card',
                'id_number' => 'GHA-000000000-0',
                'email' => $user->email,
                'created_by' => $user->id,
            ]
        );

        Owner::updateOrCreate(
            ['erms_id' => $user->erms_id],
            [
                'fname' => 'Ama',
                'lname' => 'Mensah',
                'email' => $user->email,
                'gender' => 'Female',
                'phone_no_primary' => $user->phone,
                'status' => 'Active',
                'house_no' => 'HSE 14',
                'residential_address' => 'Ringway Estates, Osu',
                'postal_address' => 'P.O. Box OS 112',
                'digital_address' => 'GA-144-2087',
                'location_geopoint' => json_encode(['lat' => 5.556021, 'lng' => -0.196900]),
                'assembly_code' => $assemblyCode,
                'created_by' => $user->id,
            ]
        );

        $property = Property::updateOrCreate(
            ['erms_id' => 'PROP-OLV-0001'],
            [
                'account_no' => 'PR-OLV-0001',
                'hseno' => 'HSE 14',
                'digitaladdress' => 'GA-144-2087',
                'location' => 'Ringway Estates, Osu',
                'streetname' => 'Independence Avenue',
                'address_line' => 'HSE 14, Independence Avenue, Ringway Estates, Osu',
                'continent' => 'Africa',
                'country' => 'Ghana',
                'country_code' => 'GH',
                'administrative_area' => 'Greater Accra Region',
                'city' => 'Accra',
                'district' => 'Accra Metropolitan Assembly',
                'postal_code' => 'GA-144',
                'owner_id' => $user->erms_id,
                'is_rated' => true,
                'rateable_value' => 185000.00,
                'remarks' => 'Demo residential property linked to the Olives Account dashboard.',
                'created_by' => $user->id,
                'assembly_code' => $assemblyCode,
            ]
        );

        $business = Business::updateOrCreate(
            ['erms_id' => 'BUS-OLV-0001'],
            [
                'account_no' => 'BS-OLV-0001',
                'business_name' => 'Olives Demo Groceries',
                'registration_no' => 'RGD-OLV-1001',
                'permit_no' => 'PERMIT-OLV-2026',
                'hseno' => 'Shop 3',
                'digitaladdress' => 'GA-144-2088',
                'location' => 'Osu Market Lane',
                'street_name' => 'Market Street',
                'phone_no' => $user->phone,
                'address' => 'Shop 3, Osu Market Lane',
                'address_line' => 'Shop 3, Osu Market Lane',
                'continent' => 'Africa',
                'country' => 'Ghana',
                'country_code' => 'GH',
                'administrative_area' => 'Greater Accra Region',
                'city' => 'Accra',
                'district' => 'Accra Metropolitan Assembly',
                'postal_code' => 'GA-144',
                'email' => $user->email,
                'owner_id' => $user->erms_id,
                'quantity' => 1,
                'remarks' => 'Demo business linked to the Olives Account dashboard.',
                'created_by' => $user->id,
                'assembly_code' => $assemblyCode,
            ]
        );

        $propertyBill = Bill::updateOrCreate(
            ['bills_id' => 'BILL-OLV-PROP-2026'],
            [
                'property_id' => $property->uuid,
                'business_id' => null,
                'assembly_code' => $assemblyCode,
                'bills_year' => 2026,
                'arrears' => 145.00,
                'amount' => 725.00,
                'discount_amount' => 0.00,
                'billing_date' => now()->subDays(20)->toDateString(),
                'issue_bill' => true,
                'issued_by' => $user->id,
                'created_by' => $user->id,
            ]
        );

        $businessBill = Bill::updateOrCreate(
            ['bills_id' => 'BILL-OLV-BUS-2026'],
            [
                'property_id' => null,
                'business_id' => $business->uuid,
                'assembly_code' => $assemblyCode,
                'bills_year' => 2026,
                'arrears' => 80.00,
                'amount' => 420.00,
                'discount_amount' => 0.00,
                'billing_date' => now()->subDays(14)->toDateString(),
                'issue_bill' => true,
                'issued_by' => $user->id,
                'created_by' => $user->id,
            ]
        );

        Payment::updateOrCreate(
            ['transaction_id' => 'PAY-OLV-PROP-0001'],
            [
                'bills_id' => $propertyBill->bills_id,
                'payment_mode' => 'Mobile Money',
                'payment_source' => 'Olives Account',
                'transaction_status' => 'Success',
                'req_transaction_id' => 'REQ-OLV-PROP-0001',
                'amount' => 300.00,
                'phone' => $user->phone,
                'network' => 'MTN',
                'prompt' => 'Demo property rate payment',
                'assembly_code' => $assemblyCode,
                'created_by' => $user->id,
                'receipt' => 'RCT-OLV-PROP-0001',
            ]
        );

        Payment::updateOrCreate(
            ['transaction_id' => 'PAY-OLV-BUS-0001'],
            [
                'bills_id' => $businessBill->bills_id,
                'payment_mode' => 'Card',
                'payment_source' => 'Olives Account',
                'transaction_status' => 'Success',
                'req_transaction_id' => 'REQ-OLV-BUS-0001',
                'amount' => 180.00,
                'phone' => $user->phone,
                'network' => 'Visa',
                'prompt' => 'Demo business operating permit payment',
                'assembly_code' => $assemblyCode,
                'created_by' => $user->id,
                'receipt' => 'RCT-OLV-BUS-0001',
            ]
        );

        AccountPropertyRequest::updateOrCreate(
            ['tracking_code' => 'MAP-OLV-0001'],
            [
                'user_id' => $user->id,
                'citizen_id' => $citizen->id,
                'owner_id' => $user->erms_id,
                'assembly_code' => $assemblyCode,
                'status' => 'pending',
                'source' => 'openstreetmap',
                'parcel_name' => 'Ringway Extension Plot',
                'display_name' => 'Mapped footprint near Ringway Estates, Osu, Accra',
                'address_line' => 'Ringway Estates, Osu',
                'continent' => 'Africa',
                'country' => 'Ghana',
                'country_code' => 'GH',
                'administrative_area' => 'Greater Accra Region',
                'city' => 'Accra',
                'district' => 'Accra Metropolitan Assembly',
                'postal_code' => 'GA-144',
                'latitude' => 5.556021,
                'longitude' => -0.196900,
                'osm_type' => 'way',
                'osm_id' => '100000001',
                'osm_place_id' => '200000001',
                'osm_class' => 'building',
                'osm_category' => 'residential',
                'parcel_geojson' => [
                    'type' => 'Polygon',
                    'coordinates' => [[
                        [-0.197100, 5.555850],
                        [-0.196720, 5.555850],
                        [-0.196720, 5.556220],
                        [-0.197100, 5.556220],
                        [-0.197100, 5.555850],
                    ]],
                ],
                'notes' => 'Demo pending map request awaiting municipal validation.',
            ]
        );

        AccountPropertyRequest::updateOrCreate(
            ['tracking_code' => 'MAP-OLV-0002'],
            [
                'user_id' => $user->id,
                'citizen_id' => $citizen->id,
                'owner_id' => $user->erms_id,
                'assembly_code' => $assemblyCode,
                'status' => 'approved',
                'source' => 'openstreetmap',
                'parcel_name' => 'Osu Shop Front',
                'display_name' => 'Selected business frontage near Osu Market Lane',
                'address_line' => 'Osu Market Lane',
                'continent' => 'Africa',
                'country' => 'Ghana',
                'country_code' => 'GH',
                'administrative_area' => 'Greater Accra Region',
                'city' => 'Accra',
                'district' => 'Accra Metropolitan Assembly',
                'postal_code' => 'GA-144',
                'latitude' => 5.555440,
                'longitude' => -0.194880,
                'osm_type' => 'node',
                'osm_id' => '100000002',
                'osm_place_id' => '200000002',
                'osm_class' => 'commercial',
                'osm_category' => 'shop',
                'parcel_geojson' => null,
                'notes' => 'Demo approved map request.',
                'reviewed_by' => $user->id,
                'reviewed_at' => now()->subDays(2),
            ]
        );

        $proUser = User::where('email', 'pro@olivesbi.local')->first();

        if ($proUser && $assemblyCode && blank($proUser->assembly_code)) {
            $proUser->update(['assembly_code' => $assemblyCode]);
        }

        $wasteCompany = WasteCompany::updateOrCreate(
            ['name' => 'Green Loop Waste Services'],
            [
                'registered_by_uuid' => $proUser?->uuid,
                'assembly_code' => $assemblyCode,
                'contact_person' => 'Kojo Owusu',
                'phone' => '0248000201',
                'email' => 'greenloop@olivesbi.local',
                'service_area' => 'Osu, Ringway, Accra Central',
                'address_line' => 'Ringway Waste Operations Yard',
                'continent' => 'Africa',
                'country' => 'Ghana',
                'country_code' => 'GH',
                'administrative_area' => 'Greater Accra Region',
                'city' => 'Accra',
                'district' => 'Accra Metropolitan Assembly',
                'postal_code' => 'GA-144',
                'status' => 'active',
                'notes' => 'Demo provider for household and small business pickup.',
            ]
        );

        $wastePlan = WasteServicePlan::updateOrCreate(
            [
                'waste_company_uuid' => $wasteCompany->uuid,
                'name' => 'Standard Household Pickup',
            ],
            [
                'waste_stream' => 'General waste',
                'default_frequency' => 'weekly',
                'service_charge' => 45.00,
                'currency_code' => 'GHS',
                'currency_symbol' => 'GHS',
                'charge_period' => 'per pickup',
                'description' => 'Weekly household bin pickup with SMS reminders.',
                'active' => true,
            ]
        );

        $businessWasteCompany = WasteCompany::updateOrCreate(
            ['name' => 'CleanCity Commercial Waste'],
            [
                'registered_by_uuid' => $proUser?->uuid,
                'assembly_code' => $assemblyCode,
                'contact_person' => 'Efua Boateng',
                'phone' => '0248000202',
                'email' => 'cleancity@olivesbi.local',
                'service_area' => 'Osu Market Lane and surrounding commercial streets',
                'address_line' => 'Osu Market Waste Collection Point',
                'continent' => 'Africa',
                'country' => 'Ghana',
                'country_code' => 'GH',
                'administrative_area' => 'Greater Accra Region',
                'city' => 'Accra',
                'district' => 'Accra Metropolitan Assembly',
                'postal_code' => 'GA-144',
                'status' => 'active',
                'notes' => 'Demo provider for commercial pickup.',
            ]
        );

        WasteServicePlan::updateOrCreate(
            [
                'waste_company_uuid' => $businessWasteCompany->uuid,
                'name' => 'Commercial Daily Pickup',
            ],
            [
                'waste_stream' => 'Commercial mixed waste',
                'default_frequency' => 'daily',
                'service_charge' => 80.00,
                'currency_code' => 'GHS',
                'currency_symbol' => 'GHS',
                'charge_period' => 'per pickup',
                'description' => 'Daily shop-front pickup for businesses.',
                'active' => true,
            ]
        );

        $wasteBooking = WastePickupBooking::updateOrCreate(
            [
                'user_uuid' => $user->uuid,
                'property_uuid' => $property->uuid,
            ],
            [
                'business_uuid' => null,
                'waste_company_uuid' => $wasteCompany->uuid,
                'waste_service_plan_uuid' => $wastePlan->uuid,
                'frequency' => 'weekly',
                'scheduled_date' => now()->addDays(3)->toDateString(),
                'scheduled_time' => '08:00',
                'next_pickup_at' => now()->addDays(3)->setTime(8, 0),
                'estimated_waste_kg' => 12.50,
                'status' => 'active',
                'notes' => 'Demo weekly waste pickup booking.',
            ]
        );

        WastePickup::updateOrCreate(
            [
                'waste_pickup_booking_uuid' => $wasteBooking->uuid,
                'scheduled_for' => now()->subWeek()->setTime(8, 0),
            ],
            [
                'waste_company_uuid' => $wasteCompany->uuid,
                'status' => 'completed',
                'waste_amount_kg' => 13.20,
                'completed_at' => now()->subWeek()->setTime(9, 10),
                'notes' => 'Demo completed pickup.',
            ]
        );

        WastePickup::updateOrCreate(
            [
                'waste_pickup_booking_uuid' => $wasteBooking->uuid,
                'scheduled_for' => now()->addDays(3)->setTime(8, 0),
            ],
            [
                'waste_company_uuid' => $wasteCompany->uuid,
                'status' => 'scheduled',
            ]
        );

        $this->command?->info('Olives Account demo seeded.');
        $this->command?->line('Login: account@olivesbi.local / Olives@2026');
        $this->command?->line('Open: /dashboard/olives-account');
    }
}
