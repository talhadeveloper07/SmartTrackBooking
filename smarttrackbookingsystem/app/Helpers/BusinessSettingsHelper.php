<?php
// app/Helpers/BusinessSettingsHelper.php

namespace App\Helpers;

use App\Models\Business;
use App\Services\BusinessSettingsService;
use Illuminate\Support\Facades\Cache;

class BusinessSettingsHelper
{
    protected static $settingsService;
    protected static $cache = [];
    protected static $cacheTimeout = 3600; // 1 hour cache

    protected static function getService()
    {
        if (!self::$settingsService) {
            self::$settingsService = app(BusinessSettingsService::class);
        }
        return self::$settingsService;
    }

    /**
     * Get a specific setting with caching
     */
    public static function get($business, $key, $default = null)
    {
        if (!$business) {
            return $default;
        }

        $businessId = $business instanceof Business ? $business->id : $business;
        
        // Check if already loaded in current request
        if (isset(self::$cache[$businessId][$key])) {
            return self::$cache[$businessId][$key];
        }

        // Try to get from cache first
        $cacheKey = "business_settings_{$businessId}_{$key}";
        $value = Cache::remember($cacheKey, self::$cacheTimeout, function() use ($business, $key, $default) {
            return self::getService()->getSetting($business, $key, $default);
        });

        // Store in request cache
        if (!isset(self::$cache[$businessId])) {
            self::$cache[$businessId] = [];
        }
        self::$cache[$businessId][$key] = $value;

        return $value;
    }

    /**
     * Get multiple settings at once
     */
    public static function getMany($business, array $keys)
    {
        $result = [];
        foreach ($keys as $key) {
            $result[$key] = self::get($business, $key);
        }
        return $result;
    }

    /**
     * Get logo URL with cache busting
     */
    public static function getLogo($business)
    {
        if (!$business) {
            return null;
        }

        $businessId = $business instanceof Business ? $business->id : $business;
        $cacheKey = "business_logo_{$businessId}";
        
        // Check if business has updated_at timestamp to use for cache busting
        $timestamp = $business instanceof Business ? $business->updated_at->timestamp : time();
        
        return Cache::remember($cacheKey, self::$cacheTimeout, function() use ($business, $timestamp) {
            $url = self::getService()->getLogoUrl($business);
            // Add timestamp for cache busting
            if ($url) {
                $url .= (strpos($url, '?') === false ? '?' : '&') . 'v=' . $timestamp;
            }
            return $url;
        });
    }

    /**
     * Get favicon URL with cache busting
     */
    public static function getFavicon($business)
    {
        if (!$business) {
            return null;
        }

        $businessId = $business instanceof Business ? $business->id : $business;
        $cacheKey = "business_favicon_{$businessId}";
        
        $timestamp = $business instanceof Business ? $business->updated_at->timestamp : time();
        
        return Cache::remember($cacheKey, self::$cacheTimeout, function() use ($business, $timestamp) {
            $url = self::getService()->getFaviconUrl($business);
            if ($url) {
                $url .= (strpos($url, '?') === false ? '?' : '&') . 'v=' . $timestamp;
            }
            return $url;
        });
    }

    /**
     * Get all colors at once with caching
     */
    public static function getColors($business)
    {
        if (!$business) {
            return [
                'primary' => '#110093',
                'secondary' => '#38c172',
                'accent' => '#f6993f',
                'primary_rgb' => '17,0,147',
                'secondary_rgb' => '56,193,114',
                'accent_rgb' => '246,153,63',
            ];
        }

        $businessId = $business instanceof Business ? $business->id : $business;
        $cacheKey = "business_colors_{$businessId}";
        
        return Cache::remember($cacheKey, self::$cacheTimeout, function() use ($business) {
            $colors = [
                'primary' => self::get($business, 'primary_color', '#110093'),
                'secondary' => self::get($business, 'secondary_color', '#38c172'),
                'accent' => self::get($business, 'accent_color', '#f6993f'),
            ];
            
            // Add RGB values for CSS rgba() usage
            $colors['primary_rgb'] = self::hexToRgb($colors['primary']);
            $colors['secondary_rgb'] = self::hexToRgb($colors['secondary']);
            $colors['accent_rgb'] = self::hexToRgb($colors['accent']);
            
            return $colors;
        });
    }

