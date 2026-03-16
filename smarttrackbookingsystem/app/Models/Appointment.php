<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Appointment extends Model
{
    protected $fillable = [
        'business_id',
        'customer_id',
        'service_id',
        'employee_id',
        'appointment_date',
        'start_time',
        'end_time',
        'duration_minutes',
        'price',
        'status',
        'notes',
        'location'
    ];
    public function service()
    {
        return $this->belongsTo(Service::class);
    }

    public function employee()
    {
        return $this->belongsTo(Employee::class);
    }

    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }
    // Appointment.php
   public function items()
    {
        return $this->hasMany(\App\Models\AppointmentItem::class)->orderBy('sort_order');
    }

    // AppointmentItem.php
    public function appointment() { return $this->belongsTo(Appointment::class); 
    }

     public function syncStatusFromItems(): void
{
    $statuses = $this->items()->pluck('status');

    if ($statuses->isEmpty()) {
        return; // nothing to sync
    }

    // If any item is pending -> parent pending
    if ($statuses->contains('pending')) {
        $this->status = 'pending';
        $this->save();
        return;
    }

    // If all items cancelled -> parent cancelled
    if ($statuses->every(fn ($s) => $s === 'cancelled')) {
        $this->status = 'cancelled';
        $this->save();
        return;
    }

    // ✅ If all items are completed or cancelled -> parent completed
    // (meaning: no active items left)
    if ($statuses->every(fn ($s) => in_array($s, ['completed', 'cancelled'], true))) {
        $this->status = 'completed';
        $this->save();
        return;
    }

    // Otherwise it is confirmed (has active confirmed items)
    $this->status = 'confirmed';
    $this->save();
}
}
