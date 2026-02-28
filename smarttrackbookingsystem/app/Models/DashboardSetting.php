<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DashboardSetting extends Model
{
    protected $fillable = [
        'logo',
        'favicon',
        'primary_color',
        'secondary_color',
        'sidebar_bg',
        'sidebar_text',
        'topbar_bg',
        'topbar_text',
    ];

    public function owner()
    {
        return $this->morphTo();
    }
}
