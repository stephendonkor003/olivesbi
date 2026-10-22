<?php

namespace App\Http\Controllers\API\USSD;

use App\Http\Controllers\Controller;
use App\Models\Owner;

class USSDController extends Controller
{
    public function customerInfo($phone)
    {
        $customer = Owner::with([
            'properties.polygon.block.division',
            'properties.polygon:id,cntr_x_coord,cntr_y_coord,block_id',
            'businesses',
            'customerType',
            'user',
        ])
            ->where('phone_no_primary', $phone)
            ->first();

        if (empty($customer)) {
            return response()->json([
                'message' => 'Customer record not found',
            ], 422);
        }

        return response()->json([
            'message' => 'Get customr records',
            'data' => $customer,
        ]);
    }
}
