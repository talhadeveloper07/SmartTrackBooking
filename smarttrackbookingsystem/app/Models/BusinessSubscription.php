<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BusinessSubscription extends Model
{
    protected $fillable = [
        'business_id',
        'plan_id',
        'stripe_customer_id',
        'stripe_subscription_id',
        'status',
        'starts_at',
        'ends_at',
    ];

    protected $casts = [
        'starts_at' => 'datetime',
        'ends_at' => 'datetime',
    ];

    public function business()
    {
        return $this->belongsTo(Business::class);
    }

    public function plan()
    {
        return $this->belongsTo(Plan::class);
    }

    public function isActive(): bool
    {
        return $this->status === 'active' &&
            (!$this->ends_at || now()->lessThanOrEqualTo($this->ends_at));
    }
}