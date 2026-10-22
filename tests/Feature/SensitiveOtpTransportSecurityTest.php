<?php

namespace Tests\Feature;

use AlhajiAki\Sms\Sms;
use App\Actions\SMS\SendSMS;
use App\Jobs\OTP\SendOTPBillSMS;
use App\Jobs\OTP\SendOTPSMS;
use App\Jobs\OTP\SendPasswordChangeOTPSMS;
use App\Models\OTP;
use App\Models\User;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Bus;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Schema;
use Illuminate\Validation\ValidationException;
use Mockery;
use Psr\Log\LoggerInterface;
use Tests\TestCase;

class SensitiveOtpTransportSecurityTest extends TestCase
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
        config(['queue.default' => 'sync', 'sms.from' => 'Synthetic', 'sms.default' => 'failover',
            'sms.senders.mnotify' => ['sender' => 'mnotify', 'api_key' => 'synthetic-mnotify-key'],
            'sms.senders.pywe' => ['sender' => 'pywe', 'public_key' => 'synthetic-public-key', 'secret_key' => 'synthetic-secret-key']]);
        Sms::forgetSenders();
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->nullable();
            foreach (['name', 'phone', 'email', 'password', 'olives_card_number', 'status'] as $field) {
                $table->string($field)->nullable();
            }
            $table->timestamps();
        });
        Schema::create('otps', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->nullable();
            foreach (['code', 'type', 'user_id', 'citizen_id', 'bills_id'] as $field) {
                $table->string($field)->nullable();
            }
            $table->timestamps();
        });
    }

    public function test_public_password_setup_rejects_log_array_and_slack_without_invalidating_an_existing_code_or_dispatching(): void
    {
        $user = $this->user();
        $existing = OTP::create(['user_id' => $user->id, 'type' => 'password', 'code' => '123456']);
        $this->forbidSmsLogging();
        Bus::fake();
        foreach (['log', 'array', 'slack', 'roundrobin'] as $default) {
            config(['sms.default' => $default]);
            $this->postJson('/api/send/otp', ['phone' => $user->phone])->assertStatus(422)->assertJsonValidationErrors('phone');
            $this->assertTrue($existing->fresh()->exists);
        }
        $this->assertSame(1, OTP::count());
        Bus::assertNothingDispatched();
        Http::assertNothingSent();
        Mail::assertNothingSent();
    }

    public function test_partial_credentials_and_sandbox_do_not_count_as_an_sms_delivery_provider(): void
    {
        $user = $this->user();
        config(['sms.senders.mnotify.api_key' => '', 'sms.senders.pywe.secret_key' => '',
            'sms.senders.failover.senders' => ['mnotify', 'pywe', 'arkesel', 'log'],
            'sms.senders.arkesel' => ['sender' => 'arkesel', 'api_key' => 'synthetic-key', 'sandbox' => true]]);
        $this->forbidSmsLogging();
        $this->postJson('/api/send/otp', ['phone' => $user->phone])->assertStatus(422)->assertJsonValidationErrors('phone');
        $this->assertSame(0, OTP::count());
        Http::assertNothingSent();
    }

    public function test_actual_password_setup_uses_network_fallback_and_global_from_for_its_bound_account(): void
    {
        $user = $this->user();
        $other = OTP::create(['user_id' => 900, 'type' => 'password', 'code' => '234567']);
        $this->forbidSmsLogging();
        Http::fake([
            'https://api.mnotify.com/*' => Http::response(['status' => 'failure', 'message' => 'synthetic-private-provider-error'], 400),
            'https://pushr.pywe.org/*' => Http::response(['status' => 'sent']),
        ]);
        $this->postJson('/api/send/otp', ['phone' => $user->phone])->assertOk();
        $code = OTP::where('user_id', $user->id)->firstOrFail();
        Http::assertSentCount(2);
        Http::assertSent(fn ($request) => str_starts_with($request->url(), 'https://pushr.pywe.org/')
            && $request['sender_id'] === 'Synthetic' && $request['recipients'] === ['+233201234567']
            && str_contains($request['message'], $code->code));
        $this->assertTrue($other->fresh()->exists);
        $this->assertSame('password', $code->type);
        $this->assertSame((string) $user->id, $code->user_id);
    }

    public function test_network_failures_return_only_a_safe_error_and_delete_only_the_newly_created_code(): void
    {
        $user = $this->user();
        $this->forbidSmsLogging();
        $concurrent = null;
        $sentCode = null;
        Http::fake(function ($request) use ($user, &$concurrent, &$sentCode) {
            preg_match('/\d{6}/', $request['message'], $matches);
            $sentCode = $matches[0];
            $concurrent ??= OTP::create(['user_id' => $user->id, 'type' => 'password', 'code' => '345678']);

            return Http::response(['status' => 'failed', 'message' => 'synthetic-secret-key '.$request['message']], 500);
        });
        $response = $this->postJson('/api/send/otp', ['phone' => $user->phone])->assertStatus(422)->assertJsonValidationErrors('phone');
        $response->assertDontSee('synthetic-secret-key')->assertDontSee($sentCode);
        $this->assertSame(1, OTP::count());
        $this->assertTrue($concurrent->fresh()->exists);
        Http::assertSentCount(2);
    }

    public function test_web_setup_uses_the_same_fail_closed_contract_without_falsely_redirecting_to_code_entry(): void
    {
        $user = $this->user();
        config(['sms.default' => 'log']);
        $this->forbidSmsLogging();
        $this->from('/send/otp')->post('/send/otp', ['phone' => $user->phone])->assertRedirect('/send/otp')->assertSessionHasErrors('phone');
        $this->assertSame(0, OTP::count());
        Http::assertNothingSent();
    }

    public function test_direct_network_sender_uses_its_existing_sender_id_when_global_from_is_empty(): void
    {
        config(['sms.default' => 'mnotify', 'sms.from' => '', 'sms.senders.mnotify.sender_id' => 'ExistingSender']);
        Http::fake(['https://api.mnotify.com/*' => Http::response(['status' => 'success'])]);
        (new SendPasswordChangeOTPSMS($this->user()))->handle();
        $code = OTP::firstOrFail();
        Http::assertSent(fn ($request) => $request['sender'] === 'ExistingSender' && str_contains($request['message'], $code->code));
        $this->assertSame(1, OTP::count());
    }

    public function test_citizen_activation_failure_does_not_leave_a_new_undelivered_code(): void
    {
        $this->forbidSmsLogging();
        Http::fake(['*' => Http::response(['status' => 'failed'], 500)]);
        $other = OTP::create(['citizen_id' => 92, 'code' => '456789']);
        try {
            (new SendOTPSMS((object) ['id' => 91, 'telephone_number' => '0201234567']))->handle();
            $this->fail('A network failure must not report activation delivery.');
        } catch (ValidationException $exception) {
            $this->assertNull($exception->getPrevious());
        }
        $this->assertSame(1, OTP::count());
        $this->assertTrue($other->fresh()->exists);
    }

    public function test_bill_activation_job_cannot_fall_back_to_logging_a_sensitive_code(): void
    {
        config(['sms.default' => 'log']);
        $this->forbidSmsLogging();
        $otp = (object) ['code' => '567890', 'bill' => (object) ['business_id' => null,
            'property' => (object) ['customer' => (object) ['phone_no_primary' => '0201234567']]]];
        $this->expectException(ValidationException::class);
        (new SendOTPBillSMS($otp))->handle();
    }

    public function test_citizen_activation_skips_incomplete_provider_credentials_and_uses_the_configured_network_fallback(): void
    {
        config(['sms.senders.mnotify.api_key' => '']);
        $this->forbidSmsLogging();
        Http::fake(['https://pushr.pywe.org/*' => Http::response(['status' => 'sent'])]);
        (new SendOTPSMS((object) ['id' => 91, 'telephone_number' => '0201234567']))->handle();
        Http::assertSentCount(1);
        $code = OTP::firstOrFail();
        $this->assertSame('91', $code->citizen_id);
        Http::assertSent(fn ($request) => str_contains($request['message'], $code->code));
    }

    public function test_normal_notification_log_transport_remains_available(): void
    {
        config(['sms.default' => 'log']);
        $logger = Mockery::mock(LoggerInterface::class);
        $logger->shouldReceive('debug')->once()->withArgs(fn ($message) => str_contains($message, 'Ordinary synthetic notification'));
        app()->instance(LoggerInterface::class, $logger);
        SendSMS::sendSMS('0201234567', 'Ordinary synthetic notification');
        Http::assertNothingSent();
    }

    private function forbidSmsLogging(): void
    {
        $logger = Mockery::mock(LoggerInterface::class);
        $logger->shouldNotReceive('debug');
        app()->instance(LoggerInterface::class, $logger);
    }

    private function user(): User
    {
        return User::create(['name' => 'Synthetic OTP Account', 'phone' => '0201234567',
            'email' => 'synthetic@example.test', 'password' => Hash::make('synthetic-test-password'), 'status' => 'Active']);
    }
}
