<?php

namespace Tests\Feature;

use App\Models\Owner;
use App\Models\Permission;
use App\Models\Role;
use App\Models\User;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Bus;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;
use Spatie\Permission\PermissionRegistrar;
use Tests\TestCase;

class OwnerRecordSecurityTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        $this->assertTrue(app()->environment('testing'));
        $this->assertSame('sqlite', config('database.default'));
        $this->assertSame(':memory:', config('database.connections.sqlite.database'));
        Bus::fake();
        Mail::fake();
        Notification::fake();
        Http::preventStrayRequests();
        foreach ([
            'users' => ['name', 'email', 'phone', 'password', 'status', 'access_level', 'regional_code', 'assembly_code', 'division_code', 'olives_card_number', 'erms_id', 'remember_token'],
            'entity_owners' => ['user_id', 'first_name', 'last_name', 'fname', 'lname', 'email', 'gender', 'phone_no_primary', 'tin_no', 'house_no', 'residential_address', 'postal_address', 'digital_address', 'assembly_code', 'regional_code', 'erms_id', 'created_by', 'customer_type', 'status'],
            'assemblies' => ['assembly_code', 'regional_code', 'name'],
            'properties' => ['owner_id', 'assembly_code', 'erms_id', 'account_no', 'digitaladdress', 'streetname', 'location', 'polygon_id'],
            'businesses' => ['owner_id', 'assembly_code', 'polygon_id'],
            'bills' => ['bills_id', 'property_id', 'business_id', 'assembly_code', 'amount'],
            'payments' => ['bills_id', 'assembly_code', 'amount', 'transaction_status'],
            'polygons' => ['cntr_x_coord', 'cntr_y_coord', 'block_id'], 'blocks' => ['division_id'], 'divisions' => ['code', 'assembly_code'],
            'customer_types' => ['name'], 'roles' => ['name', 'guard_name'], 'permissions' => ['name', 'guard_name'],
        ] as $name => $fields) {
            Schema::create($name, function (Blueprint $table) use ($name, $fields) {
                $table->id();
                $table->uuid('uuid')->nullable();
                foreach ($fields as $field) {
                    $table->string($field)->nullable();
                }
                if ($name === 'roles') {
                    $table->boolean('active')->default(true);
                }
                $table->timestamps();
            });
        }
        foreach (['model_has_roles' => 'role_id', 'model_has_permissions' => 'permission_id'] as $name => $foreign) {
            Schema::create($name, function (Blueprint $table) use ($foreign) {
                $table->unsignedBigInteger($foreign);
                $table->string('model_type');
                $table->unsignedBigInteger('model_id');
            });
        }
        Schema::create('role_has_permissions', function (Blueprint $table) {
            $table->unsignedBigInteger('role_id');
            $table->unsignedBigInteger('permission_id');
        });
        Schema::create('personal_access_tokens', function (Blueprint $table) {
            $table->id();
            $table->morphs('tokenable');
            $table->string('name');
            $table->string('token', 64)->unique();
            $table->text('abilities')->nullable();
            $table->timestamp('last_used_at')->nullable();
            $table->timestamp('expires_at')->nullable();
            $table->timestamps();
        });
        DB::table('assemblies')->insert([['assembly_code' => 'A', 'regional_code' => 'R', 'name' => 'Assembly A'],
            ['assembly_code' => 'B', 'regional_code' => 'S', 'name' => 'Assembly B'],
            ['assembly_code' => 'C', 'regional_code' => 'R', 'name' => 'Assembly C']]);
        app(PermissionRegistrar::class)->forgetCachedPermissions();
    }

    public function test_low_privilege_tokens_cannot_mutate_foreign_owners_through_any_identifier_or_web_method_override(): void
    {
        $actor = $this->user('customer');
        $foreign = $this->owner('B');
        foreach ([$foreign->id, $foreign->uuid, $foreign->erms_id] as $identifier) {
            $this->token($actor)->postJson('/api/customer/update/'.$identifier, $this->payload($foreign))->assertForbidden();
        }
        $this->token($actor)->putJson('/owner/update/'.$foreign->uuid, $this->payload($foreign))->assertForbidden();
        $this->token($actor)->postJson('/owner/update/'.$foreign->uuid, $this->payload($foreign) + ['_method' => 'PUT'])->assertForbidden();
        $this->assertSame('Original Owner', $foreign->fresh()->fname);
        $this->assertSame('B', $foreign->fresh()->assembly_code);
    }

    public function test_staff_permission_allows_own_assembly_writes_but_rejects_foreign_moves_and_array_scopes(): void
    {
        $actor = $this->user('Assembly_Administrator', 'A', 'R');
        $this->grant($actor, 'customers.update', 'customers.view');
        $own = $this->owner('A');
        $foreign = $this->owner('B');
        $this->token($actor)->postJson('/api/customer/update/'.$own->erms_id, $this->payload($own))->assertOk();
        $this->assertSame('Updated Owner', $own->fresh()->fname);
        $this->token($actor)->put('/owner/update/'.$own->uuid, $this->payload($own))->assertRedirect(route('owners.index'));
        $this->token($actor)->postJson('/api/customer/update/'.$foreign->uuid, $this->payload($foreign))->assertForbidden();
        $move = $this->payload($own);
        $move['assembly_code'] = 'B';
        $this->token($actor)->postJson('/api/customer/update/'.$own->uuid, $move)->assertForbidden();
        $move['assembly_code'] = ['A'];
        $this->token($actor)->putJson('/owner/update/'.$own->uuid, $move)->assertForbidden();
        $this->assertSame('A', $own->fresh()->assembly_code);
    }

    public function test_regional_and_global_permission_holders_preserve_their_established_authority(): void
    {
        $regional = $this->user('Regional_Administrator', null, 'R');
        $this->grant($regional, 'customers.update', 'customers.view');
        $allowed = $this->owner('C');
        $foreign = $this->owner('B');
        $this->token($regional)->postJson('/api/customer/update/'.$allowed->id, $this->payload($allowed))->assertOk();
        $this->token($regional)->postJson('/api/customer/update/'.$foreign->id, $this->payload($foreign))->assertForbidden();
        $this->token($regional)->getJson('/api/customer/show/'.$foreign->erms_id)->assertStatus(422);
        $global = $this->user('Super_User');
        $this->grant($global, 'customers.update', 'customers.view');
        $this->token($global)->postJson('/api/customer/update/'.$foreign->id, $this->payload($foreign))->assertOk();
        $this->token($global)->getJson('/api/customer/show/'.$foreign->uuid)->assertOk()->assertJsonPath('data.id', $foreign->id);
    }

    public function test_self_profile_uses_account_identity_and_cannot_move_tenants_or_claim_another_profiles_phone(): void
    {
        $own = $this->owner('B', ['user_id' => 0]);
        $foreign = $this->owner('A');
        $actor = $this->user('customer');
        $actor->forceFill(['erms_id' => $own->erms_id, 'phone' => $foreign->phone_no_primary])->saveQuietly();
        $this->token($actor)->postJson('/api/customer/update/'.$own->uuid, $this->payload($own))->assertOk();
        $this->token($actor)->getJson('/api/customer')->assertOk()->assertJsonCount(1, 'data.data')->assertJsonPath('data.data.0.id', $own->id);
        $this->token($actor)->getJson('/api/customer/show/'.$foreign->id)->assertStatus(422);
        $this->token($actor)->postJson('/api/customer/update/'.$foreign->id, $this->payload($foreign))->assertForbidden();
        $move = $this->payload($own);
        $move['assembly_code'] = 'A';
        $this->token($actor)->postJson('/api/customer/update/'.$own->uuid, $move)->assertForbidden();
        $this->assertSame('B', $own->fresh()->assembly_code);
    }

    public function test_explicit_user_link_works_but_conflicting_account_links_fail_closed(): void
    {
        $actor = $this->user('customer');
        $other = $this->user('customer');
        $own = $this->owner('B', ['user_id' => $actor->id]);
        $this->assertSame((string) $actor->id, (string) $own->user_id);
        $this->token($actor)->postJson('/api/customer/update/'.$own->id, $this->payload($own))->assertOk();
        $actor->forceFill(['erms_id' => $own->erms_id])->saveQuietly();
        $own->forceFill(['user_id' => $other->id])->save();
        $this->token($actor)->postJson('/api/customer/update/'.$own->id, $this->payload($own))->assertForbidden();
        $this->token($actor)->getJson('/api/customer/show/'.$own->uuid)->assertStatus(422);
    }

    public function test_shared_owner_reads_filter_foreign_assets_bills_payments_and_never_grant_master_write_authority(): void
    {
        $actor = $this->user('Assembly_Administrator', 'A', 'R');
        $this->grant($actor, 'customers.view', 'customers.update');
        $owner = $this->owner('B');
        $ownProperty = $this->property($owner, 'A');
        $foreignProperty = $this->property($owner, 'B');
        DB::table('bills')->insert([['uuid' => (string) Str::uuid(), 'bills_id' => 'VISIBLE-BILL', 'property_id' => $ownProperty, 'assembly_code' => 'A'],
            ['uuid' => (string) Str::uuid(), 'bills_id' => 'PRIVATE-FOREIGN-BILL', 'property_id' => $ownProperty, 'assembly_code' => 'B']]);
        DB::table('payments')->insert([['uuid' => (string) Str::uuid(), 'bills_id' => 'VISIBLE-BILL', 'assembly_code' => 'A'],
            ['uuid' => (string) Str::uuid(), 'bills_id' => 'VISIBLE-BILL', 'assembly_code' => 'B']]);
        $this->token($actor)->getJson('/api/customer/fetch/'.$owner->erms_id)->assertOk()->assertJsonCount(1, 'data.0.properties')
            ->assertJsonPath('data.0.properties.0.uuid', $ownProperty);
        $response = $this->token($actor)->getJson('/api/customer/search/'.$owner->erms_id)->assertOk()
            ->assertJsonCount(1, 'data.properties')->assertJsonCount(1, 'data.properties.0.bills')
            ->assertJsonCount(1, 'data.properties.0.bills.0.payments');
        $this->assertStringNotContainsString($foreignProperty, $response->getContent());
        $this->assertStringNotContainsString('PRIVATE-FOREIGN-BILL', $response->getContent());
        $this->token($actor)->postJson('/api/customer/update/'.$owner->erms_id, $this->payload($owner))->assertForbidden();
    }

    public function test_ussd_keeps_authorized_phone_lookup_shape_and_filters_related_assets(): void
    {
        $owner = $this->owner('B');
        $own = $this->property($owner, 'A');
        $foreign = $this->property($owner, 'B');
        $actor = $this->user('Assembly_Administrator', 'A', 'R');
        $this->grant($actor, 'customers.view');
        $response = $this->token($actor)->getJson('/api/ussd/customer/'.$owner->phone_no_primary)->assertOk()
            ->assertJsonPath('message', 'Get customr records')->assertJsonCount(1, 'data.properties')
            ->assertJsonPath('data.properties.0.uuid', $own);
        $this->assertStringNotContainsString($foreign, $response->getContent());
        $unprivileged = $this->user('customer');
        $this->token($unprivileged)->getJson('/api/ussd/customer/'.$owner->phone_no_primary)->assertStatus(422);
        $global = $this->user('GOG_Administrator');
        $this->grant($global, 'customers.view');
        $this->token($global)->getJson('/api/ussd/customer/'.$owner->phone_no_primary)->assertOk()->assertJsonCount(2, 'data.properties');
    }

    public function test_authorized_web_table_filters_foreign_records_and_preserves_created_at_sorting(): void
    {
        $actor = $this->user('Assembly_Administrator', 'A', 'R');
        $this->grant($actor, 'customers.view');
        $own = $this->owner('A');
        $foreign = $this->owner('B');
        $this->token($actor)->getJson('/owner?draw=1&columns[0][data]=created_at&columns[0][name]=created_at&order[0][column]=0&order[0][dir]=desc')
            ->assertOk()->assertJsonPath('recordsTotal', 1)->assertJsonPath('data.0.id', $own->id)
            ->assertJsonMissing(['id' => $foreign->id]);
    }

    public function test_inactive_users_and_disabled_roles_cannot_reuse_tokens_for_owner_access(): void
    {
        $actor = $this->user('Assembly_Administrator', 'A', 'R');
        $owner = $this->owner('A');
        $role = Role::create(['name' => 'Synthetic disabled manager', 'guard_name' => 'web', 'active' => false]);
        $role->givePermissionTo(Permission::findOrCreate('customers.update', 'web'), Permission::findOrCreate('customers.view', 'web'));
        $actor->assignRole($role);
        $this->token($actor)->postJson('/api/customer/update/'.$owner->id, $this->payload($owner))->assertForbidden();
        $this->token($actor)->getJson('/api/customer/show/'.$owner->id)->assertStatus(422);
        $this->grant($actor, 'customers.update', 'customers.view');
        $actor->forceFill(['status' => 'Inactive'])->saveQuietly();
        $this->token($actor)->getJson('/api/customer/show/'.$owner->id)->assertForbidden();
        $this->token($actor)->putJson('/owner/update/'.$owner->uuid, $this->payload($owner))->assertForbidden();
    }

    public function test_protected_creation_requires_permission_and_binds_tenant_while_public_onboarding_stays_open(): void
    {
        $actor = $this->user('Assembly_Administrator', 'A', 'R');
        $payload = ['first_name' => 'Synthetic', 'last_name' => 'New Owner', 'phone_no_primary' => '0201234567', 'gender' => 'M', 'assembly_code' => 'A'];
        $this->token($actor)->postJson('/api/customer/create', $payload)->assertForbidden();
        $this->token($actor)->postJson('/owner/create', $payload)->assertForbidden();
        $this->grant($actor, 'customers.create');
        $foreign = $payload;
        $foreign['assembly_code'] = 'B';
        $this->token($actor)->postJson('/api/customer/create', $foreign)->assertForbidden();
        $this->token($actor)->postJson('/api/customer/create', $payload)->assertOk()->assertJsonPath('setup_required', true);
        $this->assertSame('A', Owner::firstOrFail()->assembly_code);
        $payload['phone_no_primary'] = '0241234567';
        $payload['assembly_code'] = 'B';
        $this->postJson('/api/customer/create/front', $payload)->assertOk()->assertJsonPath('setup_required', true);
        Bus::assertNothingDispatched();
        Mail::assertNothingSent();
    }

    private function user(string $level, ?string $assembly = null, ?string $region = null): User
    {
        return User::create(['name' => 'Synthetic Actor', 'email' => Str::uuid().'@example.test', 'phone' => '027'.random_int(1000000, 9999999),
            'password' => Hash::make('Synthetic-existing-password'), 'status' => 'Active', 'access_level' => $level,
            'assembly_code' => $assembly, 'regional_code' => $region]);
    }

    private function owner(string $assembly, array $extra = []): Owner
    {
        return Owner::query()->forceCreate(array_merge(['erms_id' => 'ERMS'.random_int(100000, 999999), 'fname' => 'Original Owner',
            'lname' => 'Synthetic', 'phone_no_primary' => '020'.random_int(1000000, 9999999), 'gender' => 'M',
            'assembly_code' => $assembly, 'regional_code' => $assembly === 'B' ? 'S' : 'R'], $extra));
    }

    private function property(Owner $owner, string $assembly): string
    {
        $uuid = (string) Str::uuid();
        DB::table('properties')->insert(['uuid' => $uuid, 'owner_id' => $owner->erms_id, 'assembly_code' => $assembly]);

        return $uuid;
    }

    private function payload(Owner $owner): array
    {
        return ['first_name' => 'Updated Owner', 'last_name' => 'Synthetic', 'gender' => 'M',
            'phone_no_primary' => $owner->phone_no_primary, 'assembly_code' => $owner->assembly_code];
    }

    private function grant(User $actor, string ...$permissions): void
    {
        foreach ($permissions as $permission) {
            $actor->givePermissionTo(Permission::findOrCreate($permission, 'web'));
        }
    }

    private function token(User $actor): static
    {
        app('auth')->forgetGuards();

        return $this->withToken($actor->createToken('synthetic-test')->plainTextToken);
    }
}
