<?php

namespace App\Http\Controllers\API\Customer;

use App\Http\Controllers\Controller;
use App\Http\Requests\API\Customer\CreateCustomerRequest;
use App\Http\Requests\API\Customer\UpdateCustomerRequest;
use App\Jobs\Registration\SendRegistrationReminder;
use App\Models\Owner;
use App\Models\Property;
use App\Models\User;
use App\Support\NewAccountSetup;

class CustomerController extends Controller
{
    public function index()
    {
        $data = Owner::orderBy('created_at', 'DESC')
            ->paginate(100);

        return response()->json([
            'message' => 'Get all customers',
            'data' => $data,
        ]);
    }

    public function store(CreateCustomerRequest $request)
    {
        $data = $request->validated();

        $randomNumbers = '';
        for ($i = 0; $i < 6; $i++) {
            $randomNumbers .= mt_rand(0, 9);
        }

        // Generate unique account number
        do {
            $erms_id = 'ERMS'.$randomNumbers;
        } while (Owner::where('erms_id', $erms_id)->exists());

        $data['erms_id'] = $erms_id;
        $data['created_by'] = $request->user()->id;
        $data['fname'] = $request->input('first_name');
        $data['lname'] = $request->input('last_name');

        $userLoginData = [
            'name' => $data['first_name'].' '.$data['last_name'],
            'email' => $data['email'] ?? $erms_id.'@erms.com',
            'phone' => $data['phone_no_primary'],
            'password' => NewAccountSetup::passwordHash(),
            'access_level' => 'customer',
            'status' => 'Active',
            'erms_id' => $data['erms_id'],
        ];

        $user = User::where('phone', $data['phone_no_primary'])->first();

        if (! empty($user)) {
            return response()->json([
                'message' => 'Phone number for user account already exist!',
            ], 422);
        }

        $userData = User::create($userLoginData);

        if (! empty($userData)) {
            $customer = Owner::create($data);
        }

        // dispatch(new SendRegistrationReminder($customer));

        return response()->json([
            'message' => 'Customer created successfully',
            'data' => $customer,
        ] + NewAccountSetup::response());
    }

    public function frontstore(CreateCustomerRequest $request)
    {
        $data = $request->validated();

        $randomNumbers = '';
        for ($i = 0; $i < 6; $i++) {
            $randomNumbers .= mt_rand(0, 9);
        }

        // Generate unique account number
        do {
            $erms_id = 'ERMS'.$randomNumbers;
        } while (Owner::where('erms_id', $erms_id)->exists());

        $data['erms_id'] = $erms_id;
        $data['created_by'] = $request->user()->id ?? null;
        $data['fname'] = $request->input('first_name');
        $data['lname'] = $request->input('last_name');

        $userLoginData = [
            'name' => $data['first_name'].' '.$data['last_name'],
            'email' => $data['email'] ?? $erms_id.'@erms.com',
            'phone' => $data['phone_no_primary'],
            'password' => NewAccountSetup::passwordHash(),
            'access_level' => 'customer',
            'status' => 'Active',
            'erms_id' => $data['erms_id'],
        ];

        $user = User::where('phone', $data['phone_no_primary'])->first();

        if (! empty($user)) {
            return response()->json([
                'message' => 'Phone number for user account already exist!',
            ], 422);
        }

        $userData = User::create($userLoginData);

        if (! empty($userData)) {
            $customer = Owner::create($data);
        }

        // dispatch(new SendRegistrationReminder($customer));

        return response()->json([
            'message' => 'Customer created successfully',
            'data' => $customer,
        ] + NewAccountSetup::response());
    }

    public function show($uuid)
    {
        $customer = Owner::query()
            ->where(function ($query) use ($uuid) {
                $query->whereUuidOrId($uuid)
                    ->orWhere('erms_id', $uuid);
            })
            ->first();

        if (empty($customer)) {
            return response()->json([
                'message' => 'Customer not found',
            ], 422);
        }

        return response()->json([
            'message' => 'Get particular customer',
            'data' => $customer,
        ]);
    }

