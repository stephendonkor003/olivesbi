<?php

namespace App\Http\Controllers\Citizens;

use App\Http\Controllers\Controller;
use App\Http\Requests\Activate\ActivateCitizenRequest;
use App\Http\Requests\Activate\ResendOTPRequest;
use App\Http\Requests\Customer\CreateCustomerRequest;
use App\Http\Requests\Customer\CreateCustomerRequestFront;
use App\Http\Requests\Customer\UpdateCustomerRequest;
use App\Http\Requests\Import\ImportCustomerRequest;
use App\Imports\Customer\CustomersImport;
use App\Jobs\OTP\SendOTPSMS;
use App\Jobs\Registration\SendRegistrationReminder;
use App\Models\Assembly;
use App\Models\AuditTrail;
use App\Models\Bill;
use App\Models\Block;
use App\Models\Business;
use App\Models\BusinessClassType;
use App\Models\Citizen;
use App\Models\CustomerType;
use App\Models\Division;
use App\Models\NewsletterSubscription;
use App\Models\OTP;
use App\Models\Owner;
use App\Models\Payment;
use App\Models\Property;
use App\Models\RevenueStream;
use App\Models\Role;
use App\Models\ServiceRequest;
use App\Models\User;
use App\Models\Zone;
use App\Support\OlivesAccountTier;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\Schema;
use Illuminate\Validation\ValidationException;
use Mpdf\Mpdf;

class CitizenController extends Controller
{
    public function index(Request $request)
    {
        if (! auth()->user()->can('customers.view')) {
            abort(403, 'Unauthorized action.');
        }

        $citizens = Citizen::orderBy('created_at', 'DESC')
            ->when($request->user()->access_level === 'Assembly_Administrator', function ($query) use ($request) {
                $assemblyCode = $request->user()->assembly_code;

                $query->where(function ($subQuery) use ($assemblyCode) {
                    $subQuery->whereHas('properties', function ($propertyQuery) use ($assemblyCode) {
                        $propertyQuery->where('assembly_code', $assemblyCode);
                    })
                        ->orWhereHas('businesses', function ($businessQuery) use ($assemblyCode) {
                            $businessQuery->where('assembly_code', $assemblyCode);
                        });
                });
            })
            ->when($request->filled('country_of_citizenship'), function ($query) use ($request) {
                $query->where('country_of_citizenship', $request->country_of_citizenship);
            })
            ->when($request->filled('customer_type'), function ($query) use ($request) {
                $query->where('customer_type', $request->customer_type);
            })
            ->when($request->filled('status'), function ($query) use ($request) {
                $query->where('status', $request->status);
            })
            ->when($request->filled('gender'), function ($query) use ($request) {
                $query->where('gender', $request->gender);
            })
            ->when($request->filled('from_date'), function ($query) use ($request) {
                $query->where('created_at', '>=', $request->from_date);
            })
            ->when($request->filled('to_date'), function ($query) use ($request) {
                $query->where('created_at', '<=', $request->to_date);
            })
            ->get();

        // Calculate totals for male, female, and total citizens
        $totalCitizens = $citizens->count();
        $male = $citizens->where('gender', 'male')->count();
        $female = $citizens->where('gender', 'female')->count();

        // Calculate percentages for gender
        $malePercentage = $totalCitizens > 0 ? ($male / $totalCitizens) * 100 : 0;
        $femalePercentage = $totalCitizens > 0 ? ($female / $totalCitizens) * 100 : 0;

        // Calculate customer type distribution
        $customerTypes = $citizens->groupBy('customer_type');
        $customerTypeCounts = $customerTypes->map(function ($group) {
            return $group->count();
        });

        // Calculate percentages for each customer type
        $customerTypePercentages = $customerTypeCounts->map(function ($count) use ($totalCitizens) {
            return $totalCitizens > 0 ? ($count / $totalCitizens) * 100 : 0;
        });

        // Group data for heat map
        $genderCustomerStatus = $citizens->groupBy(['customer_type', 'gender', 'status']);

        // Ensure genderCustomerStatus is iterable
        if (! is_iterable($genderCustomerStatus)) {
            $genderCustomerStatus = collect(); // Default to an empty Collection
        }

        // Prepare heat map data

        $heatMapData = [];
        foreach ($genderCustomerStatus as $customerType => $genderGroups) {
            foreach ($genderGroups as $gender => $statusGroups) {
                foreach ($statusGroups as $status => $groupedItems) {
                    $heatMapData[] = [
                        'customer_type' => $customerType,
                        'gender' => ucfirst(strtolower($gender)),
                        'status' => ucfirst(strtolower($status)),
                        'count' => count($groupedItems),
                    ];
                }
            }
        }

        // Calculate total active citizens
        $totalActive = $citizens->where('status', 'active')->count();
        $inActive = $citizens->where('status', 'inactive')->count();

        // Prepare data for the view
        $totals = [
            'male' => $male,
            'female' => $female,
            'male_percentage' => round($malePercentage, 2),
            'female_percentage' => round($femalePercentage, 2),
            'customer_type_counts' => $customerTypeCounts,
            'customer_type_percentages' => $customerTypePercentages,
            'total_active' => $totalActive,
            'inactive' => $inActive,
        ];

        return view('citizens.index', compact('citizens', 'totals', 'heatMapData'));
    }

