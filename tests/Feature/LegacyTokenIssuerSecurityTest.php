<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Schema;
use Tests\TestCase;

class LegacyTokenIssuerSecurityTest extends TestCase
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
            $table->string('name');
            $table->string('email')->unique();
            $table->string('password');
            $table->string('status')->default('Active');
            $table->rememberToken();
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
            $table->text('payload');
            $table->integer('last_activity');
        });
    }

    public function test_unauthenticated_requests_cannot_issue_or_replace_the_legacy_token(): void
    {
        $user = $this->user(1);
        $existing = $user->createToken('Static Token')->accessToken;

        $this->getJson('/api/ussd/generate/token')->assertNotFound()->assertJsonMissingPath('token');
        $this->head('/api/ussd/generate/token')->assertNotFound();

        $this->assertDatabaseCount('personal_access_tokens', 1);
        $this->assertDatabaseHas('personal_access_tokens', ['id' => $existing->id]);
    }

    public function test_revoker_preserves_other_actors_morphs_tokens_and_existing_account_sessions(): void
    {
        $target = $this->user(1);
        $other = $this->user(2);
        $legacy = $target->createToken('Static Token')->accessToken;
        $regular = $target->createToken('Existing authenticated session API token')->accessToken;
        $otherLegacy = $other->createToken('Static Token')->accessToken;
        $foreignMorph = DB::table('personal_access_tokens')->insertGetId([
            'tokenable_type' => 'App\\Models\\Owner',
            'tokenable_id' => 1,
            'name' => 'Static Token',
            'token' => hash('sha256', random_bytes(32)),
            'abilities' => '["*"]',
            'created_at' => now(),
            'updated_at' => now(),
        ]);
        DB::table('sessions')->insert([
            ['id' => 'existing-user-one', 'user_id' => 1, 'payload' => 'preserved', 'last_activity' => time()],
            ['id' => 'existing-user-two', 'user_id' => 2, 'payload' => 'preserved', 'last_activity' => time()],
        ]);
        session()->put('security_review_marker', 'preserved');
        $password = $target->password;
        $rememberToken = $target->remember_token;
        $appKey = config('app.key');

        $this->artisan('security:revoke-legacy-static-tokens')
            ->expectsOutput('Revoked 1 legacy static API token(s).')
            ->assertSuccessful();

        $this->assertDatabaseMissing('personal_access_tokens', ['id' => $legacy->id]);
        foreach ([$regular->id, $otherLegacy->id, $foreignMorph] as $id) {
            $this->assertDatabaseHas('personal_access_tokens', ['id' => $id]);
        }
        $this->assertDatabaseCount('sessions', 2);
        $this->assertSame('preserved', session('security_review_marker'));
        $this->assertSame($password, $target->fresh()->password);
        $this->assertSame($rememberToken, $target->fresh()->remember_token);
        $this->assertSame($appKey, config('app.key'));

        $this->artisan('security:revoke-legacy-static-tokens')
            ->expectsOutput('Revoked 0 legacy static API token(s).')
            ->assertSuccessful();
        $this->assertDatabaseCount('personal_access_tokens', 3);
    }

    public function test_missing_historic_actor_is_a_safe_noop(): void
    {
        $other = $this->user(2);
        $token = $other->createToken('Static Token')->accessToken;

        $this->artisan('security:revoke-legacy-static-tokens')
            ->expectsOutput('Revoked 0 legacy static API token(s).')
            ->assertSuccessful();

        $this->assertDatabaseHas('personal_access_tokens', ['id' => $token->id]);
    }

    private function user(int $id): User
    {
        return User::withoutEvents(fn () => User::query()->forceCreate([
            'id' => $id,
            'name' => 'Isolated security fixture',
            'email' => 'fixture-'.$id.'@example.invalid',
            'password' => Hash::make(bin2hex(random_bytes(16))),
            'remember_token' => bin2hex(random_bytes(16)),
            'status' => 'Active',
        ]));
    }
}
