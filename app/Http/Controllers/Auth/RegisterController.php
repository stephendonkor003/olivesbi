<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\CreateUserRequest;
use App\Http\Requests\Auth\UpdateUserRequest;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class RegisterController extends Controller
{
    public function __construct() {}

    public function index()
    {
        try {
            $data = User::get();

            return response()->json([
                'message' => 'Get all users',
                'data' => $data,
            ]);
        } catch (\Exception $ex) {
            return response()->json([
                'message' => 'Unable to fetch users '.$ex->getMessage(),
            ], 422);
        }
    }

    public function store(CreateUserRequest $request)
    {
        try {
            $data = $request->validated();
            $data['password'] = Hash::make($request->validated('password'));

            User::create($data);

            return response()->json([
                'message' => 'User created successfully',
                'data' => $data,
            ]);
        } catch (\Exception $ex) {
            return response()->json([
                'message' => 'Unable to create user '.$ex->getMessage(),
            ], 422);
        }
    }

    public function show($id)
    {
        try {
            $data = User::where('id', $id)->first();

            if (empty($data)) {
                return response()->json([
                    'message' => 'User not found',
                ], 422);
            }

            return response()->json([
                'message' => 'Get single user',
                'data' => $data,
            ]);
        } catch (\Exception $ex) {
            return response()->json([
                'message' => 'Unable to fetch user '.$ex->getMessage(),
            ], 422);
        }
    }

    public function update(UpdateUserRequest $request, $id)
    {
        try {
            $data = $request->validated();
            $data['password'] = Hash::make($request->validated('password'));

            $user = User::where('id', $id)->first();

            if (empty($user)) {
                return response()->json([
                    'message' => 'Cannot update user, user not found',
                ], 422);
            }

            // return $user;
            $user->update($data);

            return response()->json([
                'message' => 'User updated successfully',
            ]);
        } catch (\Exception $ex) {
            return response()->json([
                'message' => 'Unable to update users '.$ex->getMessage(),
            ], 422);
        }
    }
}
