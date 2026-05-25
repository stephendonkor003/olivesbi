<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Models\AccountPropertyRequest;
use App\Models\Assembly;
use App\Models\AuditTrail;
use App\Models\Bill;
use App\Models\Business;
use App\Models\Citizen;
use App\Models\Owner;
use App\Models\Payment;
use App\Models\ProIntegration;
use App\Models\ProPaymentRequest;
use App\Models\Property;
use App\Models\RevenueStream;
use App\Models\Permission;
use App\Models\Role;
use App\Models\User;
use App\Models\WasteCompany;
use App\Models\WastePickupBooking;
use App\Models\WasteServicePlan;
use App\Services\ProPaymentGatewayService;
use App\Services\ProSmsService;
use App\Services\PropertyRateBillGenerator;
use App\Support\GlobalLocation;
use App\Support\OlivesAccountTier;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;
use Throwable;

class AccountDashboardController extends Controller
{
    public function entry(Request $request)
    {
        return redirect()->route(OlivesAccountTier::routeNameFor($request->user()));
    }

    public function subscriber()
    {
        return view('dashboard.tiers.subscriber.page');
    }

    public function subscriberNews()
    {
        return $this->subscriber();
    }

    public function subscriberAlerts()
    {
        return $this->subscriber();
    }

    public function subscriberLocality()
    {
        return $this->subscriber();
    }

    public function subscriberNewsletter()
    {
        return $this->subscriber();
    }

    public function subscriberNewsletterUpdate(Request $request)
    {
        return back()->with('status', 'Newsletter preferences updated.');
    }

    public function subscriberProfile()
    {
        return $this->subscriber();
    }

    public function subscriberProfileUpdate(Request $request)
    {
        $request->user()->update($request->validate([
            'name' => ['required', 'string', 'max:255'],
        ]));

        return back()->with('status', 'Profile updated successfully.');
    }

    public function account(Request $request)
    {
        return $this->accountShell($request);
    }

    public function accountProperties(Request $request)
    {
        return $this->accountShell($request, 'dashboard.tiers.account.partials.properties', 'My Properties', 'Linked property records and map requests.');
    }

    public function accountPropertyMap(Request $request)
    {
        return $this->accountShell($request, 'dashboard.tiers.account.partials.property-map', 'Add Property From Map', 'Select a parcel or location and submit it for assembly confirmation.', [
            'assemblies' => Assembly::orderBy('name')->get(),
        ]);
    }

    public function accountPropertyMapStore(Request $request)
    {
        $data = $request->validate([
            'parcel_name' => ['nullable', 'string', 'max:255'],
            'display_name' => ['nullable', 'string'],
            'address_line' => ['nullable', 'string', 'max:255'],
            ...GlobalLocation::rules(includeAddressLine: false),
            'assembly_code' => ['nullable', 'string', 'max:255'],
            'latitude' => ['required', 'numeric', 'between:-90,90'],
            'longitude' => ['required', 'numeric', 'between:-180,180'],
            'osm_type' => ['nullable', 'string', 'max:255'],
            'osm_id' => ['nullable', 'string', 'max:255'],
            'osm_place_id' => ['nullable', 'string', 'max:255'],
            'osm_class' => ['nullable', 'string', 'max:255'],
            'osm_category' => ['nullable', 'string', 'max:255'],
            'parcel_geojson' => ['nullable', 'json'],
            'notes' => ['nullable', 'string'],
        ]);

        $identity = $this->accountIdentity($request->user());

        AccountPropertyRequest::create([
            ...$data,
            ...GlobalLocation::from($data, $data['address_line'] ?? $data['display_name'] ?? null),
            'tracking_code' => $this->trackingCode(),
            'user_id' => $request->user()->id,
            'citizen_id' => $identity['citizen']?->id,
            'owner_id' => $identity['owner_id'],
            'status' => 'pending',
            'source' => 'openstreetmap',
            'parcel_geojson' => $data['parcel_geojson'] ? json_decode($data['parcel_geojson'], true) : null,
        ]);

        return redirect()->route('dashboard.olives-account.properties')
            ->with('status', 'Map property request submitted for assembly confirmation.');
    }

    public function accountBusinesses(Request $request)
    {
        return $this->accountShell($request, 'dashboard.tiers.account.partials.businesses', 'My Businesses', 'Business records linked to your account.');
    }

    public function accountBills(Request $request)
    {
        return $this->accountShell($request, 'dashboard.tiers.account.partials.bills', 'My Bills', 'Bills and levies linked to your assets.');
    }

    public function accountPayments(Request $request)
    {
        return $this->accountShell($request, 'dashboard.tiers.account.partials.payments', 'Payment History', 'Receipts and payment records linked to your bills.');
    }

    public function accountReports(Request $request)
    {
        return $this->accountReportShell(
            $request,
            'index',
            'Reports',
            'Choose a report for bills, payments, waste pickups, or account activity.'
        );
    }

    public function accountBillReport(Request $request)
    {
        return $this->accountReportShell(
            $request,
            'bills',
            'Bill Report',
            'Bill records, arrears, invoices, and linked asset summaries.'
        );
    }

    public function accountPaymentReport(Request $request)
    {
        return $this->accountReportShell(
            $request,
            'payments',
            'Payment Report',
            'Payment records, receipts, statuses, and successful payment totals.'
        );
    }

    public function accountWasteReport(Request $request)
    {
        return $this->accountReportShell(
            $request,
            'waste',
            'Waste Pickup Report',
            'Waste bookings, pickup history, missed collections, and generated waste.'
        );
    }

    public function accountAuditReport(Request $request)
    {
        return $this->accountReportShell(
            $request,
            'audit',
            'Audit Trail',
            'Recent account activity recorded for your signed-in user profile.'
        );
    }

    private function accountReportShell(Request $request, string $reportPage, string $scope, string $subtitle)
    {
        $auditLogs = AuditTrail::where('user_id', $request->user()->id)
            ->latest()
            ->limit(30)
            ->get();

        return $this->accountShell(
            $request,
            'dashboard.tiers.account.partials.reports',
            $scope,
            $subtitle,
            [
                'auditLogs' => $auditLogs,
                'reportPage' => $reportPage,
            ],
            ['contentWide' => true]
        );
    }

    public function accountLocality(Request $request)
    {
        return $this->accountShell($request, 'dashboard.tiers.account.partials.locality', 'Check Locality', 'Environmental and locality intelligence tools.');
    }

    public function accountProfile(Request $request)
    {
        return $this->accountShell($request, 'dashboard.tiers.account.partials.profile', 'My Profile', 'Account holder details and profile settings.');
    }

    public function accountProfileUpdate(Request $request)
    {
        $request->user()->update($request->validate([
            'name' => ['required', 'string', 'max:255'],
        ]));

        return back()->with('status', 'Account name updated successfully.');
    }

    public function pro(Request $request)
    {
        return $this->proShell($request);
    }

    public function proProperties(Request $request)
    {
        return $this->proShell($request, 'properties', 'Properties', 'Jurisdiction property records for your municipality or assembly.');
    }

