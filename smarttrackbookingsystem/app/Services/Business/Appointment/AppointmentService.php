<?php

namespace App\Services\Business\Appointment;
use App\Models\Appointment;
use App\Models\Business;
use App\Models\Customer;
use App\Models\EmployeeWorkingHour;
use App\Models\Service;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use App\Models\User;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Hash;

class AppointmentService
{
    public function getAvailableSlots(
        Business $business,
        int $serviceId,
        int $employeeId,
        string $date,               // Y-m-d
        int $durationMinutes
    ): array {
        // ensure service belongs to business
        Service::where('business_id', $business->id)->findOrFail($serviceId);

        // ensure employee belongs to business (adjust if your relation differs)
        $employee = $business->employees()->findOrFail($employeeId);

        // 0=Sun..6=Sat
        $dayIndex = (int) Carbon::parse($date)->format('w');

        // ✅ Pull working hours from table
        $workingRanges = EmployeeWorkingHour::query()
            ->where('employee_id', $employee->id)
            ->where('day_of_week', $dayIndex)
            ->where(function ($q) {
                $q->whereNull('is_off')->orWhere('is_off', 0);
            })
            ->orderBy('start_time')
            ->get(['start_time', 'end_time']);

        if ($workingRanges->isEmpty()) {
            return [];
        }

        // existing appointments to block overlaps
        $existing = Appointment::query()
    ->where('business_id', $business->id)
    ->where('employee_id', $employeeId)
    ->whereDate('appointment_date', $date)
    ->whereIn('status', ['confirmed', 'pending']) // ✅ only block active bookings
    ->get(['start_time', 'end_time']);

        $available = [];

       foreach ($workingRanges as $range) {
    $start = $this->normalizeTime($range->start_time);
    $end   = $this->normalizeTime($range->end_time);

    if (!$start || !$end) continue;

    $generated = $this->generateSlots($date, $start, $end, $durationMinutes);

    foreach ($generated as $startTime) {
        $endTime = Carbon::parse("$date $startTime")->addMinutes($durationMinutes)->format('H:i');

        if (!$this->overlapsExisting($date, $startTime, $endTime, $existing)) {
            $available[] = $startTime;
        }
    }
}

        $available = array_values(array_unique($available));
        sort($available);

        return $available;
    }

    public function bookAppointment(Business $business, $actor, array $data): Appointment
{
    return DB::transaction(function () use ($business, $data) {

        // ✅ 1) Resolve customer
        $customerId = $data['customer_id'] ?? null;

        if ($customerId) {
            $customer = Customer::where('business_id', $business->id)->findOrFail($customerId);
        } else {
            $name  = trim($data['new_customer_name'] ?? '');
            $email = trim($data['new_customer_email'] ?? '');
            $phone = trim($data['new_customer_phone'] ?? '');

            if ($name === '' || $email === '') {
                throw ValidationException::withMessages([
                    'customer_id' => 'Select a customer or add a new customer (name & email required).',
                ]);
            }

            // ✅ create customer (and ensure business_id saved)
         $tempPassword = Str::random(12);

$user = User::create([
    'name'      => $name,
    'email'     => $email,
    'password'  => Hash::make($tempPassword),
    'user_type' => 'customer',
]);

// ⭐ Generate unique customer_id
$customerCode = $this->generateCustomerId($business->name);

$customer = Customer::create([
    'business_id' => $business->id,
    'user_id'     => $user->id,
    'customer_id' => $customerCode,   // ⭐ REQUIRED FIELD
    'status'      => 'active',
]);

$customerId = $customer->id;
        }

        // ✅ 2) Continue booking normally...
        $date      = \Carbon\Carbon::parse($data['appointment_date'])->format('Y-m-d');
        $duration  = (int) $data['duration_minutes'];
        $startTime = $data['start_time'];
        $endTime   = \Carbon\Carbon::parse("$date $startTime")->addMinutes($duration)->format('H:i');

        // slot check (recommended)
        $slots = $this->getAvailableSlots(
            business: $business,
            serviceId: (int)$data['service_id'],
            employeeId: (int)$data['employee_id'],
            date: $date,
            durationMinutes: $duration
        );

        if (!in_array($startTime, $slots, true)) {
            throw ValidationException::withMessages([
                'start_time' => 'Selected time slot is not available.',
            ]);
        }

        return Appointment::create([
            'business_id'       => $business->id,
            'customer_id'       => $customerId,              // ✅ always set now
            'service_id'        => (int)$data['service_id'],
            'employee_id'       => (int)$data['employee_id'],
            'appointment_date'  => $date,
            'start_time'        => $startTime,
            'end_time'          => $endTime,
            'duration_minutes'  => $duration,
            'price'             => $data['price'] ?? null,
            'status'            => 'confirmed',
            'notes'             => $data['notes'] ?? null,
        ]);
    });
}
private function generateCustomerId(string $businessName): string
{
    // Take first 3 letters of business name
    $prefix = strtoupper(substr(preg_replace('/[^A-Za-z]/', '', $businessName), 0, 3));

    $lastCustomer = \App\Models\Customer::latest('id')->first();

    $nextNumber = $lastCustomer ? $lastCustomer->id + 1 : 1;

    return $prefix . '-' . str_pad($nextNumber, 4, '0', STR_PAD_LEFT);
}
    private function generateSlots(string $date, string $startTime, string $endTime, int $durationMinutes): array
    {
        $slots = [];

        $start = Carbon::parse("$date $startTime");
        $end   = Carbon::parse("$date $endTime");

        while ($start->copy()->addMinutes($durationMinutes)->lte($end)) {
            $slots[] = $start->format('H:i');
            $start->addMinutes($durationMinutes);
        }

        return $slots;
    }

   private function overlapsExisting(string $date, string $startTime, string $endTime, $existingAppointments): bool
{
    $slotStart = Carbon::parse("$date $startTime");
    $slotEnd   = Carbon::parse("$date $endTime");

    foreach ($existingAppointments as $appt) {
        // normalize stored times (works for H:i and H:i:s)
        $apptStart = Carbon::parse("$date {$appt->start_time}");
        $apptEnd   = Carbon::parse("$date {$appt->end_time}");

        // overlap if slotStart < apptEnd AND slotEnd > apptStart
        if ($slotStart->lt($apptEnd) && $slotEnd->gt($apptStart)) {
            return true;
        }
    }
    return false;
}

    private function normalizeTime($time): ?string
    {
        if (!$time) return null;

        // handles "09:00", "09:00:00", Carbon, etc.
        try {
            return Carbon::parse($time)->format('H:i');
        } catch (\Throwable $e) {
            return null;
        }
    }
}