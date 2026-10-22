<?php

namespace Tests\Feature;

use App\Jobs\Payment\SendPaymentSMS;
use App\Models\Bill;
use App\Models\Owner;
use App\Models\Payment;
use App\Models\Permission;
use App\Models\Role;
use App\Models\User;
use App\Support\PaymentAmounts;
use Illuminate\Contracts\Console\Kernel;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Foundation\Application;
use Illuminate\Http\UploadedFile;
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

class PaymentMutationSecurityTest extends TestCase
{
    public function createApplication(): Application
    {
        $app = require __DIR__.'/../../bootstrap/app.php';
        $app->beforeBootstrapping(\Illuminate\Foundation\Bootstrap\RegisterProviders::class, function ($app) {
            if (! $app->environment('testing') || $app['config']->get('database.default') !== 'sqlite'
                || $app['config']->get('database.connections.sqlite.database') !== ':memory:') {
                throw new \RuntimeException('Payment security tests require private SQLite memory isolation');
            }
            foreach (array_keys($app['config']->get('database.connections', [])) as $name) {
                $app['config']->set('database.connections.'.$name, ['driver' => 'sqlite', 'database' => ':memory:', 'prefix' => '']);
            }
            foreach (['mail.default', 'mail.driver', 'cache.default', 'session.driver'] as $key) {
                $app['config']->set($key, 'array');
            }
            Http::preventStrayRequests();
        });
        $app->make(Kernel::class)->bootstrap();

        return $app;
    }

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
        config(['services.paystack.secret_key' => 'synthetic-test-key-not-a-provider-credential',
            'services.paystack.url' => 'https://api.paystack.co']);
        foreach ([
            'users' => ['name', 'email', 'phone', 'password', 'status', 'access_level', 'regional_code', 'assembly_code', 'division_code', 'olives_card_number', 'erms_id', 'remember_token'],
            'entity_owners' => ['user_id', 'fname', 'lname', 'erms_id', 'assembly_code', 'regional_code', 'phone_no_primary'],
            'assemblies' => ['assembly_code', 'regional_code', 'name'],
            'properties' => ['owner_id', 'assembly_code'], 'businesses' => ['owner_id', 'assembly_code'],
            'bills' => ['bills_id', 'property_id', 'business_id', 'assembly_code', 'amount'],
            'payments' => ['bills_id', 'assembly_code', 'amount', 'transaction_status', 'transaction_id', 'payment_mode', 'payment_source', 'created_by', 'receipt', 'phone', 'network', 'prompt', 'authorization_url'],
            'service_requests' => ['user_id', 'service_used', 'usage_date', 'service_channel', 'status'],
            'audit_trails' => ['user_id', 'action_performed', 'action_date', 'ip_address', 'device_used', 'remarks'],
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
        DB::table('assemblies')->insert([['assembly_code' => 'A', 'regional_code' => 'R', 'name' => 'Synthetic A'],
            ['assembly_code' => 'B', 'regional_code' => 'S', 'name' => 'Synthetic B'],
            ['assembly_code' => 'C', 'regional_code' => 'R', 'name' => 'Synthetic C']]);
        app(PermissionRegistrar::class)->forgetCachedPermissions();
    }

    public function test_unsigned_badly_signed_and_reserialized_webhooks_never_credit_a_payment(): void
    {
        $payment = $this->payment();
        $payload = $this->evidence($payment);
        $body = json_encode($payload, JSON_THROW_ON_ERROR);
        $this->rawWebhook($body, null)->assertForbidden();
        $this->rawWebhook($body, str_repeat('0', 128))->assertForbidden();
        $this->rawWebhook(json_encode($payload, JSON_PRETTY_PRINT), $this->signature($body))->assertForbidden();
        config(['services.paystack.secret_key' => '']);
        $this->rawWebhook($body, hash_hmac('sha512', $body, ''))->assertForbidden();
        $this->assertSame('Pending', $payment->fresh()->transaction_status);
        Http::assertNothingSent();
        Bus::assertNothingDispatched();
    }

    public function test_signed_provider_success_is_exact_and_idempotent_without_terminal_downgrade(): void
    {
        $payment = $this->payment();
        $this->webhook($this->evidence($payment))->assertOk();
        $first = $payment->fresh();
        $this->assertSame('Success', $first->transaction_status);
        $this->assertSame('mobile_money', $first->payment_mode);
        $updated = $first->updated_at->toISOString();
        $this->travel(5)->minutes();
        $this->webhook($this->evidence($payment))->assertOk();
        $this->assertSame($updated, $payment->fresh()->updated_at->toISOString());
        $bad = $this->evidence($payment);
        $bad['data']['status'] = 'failed';
        $this->webhook($bad)->assertStatus(422);
        $this->assertSame('Success', $payment->fresh()->transaction_status);
        $this->assertSame(12.34, (float) $payment->bill->total_payments_sum);
        Http::assertNothingSent();
    }

    public function test_missing_nonstring_and_whitespace_provider_keys_fail_closed_without_mutation_or_provider_calls(): void
    {
        $actor = $this->user('customer');
        $bill = $this->bill('A', $actor);
        $payment = $this->payment(['bills_id' => $bill->bills_id, 'created_by' => $actor->id]);
        $body = json_encode($this->evidence($payment), JSON_THROW_ON_ERROR);
        foreach ([null, false, ['invalid-config-type'], '', '   ', "\t\r\n"] as $invalid) {
            config(['services.paystack.secret_key' => $invalid]);
            $this->rawWebhook($body, str_repeat('0', 128))->assertForbidden();
            $this->token($actor)->postJson('/api/payment/create', $this->online($bill))->assertStatus(503);
            $this->token($actor)->postJson('/payment/create', $this->online($bill))->assertStatus(503);
            $this->token($actor)->postJson('/api/payment/confirm', ['payment_id' => $payment->id, 'otp' => '123456'])->assertStatus(503);
            $this->get('/payment/verify?reference='.$payment->transaction_id)->assertStatus(503);
            $this->get('/payment/public/verify?reference='.$payment->transaction_id)->assertStatus(503);
            $this->assertSame(1, Payment::count());
            $this->assertSame('Pending', $payment->fresh()->transaction_status);
        }
        Http::assertNothingSent();
        Bus::assertNothingDispatched();
    }

    public function test_signed_events_with_wrong_amount_currency_status_or_ambiguous_reference_cannot_settle(): void
    {
        $payment = $this->payment();
        foreach ([['amount' => 1233], ['amount' => 1235], ['amount' => '1234'], ['currency' => 'USD'],
            ['currency' => 'ghs'], ['status' => 'failed'], ['status' => 'pending']] as $change) {
            $event = $this->evidence($payment);
            $event['data'] = array_replace($event['data'], $change);
            $this->webhook($event)->assertStatus(422);
            $this->assertSame('Pending', $payment->fresh()->transaction_status);
        }
        $other = $this->payment();
        $other->update(['transaction_id' => $payment->transaction_id]);
        $this->webhook($this->evidence($payment))->assertStatus(422);
        $this->assertSame('Pending', $payment->fresh()->transaction_status);
    }

    public function test_webhook_ignores_other_signed_events_and_rejects_invalid_json_after_authentication(): void
    {
        $payment = $this->payment();
        $event = $this->evidence($payment);
        $event['event'] = 'charge.failed';
        $this->webhook($event)->assertOk();
        $event = $this->evidence($payment);
        $event['data']['reference'] = 'unknown-other-integration';
        $this->webhook($event)->assertOk();
        $this->rawWebhook('{', $this->signature('{'))->assertStatus(400);
        $this->assertSame('Pending', $payment->fresh()->transaction_status);
    }

    public function test_online_evidence_cannot_credit_manual_rows_or_cross_assembly_ledger_rows(): void
    {
        $payment = $this->payment(['payment_mode' => 'cash']);
        $this->webhook($this->evidence($payment))->assertStatus(422);
        $payment->update(['payment_mode' => 'momo', 'assembly_code' => 'B']);
        $this->webhook($this->evidence($payment))->assertStatus(422);
        $this->assertSame('Pending', $payment->fresh()->transaction_status);
    }

    public function test_low_privilege_or_inactive_actors_cannot_create_cash_on_either_route(): void
    {
        $actor = $this->user('customer');
        $bill = $this->bill('A');
        foreach (['/api/payment/create', '/payment/create'] as $path) {
            $this->token($actor)->postJson($path, ['bills_id' => $bill->bills_id, 'payment_mode' => 'cash', 'amount' => '12.34'])->assertForbidden();
        }
        $this->grant($actor);
        $actor->update(['status' => 'Inactive']);
        $this->token($actor)->postJson('/api/payment/create', ['bills_id' => $bill->bills_id, 'payment_mode' => 'cash', 'amount' => '12.34'])->assertForbidden();
        $this->assertSame(0, Payment::count());
        Http::assertNothingSent();
    }

    public function test_disabled_role_cash_grants_remain_rejected(): void
    {
        $actor = $this->user('Assembly_Administrator', 'A', 'R');
        $role = Role::create(['name' => 'Synthetic disabled payment role', 'guard_name' => 'web', 'active' => false]);
        $role->givePermissionTo(Permission::findOrCreate('payments.create', 'web'));
        $actor->assignRole($role);
        $this->token($actor)->postJson('/api/payment/create', ['bills_id' => $this->bill('A')->bills_id, 'payment_mode' => 'cash', 'amount' => '1.00'])->assertForbidden();
        $this->assertSame(0, Payment::count());
    }

    public function test_cash_creation_uses_bill_scope_and_rejects_forged_assembly_before_any_upload(): void
    {
        $actor = $this->user('Assembly_Administrator', 'A', 'R');
        $this->grant($actor);
        $bill = $this->bill('A');
        $this->token($actor)->post('/api/payment/create', $this->cash($bill, 'B'))->assertSessionHasErrors('assembly_code');
        $this->token($actor)->postJson('/api/payment/create', $this->cash($this->bill('B'), 'B'))->assertForbidden();
        $this->assertSame(0, Payment::count());
        $this->token($actor)->post('/api/payment/create', $this->cash($bill))->assertOk();
        $this->assertSame('A', Payment::firstOrFail()->assembly_code);
        $this->assertSame('12.34', Payment::firstOrFail()->amount);
        $this->assertSame('Success', Payment::firstOrFail()->transaction_status);
        Http::assertNothingSent();
    }

    public function test_web_cash_create_preserves_authorized_manual_business_flow_and_derives_assembly(): void
    {
        $actor = $this->user('Assembly_Administrator', 'A', 'R');
        $this->grant($actor);
        $this->token($actor)->post('/payment/create', $this->cash($this->bill('A')))->assertRedirect(route('payments.index'));
        $this->assertSame('A', Payment::firstOrFail()->assembly_code);
        $this->assertSame('Success', Payment::firstOrFail()->transaction_status);
    }

    public function test_receipt_storage_never_uses_a_client_supplied_executable_suffix(): void
    {
        $actor = $this->user('Assembly_Administrator', 'A', 'R');
        $this->grant($actor);
        foreach (['/api/payment/create', '/payment/create'] as $path) {
            $payload = $this->cash($this->bill('A'));
            $payload['receipt'] = UploadedFile::fake()->image('synthetic-deceptive.php');
            $this->token($actor)->postJson($path, $payload)->assertUnprocessable()->assertJsonValidationErrors('receipt');
            $payload['receipt'] = UploadedFile::fake()->create('synthetic-mismatched.txt', 1, 'application/pdf');
            $response = $this->token($actor)->post($path, $payload);
            $path === '/api/payment/create' ? $response->assertOk() : $response->assertRedirect();
            $this->assertStringEndsWith('.pdf', Payment::latest('id')->firstOrFail()->receipt);
        }
    }

    public function test_regional_and_super_user_scopes_use_authoritative_bill_assembly(): void
    {
        $regional = $this->user('Regional_Administrator', null, 'R');
        $this->grant($regional);
        $this->token($regional)->post('/api/payment/create', $this->cash($this->bill('C')))->assertOk();
        $this->token($regional)->postJson('/payment/create', $this->cash($this->bill('B')))->assertForbidden();
        $global = $this->user('Super_User', 'A', 'R');
        $this->grant($global);
        $this->token($global)->post('/api/payment/create', $this->cash($this->bill('B')))->assertOk();
        $this->assertSame(2, Payment::count());
    }

    public function test_money_rejects_nonpositive_extra_precision_exponents_and_overflow_on_create(): void
    {
        $actor = $this->user('Assembly_Administrator', 'A', 'R');
        $this->grant($actor);
        foreach (['0', '-1', '0.001', '12.345', '1e2', '99999999999999999999999999'] as $amount) {
            $payload = $this->cash($this->bill('A'));
            $payload['amount'] = $amount;
            $this->token($actor)->postJson('/api/payment/create', $payload)->assertUnprocessable()->assertJsonValidationErrors('amount');
        }
        $this->assertSame(0, Payment::count());
        $this->assertSame(1, PaymentAmounts::minor('0.01'));
        $this->assertSame(1234, PaymentAmounts::minor('12.34'));
        $this->assertSame(1234, PaymentAmounts::minor('12.3400'));
    }

    public function test_own_online_payer_is_bound_to_asset_identity_and_exact_minor_charge(): void
    {
        $actor = $this->user('customer');
        $bill = $this->bill('B', $actor);
        Http::fake(['api.paystack.co/charge' => fn ($request) => Http::response(['status' => true,
            'data' => ['reference' => $request['reference'], 'display_text' => 'Synthetic OTP prompt', 'status' => 'send_otp']])]);
        $payload = $this->online($bill);
        $payload['amount'] = '0.29';
        $this->token($actor)->postJson('/api/payment/create', $payload)->assertOk()->assertJsonPath('data.send_otp', true);
        $payment = Payment::firstOrFail();
        $this->assertSame('B', $payment->assembly_code);
        $this->assertSame('Pending', $payment->transaction_status);
        Http::assertSent(fn ($request) => $request['amount'] === 29 && $request['currency'] === 'GHS'
            && $request['reference'] === $payment->transaction_id);
        $foreign = $this->bill('B', $this->user('customer'));
        $this->token($actor)->postJson('/api/payment/create', $this->online($foreign))->assertForbidden();
        $this->assertSame(1, Payment::count());
    }

    public function test_web_online_initialization_preserves_own_payer_and_exact_minor_reference_contract(): void
    {
        $actor = $this->user('customer');
        $bill = $this->bill('B', $actor);
        $first = true;
        $url = 'https://checkout.paystack.co/synthetic-not-a-real-payment';
        Http::fake(['api.paystack.co/transaction/initialize' => function ($request) use (&$first, $url) {
            $reference = $first ? $request['reference'] : 'unrelated-reference';
            $first = false;
            return Http::response(['status' => true, 'data' => ['reference' => $reference, 'authorization_url' => $url]]);
        }]);
        $payload = $this->online($bill);
        $payload['amount'] = '0.29';
        $this->token($actor)->post('/payment/create', $payload)->assertRedirect($url);
        $payment = Payment::firstOrFail();
        $this->assertSame('0.29', $payment->amount);
        $this->assertSame('B', $payment->assembly_code);
        $this->assertSame('Pending', $payment->transaction_status);
        Http::assertSent(fn ($request) => $request['amount'] === 29 && $request['currency'] === 'GHS'
            && $request['reference'] === $payment->transaction_id);
        $this->token($actor)->post('/payment/create', $payload)->assertSessionHas('error');
        $this->assertSame(2, Payment::where('transaction_status', 'Pending')->count());
        Http::assertSentCount(2);
    }

    public function test_conflicting_owner_links_and_same_phone_never_grant_online_payment_authority(): void
    {
        $actor = $this->user('customer');
        $bill = $this->bill('A', $actor);
        $owner = $bill->property->customer;
        $other = $this->user('customer');
        // Other suites use reduced owner schemas; force the explicit fixture link
        // instead of depending on Eloquent's process-wide guardable-column cache.
        $owner->forceFill(['user_id' => $other->id])->save();
        $this->assertNotSame((string) $actor->id, (string) $other->id);
        $this->assertSame((string) $other->id, (string) $owner->fresh()->user_id);
        $this->assertFalse(\App\Support\PaymentRecordAccess::ownBill($actor, $bill->fresh()));
        $this->assertFalse(\App\Support\PaymentRecordAccess::scopedStaff($actor, $bill->fresh()));
        $this->token($actor)->postJson('/api/payment/create', $this->online($bill))->assertForbidden();
        $foreign = $this->bill('B', $this->user('customer'));
        $foreign->property->customer->update(['phone_no_primary' => $actor->phone]);
        $this->token($actor)->postJson('/api/payment/create', $this->online($foreign))->assertForbidden();
        Http::assertNothingSent();
        $this->assertSame(0, Payment::count());
    }

    public function test_business_payer_identity_works_but_ambiguous_or_cross_assembly_assets_fail_closed(): void
    {
        $actor = $this->user('customer');
        $bill = $this->bill('C', $actor);
        $property = $bill->property_id;
        $business = (string) Str::uuid();
        DB::table('businesses')->insert(['uuid' => $business, 'owner_id' => $actor->erms_id, 'assembly_code' => 'C']);
        $bill->update(['property_id' => null, 'business_id' => $business]);
        Http::fake(['api.paystack.co/charge' => fn ($request) => Http::response(['status' => true,
            'data' => ['reference' => $request['reference'], 'status' => 'send_otp', 'display_text' => 'Synthetic prompt']])]);
        $this->token($actor)->postJson('/api/payment/create', $this->online($bill))->assertOk();
        DB::table('businesses')->where('uuid', $business)->update(['assembly_code' => 'B']);
        $this->token($actor)->postJson('/api/payment/create', $this->online($bill))->assertForbidden();
        $bill->update(['property_id' => $property]);
        $this->token($actor)->postJson('/api/payment/create', $this->online($bill))->assertForbidden();
        $this->assertSame(1, Payment::count());
        Http::assertSentCount(1);
    }

    public function test_confirmation_rejects_foreign_cash_completed_and_mismatched_assembly_before_provider_call(): void
    {
        $actor = $this->user('customer');
        $foreign = $this->payment();
        $this->token($actor)->postJson('/api/payment/confirm', ['payment_id' => $foreign->id, 'otp' => '123456'])->assertForbidden();
        $bill = $this->bill('A', $actor);
        foreach ([['payment_mode' => 'cash'], ['transaction_status' => 'Success'], ['transaction_status' => 'Failed'], ['assembly_code' => 'B']] as $change) {
            $payment = $this->payment(array_merge(['bills_id' => $bill->bills_id, 'assembly_code' => 'A', 'created_by' => $actor->id], $change));
            $this->token($actor)->postJson('/api/payment/confirm', ['payment_id' => $payment->id, 'otp' => '123456'])->assertStatus(isset($change['assembly_code']) ? 403 : 422);
        }
        Http::assertNothingSent();
    }

    public function test_confirmation_preserves_provider_otp_check_and_never_rebinds_reference_or_credits_it(): void
    {
        $actor = $this->user('customer');
        $bill = $this->bill('A', $actor);
        $payment = $this->payment(['bills_id' => $bill->bills_id, 'assembly_code' => 'A', 'created_by' => $actor->id]);
        Http::fake(['api.paystack.co/charge/submit_otp' => Http::sequence()
            ->push(['status' => true, 'data' => ['reference' => 'different-reference', 'display_text' => 'Synthetic prompt']])
            ->push(['status' => false], 400)
            ->push(['status' => true, 'data' => ['reference' => $payment->transaction_id, 'display_text' => 'Synthetic verified prompt', 'status' => 'success']])]);
        $payload = ['payment_id' => $payment->id, 'otp' => '123456'];
        $this->token($actor)->postJson('/api/payment/confirm', $payload)->assertStatus(422);
        $this->token($actor)->postJson('/api/payment/confirm', $payload)->assertStatus(422);
        $this->token($actor)->postJson('/api/payment/confirm', $payload)->assertOk();
        $this->assertSame($payment->transaction_id, $payment->fresh()->transaction_id);
        $this->assertSame('Pending', $payment->fresh()->transaction_status);
        $this->assertSame('Synthetic verified prompt', $payment->fresh()->prompt);
        Http::assertSentCount(3);
        Http::assertSent(fn ($request) => $request['otp'] === '123456' && $request['reference'] === $payment->transaction_id);
    }

    public function test_scoped_staff_can_confirm_pending_online_but_not_foreign_assembly(): void
    {
        $actor = $this->user('Assembly_Administrator', 'A', 'R');
        $this->grant($actor);
        $payment = $this->payment();
        Http::fake(['api.paystack.co/charge/submit_otp' => Http::response(['status' => true,
            'data' => ['reference' => $payment->transaction_id, 'display_text' => 'Synthetic prompt', 'status' => 'send_otp']])]);
        $this->token($actor)->postJson('/api/payment/confirm', ['payment_id' => $payment->id, 'otp' => '123456'])->assertOk();
        $foreign = $this->payment(['bills_id' => $this->bill('B')->bills_id, 'assembly_code' => 'B']);
        $this->token($actor)->postJson('/api/payment/confirm', ['payment_id' => $foreign->id, 'otp' => '123456'])->assertForbidden();
        Http::assertSentCount(1);
    }

    public function test_manual_web_completion_requires_cash_permission_scope_and_dispatches_only_once(): void
    {
        $cash = $this->payment(['payment_mode' => 'cash', 'transaction_id' => null]);
        $low = $this->user('customer');
        $this->token($low)->postJson('/payment/make/payment', ['payment_id' => $cash->id])->assertForbidden();
        $staff = $this->user('Assembly_Administrator', 'A', 'R');
        $this->grant($staff);
        $online = $this->payment();
        $this->token($staff)->postJson('/payment/make/payment', ['payment_id' => $online->id])->assertStatus(422);
        $this->assertSame('Pending', $online->fresh()->transaction_status);
        $foreign = $this->payment(['bills_id' => $this->bill('B')->bills_id, 'assembly_code' => 'B', 'payment_mode' => 'cash']);
        $this->token($staff)->postJson('/payment/make/payment', ['payment_id' => $foreign->id])->assertForbidden();
        $this->token($staff)->postJson('/payment/make/payment', ['payment_id' => $cash->id])->assertOk();
        $this->token($staff)->postJson('/payment/make/payment', ['payment_id' => $cash->id])->assertOk();
        $this->assertSame('Success', $cash->fresh()->transaction_status);
        Bus::assertDispatchedTimes(SendPaymentSMS::class, 1);
    }

    public function test_both_public_provider_verified_get_paths_require_exact_minor_currency_reference_and_success(): void
    {
        $responses = [];
        Http::fake(['api.paystack.co/transaction/verify/*' => function ($request) use (&$responses) {
            $reference = rawurldecode(basename(parse_url($request->url(), PHP_URL_PATH)));
            $this->assertArrayHasKey($reference, $responses);
            return Http::response(['status' => true, 'data' => $responses[$reference]]);
        }]);
        foreach (['/payment/verify', '/payment/public/verify'] as $path) {
            foreach ([['amount' => 1233], ['currency' => 'USD'], ['reference' => 'other'], ['status' => 'failed']] as $change) {
                $payment = $this->payment();
                $data = array_replace($this->evidence($payment)['data'], $change);
                $responses[$payment->transaction_id] = $data;
                $response = $this->get($path.'?reference='.$payment->transaction_id);
                $path === '/payment/verify' ? $response->assertRedirect(route('payments.index')) : $response->assertNotFound();
                $this->assertSame('Pending', $payment->fresh()->transaction_status);
            }
            $payment = $this->payment();
            $responses[$payment->transaction_id] = $this->evidence($payment)['data'];
            $this->get($path.'?reference='.$payment->transaction_id)->assertRedirect();
            $this->assertSame('Success', $payment->fresh()->transaction_status);
        }
    }

    public function test_legacy_callback_stays_a_financial_noop(): void
    {
        $payment = $this->payment();
        $this->postJson('/api/payment/callback', ['transactionid' => $payment->transaction_id, 'status' => 'success'])->assertOk();
        $this->assertSame('Pending', $payment->fresh()->transaction_status);
    }

    private function user(string $level, ?string $assembly = null, ?string $region = null): User
    {
        return User::create(['name' => 'Synthetic Payment Actor', 'email' => Str::uuid().'@example.test', 'phone' => '027'.random_int(1000000, 9999999),
            'password' => Hash::make('Synthetic-unused-password'), 'status' => 'Active', 'access_level' => $level,
            'assembly_code' => $assembly, 'regional_code' => $region]);
    }

    private function bill(string $assembly, ?User $payer = null): Bill
    {
        $property = null;
        if ($payer) {
            $owner = Owner::forceCreate(['user_id' => $payer->id, 'erms_id' => 'SYNTHETIC-'.Str::uuid(), 'assembly_code' => $assembly]);
            $payer->update(['erms_id' => $owner->erms_id]);
            $property = (string) Str::uuid();
            DB::table('properties')->insert(['uuid' => $property, 'owner_id' => $owner->erms_id, 'assembly_code' => $assembly]);
        }

        return Bill::create(['bills_id' => 'SYNTHETIC-BILL-'.Str::uuid(), 'property_id' => $property, 'assembly_code' => $assembly, 'amount' => '100.00']);
    }

    private function payment(array $extra = []): Payment
    {
        return Payment::create(array_merge(['bills_id' => $this->bill('A')->bills_id, 'assembly_code' => 'A', 'amount' => '12.34',
            'transaction_id' => 'SYNTHETIC-REF-'.Str::uuid(), 'transaction_status' => 'Pending', 'payment_mode' => 'momo'], $extra));
    }

    private function grant(User $actor): void
    {
        $actor->givePermissionTo(Permission::findOrCreate('payments.create', 'web'));
    }

    private function token(User $actor): static
    {
        app('auth')->forgetGuards();

        return $this->withToken($actor->createToken('synthetic-payment-test')->plainTextToken);
    }

    private function cash(Bill $bill, ?string $assembly = null): array
    {
        $data = ['bills_id' => $bill->bills_id, 'payment_mode' => 'cash', 'amount' => '12.34',
            'receipt' => UploadedFile::fake()->create('synthetic-receipt.pdf', 1, 'application/pdf')];
        if ($assembly !== null) {
            $data['assembly_code'] = $assembly;
        }

        return $data;
    }

    private function online(Bill $bill): array
    {
        return ['bills_id' => $bill->bills_id, 'payment_mode' => 'momo', 'amount' => '12.34', 'phone' => '0241234567', 'network' => 'MTN'];
    }

    private function evidence(Payment $payment): array
    {
        return ['event' => 'charge.success', 'data' => ['reference' => $payment->transaction_id, 'status' => 'success',
            'amount' => 1234, 'currency' => 'GHS', 'channel' => 'mobile_money']];
    }

    private function signature(string $body): string
    {
        return hash_hmac('sha512', $body, config('services.paystack.secret_key'));
    }

    private function rawWebhook(string $body, ?string $signature)
    {
        $server = ['CONTENT_TYPE' => 'application/json', 'HTTP_ACCEPT' => 'application/json'];
        if ($signature !== null) {
            $server['HTTP_X_PAYSTACK_SIGNATURE'] = $signature;
        }

        return $this->call('POST', '/api/payment/paystack/webhook', [], [], [], $server, $body);
    }

    private function webhook(array $event)
    {
        $body = json_encode($event, JSON_THROW_ON_ERROR);

        return $this->rawWebhook($body, $this->signature($body));
    }
}
