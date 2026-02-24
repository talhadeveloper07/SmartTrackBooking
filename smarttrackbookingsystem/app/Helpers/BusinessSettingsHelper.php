<?php
namespace App\Helpers;

use App\Models\Business;
use App\Services\BusinessSettingsService;

class BusinessSettingsHelper
{
    protected static $settingsService;
    protected static $requestCache = [];

    protected static function getService()
    {
        if (!self::$settingsService) {
            self::$settingsService = app(BusinessSettingsService::class);
        }
        return self::$settingsService;
    }

    public static function get($business, $key, $default = null)
    {
        if (!$business) return $default;

        $businessId = $business instanceof Business ? $business->id : $business;
        
        if (isset(self::$requestCache[$businessId][$key])) {
            return self::$requestCache[$businessId][$key];
        }

        $value = self::getService()->getSetting($business, $key, $default);

        if (!isset(self::$requestCache[$businessId])) {
            self::$requestCache[$businessId] = [];
        }
        self::$requestCache[$businessId][$key] = $value;

        return $value;
    }

    public static function getLogo($business)
    {
        if (!$business) return null;
        $url = self::getService()->getLogoUrl($business);
        if ($url) {
            $timestamp = $business instanceof Business ? $business->updated_at->timestamp : time();
            $url .= (strpos($url, '?') === false ? '?' : '&') . 't=' . $timestamp;
        }
        return $url;
    }

    public static function getFavicon($business)
    {
        if (!$business) return null;
        $url = self::getService()->getFaviconUrl($business);
        if ($url) {
            $timestamp = $business instanceof Business ? $business->updated_at->timestamp : time();
            $url .= (strpos($url, '?') === false ? '?' : '&') . 't=' . $timestamp;
        }
        return $url;
    }

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

        $colors = [
            'primary' => self::get($business, 'primary_color', '#110093'),
            'secondary' => self::get($business, 'secondary_color', '#38c172'),
            'accent' => self::get($business, 'accent_color', '#f6993f'),
        ];
        
        $colors['primary_rgb'] = self::hexToRgb($colors['primary']);
        $colors['secondary_rgb'] = self::hexToRgb($colors['secondary']);
        $colors['accent_rgb'] = self::hexToRgb($colors['accent']);
        
        return $colors;
    }

    public static function getAll($business)
    {
        return $business ? self::getService()->getSettings($business) : [];
    }

    public static function clearRequestCache($business)
    {
        $businessId = $business instanceof Business ? $business->id : $business;
        if (isset(self::$requestCache[$businessId])) {
            unset(self::$requestCache[$businessId]);
        }
    }

    public static function hexToRgb($hex)
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
}