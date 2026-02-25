<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Service extends Model
{
     protected $fillable = [
        'name',
        'slug',
        'description',
        'business_id'
    ];

    public function durations()
    {
        return $this->hasMany(ServiceDuration::class);
    }
    public function employees()
{
    return $this->belongsToMany(\App\Models\Employee::class, 'employee_services')
        ->withPivot('status')
        ->withTimestamps();
}
}
