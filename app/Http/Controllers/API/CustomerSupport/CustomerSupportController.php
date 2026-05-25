<?php

namespace App\Http\Controllers\API\CustomerSupport;

use App\Http\Controllers\Controller;
use App\Http\Requests\API\CustomerSupport\CreateCustomerSupportRequest;
use App\Http\Requests\API\CustomerSupport\UpdateCustomerSupportRequest;
use App\Models\CustomerSupport;
use App\Models\User;
use Illuminate\Http\Request;

class CustomerSupportController extends Controller
{
    public function index(Request $request)
    {
        $data = CustomerSupport::orderBy('created_at', 'DESC')
            ->with(['user', 'assembly', 'responseBy'])
            ->when(! empty($request->user()->assembly_code), function ($query) use ($request) {
                $query->where('assembly_code', $request->user()->assembly_code);
            })
            ->get();

        return response()->json([
            'message' => 'Get all customer supports',
            'data' => $data,
        ]);
    }

    public function store(CreateCustomerSupportRequest $request)
    {
        $data = $request->validated();
        $data['user_id'] = $request->user()->id ?? '';
        $data['assembly_code'] = $request->user()->assembly_code ?? '';
        $data['created_by'] = $request->user()->id ?? '';

        $customerSupport = CustomerSupport::create($data);

        return response()->json([
            'message' => 'Customer support created successfully',
            'data' => $customerSupport,
        ]);
    }

    public function show($uuid)
    {
        $customerSupport = CustomerSupport::whereUuidOrId($uuid)
            ->with(['user', 'assembly', 'responseBy'])
            ->first();

        if (empty($customerSupport)) {
            return response()->json([
                'message' => 'Customer support not found',
            ], 422);
        }

        return response()->json([
            'message' => 'Get particular customer support',
            'data' => $customerSupport,
        ]);
    }

    public function userShow($uuid)
    {
        $user = User::whereUuidOrId($uuid)->first();

        $customerSupport = CustomerSupport::query()
            ->when($user, function ($query) use ($user) {
                $query->where('user_id', $user->id)
                    ->orWhere('created_by', $user->id);
            }, function ($query) use ($uuid) {
                $query->whereUuidOrId($uuid);
            })
            ->with(['user', 'assembly', 'responseBy'])
            ->get();

        return response()->json([
            'message' => 'Get customer supports',
            'data' => $customerSupport,
        ]);
    }

    public function update(UpdateCustomerSupportRequest $request, $uuid)
    {
        $customerSupport = CustomerSupport::whereUuidOrId($uuid)
            ->with(['user', 'assembly', 'responseBy'])
            ->first();

        if (empty($customerSupport)) {
            return response()->json([
                'message' => 'Customer support not found',
            ], 422);
        }

        $customerSupport->update($request->validated());

        return response()->json([
            'message' => 'Customer support updated successfully',
        ]);
    }
}
