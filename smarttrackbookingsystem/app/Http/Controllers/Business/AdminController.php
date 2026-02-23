<?php
// app/Http/Controllers/Business/AdminController.php

namespace App\Http\Controllers\Business;

use App\Http\Controllers\Controller;
use App\Models\Business;
use App\Services\BusinessSettingsService;
use Illuminate\Http\Request;

class AdminController extends Controller
{
    protected $settingsService;

    public function __construct(BusinessSettingsService $settingsService)
    {
        $this->settingsService = $settingsService;
    }

    public function index(Business $business)
    {
        // Get business settings
        $settings = $this->settingsService->getSettings($business);
        
        // Get some dashboard stats
        $stats = [
            'total_employees' => $business->employees()->count(),
            'total_customers' => $business->customers()->count(),
            'recent_activities' => [], // You can add activity log here
        ];
        
        return view('business.admin.dashboard', compact('business', 'settings', 'stats'));
    }
}