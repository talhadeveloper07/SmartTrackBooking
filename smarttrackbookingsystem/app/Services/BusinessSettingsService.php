<?php
// app/Services/BusinessSettingsService.php

namespace App\Services;

use App\Models\Business;
use App\Helpers\BusinessSettingsHelper;
use Illuminate\Support\Arr;

class BusinessSettingsService
{
    protected $defaultSettings = [
        'timezone' => 'UTC',
        'date_format' => 'Y-m-d',
        'time_format' => 'H:i',
        'currency' => 'USD',
        'week_start' => 'monday',
        'primary_color' => '#110093',
        'secondary_color' => '#38c172',
        'accent_color' => '#f6993f',
        'font_family' => 'Inter, sans-serif',
        'logo_path' => null,
        'favicon_path' => null,
        'email_notifications' => true,
        'sms_notifications' => false,
        'push_notifications' => false,
        'notification_events' => ['new_order', 'new_customer', 'low_stock'],
        'invoice_prefix' => 'INV-',
        'invoice_logo_path' => null,
        'invoice_footer' => 'Thank you for your business!',
        'invoice_terms' => 'Payment is due within 30 days.',
        'tax_rate' => 0,
        'tax_name' => 'VAT',
        'two_factor_auth' => false,
        'session_timeout' => 30,
        'password_expiry_days' => 90,
        'email_header' => null,
        'email_footer' => null,
        'email_signature' => null,
        'language' => 'en',
        'country' => 'US',
    ];

    public function getSettings(Business $business): array
    {
        $settings = $business->settings ?? [];
        return array_merge($this->defaultSettings, $settings);
    }

    public function getSetting(Business $business, string $key, $default = null)
    {
        $settings = $this->getSettings($business);
        return Arr::get($settings, $key, $default);
    }

    public function updateSettings(Business $business, array $newSettings): Business
    {
        $currentSettings = $business->settings ?? [];
        $updatedSettings = array_merge($currentSettings, $newSettings);
        
        $business->settings = $updatedSettings;
        $business->save();
        
        // Clear the cache after updating
        BusinessSettingsHelper::clearCache($business);
        
        return $business;
    }

    public function getLogoUrl(Business $business): ?string
    {
        $path = $this->getSetting($business, 'logo_path');
        return $path ? asset('storage/' . $path) : null;
    }

    public function getFaviconUrl(Business $business): ?string
    {
        $path = $this->getSetting($business, 'favicon_path');
        return $path ? asset('storage/' . $path) : null;
    }

    public function isEnabled(Business $business, string $key): bool
    {
        return (bool) $this->getSetting($business, $key, false);
    }

    public function getFormattedBusinessHours(Business $business): array
    {
        $hours = $business->business_hours ?? [];
        $formatted = [];
        
        $days = ['monday', 'tuesday', 'wednesday', 'thursday', 'friday', 'saturday', 'sunday'];
        
        foreach ($days as $day) {
            if (isset($hours[$day]) && $hours[$day]['open'] && $hours[$day]['close']) {
                $formatted[$day] = $hours[$day]['open'] . ' - ' . $hours[$day]['close'];
            } else {
                $formatted[$day] = 'Closed';
            }
        }
        
        return $formatted;
    }
}