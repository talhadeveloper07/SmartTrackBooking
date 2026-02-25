<?php
// app/Providers/AuthServiceProvider.php

namespace App\Providers;

use App\Models\Business;
use App\Policies\BusinessPolicy;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;

class AuthServiceProvider extends ServiceProvider
{
    protected $policies = [
        Business::class => BusinessPolicy::class,
    ];

    public function boot(): void
    {
        $this->registerPolicies();
    }
}