<?php

namespace App\Services;

use App\Models\Appointment;
use App\Models\Customer;
use App\Models\Employee;
use App\Models\Service;

class SearchService
{
  public function globalSearch(string $query)
{
    return [
        'appointments' => Appointment::where(function ($q) use ($query) {

            // If numeric → search by ID
            if (is_numeric($query)) {
                $q->orWhere('id', $query);
            }

            $q->orWhere('appointment_date', 'like', "%{$query}%")
              ->orWhere('status', 'like', "%{$query}%")

              // Go through customer → user to search name
              ->orWhereHas('customer.user', function ($q2) use ($query) {
                  $q2->where('name', 'like', "%{$query}%");
              })

              ->orWhereHas('employee', function ($q3) use ($query) {
                  $q3->where('name', 'like', "%{$query}%");
              })

              ->orWhereHas('service', function ($q4) use ($query) {
                  $q4->where('name', 'like', "%{$query}%");
              });

        })
        ->with(['customer.user', 'employee', 'service'])
        ->limit(5)
        ->get(),

        'customer' => Customer::where(function ($q) use ($query) {

            $q->orWhere('customer_id', 'like', "%{$query}%")
              ->orWhere('status', 'like', "%{$query}%")
              ->orWhere('tags', 'like', "%{$query}%")

              ->orWhereHas('user', function ($q2) use ($query) {
                  $q2->where('name', 'like', "%{$query}%")
                     ->orWhere('email', 'like', "%{$query}%");
              });

        })
        ->with('user')
        ->limit(5)
        ->get(),

        'employee' => Employee::where('name', 'like', "%{$query}%")
            ->limit(5)
            ->get(),

        'service' => Service::where('name', 'like', "%{$query}%")
            ->limit(5)
            ->get(),
    ];
}
}