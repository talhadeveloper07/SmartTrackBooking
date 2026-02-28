<?php

namespace App\Services\Business\Customer;

use App\Models\Business;
use App\Models\Customer;
use App\Models\User;
use App\Notifications\CustomerSetPasswordNotification;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class CustomerService
{
    public function store(Business $business, array $validated): Customer
    {
        $user = null;
        $customer = null;

        DB::beginTransaction();

        try {
            $tempPassword = Str::random(32);

            $user = User::create([
                'name'      => $validated['name'],
                'email'     => $validated['email'],
                'password'  => Hash::make($tempPassword),
                'user_type' => 'customer',
            ]);

            $customerId = $validated['customer_id'] ?? $this->generateCustomerId($business->name);

            $customer = Customer::create([
                'business_id' => $business->id,
                'user_id'     => $user->id,
                'customer_id' => $customerId,
                'status'      => $validated['status'],
            ]);

            DB::commit();
        } catch (\Throwable $e) {
            DB::rollBack();
            Log::error('Customer store failed', ['error' => $e->getMessage()]);
            throw $e; // let controller handle message
        }

        // Send email AFTER commit (never blocks saving)
        $this->sendSetPasswordEmailSilently($user, $business);

        return $customer;
    }

    private function sendSetPasswordEmailSilently(User $user, Business $business): void
    {
        try {
            $token = Password::broker()->createToken($user);
            $user->notify(new CustomerSetPasswordNotification($token, $business));
        } catch (\Throwable $e) {
            Log::warning('Customer created but email failed', ['error' => $e->getMessage()]);
        }
    }

    /**
     * Generates something like: GYM-8F3C2A (business initials + random)
     * You can change logic as per your previous requirement.
     */
    private function generateCustomerId(string $businessName): string
    {
        $prefix = strtoupper(preg_replace('/[^A-Za-z0-9]/', '', substr($businessName, 0, 3)));
        $prefix = $prefix ?: 'CUS';

        return $prefix . '-' . strtoupper(Str::random(6));
    }

    public function update(Business $business, Customer $customer, array $data): Customer
    {
        // Security check (same as abort_if but service-friendly)
        if ($customer->business_id !== $business->id) {
            throw ValidationException::withMessages([
                'customer' => 'Customer does not belong to this business.',
            ]);
        }

        return DB::transaction(function () use ($customer, $data) {

            // Update linked user
            if ($customer->user) {
                $customer->user()->update([
                    'name'  => $data['name'],
                    'email' => $data['email'],
                ]);
            }

            // Update customer
            $customer->update([
                'customer_id'   => $data['customer_id'] ?? $customer->customer_id,
                'name'          => $data['name'],
                'email'         => $data['email'],
                'phone'         => $data['phone'] ?? null,
                'address'       => $data['address'] ?? null,
                'date_of_birth' => $data['date_of_birth'] ?? null,
                'status'        => $data['status'],
            ]);

            return $customer->fresh();
        });
    }
    public function deleteCustomer(Business $business, Customer $customer): void
    {
        if ($customer->business_id !== $business->id) {
            throw ValidationException::withMessages([
                'customer' => 'Customer does not belong to this business.',
            ]);
        }

        DB::transaction(function () use ($customer) {
            // If FK cascade exists from users -> customers, this will remove customer too.
            // Otherwise, delete customer first then user (or vice versa depending on FK).
            if ($customer->user) {
                $customer->user()->delete();
            } else {
                $customer->delete();
            }
        });
    }
}