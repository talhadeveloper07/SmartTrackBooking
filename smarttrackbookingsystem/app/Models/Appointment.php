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
}