    // Show the form for creating a new resource.
    public function create(Request $request)
    {
        if (! auth()->user()->can('customers.create')) {
            abort(403, 'Unauthorized action.');
        }

        $customerTypes = CustomerType::get();

        return view('citizens.create', compact('customerTypes'));
    }

    // Store a newly created resource in storage.

    public function store(CreateCustomerRequest $request)
    {
        try {
            $data = $request->validated();

            $randomNumbers = '';
            for ($i = 0; $i < 6; $i++) {
                $randomNumbers .= mt_rand(0, 9);
            }

            // Generate unique account number
            do {
                $accountNumber = 'ERMS'.$randomNumbers;
            } while (Citizen::where('account_number', $accountNumber)->exists());

            $data['account_number'] = $accountNumber;
            $data['created_by'] = $request->user()->id;
            $data['status'] = 'Active';

            $userLoginData = [
                'name' => $data['first_name'].' '.$data['last_name'],
                'email' => $data['account_number'],
                'phone' => $data['telephone_number'],
                'password' => Hash::make(config('erms.default_password')),
                'access_level' => 'customer',
                'status' => 'Active',
            ];

            $user = User::where('phone', $data['telephone_number'])->first();

            if (! empty($user)) {
                return redirect()->route('citizens.create')->with('error', 'Phone number for user account already exist!');
            }

            $userData = User::create($userLoginData);
            $role = Role::where('name', 'like', '%customer%')->first();
            if ($role) {
                $userData->roles()->sync($role->id);
            }
            $data['user_id'] = $userData->id;

            if (! empty($userData)) {
                $customer = Citizen::create($data);
            }

            dispatch(new SendRegistrationReminder($customer));

            $serviceData = [
                'user_id' => $request->user()->id,
                'service_used' => 'Taxpayer Registration',
                'usage_date' => now(),
                'service_channel' => 'Web Portal',
                'status' => 'Completed',
            ];

            $auditTrailData = [
                'user_id' => $request->user()->id,
                'action_performed' => 'Taxpayer Registration',
                'action_date' => now(),
                'ip_address' => $request->ip(),
                'device_used' => request()->userAgent(),
                'remarks' => 'Success',
            ];

            ServiceRequest::create($serviceData);
            AuditTrail::create($auditTrailData);

            return redirect()->route('citizens.index')->with('status', 'Assembly customer created successfully!');
        } catch (\Exception $ex) {
            return $ex->getMessage();
        }
    }

