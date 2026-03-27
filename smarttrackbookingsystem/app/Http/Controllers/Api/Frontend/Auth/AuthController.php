<?php

namespace App\Http\Controllers\Api\Frontend\Auth;

use App\Http\Controllers\Controller;
use App\Models\Business;
use App\Models\Customer;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
    public function login(Request $request, Business $business): JsonResponse
    {
        $validated = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required', 'string'],
        ]);

        $user = User::where('email', $validated['email'])->first();

        if (!$user || !Hash::check($validated['password'], $user->password)) {
            throw ValidationException::withMessages([
                'email' => ['Invalid email or password.'],
            ]);
        }

        $customer = Customer::where('business_id', $business->id)
            ->where('user_id', $user->id)
            ->first();

        if (!$customer) {
            return response()->json([
                'success' => false,
                'message' => 'This account is not registered as a customer for this business.',
            ], 403);
        }

        $token = $user->createToken('frontend-booking-token')->plainTextToken;

        return response()->json([
            'success' => true,
            'message' => 'Login successful.',
            'data' => [
                'token' => $token,
                'user' => [
                    'id' => $user->id,
                    'name' => $user->name,
                    'email' => $user->email,
                ],
                'customer' => [
                    'id' => $customer->id,
                    'customer_id' => $customer->customer_id,
                    'phone' => $customer->phone,
                    'status' => $customer->status,
                ],
            ],
        ]);
    }

    public function register(Request $request, Business $business): JsonResponse
    {
        $validated = $request->validate([
            'first_name' => ['required', 'string', 'max:255'],
            'last_name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255'],
            'phone' => ['nullable', 'string', 'max:255'],
            'password' => ['required', 'string', 'min:6'],
        ]);

        $fullName = trim($validated['first_name'] . ' ' . $validated['last_name']);

        // 1) Find existing user by email
        $user = User::where('email', $validated['email'])->first();

        // 2) If user doesn't exist, create new one
        if (!$user) {
            $user = User::create([
                'name' => $fullName,
                'email' => $validated['email'],
                'password' => Hash::make($validated['password']),
                'user_type' => 'customer',
            ]);
        } else {
            // Optional: update name if missing / old
            if (empty($user->name)) {
                $user->name = $fullName;
                $user->save();
            }

            // Optional: if existing user_type is empty, make it customer
            if (empty($user->user_type)) {
                $user->user_type = 'customer';
                $user->save();
            }
        }

        // 3) Check if customer record already exists for this business
        $existingCustomer = Customer::where('business_id', $business->id)
            ->where('user_id', $user->id)
            ->first();

        if ($existingCustomer) {
            return response()->json([
                'success' => false,
                'message' => 'This email is already registered as a customer for this business.',
                'data' => [
                    'user' => [
                        'id' => $user->id,
                        'name' => $user->name,
                        'email' => $user->email,
                    ],
                    'customer' => [
                        'id' => $existingCustomer->id,
                        'customer_id' => $existingCustomer->customer_id,
                        'phone' => $existingCustomer->phone,
                        'status' => $existingCustomer->status,
                    ],
                ],
            ], 409);
        }

        // 4) Create new customer record for this business
        $customer = Customer::create([
            'business_id' => $business->id,
            'user_id' => $user->id,
            'customer_id' => $this->generateCustomerId($business->name),
            'phone' => $validated['phone'] ?? null,
            'status' => 'active',
        ]);

        $token = $user->createToken('frontend-booking-token')->plainTextToken;

        return response()->json([
            'success' => true,
            'message' => 'Account created successfully.',
            'data' => [
                'token' => $token,
                'user' => [
                    'id' => $user->id,
                    'name' => $user->name,
                    'email' => $user->email,
                ],
                'customer' => [
                    'id' => $customer->id,
                    'customer_id' => $customer->customer_id,
                    'phone' => $customer->phone,
                    'status' => $customer->status,
                ],
            ],
        ], 201);
    }

    public function logout(Request $request, Business $business): JsonResponse
    {
        $request->user()?->currentAccessToken()?->delete();

        return response()->json([
            'success' => true,
            'message' => 'Logged out successfully.',
        ]);
    }

    private function generateCustomerId(string $businessName): string
    {
        $prefix = strtoupper(substr(preg_replace('/[^A-Za-z]/', '', $businessName), 0, 3));

        $lastCustomer = Customer::latest('id')->first();
        $nextNumber = $lastCustomer ? $lastCustomer->id + 1 : 1;

        return $prefix . '-' . str_pad($nextNumber, 4, '0', STR_PAD_LEFT);
    }
}