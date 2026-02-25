<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ServiceDuration extends Model
{
     protected $fillable = [
        'service_id',
        'duration_name',
        'duration_minutes',
        'price',
        'deposit',
        'status'
    ];

    public function service()
    {
        return $this->belongsTo(Service::class);
    }
}
