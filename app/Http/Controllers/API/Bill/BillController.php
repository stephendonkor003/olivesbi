<?php

namespace App\Http\Controllers\API\Bill;

use App\Http\Controllers\Controller;
use App\Http\Requests\API\Bill\IssueBillRequest;
use App\Http\Requests\API\OTP\SendOTPRequest;
use App\Jobs\OTP\SendOTPBillSMS;
use App\Models\Bill;
use App\Models\Owner;
use App\Support\BillOtpCodes;
use Illuminate\Http\Request;

class BillController extends Controller
{
    public function index(Request $request)
    {
        $data = Bill::orderBy('created_at', 'DESC')
            ->with([
                'property.polygon.block.division',
                'property.polygon:id,cntr_x_coord,cntr_y_coord,block_id',
                'business.polygon:id,cntr_x_coord,cntr_y_coord,block_id',
                'assembly',
                'payments',
            ])
            ->when(! empty($request->user()->assembly_code), function ($query) use ($request) {
                $query->where('assembly_code', $request->user()->assembly_code);
            })
            ->get()
            ->map(function ($bill) {
                if (is_null($bill->property_id)) {
                    $bill->bill_type = 'Business Bill';
                } elseif (is_null($bill->business_id)) {
                    $bill->bill_type = 'Property Bill';
                } else {
                    $bill->bill_type = 'Unknown';
                }

                return $bill;
            });

        return response()->json([
            'message' => 'Get all bills',
            'data' => $data,
        ]);
    }

    public function myBills(Request $request)
    {
        $owner = auth()->user()->customer;

        $data = Bill::query()
            ->with([
                'property.polygon.block.division',
                'property.polygon:id,cntr_x_coord,cntr_y_coord,block_id',
                'business.polygon:id,cntr_x_coord,cntr_y_coord,block_id',
                'assembly',
                'payments',
            ])
            ->where(function ($query) use ($owner) {
                $query->whereHas('property', fn ($q) => $q->where('owner_id', $owner->erms_id))
                    ->orWhereHas('business', fn ($q) => $q->where('owner_id', $owner->erms_id));
            })->orderBy('created_at', 'DESC')
            ->get()
            ->map(function ($bill) {
                if (is_null($bill->property_id)) {
                    $bill->bill_type = 'Business Bill';
                } elseif (is_null($bill->business_id)) {
                    $bill->bill_type = 'Property Bill';
                } else {
                    $bill->bill_type = 'Unknown';
                }

                return $bill;
            });

        return response()->json([
            'message' => 'Get all bills',
            'data' => $data,
        ]);
    }

    public function show($uuid)
    {
        $bill = Bill::with([
            'property.polygon.block.division',
            'property.polygon:id,cntr_x_coord,cntr_y_coord,block_id',
            'business.polygon:id,cntr_x_coord,cntr_y_coord,block_id',
            'assembly',
            'payments',
        ])
            ->where(function ($query) use ($uuid) {
                $query->whereUuidOrId($uuid)
                    ->orWhere('bills_id', $uuid);
            })
            ->first();

        if ($bill) {
            if (is_null($bill->property_id)) {
                $bill->bill_type = 'Business Bill';
            } elseif (is_null($bill->business_id)) {
                $bill->bill_type = 'Property Bill';
            } else {
                $bill->bill_type = 'Unknown';
            }
        }

        if (empty($bill)) {
            return response()->json([
                'message' => 'Bill not found',
            ], 422);
        }

        return response()->json([
            'message' => 'Get particular bill',
            'data' => $bill,
        ]);
    }

