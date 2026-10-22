<?php

namespace Tests\Feature;

use App\Jobs\OTP\SendOTPBillSMS;
use App\Models\Bill;
use App\Models\Business;
use App\Models\OTP;
use App\Models\Owner;
use App\Models\Permission;
use App\Models\Property;
use App\Models\Role;
use App\Models\User;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Schema;
use Illuminate\Validation\ValidationException;
use Tests\TestCase;

class BillOtpSecurityTest extends TestCase
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
        config(['queue.default' => 'sync', 'sms.default' => 'mnotify', 'sms.from' => 'Synthetic',
            'sms.senders.mnotify' => ['sender' => 'mnotify', 'api_key' => 'synthetic-network-key']]);
        foreach ([
            'users' => ['name', 'phone', 'email', 'password', 'access_level', 'status', 'assembly_code', 'regional_code', 'olives_card_number'],
            'entity_owners' => ['user_id', 'first_name', 'last_name', 'fname', 'lname', 'email', 'gender', 'phone_no_primary', 'tin_no', 'house_no', 'residential_address', 'postal_address', 'digital_address', 'assembly_code', 'regional_code', 'erms_id', 'created_by', 'customer_type', 'status'],
            'properties' => ['owner_id', 'assembly_code', 'erms_id', 'account_no', 'digitaladdress', 'streetname', 'location', 'polygon_id'],
            'businesses' => ['owner_id', 'assembly_code', 'polygon_id'],
            'bills' => ['bills_id', 'property_id', 'business_id', 'assembly_code', 'issue_bill', 'issued_by'],
            'otps' => ['user_id', 'citizen_id', 'bills_id', 'code', 'type'],
            'assemblies' => ['assembly_code', 'regional_code'],
            'roles' => ['name', 'guard_name'], 'permissions' => ['name', 'guard_name'],
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
        DB::table('assemblies')->insert([
            ['assembly_code' => 'A', 'regional_code' => 'RA'], ['assembly_code' => 'B', 'regional_code' => 'RB'],
            ['assembly_code' => 'C', 'regional_code' => 'RA'],
        ]);
    }

    public function test_authorized_sender_returns_metadata_without_the_secret_and_delivers_the_typed_bound_challenge(): void
    {
        Http::fake(['https://api.mnotify.com/*' => Http::response(['status' => 'success'])]);
        $bill = $this->bill('A');
        $response = $this->as($this->actor())->postJson('/api/bill/send-otp', ['bills_id' => $bill->bills_id])->assertOk();
        $otp = OTP::firstOrFail();
        $response->assertJsonPath('data.bills_id', $bill->bills_id)->assertJsonMissingPath('data.code')->assertDontSee($otp->code);
        $this->assertSame('bill', $otp->type);
        $this->assertSame($bill->property->customer->erms_id, $otp->citizen_id);
        $this->assertMatchesRegularExpression('/^[1-9][0-9]{5}$/', $otp->code);
        Http::assertSent(fn ($request) => $request['recipient'] === ['+233201234567'] && str_contains($request['message'], $otp->code));
        Mail::assertNothingSent();
        $this->assertSame('No', $bill->fresh()->issue_bill);
    }

    public function test_unprivileged_inactive_and_disabled_role_tokens_cannot_send_or_issue(): void
    {
        $bill = $this->bill('A');
        $code = $this->code($bill);
        $unprivileged = $this->actor(permission: false);
        $inactive = $this->actor();
        $inactive->update(['status' => 'Inactive']);
        $disabledRoleActor = $this->actor(permission: false);
        $role = Role::create(['name' => 'Disabled bill role', 'guard_name' => 'web', 'active' => false]);
        $role->givePermissionTo(Permission::findOrCreate('bills.update', 'web'));
        $disabledRoleActor->assignRole($role);
        foreach ([$unprivileged, $inactive, $disabledRoleActor] as $actor) {
            $this->as($actor)->postJson('/api/bill/send-otp', ['bills_id' => $bill->bills_id])->assertForbidden();
            $this->as($actor)->postJson('/api/bill/issue', ['bills_id' => $bill->bills_id, 'code' => $code->code])->assertForbidden();
        }
        $this->assertTrue($code->fresh()->exists);
        $this->assertSame('No', $bill->fresh()->issue_bill);
        Http::assertNothingSent();
    }

    public function test_assembly_and_regional_scopes_are_authoritative_and_super_user_retains_global_scope(): void
    {
        $foreign = $this->bill('B');
        $regional = $this->bill('C');
        $foreignCode = $this->code($foreign);
        $regionalCode = $this->code($regional, '234567');
        $this->as($this->actor())->postJson('/api/bill/send-otp', ['bills_id' => $foreign->bills_id, 'assembly_code' => 'A'])->assertNotFound();
        $this->as($this->actor())->postJson('/api/bill/issue', ['bills_id' => $foreign->bills_id, 'code' => $foreignCode->code])->assertNotFound();
        $national = $this->actor(assembly: null, region: 'RA');
        $this->as($national)->postJson('/api/bill/issue', ['bills_id' => $foreign->bills_id, 'code' => $foreignCode->code])->assertNotFound();
        $this->as($national)->postJson('/api/bill/issue', ['bills_id' => $regional->bills_id, 'code' => $regionalCode->code])->assertOk();
        $global = $this->actor();
        $global->update(['access_level' => 'Super_User']);
        $this->as($global)->postJson('/api/bill/issue', ['bills_id' => $foreign->bills_id, 'code' => $foreignCode->code])->assertOk();
        $this->assertSame((string) $global->id, $foreign->fresh()->issued_by);
    }

    public function test_password_citizen_untyped_and_user_bound_codes_cannot_authorize_bill_issuance(): void
    {
        $bill = $this->bill('A');
        $actor = $this->actor();
        foreach (['password', 'citizen', null, 'bill'] as $index => $type) {
            $otp = $this->code($bill, (string) (400000 + $index), $type);
            if ($type === 'bill') {
                $otp->update(['user_id' => $actor->id]);
            }
            $this->as($actor)->postJson('/api/bill/issue', ['bills_id' => $bill->bills_id, 'code' => $otp->code])->assertStatus(422);
            $this->assertTrue($otp->fresh()->exists);
            $this->assertSame('No', $bill->fresh()->issue_bill);
        }
    }

    public function test_a_code_for_another_bill_or_previous_owner_is_rejected_without_consuming_either(): void
    {
        $bill = $this->bill('A');
        $other = $this->bill('A');
        $otherCode = $this->code($other);
        $wrongOwner = $this->code($bill, '234567');
        $wrongOwner->update(['citizen_id' => 'ANOTHER-OWNER']);
        $actor = $this->actor();
        foreach ([$otherCode, $wrongOwner] as $otp) {
            $this->as($actor)->postJson('/api/bill/issue', ['bills_id' => $bill->bills_id, 'code' => $otp->code])->assertStatus(422);
            $this->assertTrue($otp->fresh()->exists);
        }
        $this->assertSame('No', $bill->fresh()->issue_bill);
        $this->assertSame('No', $other->fresh()->issue_bill);
    }

    public function test_expired_future_and_colliding_active_bill_codes_fail_closed(): void
    {
        $bill = $this->bill('A');
        $actor = $this->actor();
        $expired = $this->code($bill, '123456');
        $expired->created_at = now()->subMinutes(11);
        $expired->save();
        $future = $this->code($bill, '234567');
        $future->created_at = now()->addMinute();
        $future->save();
        $collision = $this->code($bill, '345678');
        $other = $this->code($this->bill('A'), '345678');
        foreach ([$expired, $future, $collision] as $otp) {
            $this->as($actor)->postJson('/api/bill/issue', ['bills_id' => $bill->bills_id, 'code' => $otp->code])->assertStatus(422);
            $this->assertTrue($otp->fresh()->exists);
        }
        $this->assertTrue($other->fresh()->exists);
        $this->assertSame('No', $bill->fresh()->issue_bill);
    }

    public function test_valid_bill_code_is_consumed_once_and_other_bill_password_and_citizen_rows_are_preserved(): void
    {
        $bill = $this->bill('A');
        $actor = $this->actor();
        $code = $this->code($bill);
        $password = $this->code($bill, '123456', 'password');
        $citizen = $this->code($bill, '456789', 'citizen');
        $other = $this->code($this->bill('A'), '234567');
        $this->as($actor)->postJson('/api/bill/issue', ['bills_id' => $bill->bills_id, 'code' => $code->code])->assertOk();
        $this->assertNull($code->fresh());
        $this->assertSame('Yes', $bill->fresh()->issue_bill);
        $this->assertSame((string) $actor->id, $bill->fresh()->issued_by);
        $this->as($actor)->postJson('/api/bill/issue', ['bills_id' => $bill->bills_id, 'code' => $code->code])->assertStatus(422);
        foreach ([$password, $citizen, $other] as $unrelated) {
            $this->assertTrue($unrelated->fresh()->exists);
        }
    }

    public function test_bill_update_failure_rolls_back_the_code_consumption_in_the_same_transaction(): void
    {
        $bill = $this->bill('A');
        $otp = $this->code($bill);
        $event = 'eloquent.updating: '.Bill::class;
        Event::listen($event, fn () => throw new \RuntimeException('synthetic-bill-write-failure'));
        $this->withoutExceptionHandling();
        try {
            $this->as($this->actor())->postJson('/api/bill/issue', ['bills_id' => $bill->bills_id, 'code' => $otp->code]);
            $this->fail('The synthetic database failure must propagate.');
        } catch (\RuntimeException $exception) {
            $this->assertSame('synthetic-bill-write-failure', $exception->getMessage());
        } finally {
            Event::forget($event);
        }
        $this->assertNotNull($otp->fresh(), 'A failed bill write must roll back OTP consumption.');
        $this->assertSame('No', $bill->fresh()->issue_bill);
    }

    public function test_failed_delivery_deletes_only_its_created_row_and_preserves_a_concurrent_replacement(): void
    {
        $bill = $this->bill('A');
        $replacement = null;
        Http::fake(function () use ($bill, &$replacement) {
            $replacement = $this->code($bill, '345678');

            return Http::response(['status' => 'failed'], 500);
        });
        $this->as($this->actor())->postJson('/api/bill/send-otp', ['bills_id' => $bill->bills_id])->assertStatus(422);
        $this->assertSame(1, OTP::count());
        $this->assertTrue($replacement->fresh()->exists);
        $this->assertSame('No', $bill->fresh()->issue_bill);
    }

    public function test_failure_cleanup_preserves_the_same_row_if_another_request_has_changed_its_code(): void
    {
        $bill = $this->bill('A');
        Http::fake(function () {
            OTP::query()->update(['code' => '345678']);

            return Http::response(['status' => 'failed'], 500);
        });
        $this->as($this->actor())->postJson('/api/bill/send-otp', ['bills_id' => $bill->bills_id])->assertStatus(422);
        $this->assertSame(1, OTP::count());
        $this->assertSame('345678', OTP::firstOrFail()->code);
    }

    public function test_unavailable_transport_preserves_all_existing_codes_before_any_bill_code_is_replaced(): void
    {
        $bill = $this->bill('A');
        $code = $this->code($bill);
        $password = $this->code($bill, '234567', 'password');
        config(['sms.default' => 'log']);
        $this->as($this->actor())->postJson('/api/bill/send-otp', ['bills_id' => $bill->bills_id])->assertStatus(422);
        $this->assertTrue($code->fresh()->exists);
        $this->assertTrue($password->fresh()->exists);
        Http::assertNothingSent();
    }

    public function test_a_queued_bill_job_cannot_deliver_to_a_changed_owner(): void
    {
        $bill = $this->bill('A');
        $code = $this->code($bill);
        $bill->property->update(['owner_id' => 'DIFFERENT-OWNER']);
        try {
            (new SendOTPBillSMS($code))->handle();
            $this->fail('A stale owner binding must not send an activation code.');
        } catch (ValidationException) {
            $this->assertNull($code->fresh());
        }
        Http::assertNothingSent();
    }

    public function test_serialized_bill_job_keeps_no_plaintext_code_or_model_and_preserves_a_newer_same_row_code(): void
    {
        $bill = $this->bill('A');
        $code = $this->code($bill);
        $payload = serialize(new SendOTPBillSMS($code));
        $this->assertStringNotContainsString('s:6:"'.$code->code.'";', $payload);
        $this->assertStringNotContainsString('i:'.$code->code.';', $payload);
        $this->assertStringNotContainsString(config('app.key'), $payload);
        $this->assertStringNotContainsString('Illuminate\\Contracts\\Database\\ModelIdentifier', $payload);
        $job = unserialize($payload);
        $code->update(['code' => '345678']);
        try {
            $job->handle();
            $this->fail('An older job must not deliver or delete the replacement code.');
        } catch (ValidationException) {
            $this->assertSame('345678', $code->fresh()->code);
        }
        Http::assertNothingSent();
    }

    public function test_guess_limits_are_per_actor_and_bill_and_do_not_block_a_different_valid_bill(): void
    {
        $bill = $this->bill('A');
        $actor = $this->actor();
        $code = $this->code($bill);
        for ($index = 0; $index < 5; $index++) {
            $this->as($actor)->postJson('/api/bill/issue', ['bills_id' => $bill->bills_id, 'code' => (string) (900000 + $index)])->assertStatus(422);
        }
        $this->as($actor)->postJson('/api/bill/issue', ['bills_id' => $bill->bills_id, 'code' => $code->code])->assertStatus(422);
        $this->assertTrue($code->fresh()->exists);
        $other = $this->bill('A');
        $otherCode = $this->code($other, '234567');
        $this->as($actor)->postJson('/api/bill/issue', ['bills_id' => $other->bills_id, 'code' => $otherCode->code])->assertOk();
    }

    public function test_business_bills_keep_the_same_owner_verification_workflow(): void
    {
        Http::fake(['https://api.mnotify.com/*' => Http::response(['status' => 'success'])]);
        $bill = $this->bill('A', business: true);
        $actor = $this->actor();
        $this->as($actor)->postJson('/api/bill/send-otp', ['bills_id' => $bill->bills_id])->assertOk()->assertJsonMissingPath('data.code');
        $code = OTP::firstOrFail();
        $this->assertSame($bill->business->customer->erms_id, $code->citizen_id);
        $this->as($actor)->postJson('/api/bill/issue', ['bills_id' => $bill->bills_id, 'code' => $code->code])->assertOk();
        $this->assertSame('Yes', $bill->fresh()->issue_bill);
    }

    private function actor(?string $assembly = 'A', ?string $region = 'RA', bool $permission = true): User
    {
        $actor = User::create(['name' => 'Synthetic Bill Actor', 'phone' => '0271234567', 'email' => 'actor@example.test',
            'password' => Hash::make('synthetic-test-password'), 'status' => 'Active', 'access_level' => 'Assembly_Agent',
            'assembly_code' => $assembly, 'regional_code' => $region]);
        if ($permission) {
            $actor->givePermissionTo(Permission::findOrCreate('bills.update', 'web'));
        }

        return $actor;
    }

    private function bill(string $assembly, bool $business = false): Bill
    {
        $suffix = (string) (Bill::count() + 1);
        $owner = Owner::create(['erms_id' => 'OWNER-'.$suffix, 'fname' => 'Synthetic', 'lname' => 'Owner', 'phone_no_primary' => '0201234567']);
        $asset = $business ? Business::create(['owner_id' => $owner->erms_id, 'assembly_code' => $assembly])
            : Property::create(['owner_id' => $owner->erms_id, 'assembly_code' => $assembly]);

        return Bill::create(['bills_id' => 'BILL-'.$suffix, $business ? 'business_id' : 'property_id' => $asset->uuid,
            'assembly_code' => $assembly, 'issue_bill' => 'No']);
    }

    private function code(Bill $bill, string $value = '123456', ?string $type = 'bill'): OTP
    {
        $owner = filled($bill->property_id) ? $bill->property->customer : $bill->business->customer;

        return OTP::create(['bills_id' => $bill->bills_id, 'citizen_id' => $owner->erms_id, 'type' => $type, 'code' => $value]);
    }

    private function as(User $actor): static
    {
        app('auth')->forgetGuards();

        return $this->withToken($actor->createToken('Synthetic bill test')->plainTextToken);
    }
}