    public function multipleParam($uuid)
    {
        $customer = Owner::with([
            'user',
            'properties.polygon.block.division',
            'properties.polygon:id,cntr_x_coord,cntr_y_coord,block_id',
        ])
            ->where(function ($query) use ($uuid) {
                $query->whereUuidOrId($uuid)
                    ->orWhere('erms_id', $uuid)
                    ->orWhere('tin_no', $uuid)
                    ->orWhere('fname', 'LIKE', "%$uuid%")
                    ->orWhere('lname', 'LIKE', "%$uuid%")
                    ->orWhere('phone_no_primary', $uuid)
                    ->orWhere('digital_address', $uuid);
            })->get();

        if (count($customer) == 0) {
            return response()->json([
                'message' => 'Customer not found',
            ], 422);
        }

        return response()->json([
            'message' => 'Get particular customer',
            'data' => $customer,
        ]);
    }

    public function update(UpdateCustomerRequest $request, $uuid)
    {
        $customer = Owner::query()
            ->where(function ($query) use ($uuid) {
                $query->whereUuidOrId($uuid)
                    ->orWhere('erms_id', $uuid);
            })
            ->first();

        if (empty($customer)) {
            return response()->json([
                'message' => 'Customer not found',
            ], 422);
        }

        $data = $request->validated();
        $data['fname'] = $request->input('first_name');
        $data['lname'] = $request->input('last_name');

        $customer->update($data);

        $user = User::where('erms_id', $customer->erms_id)
            ->with(['customer'])
            ->first();

        return response()->json([
            'message' => 'Customer updated successfully',
            'data' => $user,
        ]);
    }

    public function globalSearch($uuid)
    {
        $customer = Owner::with([
            'properties.customer:id,erms_id,fname,lname,phone_no_primary,email,tin_no',
            'properties.bills.payments',
            'properties.assembly',
            'properties.polygon.block.division',
            'properties.polygon:id,cntr_x_coord,cntr_y_coord,block_id',
        ])
            ->where(function ($query) use ($uuid) {
                $query->whereUuidOrId($uuid)
                    ->orWhere('erms_id', $uuid)
                    ->orWhere('tin_no', $uuid)
                    ->orWhere('fname', 'LIKE', "%$uuid%")
                    ->orWhere('lname', 'LIKE', "%$uuid%")
                    ->orWhere('phone_no_primary', $uuid)
                    ->orWhere('digital_address', $uuid);
            })
            ->get();

        $property = Property::with([
            'customer' => function ($query) {
                $query->select(
                    'id',
                    'erms_id',
                    'fname',
                    'lname',
                    'tin_no',
                    'phone_no_primary',
                    'digital_address'
                );
            },
            'bills.payments',
            'polygon.block.division',
            'polygon:id,cntr_x_coord,cntr_y_coord,block_id',
        ])
            ->where(function ($query) use ($uuid) {
                $query->whereUuidOrId($uuid)
                    ->orWhere('erms_id', $uuid)
                    ->orWhere('account_no', $uuid)
                    ->orWhere('digitaladdress', $uuid)
                    ->orWhere('streetname', 'LIKE', "%$uuid%")
                    ->orWhere('location', 'LIKE', "%$uuid%");
            })
            ->get();

        $customerProperties = $customer->flatMap(function ($citizen) {
            return $citizen->properties;
        });

        $allProperties = $property->concat($customerProperties)->unique('id')->values();

        $propertyCustomers = $property->map(function ($prop) {
            return $prop->customer;
        })->filter()->unique('id')->values();

        $allCustomers = $customer->concat($propertyCustomers)->unique('id')->values();

        if ($allCustomers->isEmpty() && $allProperties->isEmpty()) {
            return response()->json([
                'message' => 'No record found for this customer/property',
            ], 422);
        }

        return response()->json([
            'message' => 'Get customer and property details',
            'data' => [
                'customers' => $allCustomers,
                'properties' => $allProperties,
            ],
        ]);
    }
}
