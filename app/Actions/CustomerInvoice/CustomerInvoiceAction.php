<?php

namespace App\Actions\CustomerInvoice;

use App\Models\Invoice;
use App\Models\Pickup;
use App\Models\Rate;

class CustomerInvoiceAction
{
    public static function createInvoice($data)
    {
        try {
            $rate = Rate::where('customer_id', $data['customer_id'])->first();

            if (empty($rate)) {
                return response()->json([
                    'message' => 'Customer has no rate generated, generate the rate for this customer first',
                ]);
            }

            $weight = Pickup::where('customer_id', $data['customer_id'])
                ->where('status', 'Approved')
                ->latest('created_at')
                ->first();

            if (empty($weight)) {
                return response()->json([
                    'message' => 'Customer has no pick up weight or the weight has not been approved yet',
                ]);
            }

            $data['rate'] = $rate->rate;
            $data['weight'] = $weight->weight;
            $data['amount'] = $data['weight'] * $data['rate'];

            $invoice = Invoice::create($data);
            // Send Invoice generated via SMS to the customer

            return $invoice;
        } catch (\Exception $ex) {
            return response()->json([
                'message' => 'Unable to create customer invoice '.$ex->getMessage(),
            ], 422);
        }
    }
}