    public function proPropertyStore(Request $request)
    {
        $currentUser = $request->user();
        $proOwner = $this->proOwnerFor($currentUser);

        if ($this->isProAgent($currentUser) && ! $this->agentCan($currentUser, 'property_registration')) {
            abort(403);
        }

        $data = $request->validate([
            'account_no' => ['nullable', 'string', 'max:255'],
            'address_line' => ['nullable', 'string', 'max:255'],
            'location' => ['nullable', 'string', 'max:255'],
            'digitaladdress' => ['nullable', 'string', 'max:255'],
            'streetname' => ['nullable', 'string', 'max:255'],
            'rateable_value' => ['nullable', 'numeric', 'min:0', 'max:999999999'],
            ...GlobalLocation::rules(),
        ]);

        $assemblyCode = $this->proAssemblyCode($proOwner);
        abort_unless($assemblyCode, 422, 'This Olives Pro account is not linked to an assembly.');

        Property::create([
            ...GlobalLocation::from($data, $data['address_line'] ?? $data['location'] ?? null),
            'erms_id' => 'PROP-'.Str::upper(Str::random(10)),
            'account_no' => ($data['account_no'] ?? null) ?: 'PR-'.$assemblyCode.'-'.Str::upper(Str::random(6)),
            'location' => $data['location'] ?? $data['address_line'] ?? null,
            'digitaladdress' => $data['digitaladdress'] ?? null,
            'streetname' => $data['streetname'] ?? null,
            'rateable_value' => $data['rateable_value'] ?? null,
            'assembly_code' => $assemblyCode,
            'created_by' => $currentUser->uuid,
            'is_rated' => false,
        ]);

        return back()->with('status', 'Property registered in your jurisdiction.');
    }

    public function proPropertyValidate(Request $request, Property $property)
    {
        $currentUser = $request->user();
        $proOwner = $this->proOwnerFor($currentUser);

        if ($this->isProAgent($currentUser) && ! $this->agentCan($currentUser, 'property_validation')) {
            abort(403);
        }

        $assemblyCode = $this->proAssemblyCode($proOwner);
        abort_unless($assemblyCode && $property->assembly_code === $assemblyCode, 404);

        $data = $request->validate([
            'rateable_value' => ['nullable', 'numeric', 'min:0', 'max:999999999'],
            'remarks' => ['nullable', 'string', 'max:255'],
        ]);

        $property->update([
            'is_rated' => true,
            'validation_id' => $currentUser->uuid,
            'rateable_value' => $data['rateable_value'] ?? $property->rateable_value,
            'remarks' => $data['remarks'] ?? $property->remarks,
            'updated_by' => $currentUser->uuid,
        ]);

        return back()->with('status', 'Property validated successfully.');
    }

    public function proBusinesses(Request $request)
    {
        return $this->proShell($request, 'businesses', 'Businesses', 'Business records and operating permits in your jurisdiction.');
    }

    public function proBusinessStore(Request $request)
    {
        $currentUser = $request->user();
        $proOwner = $this->proOwnerFor($currentUser);

        if ($this->isProAgent($currentUser) && ! $this->agentCan($currentUser, 'business_registration')) {
            abort(403);
        }

        $data = $request->validate([
            'account_no' => ['nullable', 'string', 'max:255'],
            'business_name' => ['required', 'string', 'max:255'],
            'registration_no' => ['nullable', 'string', 'max:255'],
            'permit_no' => ['nullable', 'string', 'max:255'],
            'phone_no' => ['nullable', 'string', 'max:40'],
            'email' => ['nullable', 'email', 'max:255'],
            'address_line' => ['nullable', 'string', 'max:255'],
            ...GlobalLocation::rules(),
        ]);

        $assemblyCode = $this->proAssemblyCode($proOwner);
        abort_unless($assemblyCode, 422, 'This Olives Pro account is not linked to an assembly.');

        Business::create([
            ...GlobalLocation::from($data, $data['address_line'] ?? null),
            'erms_id' => 'BUS-'.Str::upper(Str::random(10)),
            'account_no' => ($data['account_no'] ?? null) ?: 'BU-'.$assemblyCode.'-'.Str::upper(Str::random(6)),
            'business_name' => $data['business_name'],
            'registration_no' => $data['registration_no'] ?? null,
            'permit_no' => $data['permit_no'] ?? null,
            'phone_no' => $data['phone_no'] ?? null,
            'email' => $data['email'] ?? null,
            'address' => $data['address_line'] ?? null,
            'assembly_code' => $assemblyCode,
            'created_by' => $currentUser->uuid,
        ]);

        return back()->with('status', 'Business registered in your jurisdiction.');
    }

    public function proBusinessUpdate(Request $request, Business $business)
    {
        $currentUser = $request->user();
        $proOwner = $this->proOwnerFor($currentUser);

        if ($this->isProAgent($currentUser) && ! $this->agentCan($currentUser, 'business_update')) {
            abort(403);
        }

        $assemblyCode = $this->proAssemblyCode($proOwner);
        abort_unless($assemblyCode && $business->assembly_code === $assemblyCode, 404);

        $data = $request->validate([
            'phone_no' => ['nullable', 'string', 'max:40'],
            'email' => ['nullable', 'email', 'max:255'],
            'address_line' => ['nullable', 'string', 'max:255'],
            'remarks' => ['nullable', 'string', 'max:1000'],
        ]);

        $business->update([
            'phone_no' => $data['phone_no'] ?? $business->phone_no,
            'email' => $data['email'] ?? $business->email,
            'address' => $data['address_line'] ?? $business->address,
            'remarks' => $data['remarks'] ?? $business->remarks,
            'updated_by' => $currentUser->uuid,
        ]);

        return back()->with('status', 'Business updated successfully.');
    }

    public function proBills(Request $request)
    {
        return $this->proShell($request, 'bills', 'Bills & Arrears', 'Bills, levies, arrears, and collections for your jurisdiction.');
    }

    public function proPropertyRateBillsGenerate(Request $request, PropertyRateBillGenerator $generator)
    {
        $currentUser = $request->user();
        $proOwner = $this->proOwnerFor($currentUser);

        if ($this->isProAgent($currentUser)) {
            abort(403);
        }

        $data = $request->validate([
            'bills_year' => ['required', 'digits:4'],
            'calculation_mode' => ['required', 'string', 'in:rateable,flat'],
            'rate_impost' => ['nullable', 'numeric', 'min:0', 'max:999999'],
            'flat_rate' => ['nullable', 'numeric', 'min:0', 'max:999999999'],
            'min_amount' => ['nullable', 'numeric', 'min:0', 'max:999999999'],
            'max_amount' => ['nullable', 'numeric', 'min:0', 'max:999999999'],
            'discount_amount' => ['nullable', 'numeric', 'min:0', 'max:999999999'],
            'only_unbilled' => ['nullable', 'boolean'],
            'action' => ['required', 'string', 'in:preview,generate'],
        ]);

        if (($data['calculation_mode'] ?? 'rateable') === 'rateable' && (float) ($data['rate_impost'] ?? 0) <= 0) {
            return back()->withErrors(['rate_impost' => 'Enter a rate impost greater than zero for rateable value billing.'])->withInput();
        }

        if (($data['calculation_mode'] ?? 'rateable') === 'flat' && (float) ($data['flat_rate'] ?? 0) <= 0) {
            return back()->withErrors(['flat_rate' => 'Enter a flat rate greater than zero for flat property billing.'])->withInput();
        }

        if (filled($data['max_amount'] ?? null) && (float) $data['max_amount'] > 0 && (float) $data['max_amount'] < (float) ($data['min_amount'] ?? 0)) {
            return back()->withErrors(['max_amount' => 'Maximum amount cannot be lower than minimum amount.'])->withInput();
        }

        $assemblyCode = $this->proAssemblyCode($proOwner);
        abort_unless($assemblyCode, 422, 'This Olives Pro account is not linked to an assembly.');

        $summary = $generator->generate(
            $assemblyCode,
            $data['bills_year'],
            (string) $currentUser->uuid,
            [
                'calculation_mode' => $data['calculation_mode'],
                'rate_impost' => $data['rate_impost'] ?? 0,
                'flat_rate' => $data['flat_rate'] ?? 0,
                'min_amount' => $data['min_amount'] ?? 0,
                'max_amount' => $data['max_amount'] ?? 0,
                'discount_amount' => $data['discount_amount'] ?? 0,
                'only_unbilled' => (bool) ($data['only_unbilled'] ?? true),
                'dry_run' => $data['action'] === 'preview',
                'notify' => $data['action'] !== 'preview',
            ]
        );

        if ($data['action'] === 'preview') {
            $previewSummary = $summary;
            $previewSummary['items'] = collect($summary['items'] ?? [])->take(100)->values()->all();

            return back()
                ->withInput()
                ->with('property_rate_preview', $previewSummary)
                ->with('status', 'Property rate preview prepared. Review it before generating bills.');
        }

        return back()->with('status', "{$summary['created']} property rate bill(s) generated. {$summary['skipped']} property record(s) skipped.");
    }

