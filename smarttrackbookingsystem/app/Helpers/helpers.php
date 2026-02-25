<?php
// app/Helpers/helpers.php

if (!function_exists('adjustBrightness')) {
    /**
     * Adjust the brightness of a hex color
     * 
     * @param string $hexCode The hex color code (with or without #)
     * @param int $adjustPercent Percentage to adjust (-100 to 100)
     * @return string The adjusted hex color
     */
    function adjustBrightness($hexCode, $adjustPercent) {
        // Remove # if present
        $hexCode = ltrim($hexCode, '#');
        
        // Handle shorthand hex (e.g., #FFF)
        if (strlen($hexCode) == 3) {
            $hexCode = $hexCode[0] . $hexCode[0] . $hexCode[1] . $hexCode[1] . $hexCode[2] . $hexCode[2];
        }
        
        // Convert to RGB
        $hex = str_split($hexCode, 2);
        $rgb = [];
        
        foreach ($hex as $color) {
            $rgb[] = hexdec($color);
        }
        
        // Adjust each color
        foreach ($rgb as $key => $val) {
            $adjust = $adjustPercent * $val / 100;
            
            // Darken (negative adjustPercent) or lighten (positive adjustPercent)
            $val = $val + $adjust;
            
            // Clamp values between 0 and 255
            $val = max(0, min(255, $val));
            $rgb[$key] = $val;
        }
        
        // Convert back to hex
        return sprintf("#%02x%02x%02x", $rgb[0], $rgb[1], $rgb[2]);
    }
}

if (!function_exists('hexToRgba')) {
    /**
     * Convert hex color to rgba
     * 
     * @param string $hex The hex color code
     * @param float $opacity The opacity value (0-1)
     * @return string The rgba color
     */
    function hexToRgba($hex, $opacity = 1.0) {
        $hex = ltrim($hex, '#');
        
        if (strlen($hex) == 3) {
            $hex = $hex[0] . $hex[0] . $hex[1] . $hex[1] . $hex[2] . $hex[2];
        }
        
        $r = hexdec(substr($hex, 0, 2));
        $g = hexdec(substr($hex, 2, 2));
        $b = hexdec(substr($hex, 4, 2));
        
        return "rgba($r, $g, $b, $opacity)";
    }
}

if (!function_exists('isHexColor')) {
    /**
     * Check if a string is a valid hex color
     * 
     * @param string $color The color string
     * @return bool
     */
    function isHexColor($color) {
        $color = ltrim($color, '#');
        return ctype_xdigit($color) && (strlen($color) == 3 || strlen($color) == 6);
    }
}

if (!function_exists('getContrastText')) {
    /**
     * Get contrasting text color (black or white) based on background color
     * 
     * @param string $hexColor The background color
     * @return string '#000000' or '#ffffff'
     */
    function getContrastText($hexColor) {
        $hexColor = ltrim($hexColor, '#');
        
        if (strlen($hexColor) == 3) {
            $hexColor = $hexColor[0] . $hexColor[0] . $hexColor[1] . $hexColor[1] . $hexColor[2] . $hexColor[2];
        }
        
        $r = hexdec(substr($hexColor, 0, 2));
        $g = hexdec(substr($hexColor, 2, 2));
        $b = hexdec(substr($hexColor, 4, 2));
        
        // Calculate luminance
        $luminance = (0.299 * $r + 0.587 * $g + 0.114 * $b) / 255;
        
        return $luminance > 0.5 ? '#000000' : '#ffffff';
    }
}