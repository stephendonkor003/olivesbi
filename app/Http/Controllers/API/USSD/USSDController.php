<?php

namespace App\Http\Controllers\API\USSD;

use App\Http\Controllers\Controller;
use App\Models\Owner;
use App\Support\OwnerRecordAccess;
use Illuminate\Http\Request;

class USSDController extends Controller
{
    public function customerInfo(Request $request, $phone)
    {
        $customer = OwnerRecordAccess::visibleOwners(Owner::query(), $request->user())->with([
            'properties' => fn ($query) => OwnerRecordAccess::visibleAssets($query, $request->user()),
            'properties.polygon.block.division',
            'properties.polygon:id,cntr_x_coord,cntr_y_coord,block_id',
            'businesses' => fn ($query) => OwnerRecordAccess::visibleAssets($query, $request->user()),
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
