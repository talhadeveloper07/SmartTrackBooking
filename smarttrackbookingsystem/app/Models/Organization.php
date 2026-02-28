<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Organization extends Model
{
    public function dashboardSetting()
    {
        return $this->morphOne(DashboardSetting::class, 'owner');
    }
}
