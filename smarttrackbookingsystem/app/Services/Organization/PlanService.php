<?php

namespace App\Services\Organization;

use App\Models\Plan;

class PlanService
{

    public function all()
    {
        return Plan::latest()->get();
    }

    public function find($id)
    {
        return Plan::findOrFail($id);
    }

    public function create(array $data): Plan
    {

        return Plan::create([
            'name' => $data['name'],
            'description' => $data['description'] ?? null,
            'price' => $data['price'],
            'max_employees' => $data['max_employees'] ?? null,
            'max_services' => $data['max_services'] ?? null,
            'max_bookings' => $data['max_bookings'] ?? null,
            'stripe_price_id' => $data['stripe_price_id'] ?? null,
            'active' => $data['active'] ?? true
        ]);

    }

    public function update($id, array $data): Plan
    {

        $plan = $this->find($id);

        $plan->update([
            'name' => $data['name'],
            'description' => $data['description'] ?? null,
            'price' => $data['price'],
            'max_employees' => $data['max_employees'] ?? null,
            'max_services' => $data['max_services'] ?? null,
            'max_bookings' => $data['max_bookings'] ?? null,
            'stripe_price_id' => $data['stripe_price_id'] ?? null,
            'active' => $data['active'] ?? true
        ]);

        return $plan;

    }

    public function delete($id): bool
    {

        $plan = $this->find($id);

        return $plan->delete();

    }

}