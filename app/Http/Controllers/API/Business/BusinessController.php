<?php

namespace App\Http\Controllers\API\Business;

use App\Http\Controllers\Controller;
use App\Models\Business;
use App\Models\Citizen;
use Illuminate\Http\Request;

class BusinessController extends Controller
{
    public function index(Request $request)
    {
        $data = Business::orderBy('created_at', 'DESC')
            ->with([
                'customer',
                'businessClass',
                'businessType',
                'businessOwner',
                'assembly',
                'polygon.block.division',
                'polygon:id,cntr_x_coord,cntr_y_coord,block_id',
            ])
            ->when(! empty($request->user()->assembly_code), function ($query) use ($request) {
                $query->where('assembly_code', $request->user()->assembly_code);
            })
            ->paginate(20);

        return response()->json([
            'message' => 'Get all businesses',
            'data' => $data,
        ]);
    }

    public function show($id)
    {
        $business = Business::where('id', $id)
            ->with([
                'customer',
                'businessClass',
                'businessType',
                'businessOwner',
                'assembly',
                'polygon.block.division',
                'polygon:id,cntr_x_coord,cntr_y_coord,block_id',
            ])
            ->first();

        if (empty($business)) {
            return response()->json([
                'message' => 'Customer business permit not found',
            ], 422);
        }

        return response()->json([
            'message' => 'Get particular customer business permit',
            'data' => $business,
        ]);
    }

    public function customerBusiness($id)
    {
        $customer = Citizen::where('user_id', $id)
            ->orWhere('id', $id)
            ->first();

        if (empty($customer)) {
            return response()->json([
                'message' => 'Customer has no business permit, check and enter the customer id again',
            ], 422);
        }

        $business = Business::where('citizen_account_number', $customer->id)
            ->with(['customer', 'businessClass', 'businessType', 'businessOwner', 'assembly', 'division', 'block'])
            ->get();

        if (count($business) == 0) {
            return response()->json([
                'message' => 'Customer business permit not found',
            ], 422);
        }

        return response()->json([
            'message' => 'Get particular customer business permit',
            'data' => $business,
        ]);
    }
}
