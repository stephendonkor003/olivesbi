<?php

namespace Tests\Feature;

use AlhajiAki\Sms\Sms;
use App\Imports\Customer\CustomersImport;
use App\Jobs\OTP\SendPasswordChangeOTPSMS;
use App\Jobs\Registration\SendRegistrationReminder;
use App\Models\Citizen;
use App\Models\OTP;
use App\Models\Owner;
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
use Tests\TestCase;

class NewAccountSetupSecurityTest extends TestCase
{
    private const SHARED_PASSWORD = 'test-only-legacy-shared-default';

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
        config(['erms.default_password' => self::SHARED_PASSWORD]);
        putenv('DEFAULT_PASSWORD='.self::SHARED_PASSWORD);
        foreach ([
            'users' => ['name', 'email', 'phone', 'password', 'status', 'access_level', 'regional_code', 'assembly_code', 'division_code', 'olives_card_number', 'erms_id', 'remember_token'],
            'entity_owners' => ['first_name', 'last_name', 'fname', 'lname', 'email', 'gender', 'phone_no_primary', 'tin_no', 'house_no', 'residential_address', 'postal_address', 'digital_address', 'assembly_code', 'regional_code', 'erms_id', 'created_by'],
            'citizens' => ['first_name', 'last_name', 'prefix', 'gender', 'date_of_birth', 'marital_status', 'account_number', 'telephone_number', 'country_of_citizenship', 'customer_type', 'status', 'Ghana_card_number', 'created_by', 'user_id', 'email'],
            'assemblies' => ['assembly_code', 'regional_code'], 'customer_types' => ['name'],
            'roles' => ['name', 'guard_name'], 'permissions' => ['name', 'guard_name'],
            'otps' => ['code', 'type', 'user_id', 'citizen_id', 'bills_id'],
            'service_requests' => ['user_id', 'service_used', 'usage_date', 'service_channel', 'status'],
            'audit_trails' => ['user_id', 'action_performed', 'action_date', 'ip_address', 'device_used', 'remarks'],
        ] as $name => $fields) {
            Schema::create($name, function (Blueprint $table) use ($name, $fields) {
                $table->id();
                $table->uuid('uuid')->nullable();
                foreach ($fields as $field) {
                    $table->string($field)->nullable();
                }
                if ($name === 'users') {
                    $table->boolean('change_password')->default(false);
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
        DB::table('assemblies')->insert(['assembly_code' => 'A', 'regional_code' => 'R']);
    }

    protected function tearDown(): void
    {
        putenv('DEFAULT_PASSWORD');
        parent::tearDown();
    }

    public function test_public_customer_creation_requires_setup_preserves_owner_link_and_never_uses_the_shared_password(): void
    {
        $existing = $this->user();
        $originalPassword = $existing->password;
        $first = $this->postJson('/api/customer/create/front', $this->ownerPayload())->assertOk()
            ->assertJsonPath('message', 'Customer created successfully')->assertJsonPath('setup_required', true)
            ->assertJsonPath('setup_url', route('auth.sendOTP'));
        $owner = Owner::firstOrFail();
        $user = User::where('phone', $owner->phone_no_primary)->firstOrFail();
        $this->assertSame($owner->erms_id, $user->erms_id);
        $this->assertSame($owner->id, $user->customer->id);
        $this->assertSame('A', $owner->assembly_code);
        $this->assertSame('customer', $user->access_level);
        $this->assertSame('Active', $user->status);
        $this->assertFalse(Hash::check(self::SHARED_PASSWORD, $user->password));
        $this->assertStringNotContainsString('password', json_encode($first->json('data')));
        $this->postJson('/api/customer/create/front', $this->ownerPayload('0241234567'))->assertOk();
        $this->assertNotSame($user->password, User::where('phone', '0241234567')->firstOrFail()->password);
        $this->postJson('/api/login', ['username' => $user->phone, 'password' => self::SHARED_PASSWORD])->assertStatus(422);
        $this->assertSame($originalPassword, $existing->fresh()->password);
        $this->assertSame(0, DB::table('personal_access_tokens')->count());
        Bus::assertNothingDispatched();
        Mail::assertNothingSent();
        Notification::assertNothingSent();
    }

    public function test_authenticated_customer_creation_retains_actor_and_business_scope_without_extra_tokens_or_jobs(): void
    {
        $actor = $this->user();
        $this->asToken($actor)->postJson('/api/customer/create', $this->ownerPayload())->assertOk()->assertJsonPath('setup_required', true);
        $owner = Owner::firstOrFail();
        $this->assertSame((string) $actor->id, (string) $owner->created_by);
        $this->assertSame('A', $owner->assembly_code);
        $this->assertFalse(Hash::check(self::SHARED_PASSWORD, User::where('erms_id', $owner->erms_id)->firstOrFail()->password));
        $this->assertSame(1, DB::table('personal_access_tokens')->count());
        Bus::assertNothingDispatched();
    }

    public function test_owner_creation_queues_setup_instructions_after_commit_without_serializing_a_password(): void
    {
        $actor = $this->user();
        $this->asToken($actor)->post('/owner/create', $this->ownerPayload())->assertRedirect(route('owners.index'));
        $owner = Owner::firstOrFail();
        $new = User::where('erms_id', $owner->erms_id)->firstOrFail();
        $this->assertTrue($new->change_password);
        $this->assertFalse(Hash::check(self::SHARED_PASSWORD, $new->password));
        Bus::assertDispatched(SendRegistrationReminder::class, fn ($job) => $job->afterCommit === true);
        $legacyArgument = 'test-only-legacy-plaintext-that-must-not-be-queued';
        $this->assertStringNotContainsString($legacyArgument, serialize(new SendRegistrationReminder($owner, $legacyArgument)));
    }

    public function test_citizen_staff_creation_retains_customer_role_and_profile_relationship(): void
    {
        $actor = $this->user();
        $role = Role::create(['name' => 'customer', 'guard_name' => 'web', 'active' => true]);
        $type = DB::table('customer_types')->insertGetId(['name' => 'Individual']);
        $this->asToken($actor)->post('/citizen', ['first_name' => 'Synthetic', 'last_name' => 'Citizen', 'prefix' => 'Ms',
            'gender' => 'Female', 'date_of_birth' => '1990-01-01', 'marital_status' => 'Single',
            'telephone_number' => '0201234567', 'country_of_citizenship' => 'Ghana', 'customer_type' => (string) $type])
            ->assertRedirect(route('citizens.index'));
        $citizen = Citizen::firstOrFail();
        $this->assertFalse(Hash::check(self::SHARED_PASSWORD, $citizen->user->password));
        $this->assertTrue($citizen->user->hasRole($role));
        $this->assertSame((string) $actor->id, (string) $citizen->created_by);
        Bus::assertDispatched(SendRegistrationReminder::class, fn ($job) => $job->afterCommit === true);
    }

    public function test_customer_import_creates_an_unknown_password_and_preserves_the_imported_profile_link(): void
    {
        $type = DB::table('customer_types')->insertGetId(['name' => 'Individual']);
        $profile = (new CustomersImport(7))->model(['first_name' => 'Synthetic', 'last_name' => 'Import',
            'phone' => '0201234567', 'customer_type' => 'Individual', 'country' => 'Ghana']);
        $profile->save();
        $this->assertSame((string) $type, (string) $profile->customer_type);
        $this->assertSame('0201234567', $profile->user->phone);
        $this->assertFalse(Hash::check(self::SHARED_PASSWORD, $profile->user->password));
        $this->assertSame('customer', $profile->user->access_level);
        Bus::assertNothingDispatched();
    }

    public function test_public_web_registration_preserves_the_users_chosen_password_and_normal_role(): void
    {
        $role = Role::create(['name' => 'customer', 'guard_name' => 'web', 'active' => true]);
        $chosen = 'Synthetic-chosen-password-123';
        $this->post('/frontstore', ['registration_type' => 'individual', 'first_name' => 'Synthetic', 'last_name' => 'Self',
            'telephone_number' => '+233201234567', 'password' => $chosen, 'password_confirmation' => $chosen,
            'account_plan' => 'account'])->assertRedirect(route('auth.index'));
        $citizen = Citizen::firstOrFail();
        $this->assertTrue(Hash::check($chosen, $citizen->user->password));
        $this->assertTrue($citizen->user->hasRole($role));
        Bus::assertDispatched(SendRegistrationReminder::class, fn ($job) => $job->afterCommit === true);
        $this->assertSame(0, DB::table('personal_access_tokens')->count());
    }

    public function test_registration_reminder_handles_both_profile_shapes_and_ignores_old_queued_passwords(): void
    {
        $messages = [];
        Sms::shouldReceive('send')->twice()->withArgs(function ($message, $phone, $sender) use (&$messages) {
            $messages[] = $message;
            $this->assertSame('+233201234567', $phone);
            $this->assertStringContainsString(route('auth.sendOTP'), $message);
            $this->assertStringNotContainsString('legacy-queued-secret', $message);
            $this->assertStringNotContainsString('Your Password is:', $message);

            return true;
        });
        foreach ([new Owner(['fname' => 'Owner', 'erms_id' => 'ERMS123456', 'phone_no_primary' => '0201234567']),
            new Citizen(['first_name' => 'Citizen', 'account_number' => 'ERMS654321', 'telephone_number' => '0201234567'])] as $profile) {
            $job = new SendRegistrationReminder($profile);
            (new \ReflectionProperty($job, 'password'))->setValue($job, 'legacy-queued-secret');
            $job->handle();
        }
        $this->assertStringContainsString('ERMS123456', $messages[0]);
        $this->assertStringContainsString('ERMS654321', $messages[1]);
    }

    public function test_existing_phone_otp_flow_sets_a_new_password_and_clears_only_that_accounts_setup_flag(): void
    {
        $this->postJson('/api/customer/create/front', $this->ownerPayload())->assertOk();
        $user = User::firstOrFail();
        $user->forceFill(['change_password' => true])->save();
        $existing = $this->user();
        $originalPassword = $existing->password;
        Sms::shouldReceive('send')->once()->andReturnNull();
        (new SendPasswordChangeOTPSMS($user))->handle();
        $code = OTP::where('user_id', $user->id)->where('type', 'password')->firstOrFail();
        $chosen = 'New-synthetic-setup-password-123';
        $this->postJson('/api/change/password', ['code' => $code->code, 'password' => $chosen,
            'password_confirmation' => $chosen])->assertOk();
        $this->assertTrue(Hash::check($chosen, $user->fresh()->password));
        $this->assertFalse($user->fresh()->change_password);
        $this->assertSame($originalPassword, $existing->fresh()->password);
        $this->assertSame(0, OTP::where('user_id', $user->id)->count());
        $this->postJson('/api/change/password', ['code' => $code->code, 'password' => $chosen,
            'password_confirmation' => $chosen])->assertStatus(422);
        $this->postJson('/api/login', ['username' => $user->phone, 'password' => $chosen])->assertOk()
            ->assertJsonPath('data.must_change_password', false);
    }

    public function test_setup_page_explains_new_and_existing_account_passwords_using_the_existing_form(): void
    {
        $this->get('/send/otp')->assertOk()->assertSee('Set or reset your password')->assertSee(route('auth.sendUserOTP'));
        Bus::assertNothingDispatched();
        Mail::assertNothingSent();
    }

    private function user(): User
    {
        return User::create(['name' => 'Existing Synthetic Actor', 'phone' => '0271234567', 'email' => 'actor@example.test',
            'password' => Hash::make('Existing-only-password'), 'access_level' => 'Assembly_Administrator',
            'assembly_code' => 'A', 'regional_code' => 'R', 'status' => 'Active']);
    }

    private function asToken(User $user): static
    {
        return $this->withToken($user->createToken('synthetic-test')->plainTextToken);
    }

    private function ownerPayload(string $phone = '0201234567'): array
    {
        return ['first_name' => 'Synthetic', 'last_name' => 'Account', 'phone_no_primary' => $phone, 'gender' => 'M', 'assembly_code' => 'A'];
    }
}
