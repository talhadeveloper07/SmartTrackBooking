<?php

namespace App\Services\Business\Appointment;

use App\Models\Appointment;
use App\Models\AppointmentItem;
use App\Models\Business;
use App\Models\Customer;
use App\Models\EmployeeWorkingHour;
use App\Models\Service;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class AppointmentService
{
    /**
     * Available slots for a specific service+employee+date+duration
     */
    public function getAvailableSlots(
        Business $business,
        int $serviceId,
        int $employeeId,
        string $date,               // Y-m-d
        int $durationMinutes
    ): array {
        // ensure service belongs to business
        Service::where('business_id', $business->id)->findOrFail($serviceId);

        // ensure employee belongs to business
        $employee = $business->employees()->findOrFail($employeeId);

        // 0=Sun..6=Sat
        $dayIndex = (int) Carbon::parse($date)->format('w');

        // Pull working hours from table
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

        /**
         * IMPORTANT:
         * Block overlaps using AppointmentItems (multi-service),
         * and only appointments that are active.
         */
        $existing = AppointmentItem::query()
            ->where('business_id', $business->id)
            ->where('employee_id', $employeeId)
            ->whereDate('appointment_date', $date)
            ->whereHas('appointment', function ($q) {
                $q->whereIn('status', ['confirmed', 'pending']);
            })
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

    /**
     * Multi-service booking
     *
     * Expected payload:
     *  - customer_id OR new_customer_*
     *  - notes (optional)
     *  - items: [
     *      [
     *        service_id, employee_id, appointment_date, start_time, duration_minutes, price(optional)
     *      ],
     *      ...
     *    ]
     */
    public function bookAppointment(Business $business, $actor, array $data): Appointment
    {
        return DB::transaction(function () use ($business, $data) {

            // ✅ 1) Resolve customer
            $customerId = $data['customer_id'] ?? null;

            if ($customerId) {
                Customer::where('business_id', $business->id)->findOrFail($customerId);
            } else {
                $name  = trim($data['new_customer_name'] ?? '');
                $email = trim($data['new_customer_email'] ?? '');
                $phone = trim($data['new_customer_phone'] ?? '');

                if ($name === '' || $email === '') {
                    throw ValidationException::withMessages([
                        'customer_id' => 'Select a customer or add a new customer (name & email required).',
                    ]);
                }

                // Create user
                $tempPassword = Str::random(12);

                $user = User::create([
                    'name'      => $name,
                    'email'     => $email,
                    'password'  => Hash::make($tempPassword),
                    'user_type' => 'customer',
                ]);

                // Generate unique customer_id
                $customerCode = $this->generateCustomerId($business->name, $business->id);

                // Create customer
                $customer = Customer::create([
                    'business_id' => $business->id,
                    'user_id'     => $user->id,
                    'customer_id' => $customerCode,
                    'status'      => 'active',
                    // if you have phone column, save it
                    // 'phone' => $phone,
                ]);

                $customerId = $customer->id;
            }

            // ✅ 2) Validate items
            $items = $data['items'] ?? [];
            if (!is_array($items) || count($items) < 1) {
                throw ValidationException::withMessages([
                    'items' => 'Please add at least one service item.',
                ]);
            }

            $totalPrice = 0.0;
            $totalDuration = 0;

            $overallDate = null;     // optional if you enforce same day
            $overallStart = null;
            $overallEnd = null;

            // We'll store computed values to use when inserting items
            $preparedItems = [];

            foreach ($items as $idx => $it) {

                $serviceId = (int)($it['service_id'] ?? 0);
                $employeeId = (int)($it['employee_id'] ?? 0);

                if (!$serviceId || !$employeeId) {
                    throw ValidationException::withMessages([
                        "items.$idx.service_id" => "Service is required for item #".($idx + 1),
                    ]);
                }

                // ensure service belongs to business
                Service::where('business_id', $business->id)->findOrFail($serviceId);

                // ensure employee belongs to business
                $business->employees()->findOrFail($employeeId);

                $date = Carbon::parse($it['appointment_date'])->format('Y-m-d');
                $duration = (int)($it['duration_minutes'] ?? 0);
                $startTime = (string)($it['start_time'] ?? '');

                if ($duration < 1 || $startTime === '') {
                    throw ValidationException::withMessages([
                        "items.$idx.start_time" => "Date, time and duration are required for item #".($idx + 1),
                    ]);
                }

                $endTime = Carbon::parse("$date $startTime")->addMinutes($duration)->format('H:i');

                // slot check
                $slots = $this->getAvailableSlots(
                    business: $business,
                    serviceId: $serviceId,
                    employeeId: $employeeId,
                    date: $date,
                    durationMinutes: $duration
                );

                if (!in_array($startTime, $slots, true)) {
                    throw ValidationException::withMessages([
                        "items.$idx.start_time" => "Selected time slot is not available for item #".($idx + 1),
                    ]);
                }

                // Prevent overlaps between items inside same request (same employee & date)
                foreach ($preparedItems as $prevIdx => $prev) {
                    if (
                        $prev['employee_id'] === $employeeId &&
                        $prev['appointment_date'] === $date
                    ) {
                        if ($this->overlapsExisting($date, $startTime, $endTime, collect([$prev]))) {
                            throw ValidationException::withMessages([
                                "items.$idx.start_time" => "This item overlaps another selected item for the same employee.",
                            ]);
                        }
                    }
                }

                $price = isset($it['price']) && $it['price'] !== '' ? (float)$it['price'] : 0.0;

                $totalDuration += $duration;
                $totalPrice += $price;

                $overallDate = $overallDate ?? $date; // if you want to enforce same day, keep first date
                $overallStart = $overallStart ? min($overallStart, $startTime) : $startTime;
                $overallEnd = $overallEnd ? max($overallEnd, $endTime) : $endTime;

                $preparedItems[] = [
                    'business_id' => $business->id,
                    'service_id' => $serviceId,
                    'employee_id' => $employeeId,
                    'appointment_date' => $date,
                    'start_time' => $startTime,
                    'end_time' => $endTime,
                    'duration_minutes' => $duration,
                    'price' => $it['price'] ?? null,
                    'location' => $it['location'] ?? null
                ];
            }

            // ✅ 3) Create parent appointment
            $appointment = Appointment::create([
                'business_id'      => $business->id,
                'customer_id'      => $customerId,
                'appointment_date' => $overallDate,
                'start_time'       => $overallStart,
                'end_time'         => $overallEnd,
                'duration_minutes' => $totalDuration,
                'price'            => $totalPrice,
                'status'           => 'confirmed',
                'notes'            => $data['notes'] ?? null,
                'location'    => $data['location'] ?? null,
            ]);

            // ✅ 4) Create items
            foreach ($preparedItems as $i => $pi) {
                AppointmentItem::create([
                    'appointment_id'   => $appointment->id,
                    'business_id'      => $pi['business_id'],
                    'service_id'       => $pi['service_id'],
                    'employee_id'      => $pi['employee_id'],
                    'appointment_date' => $pi['appointment_date'],
                    'start_time'       => $pi['start_time'],
                    'end_time'         => $pi['end_time'],
                    'duration_minutes' => $pi['duration_minutes'],
                    'price'            => $pi['price'],
                    'status' => 'confirmed', 
                    'sort_order'       => $i + 1,
                    'location'    => $pi['location'] ?? null,
                ]);
            }

            return $appointment;
        });
    }

    /**
     * Better customer id generation:
     * - per business sequence (avoids global collisions)
     */
    private function generateCustomerId(string $businessName, int $businessId): string
    {
        $prefix = strtoupper(substr(preg_replace('/[^A-Za-z]/', '', $businessName), 0, 3));
        if ($prefix === '') $prefix = 'CUS';

        $lastId = Customer::where('business_id', $businessId)->max('id');
        $nextNumber = $lastId ? ($lastId + 1) : 1;

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

    /**
     * Works for both:
     * - Eloquent collections of AppointmentItem/Appointment (having start_time/end_time)
     * - simple arrays with start_time/end_time keys (we wrap in collect in one place)
     */
    private function overlapsExisting(string $date, string $startTime, string $endTime, $existingAppointments): bool
    {
        $slotStart = Carbon::parse("$date $startTime");
        $slotEnd   = Carbon::parse("$date $endTime");

        foreach ($existingAppointments as $appt) {
            $st = is_array($appt) ? ($appt['start_time'] ?? null) : ($appt->start_time ?? null);
            $en = is_array($appt) ? ($appt['end_time'] ?? null) : ($appt->end_time ?? null);

            if (!$st || !$en) continue;

            $apptStart = Carbon::parse("$date {$st}");
            $apptEnd   = Carbon::parse("$date {$en}");

            if ($slotStart->lt($apptEnd) && $slotEnd->gt($apptStart)) {
                return true;
            }
        }
        return false;
    }

    private function normalizeTime($time): ?string
    {
        if (!$time) return null;

        try {
            return Carbon::parse($time)->format('H:i');
        } catch (\Throwable $e) {
            return null;
        }
    }

    public function getAvailableDates(
        Business $business,
        int $serviceId,
        int $employeeId,
        int $durationMinutes,
        int $daysAhead = 30
    ): array {
        $dates = [];

        $startDate = Carbon::today();

        for ($i = 0; $i < $daysAhead; $i++) {
            $date = $startDate->copy()->addDays($i)->format('Y-m-d');

            $slots = $this->getAvailableSlots(
                business: $business,
                serviceId: $serviceId,
                employeeId: $employeeId,
                date: $date,
                durationMinutes: $durationMinutes
            );

            if (!empty($slots)) {
                $dates[] = $date;
            }
        }

        return $dates;
    }
}