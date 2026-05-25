<?php

namespace App\Models;

use App\Models\Concerns\HasUuid;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProPaymentRequest extends Model
{
    use HasFactory, HasUuid;

    protected $guarded = ['id'];

    protected $casts = [
        'amount' => 'decimal:2',
        'raw_response' => 'array',
        'sent_email_at' => 'datetime',
        'sent_sms_at' => 'datetime',
        'paid_at' => 'datetime',
        'expires_at' => 'datetime',
    ];

    public function proUser()
    {
        return $this->belongsTo(User::class, 'pro_user_uuid', 'uuid');
    }

    public function requestedByAgent()
    {
        return $this->belongsTo(User::class, 'requested_by_agent_uuid', 'uuid');
    }

    public function integration()
    {
        return $this->belongsTo(ProIntegration::class, 'integration_uuid', 'uuid');
    }

    public function assembly()
    {
        return $this->belongsTo(Assembly::class, 'assembly_code', 'assembly_code');
    }

    public function bill()
    {
        return $this->belongsTo(Bill::class, 'bill_uuid', 'uuid');
    }

    public function wasteCompany()
    {
        return $this->belongsTo(WasteCompany::class, 'waste_company_uuid', 'uuid');
    }

    public function wasteServicePlan()
    {
        return $this->belongsTo(WasteServicePlan::class, 'waste_service_plan_uuid', 'uuid');
    }

    public function isPaid(): bool
    {
        return strtolower((string) $this->status) === 'paid' || filled($this->paid_at);
    }
}