    public function frontstore(CreateCustomerRequestFront $request)
    {
        //        try {
        $data = $request->validated();

        $randomNumbers = '';
        for ($i = 0; $i < 6; $i++) {
            $randomNumbers .= mt_rand(0, 9);
        }

        // Generate unique account number
        do {
            $accountNumber = 'ERMS'.$randomNumbers;
        } while (Citizen::where('account_number', $accountNumber)->exists());

        $data['account_number'] = $accountNumber;
        $data['created_by'] = $request->user()->id ?? 'customer';
        $data['status'] = 'Active';

        if ($request->input('registration_type') === 'organization') {
            $data['first_name'] = $data['org_first_name'];
            $data['last_name'] = $data['org_last_name'];
            $data['telephone_number'] = $data['org_telephone_number'];
            $data['password'] = $data['org_password'];
        }

        $requestedPlan = $request->input('account_plan', 'account');
        $accessLevel = match ($requestedPlan) {
            'subscriber' => 'Subscriber',
            'pro' => 'Olives_Pro_Applicant',
            'sky' => 'Olives_Sky_Applicant',
            'cloud' => 'Olives_Cloud_Applicant',
            'shell' => 'Olives_Shell_Applicant',
            default => 'customer',
        };

        $userLoginData = [
            'name' => $data['first_name'].' '.$data['last_name'],
            'email' => $data['email'] ?? $data['account_number'],
            'phone' => $data['telephone_number'],
            'password' => Hash::make($data['password']),
            'access_level' => $accessLevel,
            'status' => 'Active',
        ];

        $user = User::where('phone', $data['telephone_number'])->first();

        if (! empty($user)) {
            $key = $request->input('registration_type') === 'organization' ? 'org_telephone_number' : 'telephone_number';
            throw ValidationException::withMessages([
                $key => 'Phone number for user account already exist!',
            ]);
        }

        $userData = User::create($userLoginData);
        $role = Role::where('name', 'like', '%customer%')->first();

        if ($role) {
            $userData->roles()->sync([$role->id]);
        }

        $data['user_id'] = $userData->id;

        if (! empty($userData)) {
            $customer = Citizen::create($data);
        }

        if ($request->boolean('newsletter_opt_in') && ! empty($data['email'])) {
            NewsletterSubscription::updateOrCreate(
                ['email' => $data['email']],
                [
                    'name' => trim(($data['first_name'] ?? '').' '.($data['last_name'] ?? '')) ?: ($data['business_name'] ?? null),
                    'phone' => $data['telephone_number'] ?? null,
                    'audience_type' => $request->input('registration_type'),
                    'topics' => $request->input('newsletter_topics', []),
                    'source' => 'registration',
                    'subscribed_at' => now(),
                ]
            );
        }

        dispatch(new SendRegistrationReminder($customer));
        // dispatch(new SendOTPSMS($customer))->delay(now()->addSeconds(5));

        return redirect()->route('auth.index')->with('status', 'Account created successfully. You can sign in with your email, phone number, or Olives account card.');
        //        } catch (\Exception $ex) {
        //            return $ex->getMessage();
        //        }
    }

    public function activate()
    {
        return view('auth.activate');
    }

    public function activateCitizen(ActivateCitizenRequest $request)
    {
        $code = OTP::where('code', $request->code)->first();

        if (empty($code)) {
            return redirect()->route('citizens.activate')->with('error', 'The code entered is invalid try again!');
        }

        $customer = Citizen::where('id', $code->citizen_id)->first();

        if ($customer) {
            $user = User::where('id', $customer->user_id)->first();

            $customer->update(['status' => 'Active']);
            $user->update(['status' => 'Active']);

            dispatch(new SendRegistrationReminder($customer));
        }

        return redirect()->route('auth.index')->with('status', 'Account activated successfully');
    }

    public function resend()
    {
        return view('auth.resend');
    }

    public function resendOTP(ResendOTPRequest $request)
    {
        $customer = Citizen::where('telephone_number', $request->telephone_number)->first();

        if (empty($customer)) {
            return redirect()->route('citizens.resend')->with('error', 'Telephone number does not exist for any of the account!');
        }

        dispatch(new SendOTPSMS($customer));

        return redirect()->route('citizens.activate')->with('status', 'OTP resent successfully');
    }

