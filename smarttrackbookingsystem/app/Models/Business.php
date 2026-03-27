<?php
// app/Models/Business.php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Business extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'name',
        'slug',
        'business_type',
        'email',
        'phone',
        'address',
        'city',
        'state',
        'country',
        'postal_code',
        'logo',
        'cover_image',
        'description',
        'business_hours',
        'settings',
        'status'
    ];

    protected $casts = [
        'business_hours' => 'array',
        'settings' => 'array'
    ];

    /**
     * Get the admins for this business.
     */
    public function admins()
    {
        return $this->belongsToMany(User::class, 'business_admins')
            ->withPivot('permissions', 'status', 'position') // add position
            ->withTimestamps();
    }

    /**
     * Get the employees for this business.
     */
    public function employees()
    {
        return $this->hasMany(Employee::class);
    }

    /**
     * Get the customers for this business.
     */
    public function customers()
    {
        return $this->hasMany(Customer::class);
    }

    /**
     * Check if business is active.
     */
    public function isActive()
    {
        return $this->status === 'active';
    }

    public function getRouteKeyName()
    {
        return 'slug';
    }

    public function dashboardSetting()
    {
        return $this->morphOne(DashboardSetting::class, 'owner');
    }


    public function subscriptions()
    {
        return $this->hasMany(BusinessSubscription::class);
    }

    public function subscription()
    {
        return $this->hasOne(BusinessSubscription::class)->latestOfMany();
    }

   public function plan()
{
    return $this->hasOneThrough(
        \App\Models\Plan::class,
        \App\Models\BusinessSubscription::class,
        'business_id', // Foreign key on subscriptions table
        'id',          // Foreign key on plans table
        'id',          // Local key on business table
        'plan_id'      // Local key on subscriptions table
    )->where('business_subscriptions.status', 'active');
}
}