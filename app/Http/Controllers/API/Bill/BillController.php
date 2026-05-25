<?php

namespace App\Http\Controllers\API\Bill;

use App\Http\Controllers\Controller;
use App\Http\Requests\API\Bill\IssueBillRequest;
use App\Http\Requests\API\OTP\SendOTPRequest;
use App\Jobs\OTP\SendOTPBillSMS;
use App\Models\Bill;
use App\Models\OTP;
use App\Models\Owner;
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
        $otp = mt_rand(100000, 999999);
        $ownerID = '';
        $bill = Bill::where('bills_id', $request->bills_id)->first();
        $existingOTP = OTP::where('bills_id', $request->bills_id)->first();

        if (! $bill) {
            return response()->json([
                'message' => 'Bill not found',
            ], 422);
        }

        if (is_null($bill->business_id) && $bill->property) {
            $ownerID = $bill->property->customer->erms_id ?? 'No owner found';
        } elseif (is_null($bill->property_id) && $bill->business) {
            $ownerID = $bill->business->customer->erms_id ?? 'No owner found';
        }

        $data = [
            'citizen_id' => $ownerID,
            'bills_id' => $request->bills_id,
            'code' => $otp ?? null,
        ];

        if (! empty($existingOTP)) {
            $existingOTP->delete();
        }

        $otp = OTP::create($data);

        dispatch(new SendOTPBillSMS($otp));

        return response()->json([
            'message' => 'Bill OTP sent successfully',
            'data' => $otp,
        ]);
    }

    public function issueBill(IssueBillRequest $request)
    {
        $bill = Bill::where('bills_id', $request->input('bills_id'))->first();
        $otp = OTP::where('code', $request->input('code'))->first();

        if (empty($otp)) {
            return response()->json([
                'message' => 'Bill OTP code does not exist',
            ], 422);
        }

        if (empty($bill)) {
            return response()->json([
                'message' => 'Customer with this bill id does not exist',
            ], 422);
        }

        if ($bill->issue_bill === 'Yes') {
            return response()->json([
                'message' => 'This bill has already been issued to the customer',
            ], 422);
        }

        if (! empty($otp)) {
            $otp->delete();
        }

        $bill->update(['issue_bill' => 'Yes', 'issued_by' => $request->user()->id]);

        return response()->json([
            'message' => 'Customer bill issued successfully',
        ]);
    }
}