    // Display the specified resource.
    public function show(Request $request, Citizen $citizen)
    {
        $bills = Bill::orderBy('created_at', 'DESC')
            ->with(['property', 'business'])
            ->where(function ($query) use ($citizen) {
                $query->whereHas('property', function ($q) use ($citizen) {
                    $q->where('customer_name', $citizen->id);
                })
                    ->whereNotNull('property_id')
                    ->whereNull('business_id');
            })
            ->orWhere(function ($query) use ($citizen) {
                $query->whereHas('business', function ($q) use ($citizen) {
                    $q->where('citizen_account_number', $citizen->id);
                })
                    ->whereNotNull('business_id')
                    ->whereNull('property_id');
            })
            ->get();

        $payments = Payment::orderBy('created_at', 'DESC')
            ->whereHas('bill', function ($query) use ($citizen) {
                $query->whereHas('property', function ($propertyQuery) use ($citizen) {
                    $propertyQuery->where('customer_name', $citizen->id)
                        ->whereNotNull('property_id')
                        ->whereNull('business_id');
                })
                    ->orWhereHas('business', function ($businessQuery) use ($citizen) {
                        $businessQuery->where('citizen_account_number', $citizen->id)
                            ->whereNotNull('business_id')
                            ->whereNull('property_id');
                    });
            })
            ->where('transaction_status', 'Success')
            ->get();

        $properties = Property::orderBy('created_at', 'DESC')
            ->where('customer_name', $citizen->id)
            ->get();

        $businesses = Business::orderBy('created_at', 'DESC')
            ->where('citizen_account_number', $citizen->id)
            ->get();

        $totalArrears = $bills->sum('arrears');
        $totalAmount = $bills->sum('amount');

        $customerData = [
            'properties' => isset($properties) ? $properties : [],
            'businesses' => isset($businesses) ? $businesses : [],
            'total' => isset($properties) ? number_format(collect($properties)->sum('ratable_value'), 2) : 0,
            'bills' => isset($bills) ? $bills : [],
            'payments' => isset($payments) ? $payments : [],
            'totalArrears' => isset($totalArrears) ? number_format($totalArrears, 2) : 0,
            'totalAmount' => isset($totalAmount) ? number_format($totalAmount, 2) : 0,
            'totalDue' => isset($totalArrears) && isset($totalAmount) ? number_format($totalArrears + $totalAmount, 2) : 0,
            'paymentTotal' => isset($payments) ? number_format(collect($payments)->sum('amount'), 2) : 0,
        ];

        return view('citizens.show', compact('citizen', 'customerData'));
    }

    // Show the form for editing the specified resource.
    public function edit(Request $request, Citizen $citizen)
    {
        if (! auth()->user()->can('customers.update')) {
            abort(403, 'Unauthorized action.');
        }

        $customerTypes = CustomerType::get();

        return view('citizens.edit', compact('citizen', 'customerTypes'));
    }

    // Update the specified resource in storage.
    public function update(UpdateCustomerRequest $request, Citizen $citizen)
    {
        $citizen->update($request->validated());

        return redirect()->route('citizens.index')->with('status', 'Citizen updated successfully!');
    }

    // Remove the specified resource from storage.
    public function destroy($id)
    {
        $citizen = Citizen::findOrFail($id);
        $citizen->delete();

        return redirect()->route('citizens.index')->with('success', 'Citizen deleted successfully!');
    }

    public function viewBill(Request $request, Bill $bill)
    {
        $this->authorizeCustomerRecord($request, $bill);

        if ($this->usesOlivesAccountLayout($request)) {
            $bill->loadMissing([
                'assembly',
                'property.assembly',
                'property.customer',
                'business.assembly',
                'business.customer',
                'successfulPayments',
                'payments',
            ]);

            return view('dashboard.tiers.account.bill-show', compact('bill'));
        }

        return view('dashboard.index-bill', compact('bill'));
    }

    public function viewProperty(Request $request, Property $property)
    {
        $this->authorizeCustomerRecord($request, $property);

        if ($this->usesOlivesAccountLayout($request)) {
            $property->loadMissing([
                'assembly',
                'customer',
                'entityType',
                'revenueStream',
                'zone',
                'propertyUse',
                'division',
                'block',
            ]);

            $propertyBills = Bill::with(['assembly', 'successfulPayments'])
                ->where('property_id', $property->uuid)
                ->latest()
                ->get();

            return view('dashboard.tiers.account.property-show', compact('property', 'propertyBills'));
        }

        return view('dashboard.index-property', compact('property'));
    }

    public function viewBusiness(Request $request, Business $business)
    {
        $this->authorizeCustomerRecord($request, $business);

        if ($this->usesOlivesAccountLayout($request)) {
            $business->loadMissing([
                'assembly',
                'customer',
                'businessType',
                'businessClass',
                'revenueStream',
                'zone',
                'propertyUse',
                'division',
                'block',
            ]);

            $businessBills = Bill::with(['assembly', 'successfulPayments'])
                ->where('business_id', $business->uuid)
                ->latest()
                ->get();

            return view('dashboard.tiers.account.business-show', compact('business', 'businessBills'));
        }

        return view('dashboard.index-business', compact('business'));
    }

