<?php

namespace App\Services\Business\Employee;

use App\Models\User;
use App\Models\Employee;
use App\Models\Business;
use App\Models\Service;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Str;
use Illuminate\Support\Collection;
use App\Notifications\EmployeeSetPasswordNotification;

class EmployeeService
{
    public function create($business, array $data)
    {
        return DB::transaction(function () use ($business, $data) {

            // Create user
            $user = $this->createUser($data);

            // Generate employee code
            $employeeId = $this->generateEmployeeCode($business, $data);

            // Create employee
            $employee = $this->createEmployee($business, $user, $data, $employeeId);

            // Sync services
            $this->syncServices($employee, $business, $data);

            // Store working hours
            $this->storeWorkingHours($employee, $data['hours']);

            // Send password setup notification
            $this->sendSetPasswordNotification($user);

            return $employee;
        });
    }

    private function createUser(array $data)
    {
        return User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => Hash::make('password'),
            'user_type' => 'employee'
        ]);
    }

    private function generateEmployeeCode($business, array &$data)
    {
        if (!empty($data['employee_id'])) {
            return $data['employee_id'];
        }

        $initials = strtoupper(
            collect(preg_split('/\s+/', trim($business->name)))
                ->filter()
                ->map(fn($w) => Str::substr($w, 0, 1))
                ->join('')
        );

        do {
            $number = str_pad(rand(1, 9999999), 7, '0', STR_PAD_LEFT);
            $code = $initials . '-' . $number;
        } while (Employee::where('employee_id', $code)->exists());

        return $code;
    }

    private function createEmployee($business, $user, array $data, $employeeId)
    {
        return Employee::create([
            'business_id' => $business->id,
            'user_id' => $user->id,
            'employee_id' => $employeeId,
            'name' => $data['name'],
            'email' => $data['email'],
            'phone' => $data['phone'] ?? null,
            'address' => $data['address'] ?? null,
            'date_of_birth' => $data['date_of_birth'] ?? null,
            'joining_date' => $data['joining_date'],
            'status' => $data['status'],
        ]);
    }

    private function syncServices($employee, $business, array $data)
    {
        $allowedServiceIds = Service::where('business_id', $business->id)
            ->pluck('id')
            ->toArray();

        $selected = array_values(array_intersect(
            $data['services'] ?? [],
            $allowedServiceIds
        ));

        $syncData = [];
        foreach ($selected as $sid) {
            $syncData[$sid] = ['status' => 'active'];
        }

        $employee->services()->sync($syncData);
    }

    private function storeWorkingHours($employee, array $hours)
    {
        foreach ($hours as $day => $payload) {

            $isOff = !empty($payload['is_off']);

            if ($isOff) {
                $employee->workingHours()->create([
                    'day_of_week' => (int) $day,
                    'is_off' => true,
                    'start_time' => null,
                    'end_time' => null,
                ]);
                continue;
            }

            foreach (($payload['slots'] ?? []) as $slot) {

                if (empty($slot['start']) || empty($slot['end']))
                    continue;

                $employee->workingHours()->create([
                    'day_of_week' => (int) $day,
                    'is_off' => false,
                    'start_time' => $slot['start'],
                    'end_time' => $slot['end'],
                ]);
            }
        }
    }

    private function sendSetPasswordNotification($user)
    {
        $token = Password::broker()->createToken($user);
        $user->notify(new EmployeeSetPasswordNotification($token));
    }

    public function getEmployeeForEdit($businessSlug, $employeeId)
    {
        $business = Business::where('slug', $businessSlug)->firstOrFail();

        $employee = Employee::with(['services', 'workingHours'])
            ->where('business_id', $business->id)
            ->findOrFail($employeeId);

        $services = Service::where('business_id', $business->id)->get();

        $employeeHours = $this->formatWorkingHours($employee);

        return [
            'business' => $business,
            'employee' => $employee,
            'services' => $services,
            'employee_services' => $employee->services->pluck('id')->toArray(),
            'employeeHours' => $employeeHours
        ];
    }

    private function formatWorkingHours($employee)
    {
        $employeeHours = [];

        foreach (range(0, 6) as $d) {
            $employeeHours[$d] = [
                'is_enabled' => 0,
                'slots' => []
            ];
        }

        foreach ($employee->workingHours as $wh) {

            if ((int) $wh->is_off === 1) {
                $employeeHours[$wh->day_of_week] = [
                    'is_enabled' => 0,
                    'slots' => []
                ];
            } else {

                $employeeHours[$wh->day_of_week]['is_enabled'] = 1;

                $employeeHours[$wh->day_of_week]['slots'][] = [
                    'start' => substr($wh->start_time, 0, 5),
                    'end' => substr($wh->end_time, 0, 5),
                ];
            }
        }

        return $employeeHours;
    }

    public function update($business, $employee, array $data)
    {
        if ($employee->business_id !== $business->id) {
            abort(404);
        }

        return DB::transaction(function () use ($employee, $business, $data) {

            // 1️⃣ Update user account
            $employee->user()->update([
                'name' => $data['name'],
                'email' => $data['email'],
            ]);

            // 2️⃣ Update employee table
            $employee->update([
                'employee_id' => $data['employee_id'] ?? $employee->employee_id,
                'name' => $data['name'],
                'email' => $data['email'],
                'phone' => $data['phone'] ?? null,
                'address' => $data['address'] ?? null,
                'date_of_birth' => $data['date_of_birth'] ?? null,
                'status' => $data['status'],
            ]);

            // 3️⃣ Sync services (secure way)
            $this->syncServicesForUpdate($employee, $business, $data);

            // 4️⃣ Replace working hours
            $this->replaceWorkingHours($employee, $data['hours']);

            return $employee;
        });
    }
    private function syncServicesForUpdate($employee, $business, array $data)
    {
        $allowedServiceIds = Service::where('business_id', $business->id)
            ->pluck('id')
            ->toArray();

        $selected = array_values(array_intersect(
            $data['services'] ?? [],
            $allowedServiceIds
        ));

        $employee->services()->sync($selected);
    }
    private function replaceWorkingHours($employee, array $hours)
    {
        // Delete old
        $employee->workingHours()->delete();

        foreach ($hours as $day => $payload) {

            $isEnabled = ((int) ($payload['is_enabled'] ?? 0) === 1);
            $isOff = !$isEnabled;

            if ($isOff) {
                $employee->workingHours()->create([
                    'day_of_week' => (int) $day,
                    'is_off' => 1,
                    'start_time' => null,
                    'end_time' => null,
                ]);
                continue;
            }

            $slots = $payload['slots'] ?? [];

            if (empty($slots)) {
                $employee->workingHours()->create([
                    'day_of_week' => (int) $day,
                    'is_off' => 0,
                    'start_time' => null,
                    'end_time' => null,
                ]);
                continue;
            }

            foreach ($slots as $slot) {

                if (empty($slot['start']) || empty($slot['end']))
                    continue;

                $employee->workingHours()->create([
                    'day_of_week' => (int) $day,
                    'is_off' => 0,
                    'start_time' => $slot['start'],
                    'end_time' => $slot['end'],
                ]);
            }
        }
    }
}