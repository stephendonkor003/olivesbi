<?php

namespace App\Models;

use App\Models\Concerns\HasUuid;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use App\Traits\HasActiveRoles;
use App\Support\OlivesAccountTier;
use App\Support\OlivesCardNumber;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasActiveRoles, HasApiTokens, HasFactory, HasUuid, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'phone',
        'access_level',
        'status',
        'regional_code',
        'assembly_code',
        'division_code',
        'pro_owner_uuid',
        'role_access',
        'agent_capabilities',
        'gender',
        'erms_id',
        'olives_card_number',
        'change_password',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'change_password' => 'boolean',
        'agent_capabilities' => 'array',
    ];

    protected static function booted(): void
    {
        static::creating(function (User $user) {
            if (empty($user->olives_card_number)) {
                $user->olives_card_number = OlivesCardNumber::generate(
                    OlivesAccountTier::fromAccessLevel($user->access_level)
                );
            }
        });

        static::saving(function (User $user) {
            if (empty($user->olives_card_number)) {
                $user->olives_card_number = OlivesCardNumber::generate(
                    OlivesAccountTier::fromAccessLevel($user->access_level)
                );

                return;
            }

            $user->olives_card_number = OlivesCardNumber::normalize($user->olives_card_number);
        });
    }

    // Define a relationship with the Assembly model
    public function assembly()
    {
        return $this->belongsTo(Assembly::class, 'assembly_code', 'assembly_code');
    }

    // Define a relationship with the Division model
    public function division()
    {
        return $this->belongsTo(Division::class, 'division_code', 'code');
    }

    public function region()
    {
        return $this->belongsTo(GhanaRegion::class, 'regional_code', 'regional_code');
    }

    public function roleName()
    {
        return $this->belongsTo(Role::class, 'role_access');
    }

    public function agentAssignments()
    {
        return $this->hasMany(AgentAssignment::class, 'agent_id');
    }

    public function proOwner()
    {
        return $this->belongsTo(User::class, 'pro_owner_uuid', 'uuid');
    }

    public function proAgents()
    {
        return $this->hasMany(User::class, 'pro_owner_uuid', 'uuid');
    }

    public function proIntegrations()
    {
        return $this->hasMany(ProIntegration::class, 'pro_user_uuid', 'uuid');
    }

    public function proPaymentRequests()
    {
        return $this->hasMany(ProPaymentRequest::class, 'pro_user_uuid', 'uuid');
    }

    public function customer()
    {
        return $this->hasOne(Owner::class, 'erms_id', 'erms_id');
    }

    public function guardName(): string
    {
        return 'web';
    }

    /**
     * Route notifications for the SMS channel.
     *
     * @param  \Illuminate\Notifications\Notification  $notification
     * @return string
     */
    public function routeNotificationForSms($notification)
    {
        return phone($this->phone, ['GH'])->formatE164();
    }
}