    public function viewPayment(Request $request, Payment $payment)
    {
        $this->authorizeCustomerRecord($request, $payment);

        if ($this->usesOlivesAccountLayout($request)) {
            $payment->loadMissing([
                'assembly',
                'bill.assembly',
                'bill.property.assembly',
                'bill.property.customer',
                'bill.business.assembly',
                'bill.business.customer',
            ]);

            return view('dashboard.tiers.account.payment-show', compact('payment'));
        }

        return view('dashboard.index-payment', compact('payment'));
    }

    public function viewBillInvoice(Request $request, Bill $bill)
    {
        $this->authorizeCustomerRecord($request, $bill);
        $this->prepareBillReceipt($bill);

        return view('dashboard.tiers.account.documents.bill-invoice-page', compact('bill'));
    }

    public function downloadBillInvoice(Request $request, Bill $bill)
    {
        $this->authorizeCustomerRecord($request, $bill);
        $this->prepareBillReceipt($bill);

        $html = view('dashboard.tiers.account.documents.bill-invoice', compact('bill'))->render();
        $mpdf = new Mpdf(['format' => 'A5-L']);
        $mpdf->WriteHTML($html);

        return response($mpdf->Output('', 'S'), 200, [
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => 'attachment; filename="bill-'.$bill->bills_id.'.pdf"',
        ]);
    }

    public function viewPaymentReceipt(Request $request, Payment $payment)
    {
        $this->authorizeCustomerRecord($request, $payment);
        $payment->loadMissing([
            'assembly',
            'bill.assembly',
            'bill.property.assembly',
            'bill.property.customer',
            'bill.business.assembly',
            'bill.business.customer',
        ]);

        return view('dashboard.tiers.account.documents.payment-receipt-page', compact('payment'));
    }

    public function downloadPaymentReceipt(Request $request, Payment $payment)
    {
        $this->authorizeCustomerRecord($request, $payment);
        $payment->loadMissing([
            'assembly',
            'bill.assembly',
            'bill.property.assembly',
            'bill.property.customer',
            'bill.business.assembly',
            'bill.business.customer',
        ]);

        $html = view('dashboard.tiers.account.documents.payment-receipt', compact('payment'))->render();
        $mpdf = new Mpdf(['format' => 'A4']);
        $mpdf->WriteHTML($html);
        $reference = $payment->transaction_id ?: ($payment->req_transaction_id ?: $payment->uuid);

        return response($mpdf->Output('', 'S'), 200, [
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => 'attachment; filename="payment-receipt-'.$reference.'.pdf"',
        ]);
    }

    private function authorizeCustomerRecord(Request $request, Bill|Property|Business|Payment $record): void
    {
        if (! $this->usesOlivesAccountLayout($request)) {
            return;
        }

        $citizen = Citizen::where('user_id', $request->user()->id)->first();
        $owner = Schema::hasColumn('entity_owners', 'user_id')
            ? Owner::where('user_id', $request->user()->id)->first()
            : null;
        $owner = $owner ?: $request->user()->customer;
        $ownerId = $owner?->erms_id ?: $request->user()->erms_id;

        if (! $this->recordBelongsToCustomer($record, $citizen, $ownerId)) {
            abort(403, 'This record is not linked to your account.');
        }
    }

    private function isOlivesAccountHolder(User $user): bool
    {
        return $user->access_level === 'customer'
            || OlivesAccountTier::fromUser($user) === OlivesAccountTier::ACCOUNT;
    }

    private function usesOlivesAccountLayout(Request $request): bool
    {
        return $this->isOlivesAccountHolder($request->user())
            || str_starts_with((string) $request->route()?->getName(), 'dashboard.olives-account.');
    }

    private function prepareBillReceipt(Bill $bill): void
    {
        $bill->loadMissing([
            'assembly',
            'property.customer',
            'property.propertyUse',
            'property.zone',
            'business.customer',
            'business.propertyUse',
            'business.zone',
        ]);

        if ($bill->property && $bill->property->customer) {
            $rateImposed = RevenueStream::where('id', $bill->property->prop_rev_id)
                ->where('assembly_code', $bill->property->assembly_code)
                ->first();
        } elseif ($bill->business && $bill->business->customer) {
            $rateImposed = RevenueStream::where('id', $bill->business->buss_rev_id)
                ->where('assembly_code', $bill->business->assembly_code)
                ->first();
        }

        $bill->rate_imposed = isset($rateImposed) ? ($rateImposed->rate_impost ?? $rateImposed->flat_rate) : 0;
    }

