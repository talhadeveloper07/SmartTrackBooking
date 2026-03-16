<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class AppointmentItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'appointment_id',
        'business_id',
        'service_id',
        'employee_id',
        'appointment_date',
        'start_time',
        'end_time',
        'duration_minutes',
        'price',
        'status',
        'sort_order',
        'location'
    ];

    protected $casts = [
        'appointment_date' => 'date:Y-m-d',
        'price' => 'decimal:2',
        'duration_minutes' => 'integer',
        'sort_order' => 'integer',
    ];

    /* =========================
     | Relationships
     ========================= */

    public function appointment()
    {
        return $this->belongsTo(Appointment::class);
    }

    public function business()
    {
        return $this->belongsTo(Business::class);
    }

    public function service()
    {
        return $this->belongsTo(Service::class);
    }

    public function employee()
    {
        return $this->belongsTo(Employee::class);
    }
   
}