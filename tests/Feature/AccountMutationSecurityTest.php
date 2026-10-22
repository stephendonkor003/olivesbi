<?php

namespace Tests\Feature;

use App\Models\Permission;
use App\Models\Role;
use App\Models\User;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;
use Spatie\Permission\PermissionRegistrar;
use Tests\TestCase;

class AccountMutationSecurityTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        $this->assertTrue(app()->environment('testing'));
        $this->assertSame('sqlite', config('database.default'));
        $this->assertSame(':memory:', config('database.connections.sqlite.database'));
        Mail::fake();
        Notification::fake();
        Http::preventStrayRequests();
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->nullable();
            foreach (['name', 'email', 'phone', 'password', 'status', 'access_level', 'regional_code', 'assembly_code', 'division_code', 'olives_card_number', 'gender'] as $field) {
                $table->string($field)->nullable();
            }
            $table->rememberToken();
            $table->timestamps();
        });
        Schema::create('assemblies', function (Blueprint $table) {
            $table->id();
            $table->string('assembly_code');
            $table->string('regional_code')->nullable();
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
        foreach (['roles', 'permissions'] as $name) {
            Schema::create($name, function (Blueprint $table) use ($name) {
                $table->id();
                $table->uuid('uuid')->nullable();
                $table->string('name');
                $table->string('guard_name');
                if ($name === 'roles') {
                    $table->boolean('active')->default(true);
                }
                $table->timestamps();
            });
        }
        foreach (['model_has_permissions' => 'permission_id', 'model_has_roles' => 'role_id'] as $name => $foreign) {
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
        app(PermissionRegistrar::class)->forgetCachedPermissions();
    }

    public function test_low_privilege_token_cannot_replace_another_users_password_or_move_their_tenant(): void
    {
        $actor = $this->user('A', 'customer');
        $foreign = $this->user('B');
        $password = $foreign->password;
        $this->asToken($actor)->postJson('/api/user/update/'.$foreign->uuid, $this->payload($foreign))->assertForbidden();
        $this->asToken($actor)->postJson('/api/user/update/'.$foreign->id, $this->payload($foreign))->assertForbidden();
        $this->assertSame($password, $foreign->fresh()->password);
        $this->assertSame('B', $foreign->fresh()->assembly_code);
    }

    public function test_api_profile_update_preserves_self_service_and_rejects_authority_changes(): void
    {
        $actor = $this->user('A', 'customer');
        $payload = $this->payload($actor);
        $payload['name'] = 'Updated own profile';
        $this->asToken($actor)->postJson('/api/user/update/'.$actor->uuid, $payload)->assertOk();
        $this->assertSame('Updated own profile', $actor->fresh()->name);
        $this->assertTrue(Hash::check($payload['password'], $actor->fresh()->password));
        foreach (['access_level' => 'Assembly_Supervisor', 'assembly_code' => 'B', 'status' => 'Inactive', 'role_access' => '1'] as $field => $value) {
            $changed = $payload;
            $changed[$field] = $value;
            $this->asToken($actor)->postJson('/api/user/update/'.$actor->uuid, $changed)->assertForbidden();
        }
        $this->assertSame('customer', $actor->fresh()->access_level);
        $this->assertSame('A', $actor->fresh()->assembly_code);
    }

    public function test_permission_holder_can_update_own_assembly_but_not_foreign_or_higher_scope_accounts(): void
    {
        $actor = $this->user('A', 'Assembly_Administrator');
        $this->grant($actor);
        $own = $this->user('A');
        $foreign = $this->user('B');
        $global = $this->user('A', 'Super_User');
        $this->asToken($actor)->postJson('/api/user/update/'.$own->uuid, $this->payload($own))->assertOk();
        $this->asToken($actor)->postJson('/api/user/update/'.$foreign->uuid, $this->payload($foreign))->assertForbidden();
        $this->asToken($actor)->postJson('/api/user/update/'.$global->uuid, $this->payload($global))->assertForbidden();
        $payload = $this->payload($own);
        $payload['assembly_code'] = 'B';
        $this->asToken($actor)->postJson('/api/user/update/'.$own->uuid, $payload)->assertForbidden();
    }

    public function test_established_global_admin_permission_retains_cross_assembly_management(): void
    {
        $actor = $this->user(null, 'Super_User');
        $this->grant($actor);
        $foreign = $this->user('B');
        DB::table('assemblies')->insert(['assembly_code' => 'B']);
        Schema::create('divisions', function (Blueprint $table) {
            $table->id();
            $table->string('code');
            $table->string('assembly_code');
        });
        DB::table('divisions')->insert(['code' => 'D', 'assembly_code' => 'B']);
        $this->asToken($actor)->postJson('/api/user/update/'.$foreign->uuid, $this->payload($foreign))->assertOk();
        $this->assertSame('B', $foreign->fresh()->assembly_code);
    }

    public function test_web_update_rejects_a_token_without_the_management_permission_before_payload_validation(): void
    {
        $actor = $this->user('A', 'customer');
        $foreign = $this->user('B');
        $this->asToken($actor)->putJson('/user/update/'.$foreign->uuid, [])->assertForbidden();
    }

    public function test_inactive_accounts_or_disabled_roles_cannot_use_existing_tokens_to_mutate_accounts(): void
    {
        $actor = $this->user('A', 'Assembly_Administrator');
        $target = $this->user('A');
        $role = Role::create(['name' => 'Assembly account manager', 'guard_name' => 'web', 'active' => false]);
        $role->givePermissionTo(Permission::findOrCreate('users.update', 'web'));
        $actor->assignRole($role);
        $this->asToken($actor)->postJson('/api/user/update/'.$target->uuid, $this->payload($target))->assertForbidden();
        $role->forceFill(['active' => true])->save();
        $actor->unsetRelation('roles');
        $this->asToken($actor)->postJson('/api/user/update/'.$target->uuid, $this->payload($target))->assertOk();
        $actor->forceFill(['status' => 'Inactive'])->saveQuietly();
        $this->asToken($actor)->postJson('/api/user/update/'.$target->uuid, $this->payload($target))->assertForbidden();
    }

    public function test_web_management_keeps_authorized_own_assembly_updates_and_rejects_hidden_role_escalation(): void
    {
        $actor = $this->user('A', 'Assembly_Administrator');
        $this->grant($actor);
        $target = $this->user('A');
        $normal = Role::create(['name' => 'Assembly operations', 'guard_name' => 'web', 'active' => true]);
        $administrator = Role::create(['name' => 'Administrator', 'guard_name' => 'web', 'active' => true]);
        $payload = $this->payload($target) + ['role' => (string) $normal->id];
        $this->asToken($actor)->putJson('/user/update/'.$target->uuid, $payload)->assertRedirect(route('users.index'));
        $this->assertTrue($target->fresh()->hasRole('Assembly operations'));
        $payload['role'] = (string) $administrator->id;
        $this->asToken($actor)->putJson('/user/update/'.$target->uuid, $payload)->assertForbidden();
        $this->assertFalse($target->fresh()->hasRole('Administrator'));
        $payload['role'] = (string) $normal->id;
        $payload['access_level'] = 'Super_User';
        $this->asToken($actor)->putJson('/user/update/'.$target->uuid, $payload)->assertForbidden();
        $this->assertSame('Assembly_Agent', $target->fresh()->access_level);
    }

    public function test_array_authority_payloads_are_rejected_before_validation_without_server_errors(): void
    {
        $actor = $this->user('A', 'Assembly_Administrator');
        $this->grant($actor);
        $target = $this->user('A');
        foreach (['access_level', 'status', 'regional_code', 'assembly_code', 'division_code', 'role_access', 'pro_owner_uuid', 'role'] as $field) {
            $payload = $this->payload($target);
            $payload[$field] = ['malformed'];
            $this->asToken($actor)->postJson('/api/user/update/'.$target->uuid, $payload)->assertForbidden();
            $this->asToken($actor)->putJson('/user/update/'.$target->uuid, $payload)->assertForbidden();
        }
    }

    public function test_api_reads_preserve_own_identity_without_exposing_other_accounts(): void
    {
        $actor = $this->user('A', 'customer');
        $foreign = $this->user('B');
        $this->asToken($actor)->getJson('/api/user')->assertOk()->assertJsonCount(1, 'data')->assertJsonPath('data.0.id', $actor->id);
        $this->asToken($actor)->getJson('/api/user/show/'.$actor->uuid)->assertOk()->assertJsonPath('data.id', $actor->id);
        $this->asToken($actor)->getJson('/api/user/show/'.$foreign->uuid)->assertUnprocessable()->assertJsonMissingPath('data');
        $this->asToken($actor)->getJson('/api/user/show/'.$foreign->id)->assertUnprocessable();
        $this->asToken($actor)->getJson('/user/show/'.$foreign->uuid)->assertForbidden();
    }

    public function test_read_permission_is_limited_to_existing_scopes_and_global_reader_keeps_all_accounts(): void
    {
        $actor = $this->user('A', 'Assembly_Administrator');
        $own = $this->user('A');
        $foreign = $this->user('B');
        $this->grant($actor, 'users.view');
        $this->asToken($actor)->getJson('/api/user')->assertOk()->assertJsonCount(2, 'data');
        $this->asToken($actor)->getJson('/api/user/show/'.$own->uuid)->assertOk();
        $this->asToken($actor)->getJson('/api/user/show/'.$foreign->uuid)->assertUnprocessable();
        $global = $this->user(null, 'Super_User');
        $this->grant($global, 'users.view');
        $this->asToken($global)->getJson('/api/user')->assertOk()->assertJsonCount(4, 'data');
        $this->asToken($global)->getJson('/api/user/show/'.$foreign->uuid)->assertOk();
    }

    public function test_user_creation_requires_existing_create_permission_on_api_and_web(): void
    {
        $actor = $this->user('A', 'customer');
        $administrator = Role::create(['name' => 'Administrator', 'guard_name' => 'web', 'active' => true]);
        $payload = $this->newUserPayload($actor) + ['role' => (string) $administrator->id];
        $payload['access_level'] = 'Super_User';
        $this->asToken($actor)->postJson('/user', $payload)->assertForbidden();
        $payload['access_level'] = 'Assembly_Supervisor';
        $this->asToken($actor)->postJson('/api/user/create', $payload)->assertForbidden();
        $this->assertDatabaseCount('users', 1);
    }

    public function test_permitted_creation_keeps_tenant_binding_and_rejects_hidden_privileged_roles(): void
    {
        $actor = $this->user('A', 'Assembly_Administrator');
        $this->grant($actor, 'users.create');
        $role = Role::create(['name' => 'Assembly operations', 'guard_name' => 'web', 'active' => true]);
        $admin = Role::create(['name' => 'Administrator', 'guard_name' => 'web', 'active' => true]);
        $payload = $this->newUserPayload($actor);
        $this->asToken($actor)->postJson('/api/user/create', $payload)->assertOk()->assertJsonPath('data.assembly_code', 'A');
        $payload = $this->newUserPayload($actor) + ['role' => (string) $role->id];
        $this->asToken($actor)->postJson('/user', $payload)->assertRedirect(route('users.index'));
        $this->assertDatabaseHas('users', ['email' => $payload['email'], 'assembly_code' => 'A']);
        $payload = $this->newUserPayload($actor) + ['role' => (string) $admin->id];
        $this->asToken($actor)->postJson('/user', $payload)->assertForbidden();
        $payload['role'] = (string) $role->id;
        $payload['assembly_code'] = 'B';
        $this->asToken($actor)->postJson('/user', $payload)->assertForbidden();
        $payload['assembly_code'] = 'A';
        $payload['access_level'] = 'Super_User';
        $this->asToken($actor)->postJson('/user', $payload)->assertForbidden();
    }

    public function test_unscoped_national_administrator_keeps_existing_permission_based_management(): void
    {
        $actor = $this->user(null, 'GOG_Administrator');
        $target = $this->user('B');
        $this->grant($actor);
        DB::table('assemblies')->insert(['assembly_code' => 'B']);
        Schema::create('divisions', function (Blueprint $table) {
            $table->id();
            $table->string('code');
            $table->string('assembly_code');
        });
        DB::table('divisions')->insert(['code' => 'D', 'assembly_code' => 'B']);
        $this->asToken($actor)->postJson('/api/user/update/'.$target->uuid, $this->payload($target))->assertOk();
    }

    public function test_public_ratepayer_onboarding_keeps_its_independent_validation_contract(): void
    {
        $this->postJson('/frontstore', [])->assertUnprocessable();
        $this->postJson('/api/customer/create/front', [])->assertUnprocessable();
        $this->assertDatabaseCount('users', 0);
    }

    public function test_regional_manager_cannot_create_or_move_users_into_another_regions_assembly(): void
    {
        $actor = $this->user(null, 'GOG_Administrator');
        $actor->forceFill(['regional_code' => 'R'])->saveQuietly();
        $this->grant($actor, 'users.create');
        $this->grant($actor);
        DB::table('assemblies')->insert(['assembly_code' => 'B', 'regional_code' => 'FOREIGN']);
        Schema::create('divisions', function (Blueprint $table) {
            $table->id();
            $table->string('code');
            $table->string('assembly_code');
        });
        DB::table('divisions')->insert(['code' => 'D', 'assembly_code' => 'B']);
        $payload = $this->newUserPayload($actor);
        $payload['assembly_code'] = 'B';
        $payload['division_code'] = 'D';
        $this->asToken($actor)->postJson('/api/user/create', $payload)->assertUnprocessable();
        $target = $this->user('A');
        $payload = $this->payload($target);
        $payload['assembly_code'] = 'B';
        $this->asToken($actor)->postJson('/api/user/update/'.$target->uuid, $payload)->assertUnprocessable();
        $this->assertSame('A', $target->fresh()->assembly_code);
    }

    private function newUserPayload(User $actor): array
    {
        $payload = $this->payload($actor);
        $payload['email'] = 'new-'.Str::uuid().'@example.invalid';
        $payload['phone'] = '+23320'.str_pad((string) (User::count() + 10), 7, '0', STR_PAD_LEFT);
        $payload['access_level'] = 'Assembly_Agent';
        $payload['gender'] = 'male';

        return $payload;
    }

    private function user(?string $assembly, string $level = 'Assembly_Agent'): User
    {
        return User::withoutEvents(fn () => User::query()->forceCreate([
            'uuid' => (string) Str::uuid(), 'name' => 'Isolated account fixture',
            'email' => Str::uuid().'@example.invalid', 'phone' => '+23320'.str_pad((string) (User::count() + 1), 7, '0', STR_PAD_LEFT),
            'password' => Hash::make('Initial-test-password'), 'status' => 'Active',
            'access_level' => $level, 'assembly_code' => $assembly,
            'olives_card_number' => '476102'.str_pad((string) random_int(1, 999999), 10, '0', STR_PAD_LEFT),
            'regional_code' => $assembly ? 'R' : null, 'division_code' => $assembly ? 'D' : null,
        ]));
    }

    private function payload(User $target): array
    {
        return ['name' => $target->name, 'email' => $target->email, 'phone' => $target->phone,
            'password' => 'Replacement-test-password', 'password_confirmation' => 'Replacement-test-password',
            'access_level' => $target->access_level, 'status' => $target->status,
            'assembly_code' => $target->assembly_code, 'division_code' => $target->division_code];
    }

    private function asToken(User $user): static
    {
        $this->app['auth']->forgetGuards();

        return $this->withToken($user->createToken('Isolated security fixture')->plainTextToken);
    }

    private function grant(User $user, string $ability = 'users.update'): void
    {
        $permission = Permission::findOrCreate($ability, 'web');
        $user->givePermissionTo($permission);
    }
}