    private function recordBelongsToCustomer(Bill|Property|Business|Payment $record, ?Citizen $citizen, ?string $ownerId): bool
    {
        if ($record instanceof Property) {
            return ($ownerId && Schema::hasColumn('properties', 'owner_id') && (string) $record->owner_id === (string) $ownerId)
                || ($citizen && Schema::hasColumn('properties', 'customer_name') && (string) $record->customer_name === (string) $citizen->id);
        }

        if ($record instanceof Business) {
            return ($ownerId && Schema::hasColumn('businesses', 'owner_id') && (string) $record->owner_id === (string) $ownerId)
                || ($citizen && Schema::hasColumn('businesses', 'citizen_account_number') && (string) $record->citizen_account_number === (string) $citizen->id);
        }

        if ($record instanceof Bill) {
            $propertyMatches = $record->property_id && Property::whereUuidOrId($record->property_id)
                ->where(function ($query) use ($citizen, $ownerId) {
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
                ->exists();

            $businessMatches = $record->business_id && Business::whereUuidOrId($record->business_id)
                ->where(function ($query) use ($citizen, $ownerId) {
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
                ->exists();

            return $propertyMatches || $businessMatches;
        }

        return Bill::where('bills_id', $record->bills_id)
            ->get()
            ->contains(fn (Bill $bill) => $this->recordBelongsToCustomer($bill, $citizen, $ownerId));
    }

    public function import()
    {
        return view('citizens.import');
    }

    public function importData(ImportCustomerRequest $request)
    {
        try {
            $data = $request->validated();
            $createdBy = $request->user()->id;

            $import = (new CustomersImport($createdBy));
            $import->import($request->file('file'));

            return redirect()->route('citizens.index')->with('status', 'Rate payer data uploaded successfully.');
        } catch (\Maatwebsite\Excel\Validators\ValidationException $exception) {
            throw ValidationException::withMessages([
                'file' => collect($exception->errors())->flatten()->toArray(),
            ]);
        }
    }

    public function downloadTemplate()
    {
        $filePath = public_path('assets/templates/rate_payer_template.xlsx');

        if (! file_exists($filePath)) {
            return response()->json(['error' => 'File not found'], 404);
        }

        return Response::download($filePath, 'rate_payer_template.xlsx');
    }

    public function linkProperty(Request $request)
    {
        $businessClassTypes = BusinessClassType::orderBy('name', 'ASC')->get();
        $customers = Citizen::where('user_id', $request->user()->id)->get();

        $assemblies = Assembly::orderBy('name', 'ASC')
            ->when(! empty($request->user()->regional_code), function ($query) use ($request) {
                $query->where('regional_code', $request->user()->regional_code);
            })
            ->when(! empty($request->user()->assembly_code), function ($query) use ($request) {
                $query->where('assembly_code', $request->user()->assembly_code);
            })
            ->get();

        $zones = Zone::orderBy('name', 'ASC')
            ->when(! empty($request->user()->regional_code), function ($query) use ($request) {
                $query->whereHas('assembly', function ($q) use ($request) {
                    $q->where('regional_code', $request->user()->regional_code);
                });
            })
            ->when(! empty($request->user()->assembly_code), function ($query) use ($request) {
                $query->where('assembly_code', $request->user()->assembly_code);
            })
            ->get();

        $divisions = Division::orderBy('division_name', 'ASC')
            ->when(! empty($request->user()->assembly_code), function ($query) use ($request) {
                $query->where('assembly_code', $request->user()->assembly_code);
            })
            ->get();

        $blocks = Block::orderBy('block_name', 'ASC')
            ->when(! empty($request->user()->assembly_code), function ($query) use ($request) {
                $query->where('assembly_code', $request->user()->assembly_code);
            })
            ->get();

        $property = Property::query()
            ->when($request->filled('property_number'), function ($query) use ($request) {
                $query->where('property_number', $request->property_number);
            })
            ->orderBy('created_at', 'DESC')
            ->whereNull('customer_name')
            ->first();

        return view('dashboard.linkproperty', compact(
            'businessClassTypes',
            'customers',
            'assemblies',
            'zones',
            'property',
            'divisions',
            'blocks'
        ));
    }
}