    public function customerBill($uuid)
    {
        $customer = Owner::query()
            ->where(function ($query) use ($uuid) {
                $query->whereUuidOrId($uuid)
                    ->orWhere('erms_id', $uuid);
            })
            ->first();

        if (empty($customer)) {
            return response()->json([
                'message' => 'Customer not found, enter customer account id',
            ], 422);
        }

        $bills = Bill::orderBy('created_at', 'DESC')
            ->with([
                'property.polygon.block.division',
                'property.polygon:id,cntr_x_coord,cntr_y_coord,block_id',
                'business.polygon:id,cntr_x_coord,cntr_y_coord,block_id',
                'assembly',
                'payments',
            ])
            ->where(function ($query) use ($customer) {
                $query->whereHas('property', function ($q) use ($customer) {
                    $q->where('owner_id', $customer->erms_id);
                })
                    ->whereNotNull('property_id')
                    ->whereNull('business_id');
            })
            ->orWhere(function ($query) use ($customer) {
                $query->whereHas('business', function ($q) use ($customer) {
                    $q->where('owner_id', $customer->erms_id);
                })
                    ->whereNotNull('business_id')
                    ->whereNull('property_id');
            })
            ->get()
            ->map(function ($bill) {
                if (is_null($bill->property_id)) {
                    $bill->bill_type = 'Business Bill';
                } elseif (is_null($bill->business_id)) {
                    $bill->bill_type = 'Property Bill';
                } else {
                    $bill->bill_type = 'Unknown';
                }

                return $bill;
            });

        return response()->json([
            'message' => 'Get all bills',
            'data' => $bills,
        ]);
    }

    public function pendingBill($uuid)
    {
        $customer = Owner::query()
            ->where(function ($query) use ($uuid) {
                $query->whereUuidOrId($uuid)
                    ->orWhere('erms_id', $uuid);
            })
            ->first();

        if (empty($customer)) {
            return response()->json([
                'message' => 'Customer not found, enter customer account id',
            ], 422);
        }

        $bills = Bill::orderBy('created_at', 'DESC')
            ->with([
                'property.polygon.block.division',
                'property.polygon:id,cntr_x_coord,cntr_y_coord,block_id',
                'business.polygon:id,cntr_x_coord,cntr_y_coord,block_id',
                'assembly',
                'payments',
            ])
            ->where(function ($query) use ($customer) {
                $query->whereHas('property', function ($q) use ($customer) {
                    $q->where('owner_id', $customer->erms_id);
                })
                    ->whereNotNull('property_id')
                    ->whereNull('business_id');
            })
            ->orWhere(function ($query) use ($customer) {
                $query->whereHas('business', function ($q) use ($customer) {
                    $q->where('owner_id', $customer->erms_id);
                })
                    ->whereNotNull('business_id')
                    ->whereNull('property_id');
            })
            ->whereDoesntHave('payments', function ($query) {
                $query->whereColumn('payments.bills_id', 'bills.id');
            })
            ->get()
            ->map(function ($bill) {
                if (is_null($bill->property_id)) {
                    $bill->bill_type = 'Business Bill';
                } elseif (is_null($bill->business_id)) {
                    $bill->bill_type = 'Property Bill';
                } else {
                    $bill->bill_type = 'Unknown';
                }

                return $bill;
            });

        return response()->json([
            'message' => 'Get all pending bills',
            'data' => $bills,
        ]);
    }

    public function sendOTP(SendOTPRequest $request)
    {
        $otp = BillOtpCodes::create($request->user(), $request->validated('bills_id'));
        try {
            dispatch(new SendOTPBillSMS($otp));
        } catch (\Throwable $exception) {
            BillOtpCodes::discardFailed($otp);
            throw $exception;
        }

        return response()->json([
            'message' => 'Bill OTP sent successfully',
            'data' => $otp->only(['id', 'uuid', 'bills_id', 'citizen_id', 'type', 'created_at', 'updated_at']),
        ]);
    }

    public function issueBill(IssueBillRequest $request)
    {
        BillOtpCodes::issue($request->user(), $request->validated('bills_id'), $request->validated('code'));

        return response()->json([
            'message' => 'Customer bill issued successfully',
        ]);
    }
}
