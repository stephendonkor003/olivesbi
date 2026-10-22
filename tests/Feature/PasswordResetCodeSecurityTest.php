<?php

namespace Tests\Feature;

use App\Models\OTP;
use App\Models\User;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;
use Tests\TestCase;

class PasswordResetCodeSecurityTest extends TestCase
{
    private ?string $privateSessions = null;

    protected function tearDown(): void
    {
        if ($this->privateSessions !== null) {
            foreach (new \FilesystemIterator($this->privateSessions, \FilesystemIterator::SKIP_DOTS) as $file) {
                unlink($file->getPathname());
            }
            rmdir($this->privateSessions);
            rmdir(dirname($this->privateSessions));
            rmdir(dirname(dirname($this->privateSessions)));
        }
        parent::tearDown();
    }

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
            $table->string('name');
            $table->string('email');
            $table->string('password');
            $table->string('status')->default('Active');
            $table->string('olives_card_number')->nullable();
            $table->rememberToken();
            $table->timestamps();
        });
        Schema::create('otps', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id')->nullable();
            $table->unsignedBigInteger('citizen_id')->nullable();
            $table->string('code');
            $table->string('type');
            $table->timestamps();
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
        Schema::create('sessions', function (Blueprint $table) {
            $table->string('id')->primary();
            $table->unsignedBigInteger('user_id')->nullable();
            $table->string('ip_address')->nullable();
            $table->text('user_agent')->nullable();
            $table->text('payload');
            $table->integer('last_activity');
        });
    }

    public function test_password_code_resets_its_user_once_and_preserves_other_accounts_and_other_otp_types(): void
    {
        $target = $this->user(1);
        $other = $this->user(2);
        $oldOtherPassword = $other->password;
        $otherToken = $other->createToken('Existing other account token')->accessToken;
        $targetToken = $target->createToken('Existing target token')->accessToken;
        $this->code(1, '123456', 'password', now());
        $unrelated = $this->code(2, '234567', 'payment', now());
        $this->postJson('/api/change/password', $this->payload('123456'))->assertOk();
        $this->assertTrue(Hash::check('New-test-password', $target->fresh()->password));
        $this->assertSame($oldOtherPassword, $other->fresh()->password);
        $this->assertDatabaseMissing('personal_access_tokens', ['id' => $targetToken->id]);
        $this->assertDatabaseHas('personal_access_tokens', ['id' => $otherToken->id]);
        $this->assertDatabaseHas('otps', ['id' => $unrelated->id]);
        $this->postJson('/api/change/password', $this->payload('123456'))->assertUnprocessable();
        Mail::assertNothingSent();
        Notification::assertNothingSent();
    }

    public function test_expired_payment_only_inactive_and_ambiguous_codes_cannot_reset_passwords(): void
    {
        $target = $this->user(1);
        $other = $this->user(2);
        $inactive = $this->user(3);
        $inactive->forceFill(['status' => 'Inactive'])->saveQuietly();
        $passwords = [$target->password, $other->password, $inactive->password];
        $this->code(1, '123456', 'password', now()->subMinutes(11));
        $this->code(1, '234567', 'payment', now())->forceFill(['citizen_id' => 2])->saveQuietly();
        $this->code(3, '345678', 'password', now());
        $this->code(1, '456789', 'password', now());
        $this->code(2, '456789', 'password', now());
        foreach (['123456', '234567', '345678', '456789'] as $code) {
            $this->postJson('/api/change/password', $this->payload($code))->assertUnprocessable();
        }
        $this->assertSame($passwords, [$target->fresh()->password, $other->fresh()->password, $inactive->fresh()->password]);
        $this->assertDatabaseCount('otps', 5);
    }

    public function test_web_reset_uses_same_lifecycle_and_deletes_only_target_database_sessions(): void
    {
        $target = $this->user(1);
        $other = $this->user(2);
        $this->code(1, '123456', 'password', now());
        DB::table('sessions')->insert([
            ['id' => 'existing-target-session', 'user_id' => 1, 'payload' => 'preserved-until-reset', 'last_activity' => time()],
            ['id' => 'existing-other-session', 'user_id' => 2, 'payload' => 'preserved', 'last_activity' => time()],
        ]);
        config(['session.driver' => 'database']);
        $this->post('/change/password/front', $this->payload('123456'))->assertRedirect(route('auth.index'));
        $this->assertTrue(Hash::check('New-test-password', $target->fresh()->password));
        $this->assertDatabaseMissing('sessions', ['id' => 'existing-target-session']);
        $this->assertDatabaseHas('sessions', ['id' => 'existing-other-session']);
        $this->assertDatabaseCount('otps', 0);
    }

    public function test_reset_attempts_are_rate_limited_without_sending_messages(): void
    {
        for ($attempt = 0; $attempt < 5; $attempt++) {
            $this->postJson('/api/change/password', $this->payload('999999'))->assertUnprocessable();
        }
        $this->postJson('/api/change/password', $this->payload('999999'))->assertStatus(429);
        Mail::assertNothingSent();
    }

    public function test_legacy_file_sessions_are_deleted_only_for_reset_user_and_late_old_hash_is_rejected(): void
    {
        $target = $this->user(1);
        $other = $this->user(2);
        $oldHash = $target->password;
        $key = Auth::guard('web')->getName();
        $directory = $this->sessionDirectory();
        $targetPath = $directory.'/'.str_repeat('a', 40);
        $otherPath = $directory.'/'.str_repeat('b', 40);
        $guestPath = $directory.'/'.str_repeat('c', 40);
        $invalidPath = $directory.'/'.str_repeat('d', 40);
        file_put_contents($targetPath, serialize([$key => 1]));
        file_put_contents($otherPath, serialize([$key => 2]));
        file_put_contents($guestPath, serialize(['guest_marker' => 'preserved']));
        file_put_contents($invalidPath, 'malformed session stays unchanged');
        $otherBytes = file_get_contents($otherPath);
        $link = $directory.'/'.str_repeat('e', 40);
        $linked = @symlink($otherPath, $link);
        $this->code(1, '123456', 'password', now());
        $this->postJson('/api/change/password', $this->payload('123456'))->assertOk();
        $this->assertFileDoesNotExist($targetPath);
        $this->assertSame($otherBytes, file_get_contents($otherPath));
        $this->assertFileExists($guestPath);
        $this->assertSame('malformed session stays unchanged', file_get_contents($invalidPath));
        if ($linked) {
            $this->assertTrue(is_link($link));
        }

        // A request started before reset may finish after file deletion. Its
        // old password binding must still fail on the next normal web request.
        Route::middleware(['web', 'auth'])->get('/__security/session-check', fn () => 'authenticated');
        $this->app['auth']->forgetGuards();
        $this->actingAs($target->fresh(), 'web')->withSession([$key => 1, 'password_hash_web' => $oldHash])
            ->getJson('/__security/session-check')->assertUnauthorized();
        $this->assertGuest('web');
        $this->app['auth']->forgetGuards();
        $this->actingAs($target->fresh(), 'web')->withSession([$key => 1, 'password_hash_web' => $oldHash])
            ->get('/__security/session-check')->assertRedirect(route('auth.index'));
        $this->app['auth']->forgetGuards();
        $this->actingAs($other, 'web')->withSession([$key => 2, 'password_hash_web' => $other->password])
            ->get('/__security/session-check')->assertOk();
    }

    public function test_encrypted_file_sessions_use_existing_encrypter_and_preserve_other_users(): void
    {
        $this->user(1);
        $this->user(2);
        $key = Auth::guard('web')->getName();
        $directory = $this->sessionDirectory();
        config(['session.encrypt' => true]);
        $targetPath = $directory.'/'.str_repeat('a', 40);
        $otherPath = $directory.'/'.str_repeat('b', 40);
        file_put_contents($targetPath, app('encrypter')->encrypt(serialize([$key => 1])));
        file_put_contents($otherPath, app('encrypter')->encrypt(serialize([$key => 2])));
        $otherBytes = file_get_contents($otherPath);
        $this->code(1, '123456', 'password', now());
        $this->postJson('/api/change/password', $this->payload('123456'))->assertOk();
        $this->assertFileDoesNotExist($targetPath);
        $this->assertSame($otherBytes, file_get_contents($otherPath));
    }

    public function test_oversized_session_scan_fails_closed_without_consuming_code_or_changing_password(): void
    {
        $target = $this->user(1);
        $oldHash = $target->password;
        $directory = $this->sessionDirectory();
        $path = $directory.'/'.str_repeat('a', 40);
        $stream = fopen($path, 'wb');
        ftruncate($stream, 16 * 1024 * 1024 + 1);
        fclose($stream);
        $this->code(1, '123456', 'password', now());
        $this->postJson('/api/change/password', $this->payload('123456'))->assertUnprocessable();
        $this->assertSame($oldHash, $target->fresh()->password);
        $this->assertDatabaseCount('otps', 1);
        $this->assertFileExists($path);
    }

    public function test_busy_session_file_fails_closed_without_changing_password_or_consuming_code(): void
    {
        $target = $this->user(1);
        $oldHash = $target->password;
        $directory = $this->sessionDirectory();
        $path = $directory.'/'.str_repeat('a', 40);
        file_put_contents($path, serialize([Auth::guard('web')->getName() => 1]));
        $stream = fopen($path, 'rb');
        flock($stream, LOCK_EX);
        try {
            $this->code(1, '123456', 'password', now());
            $this->postJson('/api/change/password', $this->payload('123456'))->assertUnprocessable();
            $this->assertSame($oldHash, $target->fresh()->password);
            $this->assertDatabaseCount('otps', 1);
            $this->assertFileExists($path);
        } finally {
            flock($stream, LOCK_UN);
            fclose($stream);
        }
    }

    public function test_canonical_directory_belonging_to_another_app_is_rejected_without_reading_or_deleting_sessions(): void
    {
        $target = $this->user(1);
        $oldHash = $target->password;
        $directory = $this->sessionDirectory();
        $path = $directory.'/'.str_repeat('a', 40);
        $body = serialize([Auth::guard('web')->getName() => 1]);
        file_put_contents($path, $body);
        $this->app->useStoragePath(dirname(dirname($directory)).'/another-app');
        $this->code(1, '123456', 'password', now());
        $this->postJson('/api/change/password', $this->payload('123456'))->assertUnprocessable();
        $this->assertSame($oldHash, $target->fresh()->password);
        $this->assertSame($body, file_get_contents($path));
        $this->assertDatabaseCount('otps', 1);
    }

    private function sessionDirectory(): string
    {
        $privateStorage = storage_path('framework/testing/security-store-'.Str::uuid());
        $this->privateSessions = $privateStorage.'/framework/sessions';
        mkdir($this->privateSessions, 0700, true);
        $this->app->useStoragePath(realpath($privateStorage));
        $this->privateSessions = realpath($this->privateSessions);
        config(['session.driver' => 'file', 'session.files' => $this->privateSessions]);

        return $this->privateSessions;
    }

    private function user(int $id): User
    {
        return User::withoutEvents(fn () => User::query()->forceCreate([
            'id' => $id, 'name' => 'Isolated reset fixture', 'email' => 'fixture-'.$id.'@example.invalid',
            'password' => Hash::make('Original-test-password'), 'status' => 'Active',
            'olives_card_number' => '476102'.str_pad((string) $id, 10, '0', STR_PAD_LEFT),
        ]));
    }

    private function code(int $id, string $code, string $type, $created): OTP
    {
        return OTP::withoutEvents(fn () => OTP::query()->forceCreate([
            'user_id' => $id, 'citizen_id' => null, 'code' => $code, 'type' => $type, 'created_at' => $created,
        ]));
    }

    private function payload(string $code): array
    {
        return ['code' => $code, 'password' => 'New-test-password', 'password_confirmation' => 'New-test-password'];
    }
}
