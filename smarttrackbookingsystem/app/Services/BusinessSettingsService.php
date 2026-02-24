<?php
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
        
        BusinessSettingsHelper::clearRequestCache($business);
        $business->touch();
        
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
}