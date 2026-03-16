<?php

namespace App\Http\Controllers\Api\Frontend\Appointment;

use App\Http\Controllers\Controller;
use App\Models\Business;
use App\Services\Business\Appointment\AppointmentService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class AvailabilityController extends Controller
{
    public function __construct(
        protected AppointmentService $appointmentService
    ) {}

    public function availableDates(Request $request, Business $business): JsonResponse
    {
        $validated = $request->validate([
            'service_id' => ['required', 'integer'],
            'employee_id' => ['required', 'integer'],
            'duration_minutes' => ['required', 'integer', 'min:1'],
            'days' => ['nullable', 'integer', 'min:1', 'max:90'],
        ]);

        $days = $validated['days'] ?? 30;

        $dates = $this->appointmentService->getAvailableDates(
            business: $business,
            serviceId: (int) $validated['service_id'],
            employeeId: (int) $validated['employee_id'],
            durationMinutes: (int) $validated['duration_minutes'],
            daysAhead: (int) $days
        );

        return response()->json([
            'success' => true,
            'message' => 'Available dates fetched successfully.',
            'data' => [
                'available_dates' => $dates,
            ],
        ]);
    }

    public function availableSlots(Request $request, Business $business): JsonResponse
    {
        $validated = $request->validate([
            'service_id' => ['required', 'integer'],
            'employee_id' => ['required', 'integer'],
            'appointment_date' => ['required', 'date'],
            'duration_minutes' => ['required', 'integer', 'min:1'],
        ]);

        $date = \Carbon\Carbon::parse($validated['appointment_date'])->format('Y-m-d');

        $slots = $this->appointmentService->getAvailableSlots(
            business: $business,
            serviceId: (int) $validated['service_id'],
            employeeId: (int) $validated['employee_id'],
            date: $date,
            durationMinutes: (int) $validated['duration_minutes']
        );

        return response()->json([
            'success' => true,
            'message' => 'Available slots fetched successfully.',
            'data' => [
                'appointment_date' => $date,
                'slots' => $slots,
            ],
        ]);
    }
}