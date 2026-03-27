<?php

namespace App\Http\Controllers\Api\Frontend\Appointment;

use App\Http\Controllers\Controller;
use App\Models\Business;
use App\Services\Business\Appointment\AppointmentService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

class BookingController extends Controller
{
    public function __construct(
        protected AppointmentService $appointmentService
    ) {}

    public function store(Request $request, Business $business): JsonResponse
    {
        $validated = $request->validate([
            'customer_id' => ['nullable', 'integer'],

            'new_customer_name' => ['nullable', 'string', 'max:255'],
            'new_customer_email' => ['nullable', 'email', 'max:255'],
            'new_customer_phone' => ['nullable', 'string', 'max:255'],

            'location' => ['nullable', 'string', 'max:255'],
            'notes' => ['nullable', 'string'],

            'items' => ['required', 'array', 'min:1'],

            'items.*.service_id' => ['required', 'integer'],
            'items.*.employee_id' => ['required', 'integer'],
            'items.*.appointment_date' => ['required', 'date'],
            'items.*.start_time' => ['required'],
            'items.*.duration_minutes' => ['required', 'integer', 'min:1'],
            'items.*.price' => ['nullable', 'numeric', 'min:0'],
            'items.*.location' => ['nullable', 'string', 'max:255'],
        ]);

        try {
            $appointment = $this->appointmentService->bookAppointment(
                business: $business,
                actor: $request->user(),
                data: $validated
            );

            $appointment->load([
                'customer.user',
                'items.service',
                'items.employee',
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Appointment booked successfully.',
                'data' => [
                    'appointment' => [
                        'id' => $appointment->id,
                        'business_id' => $appointment->business_id,
                        'customer_id' => $appointment->customer_id,
                        'appointment_date' => $appointment->appointment_date,
                        'start_time' => $appointment->start_time,
                        'end_time' => $appointment->end_time,
                        'duration_minutes' => $appointment->duration_minutes,
                        'price' => $appointment->price,
                        'status' => $appointment->status,
                        'notes' => $appointment->notes,
                        'location' => $appointment->location,
                        'items_count' => $appointment->items->count(),
                    ],
                    'items' => $appointment->items->map(function ($item) {
                        return [
                            'id' => $item->id,
                            'service_id' => $item->service_id,
                            'service_name' => $item->service?->name,
                            'employee_id' => $item->employee_id,
                            'employee_name' => $item->employee?->name,
                            'appointment_date' => $item->appointment_date,
                            'start_time' => $item->start_time,
                            'end_time' => $item->end_time,
                            'duration_minutes' => $item->duration_minutes,
                            'price' => $item->price,
                            'status' => $item->status,
                            'location' => $item->location,
                            'sort_order' => $item->sort_order,
                        ];
                    })->values(),
                ],
            ], 201);
        } catch (ValidationException $e) {
            throw $e;
        } catch (\Throwable $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage() ?: 'Unable to book appointment.',
            ], 422);
        }
    }
}