    public function proPayments(Request $request)
    {
        return $this->proShell($request, 'payments', 'Payments', 'Payment records received for bills in your jurisdiction.');
    }

    public function proPaymentLinkStore(Request $request, ProPaymentGatewayService $gateway, ProSmsService $sms)
    {
        $currentUser = $request->user();
        $proOwner = $this->proOwnerFor($currentUser);

        if ($this->isProAgent($currentUser) && ! $this->agentCan($currentUser, ['payment_collection', 'waste_payment_collection'])) {
            abort(403);
        }

        $data = $request->validate([
            'integration_uuid' => ['required', 'uuid', 'exists:pro_integrations,uuid'],
            'purpose' => ['required', 'string', 'in:bill,waste,custom'],
            'bill_uuid' => ['nullable', 'uuid', 'exists:bills,uuid'],
            'waste_service_plan_uuid' => ['nullable', 'uuid', 'exists:waste_service_plans,uuid'],
            'payer_name' => ['nullable', 'string', 'max:255'],
            'payer_email' => ['nullable', 'email', 'max:255'],
            'payer_phone' => ['nullable', 'string', 'max:40'],
            'description' => ['nullable', 'string', 'max:1000'],
            'amount' => ['required', 'numeric', 'min:1', 'max:999999999'],
        ]);

        if (blank($data['payer_email'] ?? null) && blank($data['payer_phone'] ?? null)) {
            return back()->withErrors(['payer_email' => 'Enter an email address or phone number to send the payment link.'])->withInput();
        }

        if ($data['purpose'] === 'bill' && blank($data['bill_uuid'] ?? null)) {
            return back()->withErrors(['bill_uuid' => 'Select the bill this payment link belongs to.'])->withInput();
        }

        if ($data['purpose'] === 'waste' && blank($data['waste_service_plan_uuid'] ?? null)) {
            return back()->withErrors(['waste_service_plan_uuid' => 'Select a waste service plan for this payment link.'])->withInput();
        }

        $assemblyCode = $this->proAssemblyCode($proOwner);
        $integration = ProIntegration::payment()
            ->where('uuid', $data['integration_uuid'])
            ->where('pro_user_uuid', $proOwner->uuid)
            ->where('is_active', true)
            ->firstOrFail();

        $bill = null;
        $wastePlan = null;
        $wasteCompanyUuid = null;
        $description = $data['description'] ?? null;
        $currencyCode = optional($proOwner->assembly)->currency_code ?: 'GHS';

        if (($data['purpose'] ?? null) === 'bill') {
            $bill = Bill::where('uuid', $data['bill_uuid'])
                ->when($assemblyCode, fn ($query) => $query->where('assembly_code', $assemblyCode))
                ->firstOrFail();
            $currencyCode = optional($bill->assembly)->currency_code ?: $currencyCode;
            $description = $description ?: 'Bill '.$bill->bills_id;
        }

        if (($data['purpose'] ?? null) === 'waste') {
            $wastePlan = WasteServicePlan::with('company')
                ->where('uuid', $data['waste_service_plan_uuid'])
                ->whereHas('company', function ($query) use ($assemblyCode, $proOwner) {
                    $query->where('registered_by_uuid', $proOwner->uuid);

                    if ($assemblyCode) {
                        $query->orWhere('assembly_code', $assemblyCode);
                    }
                })
                ->firstOrFail();
            $wasteCompanyUuid = $wastePlan->waste_company_uuid;
            $currencyCode = $wastePlan->currency_code ?: $currencyCode;
            $description = $description ?: 'Waste service: '.$wastePlan->name;
        }

        $paymentRequest = ProPaymentRequest::create([
            'pro_user_uuid' => $proOwner->uuid,
            'requested_by_agent_uuid' => $this->isProAgent($currentUser) ? $currentUser->uuid : null,
            'integration_uuid' => $integration->uuid,
            'assembly_code' => $assemblyCode,
            'bill_uuid' => $bill?->uuid,
            'waste_company_uuid' => $wasteCompanyUuid,
            'waste_service_plan_uuid' => $wastePlan?->uuid,
            'purpose' => $data['purpose'],
            'payer_name' => $data['payer_name'] ?? null,
            'payer_email' => $data['payer_email'] ?? null,
            'payer_phone' => $data['payer_phone'] ?? null,
            'description' => $description ?: 'Olives BI payment request',
            'amount' => round((float) $data['amount'], 2),
            'currency_code' => strtoupper($currencyCode),
            'provider' => $integration->provider,
            'status' => 'pending',
            'expires_at' => now()->addDays(14),
        ]);

        $paymentRequest->update([
            'public_url' => route('public.pro-payment-requests.show', $paymentRequest),
        ]);

        try {
            $checkout = $gateway->createCheckout($paymentRequest, $integration);
        } catch (Throwable $exception) {
            $paymentRequest->update([
                'status' => 'failed',
                'raw_response' => ['error' => $exception->getMessage()],
            ]);

            return back()->withErrors(['gateway' => $exception->getMessage()])->withInput();
        }

        $paymentRequest->update([
            'payment_reference' => $checkout['reference'] ?? $paymentRequest->payment_reference,
            'payment_url' => $checkout['payment_url'] ?: $paymentRequest->public_url,
            'raw_response' => $checkout['raw_response'] ?? [],
            'status' => 'sent',
        ]);

        $message = $this->paymentLinkMessage($paymentRequest);
        $deliveryNotes = [];

        if (filled($paymentRequest->payer_email)) {
            try {
                Mail::raw($message, function ($mail) use ($paymentRequest, $proOwner) {
                    $mail->to($paymentRequest->payer_email)
                        ->subject('Payment request from '.($proOwner->assembly?->name ?: 'Olives BI'));
                });
                $paymentRequest->update(['sent_email_at' => now()]);
            } catch (Throwable $exception) {
                report($exception);
                $deliveryNotes[] = 'email could not be sent';
            }
        }

        if (filled($paymentRequest->payer_phone)) {
            if ($sms->sendForPro($proOwner, $paymentRequest->payer_phone, $message)) {
                $paymentRequest->update(['sent_sms_at' => now()]);
            } else {
                $deliveryNotes[] = 'SMS could not be sent';
            }
        }

        $status = 'Payment link created and sent.';
        if ($deliveryNotes) {
            $status .= ' Note: '.implode(', ', $deliveryNotes).'.';
        }

        return back()->with('status', $status);
    }

