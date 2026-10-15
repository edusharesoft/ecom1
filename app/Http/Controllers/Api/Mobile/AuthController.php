<?php

namespace App\Http\Controllers\Api\Mobile;

use App\Http\Controllers\Controller;
use App\Models\Customer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
    /**
     * Customer Registration
     * 
     * POST /api/v1/mobile/auth/register
     */
    public function register(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'nullable|email|max:255|unique:customers,email',
            'phone' => 'required|string|max:20|unique:customers,phone',
            'password' => 'required|string|min:6|confirmed',
            'password_confirmation' => 'required|string|min:6',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $customer = Customer::create([
                'name' => $request->name,
                'email' => $request->email,
                'phone' => $request->phone,
                'password' => Hash::make($request->password),
                'status' => 1,
            ]);

            // Create token
            $token = $customer->createToken('mobile-app')->plainTextToken;

            return response()->json([
                'status' => 'success',
                'message' => 'Registration successful',
                'data' => [
                    'customer' => [
                        'id' => $customer->id,
                        'name' => $customer->name,
                        'email' => $customer->email,
                        'phone' => $customer->phone,
                    ],
                    'token' => $token,
                    'token_type' => 'Bearer',
                ]
            ], 201);

        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Registration failed: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Customer Login
     * 
     * POST /api/v1/mobile/auth/login
     */
    public function login(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'login' => 'required|string', // phone or email
            'password' => 'required|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        $login = $request->input('login');
        $password = $request->input('password');

        // Determine if login is email or phone
        $isEmail = filter_var($login, FILTER_VALIDATE_EMAIL);
        
        $customer = null;
        if ($isEmail) {
            $customer = Customer::where('email', $login)->first();
        } else {
            $customer = Customer::where('phone', $login)->first();
        }

        if (!$customer || !Hash::check($password, $customer->password)) {
            return response()->json([
                'status' => 'error',
                'message' => 'Invalid credentials'
            ], 401);
        }

        if ($customer->status != 1) {
            return response()->json([
                'status' => 'error',
                'message' => 'Account is inactive. Please contact support.'
            ], 403);
        }

        // Revoke all existing tokens (optional - for single device login)
        // $customer->tokens()->delete();

        // Create new token
        $token = $customer->createToken('mobile-app')->plainTextToken;

        return response()->json([
            'status' => 'success',
            'message' => 'Login successful',
            'data' => [
                'customer' => [
                    'id' => $customer->id,
                    'name' => $customer->name,
                    'email' => $customer->email,
                    'phone' => $customer->phone,
                    'image' => $customer->image ? url($customer->image) : null,
                ],
                'token' => $token,
                'token_type' => 'Bearer',
            ]
        ], 200);
    }

    /**
     * Get Authenticated Customer
     * 
     * GET /api/v1/mobile/auth/me
     */
    public function me(Request $request)
    {
        $customer = $request->user();

        return response()->json([
            'status' => 'success',
            'message' => 'Customer data retrieved',
            'data' => [
                'id' => $customer->id,
                'name' => $customer->name,
                'email' => $customer->email,
                'phone' => $customer->phone,
                'image' => $customer->image ? url($customer->image) : null,
                'address' => $customer->address,
                'city' => $customer->city,
                'postal_code' => $customer->postal_code,
                'status' => $customer->status,
            ]
        ], 200);
    }

    /**
     * Update Profile
     * 
     * PUT /api/v1/mobile/auth/profile
     */
    public function updateProfile(Request $request)
    {
        $customer = $request->user();

        $validator = Validator::make($request->all(), [
            'name' => 'sometimes|string|max:255',
            'email' => 'sometimes|email|max:255|unique:customers,email,' . $customer->id,
            'phone' => 'sometimes|string|max:20|unique:customers,phone,' . $customer->id,
            'address' => 'nullable|string|max:500',
            'city' => 'nullable|string|max:100',
            'postal_code' => 'nullable|string|max:20',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $customer->update($request->only([
                'name', 'email', 'phone', 'address', 'city', 'postal_code'
            ]));

            return response()->json([
                'status' => 'success',
                'message' => 'Profile updated successfully',
                'data' => [
                    'id' => $customer->id,
                    'name' => $customer->name,
                    'email' => $customer->email,
                    'phone' => $customer->phone,
                    'image' => $customer->image ? url($customer->image) : null,
                    'address' => $customer->address,
                    'city' => $customer->city,
                    'postal_code' => $customer->postal_code,
                ]
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Profile update failed: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Change Password
     * 
     * POST /api/v1/mobile/auth/change-password
     */
    public function changePassword(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'current_password' => 'required|string',
            'password' => 'required|string|min:6|confirmed',
            'password_confirmation' => 'required|string|min:6',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        $customer = $request->user();

        if (!Hash::check($request->current_password, $customer->password)) {
            return response()->json([
                'status' => 'error',
                'message' => 'Current password is incorrect'
            ], 400);
        }

        try {
            $customer->update([
                'password' => Hash::make($request->password)
            ]);

            return response()->json([
                'status' => 'success',
                'message' => 'Password changed successfully'
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Password change failed: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Logout
     * 
     * POST /api/v1/mobile/auth/logout
     */
    public function logout(Request $request)
    {
        try {
            // Revoke current token
            $request->user()->currentAccessToken()->delete();

            return response()->json([
                'status' => 'success',
                'message' => 'Logged out successfully'
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Logout failed: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Logout from all devices
     * 
     * POST /api/v1/mobile/auth/logout-all
     */
    public function logoutAll(Request $request)
    {
        try {
            // Revoke all tokens
            $request->user()->tokens()->delete();

            return response()->json([
                'status' => 'success',
                'message' => 'Logged out from all devices successfully'
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Logout failed: ' . $e->getMessage()
            ], 500);
        }
    }
}
