<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BusinessAdmin extends Model
{
    protected $fillable = [
        'business_id','user_id','position','permissions','status'
    ];

    protected $casts = [
        'permissions' => 'array', // IMPORTANT: permissions stored as JSON
    ];

    public function business()
    {
        return $this->belongsTo(Business::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