    /**
     * Get all settings at once (useful for JSON responses)
     */
    public static function getAll($business)
    {
        if (!$business) {
            return [];
        }

        $businessId = $business instanceof Business ? $business->id : $business;
        $cacheKey = "business_all_settings_{$businessId}";
        
        return Cache::remember($cacheKey, self::$cacheTimeout, function() use ($business) {
            return self::getService()->getSettings($business);
        });
    }

    /**
     * Clear cache for a business (call after updating settings)
     */
    public static function clearCache($business)
    {
        $businessId = $business instanceof Business ? $business->id : $business;
        
        // Clear specific caches
        Cache::forget("business_colors_{$businessId}");
        Cache::forget("business_logo_{$businessId}");
        Cache::forget("business_favicon_{$businessId}");
        Cache::forget("business_all_settings_{$businessId}");
        
        // Clear pattern
        $keys = Cache::get("business_keys_{$businessId}", []);
        foreach ($keys as $key) {
            Cache::forget("business_settings_{$businessId}_{$key}");
        }
        
        // Clear request cache
        if (isset(self::$cache[$businessId])) {
            unset(self::$cache[$businessId]);
        }
    }

    /**
     * Check if a feature is enabled
     */
    public static function isEnabled($business, $feature, $default = false)
    {
        return (bool) self::get($business, "{$feature}_enabled", $default);
    }

    /**
     * Get formatted business hours
     */
    public static function getBusinessHours($business, $format = 'full')
    {
        if (!$business) {
            return [];
        }

        $hours = $business->business_hours ?? [];
        $formatted = [];
        
        $days = ['monday', 'tuesday', 'wednesday', 'thursday', 'friday', 'saturday', 'sunday'];
        $dayNames = [
            'monday' => 'Monday',
            'tuesday' => 'Tuesday',
            'wednesday' => 'Wednesday',
            'thursday' => 'Thursday',
            'friday' => 'Friday',
            'saturday' => 'Saturday',
            'sunday' => 'Sunday'
        ];
        
        foreach ($days as $day) {
            if (isset($hours[$day]) && $hours[$day]['open'] && $hours[$day]['close']) {
                if ($format === 'short') {
                    $formatted[$day] = $hours[$day]['open'] . ' - ' . $hours[$day]['close'];
                } else {
                    $formatted[$dayNames[$day]] = date('g:i A', strtotime($hours[$day]['open'])) . 
                                                  ' - ' . 
                                                  date('g:i A', strtotime($hours[$day]['close']));
                }
            } else {
                $formatted[$format === 'short' ? $day : $dayNames[$day]] = 'Closed';
            }
        }
        
        return $formatted;
    }

    /**
     * Convert hex color to RGB string
     */
    protected static function hexToRgb($hex)
    {
        $hex = ltrim($hex, '#');
        
        if (strlen($hex) == 3) {
            $hex = $hex[0] . $hex[0] . $hex[1] . $hex[1] . $hex[2] . $hex[2];
        }
        
        $r = hexdec(substr($hex, 0, 2));
        $g = hexdec(substr($hex, 2, 2));
        $b = hexdec(substr($hex, 4, 2));
        
        return "{$r},{$g},{$b}";
    }

    /**
     * Magic method to handle dynamic method calls
     */
    public static function __callStatic($method, $arguments)
    {
        // Handle methods like getPrimaryColor(), isNotificationEnabled(), etc.
        if (strpos($method, 'get') === 0) {
            $property = strtolower(preg_replace('/(?<!^)[A-Z]/', '_$0', substr($method, 3)));
            if (!empty($arguments)) {
                return self::get($arguments[0], $property, $arguments[1] ?? null);
            }
        }
        
        if (strpos($method, 'is') === 0) {
            $feature = strtolower(preg_replace('/(?<!^)[A-Z]/', '_$0', substr($method, 2)));
            if (!empty($arguments)) {
                return self::isEnabled($arguments[0], $feature, $arguments[1] ?? false);
            }
        }
        
        throw new \BadMethodCallException("Method {$method} does not exist");
    }
}