    public function proLawsFees(Request $request)
    {
        return $this->proShell($request, 'laws', 'Laws & Fees', 'Revenue streams, fee settings, and charge rules for your assembly.');
    }

    public function proCitizens(Request $request)
    {
        return $this->proShell($request, 'citizens', 'Citizens', 'Rate-payer records linked to jurisdiction properties and businesses.');
    }

    public function proUsers(Request $request)
    {
        return $this->proShell($request, 'users', 'Agents & Users', 'Municipal staff and user accounts linked to your assembly.');
    }

    public function proAgentStore(Request $request)
    {
        $proOwner = $request->user();

        if ($this->isProAgent($proOwner)) {
            abort(403);
        }

        $allowedCapabilities = array_keys($this->agentCapabilityLabels());

        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255'],
            'phone' => ['required', 'string', 'max:40'],
            'password' => ['required', 'string', 'min:8'],
            'agent_capabilities' => ['required', 'array', 'min:1'],
            'agent_capabilities.*' => ['string', 'in:'.implode(',', $allowedCapabilities)],
        ]);

        $assemblyCode = $this->proAssemblyCode($proOwner);
        $role = Role::firstOrCreate(
            ['name' => 'Olives Pro Agent', 'guard_name' => 'web'],
            ['active' => true]
        );

        $agent = User::updateOrCreate(
            ['email' => $data['email']],
            [
                'name' => $data['name'],
                'phone' => $data['phone'],
                'password' => Hash::make($data['password']),
                'access_level' => 'Olives_Pro_Agent',
                'status' => 'Active',
                'assembly_code' => $assemblyCode,
                'pro_owner_uuid' => $proOwner->uuid,
                'agent_capabilities' => array_values($data['agent_capabilities']),
                'role_access' => $role->id,
                'change_password' => true,
            ]
        );

        $permissions = $this->permissionsForAgentCapabilities($data['agent_capabilities']);
        foreach ($permissions as $permission) {
            Permission::firstOrCreate(['name' => $permission, 'guard_name' => 'web']);
        }

        $agent->syncRoles([$role]);
        $agent->syncPermissions($permissions);

        return back()->with('status', 'Agent registered with scoped Olives Pro capabilities.');
    }

    public function proReports(Request $request)
    {
        return $this->proShell($request, 'reports', 'Reports', 'Jurisdiction summaries for properties, businesses, bills, payments, and waste services.');
    }

    public function proIntegrations(Request $request)
    {
        return $this->proShell($request, 'integrations', 'Payment & SMS Settings', 'Configure the payment and SMS providers used by this Olives Pro account.');
    }

    public function proIntegrationStore(Request $request)
    {
        $proOwner = $request->user();

        if ($this->isProAgent($proOwner)) {
            abort(403);
        }

        $paymentProviders = implode(',', array_keys(ProIntegration::PAYMENT_PROVIDERS));
        $smsProviders = implode(',', array_keys(ProIntegration::SMS_PROVIDERS));

        $data = $request->validate([
            'category' => ['required', 'string', 'in:payment,sms'],
            'provider' => ['required', 'string', 'max:64'],
            'label' => ['nullable', 'string', 'max:255'],
            'is_active' => ['nullable', 'boolean'],
            'is_default' => ['nullable', 'boolean'],
            'credentials' => ['nullable', 'array'],
        ]);

        if ($data['category'] === 'payment' && ! in_array($data['provider'], explode(',', $paymentProviders), true)) {
            return back()->withErrors(['provider' => 'Unsupported payment provider selected.'])->withInput();
        }

        if ($data['category'] === 'sms' && ! in_array($data['provider'], explode(',', $smsProviders), true)) {
            return back()->withErrors(['provider' => 'Unsupported SMS provider selected.'])->withInput();
        }

        $credentials = $this->integrationCredentials($data['category'], $data['provider'], $data['credentials'] ?? []);
        $isDefault = (bool) ($data['is_default'] ?? false);

        if ($isDefault) {
            ProIntegration::where('pro_user_uuid', $proOwner->uuid)
                ->where('category', $data['category'])
                ->update(['is_default' => false]);
        }

        ProIntegration::updateOrCreate(
            [
                'pro_user_uuid' => $proOwner->uuid,
                'category' => $data['category'],
                'provider' => $data['provider'],
            ],
            [
                'assembly_code' => $this->proAssemblyCode($proOwner),
                'label' => $data['label'] ?: $this->providerName($data['category'], $data['provider']),
                'credentials' => $credentials,
                'is_active' => (bool) ($data['is_active'] ?? true),
                'is_default' => $isDefault,
            ]
        );

        return back()->with('status', 'Olives Pro integration saved.');
    }

    public function proProfile(Request $request)
    {
        return $this->proShell($request, 'profile', 'My Profile', 'Olives Pro account holder profile and assembly assignment.');
    }

    public function proProfileUpdate(Request $request)
    {
        $request->user()->update($request->validate([
            'name' => ['required', 'string', 'max:255'],
        ]));

        return back()->with('status', 'Olives Pro profile updated successfully.');
    }

    public function sky()
    {
        return view('dashboard.tiers.olives-sky');
    }

    public function cloud()
    {
        return view('dashboard.tiers.olives-cloud');
    }

    public function shell()
    {
        return view('dashboard.tiers.olives-shell');
    }

    private function accountShell(Request $request, ?string $contentView = null, string $scope = 'Olives Account', string $subtitle = 'Your properties, businesses, bills, payments, and locality tools in one place.', array $extraContentData = [], array $shellOverrides = [])
    {
        $accountData = $this->accountHolderData($request->user());

        return view('dashboard.tiers.partials.shell', array_merge([
            'accent' => '#2d9e4b',
            'tier' => 'Olives Account',
            'tierSlug' => 'account',
            'resolvedTier' => 'account',
            'scope' => $scope,
            'subtitle' => $subtitle,
            'user' => $request->user(),
            'profileRoute' => route('dashboard.olives-account.profile'),
            'sidebarNav' => $this->accountNav(),
            'metricKeys' => [
                'Properties' => 'properties',
                'Businesses' => 'businesses',
                'Bills' => 'bills',
                'Payments' => 'payments',
                'Waste Pickups' => 'waste',
            ],
            'stats' => [
                'properties' => $accountData['properties']->count(),
                'businesses' => $accountData['businesses']->count(),
                'bills' => $accountData['bills']->count(),
                'payments' => $accountData['payments']->count(),
                'waste' => $accountData['wasteBookings']->where('status', 'active')->count(),
            ],
            'focus' => $contentView ? [] : [
                'View properties and businesses linked to your rate-payer record.',
                'Track bills, arrears, payments, receipts, and account documents.',
                'Submit property map selections for assembly confirmation.',
                'Check locality and environmental context around your assets.',
            ],
            'cards' => $contentView ? [] : [
                ['icon' => 'bi-buildings', 'title' => 'My Properties', 'text' => 'View linked property records and pending map requests.', 'url' => route('dashboard.olives-account.properties')],
                ['icon' => 'bi-briefcase', 'title' => 'My Businesses', 'text' => 'Review businesses connected to your account.', 'url' => route('dashboard.olives-account.businesses')],
                ['icon' => 'bi-receipt', 'title' => 'Bills & Levies', 'text' => 'Open bill details and download invoices.', 'url' => route('dashboard.olives-account.bills')],
                ['icon' => 'bi-clock-history', 'title' => 'Payments', 'text' => 'View payment history and download receipts.', 'url' => route('dashboard.olives-account.payments')],
                ['icon' => 'bi-recycle', 'title' => 'Waste Pickup', 'text' => 'Book providers, track pickups, and record waste generated.', 'url' => route('dashboard.olives-account.waste')],
                ['icon' => 'bi-file-earmark-bar-graph', 'title' => 'Reports', 'text' => 'Review bills, payments, waste pickups, and audit activity.', 'url' => route('dashboard.olives-account.reports')],
            ],
            'quickLinks' => [
                ['label' => 'Add From Map', 'url' => route('dashboard.olives-account.properties.map'), 'hint' => 'Submit a property selection'],
                ['label' => 'Waste Pickup', 'url' => route('dashboard.olives-account.waste'), 'hint' => 'Book and track disposal'],
                ['label' => 'Reports', 'url' => route('dashboard.olives-account.reports'), 'hint' => 'Bills, payments, waste, audit'],
                ['label' => 'Check Locality', 'url' => route('dashboard.olives-account.locality'), 'hint' => 'Environmental context'],
                ['label' => 'My Profile', 'url' => route('dashboard.olives-account.profile'), 'hint' => 'Account settings'],
                ['label' => 'Sign Out', 'url' => route('auth.logout'), 'hint' => 'End your session securely', 'danger' => true],
            ],
            'contentView' => $contentView,
            'contentData' => [
                'accountData' => $accountData,
                'user' => $request->user(),
                ...$extraContentData,
            ],
        ], $shellOverrides));
    }

    private function accountNav(): array
    {
        return [
            ['icon' => 'bi-speedometer2', 'label' => 'Dashboard', 'url' => route('dashboard.olives-account'), 'active' => request()->routeIs('dashboard.olives-account')],
            ['icon' => 'bi-buildings', 'label' => 'My Properties', 'url' => route('dashboard.olives-account.properties'), 'active' => request()->routeIs('dashboard.olives-account.properties*')],
            ['icon' => 'bi-briefcase', 'label' => 'My Business', 'url' => route('dashboard.olives-account.businesses'), 'active' => request()->routeIs('dashboard.olives-account.businesses*')],
            ['icon' => 'bi-receipt', 'label' => 'My Bills', 'url' => route('dashboard.olives-account.bills'), 'active' => request()->routeIs('dashboard.olives-account.bills*')],
            ['icon' => 'bi-clock-history', 'label' => 'Payment History', 'url' => route('dashboard.olives-account.payments'), 'active' => request()->routeIs('dashboard.olives-account.payments*')],
            ['icon' => 'bi-recycle', 'label' => 'Waste Pickup', 'url' => route('dashboard.olives-account.waste'), 'active' => request()->routeIs('dashboard.olives-account.waste*')],
            [
                'icon' => 'bi-file-earmark-bar-graph',
                'label' => 'Reports',
                'url' => route('dashboard.olives-account.reports'),
                'active' => request()->routeIs('dashboard.olives-account.reports*'),
                'children' => [
                    ['icon' => 'bi-grid-1x2', 'label' => 'Overview', 'url' => route('dashboard.olives-account.reports'), 'active' => request()->routeIs('dashboard.olives-account.reports')],
                    ['icon' => 'bi-receipt-cutoff', 'label' => 'Bill Report', 'url' => route('dashboard.olives-account.reports.bills'), 'active' => request()->routeIs('dashboard.olives-account.reports.bills')],
                    ['icon' => 'bi-credit-card-2-front', 'label' => 'Payment Report', 'url' => route('dashboard.olives-account.reports.payments'), 'active' => request()->routeIs('dashboard.olives-account.reports.payments')],
                    ['icon' => 'bi-recycle', 'label' => 'Waste Report', 'url' => route('dashboard.olives-account.reports.waste'), 'active' => request()->routeIs('dashboard.olives-account.reports.waste')],
                    ['icon' => 'bi-shield-check', 'label' => 'Audit Trail', 'url' => route('dashboard.olives-account.reports.audit'), 'active' => request()->routeIs('dashboard.olives-account.reports.audit')],
                ],
            ],
            ['icon' => 'bi-geo-alt', 'label' => 'Check Locality', 'url' => route('dashboard.olives-account.locality'), 'active' => request()->routeIs('dashboard.olives-account.locality')],
            ['icon' => 'bi-person-circle', 'label' => 'My Profile', 'url' => route('dashboard.olives-account.profile'), 'active' => request()->routeIs('dashboard.olives-account.profile')],
            ['icon' => 'bi-box-arrow-left', 'label' => 'Sign Out', 'url' => route('auth.logout')],
        ];
    }

    private function proShell(Request $request, ?string $section = null, string $scope = 'Municipality & Assembly Portal', string $subtitle = 'Configure local services, manage jurisdiction data, and monitor municipal operations.')
    {
        $user = $request->user() ?: auth()->user();
        abort_unless($user, 403);

        $isProAgent = $this->isProAgent($user);
        if ($isProAgent && ! in_array($section, [null, 'properties', 'businesses', 'bills', 'payments', 'profile'], true)) {
            abort(403);
        }

        $proData = $this->proDashboardData($user);
        $dashboardCards = [
            ['icon' => 'bi-buildings-fill', 'title' => 'Properties', 'text' => 'View property records scoped to your jurisdiction.', 'url' => route('dashboard.olives-pro.properties')],
            ['icon' => 'bi-briefcase-fill', 'title' => 'Businesses', 'text' => 'Review businesses and operating permit records.', 'url' => route('dashboard.olives-pro.businesses')],
            ['icon' => 'bi-receipt', 'title' => 'Bills & Arrears', 'text' => 'Track bills, arrears, and issued levies.', 'url' => route('dashboard.olives-pro.bills')],
            ['icon' => 'bi-cash-stack', 'title' => 'Payments', 'text' => 'Create payment links and monitor collections.', 'url' => route('dashboard.olives-pro.payments')],
        ];

        if (! $isProAgent) {
            $dashboardCards = array_merge($dashboardCards, [
                ['icon' => 'bi-bank2', 'title' => 'Laws & Fees', 'text' => 'Review revenue streams and fee settings.', 'url' => route('dashboard.olives-pro.laws-fees')],
                ['icon' => 'bi-recycle', 'title' => 'Waste Companies', 'text' => 'Register providers and manage service charges.', 'url' => route('dashboard.olives-pro.waste-companies')],
                ['icon' => 'bi-person-gear', 'title' => 'Agents & Users', 'text' => 'Register agents and assign scoped capabilities.', 'url' => route('dashboard.olives-pro.users')],
                ['icon' => 'bi-sliders', 'title' => 'Payment & SMS Settings', 'text' => 'Configure your own gateways and messaging providers.', 'url' => route('dashboard.olives-pro.integrations')],
                ['icon' => 'bi-file-earmark-bar-graph', 'title' => 'Reports', 'text' => 'Open jurisdiction summary reports.', 'url' => route('dashboard.olives-pro.reports')],
            ]);
        }

        return view('dashboard.tiers.partials.shell', [
            'accent' => '#d4a017',
            'tier' => 'Olives Pro',
            'tierSlug' => 'pro',
            'resolvedTier' => 'pro',
            'scope' => $scope,
            'subtitle' => $subtitle,
            'user' => $user,
            'profileRoute' => route('dashboard.olives-pro.profile'),
            'sidebarNav' => $this->proNav($user),
            'metricKeys' => [
                'Properties' => 'properties',
                'Businesses' => 'businesses',
                'Bills' => 'bills',
                'Payments' => 'payments',
                'Payment Links' => 'paymentRequests',
            ],
            'stats' => [
                'properties' => $proData['properties']->count(),
                'businesses' => $proData['businesses']->count(),
                'bills' => $proData['bills']->count(),
                'payments' => $proData['payments']->count(),
                'paymentRequests' => $proData['paymentRequests']->count(),
            ],
            'focus' => $section ? [] : ($isProAgent ? [
                'Work only inside the capabilities assigned by the Olives Pro holder.',
                'Register and update field records through scoped permissions.',
                'Create electronic payment links instead of collecting cash.',
            ] : [
                'Manage jurisdiction properties, businesses, bills, and payments inside the Olives Pro layout.',
                'Register waste companies, publish service charges, and monitor pickup obligations.',
                'Register agents with limited permissions for properties, businesses, validations, and electronic collections.',
                'Configure gateway and SMS credentials per Pro account instead of using one global payment system.',
            ]),
            'cards' => $section ? [] : $dashboardCards,
            'quickLinks' => $isProAgent ? [
                ['label' => 'Properties', 'url' => route('dashboard.olives-pro.properties'), 'hint' => 'Jurisdiction properties'],
                ['label' => 'Businesses', 'url' => route('dashboard.olives-pro.businesses'), 'hint' => 'Jurisdiction businesses'],
                ['label' => 'Payment Links', 'url' => route('dashboard.olives-pro.payments'), 'hint' => 'Electronic collections'],
            ] : [
                ['label' => 'Waste Companies', 'url' => route('dashboard.olives-pro.waste-companies'), 'hint' => 'Providers and service charges'],
                ['label' => 'Bills & Arrears', 'url' => route('dashboard.olives-pro.bills'), 'hint' => 'Jurisdiction billing'],
                ['label' => 'Payments', 'url' => route('dashboard.olives-pro.payments'), 'hint' => 'Collections'],
                ['label' => 'Integrations', 'url' => route('dashboard.olives-pro.integrations'), 'hint' => 'Payment and SMS setup'],
                ['label' => 'Reports', 'url' => route('dashboard.olives-pro.reports'), 'hint' => 'Summary data'],
            ],
            'contentView' => $section ? 'dashboard.tiers.pro.partials.workspace' : null,
            'contentWide' => true,
            'contentData' => [
                'proData' => $proData,
                'section' => $section,
                'user' => $user,
            ],
        ]);
    }

    private function proNav(?User $user = null): array
    {
        $user = $user ?: request()->user();
        $isProAgent = $user && $this->isProAgent($user);

        $nav = [
            ['icon' => 'bi-speedometer2', 'label' => 'Dashboard', 'url' => route('dashboard.olives-pro'), 'active' => request()->routeIs('dashboard.olives-pro')],
            ['icon' => 'bi-buildings', 'label' => 'Properties', 'url' => route('dashboard.olives-pro.properties'), 'active' => request()->routeIs('dashboard.olives-pro.properties')],
            ['icon' => 'bi-briefcase', 'label' => 'Businesses', 'url' => route('dashboard.olives-pro.businesses'), 'active' => request()->routeIs('dashboard.olives-pro.businesses')],
            ['icon' => 'bi-receipt', 'label' => 'Bills & Arrears', 'url' => route('dashboard.olives-pro.bills'), 'active' => request()->routeIs('dashboard.olives-pro.bills')],
            ['icon' => 'bi-cash-coin', 'label' => 'Payments', 'url' => route('dashboard.olives-pro.payments'), 'active' => request()->routeIs('dashboard.olives-pro.payments')],
        ];

        if (! $isProAgent) {
            $nav = array_merge($nav, [
            ['icon' => 'bi-bank', 'label' => 'Laws & Fees', 'url' => route('dashboard.olives-pro.laws-fees'), 'active' => request()->routeIs('dashboard.olives-pro.laws-fees')],
            ['icon' => 'bi-recycle', 'label' => 'Waste Companies', 'url' => route('dashboard.olives-pro.waste-companies'), 'active' => request()->routeIs('dashboard.olives-pro.waste-companies*')],
            ['icon' => 'bi-people', 'label' => 'Citizens', 'url' => route('dashboard.olives-pro.citizens'), 'active' => request()->routeIs('dashboard.olives-pro.citizens')],
            ['icon' => 'bi-person-gear', 'label' => 'Agents & Users', 'url' => route('dashboard.olives-pro.users'), 'active' => request()->routeIs('dashboard.olives-pro.users')],
            ['icon' => 'bi-file-earmark-bar-graph', 'label' => 'Reports', 'url' => route('dashboard.olives-pro.reports'), 'active' => request()->routeIs('dashboard.olives-pro.reports')],
            ['icon' => 'bi-sliders', 'label' => 'Payment & SMS Settings', 'url' => route('dashboard.olives-pro.integrations'), 'active' => request()->routeIs('dashboard.olives-pro.integrations')],
            ]);
        }

        return array_merge($nav, [
            ['icon' => 'bi-geo-alt', 'label' => 'Check Locality', 'url' => route('locality.check')],
            ['icon' => 'bi-person-circle', 'label' => 'My Profile', 'url' => route('dashboard.olives-pro.profile'), 'active' => request()->routeIs('dashboard.olives-pro.profile')],
            ['icon' => 'bi-box-arrow-left', 'label' => 'Sign Out', 'url' => route('auth.logout')],
        ]);
    }

    private function proDashboardData(User $user): array
    {
        $proOwner = $this->proOwnerFor($user);
        $assemblyCode = $this->proAssemblyCode($proOwner);
        $assembly = $assemblyCode ? Assembly::where('assembly_code', $assemblyCode)->first() : null;

        $properties = Property::with('assembly')
            ->when($assemblyCode, fn ($query) => $query->where('assembly_code', $assemblyCode), fn ($query) => $query->whereRaw('1 = 0'))
            ->latest()
            ->limit(100)
            ->get();

        $businesses = Business::with('assembly')
            ->when($assemblyCode, fn ($query) => $query->where('assembly_code', $assemblyCode), fn ($query) => $query->whereRaw('1 = 0'))
            ->latest()
            ->limit(100)
            ->get();

        $bills = Bill::with(['property', 'business', 'successfulPayments'])
            ->when($assemblyCode, fn ($query) => $query->where('assembly_code', $assemblyCode), fn ($query) => $query->whereRaw('1 = 0'))
            ->latest()
            ->limit(100)
            ->get();

        $payments = Payment::with(['bill.property', 'bill.business'])
            ->when($assemblyCode, fn ($query) => $query->where('assembly_code', $assemblyCode), fn ($query) => $query->whereRaw('1 = 0'))
            ->latest()
            ->limit(100)
            ->get();

        $citizenIds = $properties->pluck('customer_name')
            ->merge($businesses->pluck('citizen_account_number'))
            ->filter()
            ->unique()
            ->values();

        $citizens = Citizen::query()
            ->when($citizenIds->isNotEmpty(), fn ($query) => $query->whereIn('id', $citizenIds), fn ($query) => $query->whereRaw('1 = 0'))
            ->latest()
            ->limit(100)
            ->get();

        $users = User::with('roles')
            ->when($assemblyCode, fn ($query) => $query->where('assembly_code', $assemblyCode), fn ($query) => $query->where('id', $user->id))
            ->latest()
            ->limit(100)
            ->get();

        $agents = User::with('roles')
            ->where('pro_owner_uuid', $proOwner->uuid)
            ->latest()
            ->limit(100)
            ->get();

        $revenueStreams = RevenueStream::with('assembly')
            ->when($assemblyCode, fn ($query) => $query->where('assembly_code', $assemblyCode), fn ($query) => $query->whereRaw('1 = 0'))
            ->latest()
            ->limit(100)
            ->get();

        $wasteCompanies = WasteCompany::with(['plans', 'bookings'])
            ->where(function ($query) use ($assemblyCode, $proOwner) {
                if ($assemblyCode) {
                    $query->where('assembly_code', $assemblyCode)
                        ->orWhere('registered_by_uuid', $proOwner->uuid);

                    return;
                }

                $query->where('registered_by_uuid', $proOwner->uuid);
            })
            ->latest()
            ->get();

        $wasteServicePlans = WasteServicePlan::with('company')
            ->whereHas('company', function ($query) use ($assemblyCode, $proOwner) {
                $query->where('registered_by_uuid', $proOwner->uuid);

                if ($assemblyCode) {
                    $query->orWhere('assembly_code', $assemblyCode);
                }
            })
            ->latest()
            ->limit(100)
            ->get();

        $integrations = ProIntegration::where('pro_user_uuid', $proOwner->uuid)
            ->latest()
            ->get();

        $paymentRequests = ProPaymentRequest::with(['bill.property', 'bill.business', 'integration', 'requestedByAgent', 'wasteServicePlan.company'])
            ->where('pro_user_uuid', $proOwner->uuid)
            ->latest()
            ->limit(100)
            ->get();

        return compact('proOwner', 'assemblyCode', 'assembly', 'properties', 'businesses', 'bills', 'payments', 'citizens', 'users', 'agents', 'revenueStreams', 'wasteCompanies', 'wasteServicePlans', 'integrations', 'paymentRequests');
    }

    private function proAssemblyCode(User $user): ?string
    {
        return $user->assembly_code
            ?: WasteCompany::where('registered_by_uuid', $user->uuid)->whereNotNull('assembly_code')->value('assembly_code');
    }

    private function proOwnerFor(User $user): User
    {
        return $user->proOwner ?: $user;
    }

    private function isProAgent(User $user): bool
    {
        return filled($user->pro_owner_uuid);
    }

    private function agentCan(User $user, array|string $capabilities): bool
    {
        $capabilities = (array) $capabilities;
        $assigned = collect($user->agent_capabilities ?: []);

        return $assigned->intersect($capabilities)->isNotEmpty();
    }

    private function agentCapabilityLabels(): array
    {
        return [
            'business_registration' => 'Business registration',
            'business_update' => 'Business updates',
            'property_registration' => 'Property registration',
            'property_validation' => 'Property validation',
            'payment_collection' => 'Bill payment collection',
            'waste_payment_collection' => 'Waste service payment collection',
        ];
    }

    private function permissionsForAgentCapabilities(array $capabilities): array
    {
        $map = [
            'business_registration' => ['businesses.view', 'businesses.create'],
            'business_update' => ['businesses.view', 'businesses.update'],
            'property_registration' => ['properties.view', 'properties.create'],
            'property_validation' => ['properties.view', 'properties.update'],
            'payment_collection' => ['payments.view', 'payments.create'],
            'waste_payment_collection' => ['payments.view', 'payments.create'],
        ];

        return collect($capabilities)
            ->flatMap(fn ($capability) => $map[$capability] ?? [])
            ->unique()
            ->values()
            ->all();
    }

    private function integrationCredentials(string $category, string $provider, array $input): array
    {
        $allowed = match ($provider) {
            'paystack' => ['public_key', 'secret_key', 'base_url'],
            'flutterwave' => ['public_key', 'secret_key', 'encryption_key', 'base_url'],
            'stripe' => ['public_key', 'secret_key', 'webhook_secret'],
            'hubtel' => $category === 'sms'
                ? ['client_id', 'client_secret', 'sender_id', 'base_url', 'send_path']
                : ['client_id', 'client_secret', 'merchant_account_number', 'base_url', 'checkout_path'],
            'expresspay' => ['merchant_id', 'api_key', 'base_url'],
            'ghipss' => ['merchant_id', 'api_key', 'checkout_url', 'payment_url', 'instructions'],
            'mnotify' => ['api_key', 'sender_id', 'base_url'],
            'twilio' => ['account_sid', 'auth_token', 'from_number', 'messaging_service_sid'],
            default => [],
        };

        return collect($allowed)
            ->mapWithKeys(fn ($key) => [$key => trim((string) data_get($input, $key, ''))])
            ->filter(fn ($value) => $value !== '')
            ->all();
    }

    private function providerName(string $category, string $provider): string
    {
        $providers = $category === 'sms' ? ProIntegration::SMS_PROVIDERS : ProIntegration::PAYMENT_PROVIDERS;

        return $providers[$provider] ?? ucfirst($provider);
    }

    private function paymentLinkMessage(ProPaymentRequest $paymentRequest): string
    {
        $amount = strtoupper($paymentRequest->currency_code).' '.number_format((float) $paymentRequest->amount, 2);
        $assembly = $paymentRequest->assembly?->name ?: 'Olives BI';

        return "{$assembly} payment request: {$amount} for {$paymentRequest->description}. Pay securely here: {$paymentRequest->public_url}";
    }

    public function accountHolderData(User $user): array
    {
        $identity = $this->accountIdentity($user);
        $citizen = $identity['citizen'];
        $ownerId = $identity['owner_id'];

        $properties = collect();
        $businesses = collect();
        $bills = collect();
        $payments = collect();
        $wasteBookings = collect();

        if ($ownerId || $citizen) {
            $properties = Property::with(['assembly'])
                ->where(function ($query) use ($ownerId, $citizen) {
                    $hasCondition = false;

                    if ($ownerId && Schema::hasColumn('properties', 'owner_id')) {
                        $query->where('owner_id', $ownerId);
                        $hasCondition = true;
                    }

                    if ($citizen && Schema::hasColumn('properties', 'customer_name')) {
                        $method = $hasCondition ? 'orWhere' : 'where';
                        $query->{$method}('customer_name', $citizen->id);
                        $hasCondition = true;
                    }

                    if (! $hasCondition) {
                        $query->whereRaw('1 = 0');
                    }
                })
                ->latest()
                ->get();

            $businesses = Business::with(['assembly'])
                ->where(function ($query) use ($ownerId, $citizen) {
                    $hasCondition = false;

                    if ($ownerId && Schema::hasColumn('businesses', 'owner_id')) {
                        $query->where('owner_id', $ownerId);
                        $hasCondition = true;
                    }

                    if ($citizen && Schema::hasColumn('businesses', 'citizen_account_number')) {
                        $method = $hasCondition ? 'orWhere' : 'where';
                        $query->{$method}('citizen_account_number', $citizen->id);
                        $hasCondition = true;
                    }

                    if (! $hasCondition) {
                        $query->whereRaw('1 = 0');
                    }
                })
                ->latest()
                ->get();

            $propertyIds = $properties->pluck('uuid')->filter()->values();
            $businessIds = $businesses->pluck('uuid')->filter()->values();

            if ($propertyIds->isNotEmpty() || $businessIds->isNotEmpty()) {
                $bills = Bill::with(['assembly', 'property.assembly', 'business.assembly', 'successfulPayments'])
                    ->where(function ($query) use ($propertyIds, $businessIds) {
                        if ($propertyIds->isNotEmpty()) {
                            $query->whereIn('property_id', $propertyIds);
                        }

                        if ($businessIds->isNotEmpty()) {
                            $method = $propertyIds->isNotEmpty() ? 'orWhereIn' : 'whereIn';
                            $query->{$method}('business_id', $businessIds);
                        }
                    })
                    ->latest()
                    ->get();
            }

            $billReferences = $bills->pluck('bills_id')->filter()->values();

            if ($billReferences->isNotEmpty()) {
                $payments = Payment::with(['bill.assembly', 'bill.property.assembly', 'bill.business.assembly', 'assembly'])
                    ->whereIn('bills_id', $billReferences)
                    ->latest()
                    ->get();
            }
        }

        $propertyRequests = AccountPropertyRequest::with('assembly')
            ->where('user_id', $user->id)
            ->when($citizen, fn ($query) => $query->orWhere('citizen_id', $citizen->id))
            ->when($ownerId, fn ($query) => $query->orWhere('owner_id', $ownerId))
            ->latest()
            ->get();

        $wasteBookings = WastePickupBooking::with(['company', 'plan', 'pickups.company', 'pickups.booking.property', 'pickups.booking.business'])
            ->where('user_uuid', $user->uuid)
            ->latest()
            ->get();

        $propertyCurrencies = $properties->mapWithKeys(fn ($property) => [
            $property->uuid => $this->assemblyCurrency($property->assembly),
        ]);
        $billCurrencies = $bills->mapWithKeys(fn ($bill) => [
            $bill->uuid => $this->billCurrency($bill),
        ]);
        $paymentCurrencies = $payments->mapWithKeys(fn ($payment) => [
            $payment->uuid => $this->paymentCurrency($payment),
        ]);

        return [
            'citizen' => $citizen,
            'owner' => $identity['owner'],
            'properties' => $properties,
            'businesses' => $businesses,
            'bills' => $bills,
            'payments' => $payments,
            'propertyRequests' => $propertyRequests,
            'wasteBookings' => $wasteBookings,
            'propertyCurrencies' => $propertyCurrencies,
            'billCurrencies' => $billCurrencies,
            'paymentCurrencies' => $paymentCurrencies,
            'propertyCurrencyTotals' => $this->summarizePropertyCurrencies($properties),
            'billCurrencyTotals' => $this->summarizeBillCurrencies($bills),
            'paymentCurrencyTotals' => $this->summarizePaymentCurrencies($payments),
            'totals' => [
                'property_value' => $properties->sum(fn ($property) => (float) $property->ratable_value),
                'bill_amount' => $bills->sum(fn ($bill) => (float) $bill->amount),
                'arrears' => $bills->sum(fn ($bill) => (float) $bill->arrears),
                'paid' => $payments->filter(fn ($payment) => strtolower((string) $payment->transaction_status) === 'success')
                    ->sum(fn ($payment) => (float) $payment->amount),
            ],
        ];
    }

    private function accountIdentity(User $user): array
    {
        $citizen = Citizen::where('user_id', $user->id)->first();
        $owner = Schema::hasColumn('entity_owners', 'user_id')
            ? Owner::where('user_id', $user->id)->first()
            : null;
        $owner = $owner ?: $user->customer;
        $ownerId = $owner?->erms_id ?: $user->erms_id;

        return [
            'citizen' => $citizen,
            'owner' => $owner,
            'owner_id' => $ownerId,
        ];
    }

    private function assemblyCurrency($assembly): array
    {
        $code = strtoupper(trim((string) ($assembly?->currency_code ?: 'GHS')));

        return [
            'code' => $code,
            'symbol' => trim((string) ($assembly?->currency_symbol ?: $code)),
            'source' => $assembly?->name ?: 'Default currency',
        ];
    }

    private function billCurrency(Bill $bill): array
    {
        return $this->assemblyCurrency($bill->assembly ?: $bill->property?->assembly ?: $bill->business?->assembly);
    }

    private function paymentCurrency(Payment $payment): array
    {
        $bill = $payment->bill;

        return $this->assemblyCurrency($payment->assembly ?: $bill?->assembly ?: $bill?->property?->assembly ?: $bill?->business?->assembly);
    }

    private function summarizePropertyCurrencies($properties)
    {
        return $properties->groupBy(fn ($property) => $this->assemblyCurrency($property->assembly)['code'])
            ->map(function ($items) {
                $currency = $this->assemblyCurrency($items->first()?->assembly);

                return [
                    ...$currency,
                    'property_value' => $items->sum(fn ($property) => (float) $property->ratable_value),
                    'count' => $items->count(),
                ];
            })
            ->values();
    }

    private function summarizeBillCurrencies($bills)
    {
        return $bills->groupBy(fn ($bill) => $this->billCurrency($bill)['code'])
            ->map(function ($items) {
                $currency = $this->billCurrency($items->first());

                return [
                    ...$currency,
                    'due' => $items->sum(fn ($bill) => (float) $bill->amount + (float) $bill->arrears - (float) $bill->discount_amount),
                    'arrears' => $items->sum(fn ($bill) => (float) $bill->arrears),
                    'count' => $items->count(),
                ];
            })
            ->values();
    }

    private function summarizePaymentCurrencies($payments)
    {
        return $payments->groupBy(fn ($payment) => $this->paymentCurrency($payment)['code'])
            ->map(function ($items) {
                $currency = $this->paymentCurrency($items->first());

                return [
                    ...$currency,
                    'paid' => $items->filter(fn ($payment) => strtolower((string) $payment->transaction_status) === 'success')
                        ->sum(fn ($payment) => (float) $payment->amount),
                    'count' => $items->count(),
                ];
            })
            ->values();
    }

    private function trackingCode(): string
    {
        do {
            $code = 'MAP-'.now()->format('ymd').'-'.Str::upper(Str::random(6));
        } while (AccountPropertyRequest::where('tracking_code', $code)->exists());

        return $code;
    }
}
