<?php

namespace App\Services;

use App\Models\Appointment;
use App\Models\Customer;
use App\Models\Employee;
use App\Models\Service;

class SearchService
{
    public function globalSearch(string $query, string $businessSlug): array
    {
        return [

            // APPOINTMENTS
            'appointments' => Appointment::where(function ($q) use ($query) {

                    if (is_numeric($query)) {
                        $q->orWhere('id', $query);
                    }

                    $q->orWhere('appointment_date', 'like', "%{$query}%")
                      ->orWhere('status', 'like', "%{$query}%")
                      ->orWhereHas('customer.user', fn($q2) => $q2->where('name', 'like', "%{$query}%"))
                      ->orWhereHas('employee', fn($q3) => $q3->where('name', 'like', "%{$query}%"))
                      ->orWhereHas('service', fn($q4) => $q4->where('name', 'like', "%{$query}%"));

                })
                ->with(['customer.user', 'employee', 'service'])
                ->limit(5)
                ->get()
                ->map(function ($a) use ($businessSlug) {
                    return [
                        'id'               => $a->id,
                        'appointment_date' => $a->appointment_date,
                        'status'           => $a->status,
                        'customer_name'    => optional(optional($a->customer)->user)->name,
                        'employee_name'    => optional($a->employee)->name,
                        'service_name'     => optional($a->service)->name,

                        'url' => route('business.appointments.show',[$businessSlug, $a->id]),
                    ];
                })->values(),

            // CUSTOMERS
            'customer' => Customer::where(function ($q) use ($query) {
                    $q->orWhere('customer_id', 'like', "%{$query}%")
                      ->orWhere('status', 'like', "%{$query}%")
                      ->orWhere('tags', 'like', "%{$query}%")
                      ->orWhereHas('user', fn($q2) =>
                          $q2->where('name', 'like', "%{$query}%")
                             ->orWhere('email', 'like', "%{$query}%")
                      );
                })
                ->with('user')
                ->limit(5)
                ->get()
                ->map(fn($c) => [
                    'id'          => $c->id,
                    'customer_id' => $c->customer_id,
                    'status'      => $c->status,
                    'name'        => optional($c->user)->name,
                    'email'       => optional($c->user)->email,
                    'url' => route('business.customers.show', [$businessSlug, $c->id]),
                ])->values(),

            // EMPLOYEES
            'employee' => Employee::where('name', 'like', "%{$query}%")
                ->orWhere('employee_id', 'like', "%{$query}%")
                ->limit(5)
                ->get()
                ->map(fn($e) => [
                    'id'   => $e->id,
                    'name' => $e->name,
                    'url'  => route('business.employees.show', [$businessSlug, $e->id]),
                ])->values(),

            // SERVICES
            'service' => Service::where('name', 'like', "%{$query}%")
                ->limit(5)
                ->get()
                ->map(fn($s) => [
                    'id'   => $s->id,
                    'name' => $s->name,
                    'url'  => route('business.services.show', [$businessSlug, $s->id]),
                ])->values(),
        ];
    }
}