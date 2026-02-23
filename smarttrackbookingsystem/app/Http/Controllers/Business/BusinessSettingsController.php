<?php
// app/Http/Controllers/Business/BusinessSettingsController.php

namespace App\Http\Controllers\Business;

use App\Http\Controllers\Controller;
use App\Models\Business;
use App\Services\BusinessSettingsService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class BusinessSettingsController extends Controller
{
    protected $settingsService;

    public function __construct(BusinessSettingsService $settingsService)
    {
        $this->settingsService = $settingsService;
    }

    /**
     * Show settings page
     */
    public function index(Business $business)
    {
        // Verify that the authenticated user is admin of this business
        $this->authorize('update', $business);
        
        $settings = $this->settingsService->getSettings($business);
        
        return view('business.admin.settings.index', compact('business', 'settings'));
    }

    /**
     * Update general settings
     */
    public function updateGeneral(Request $request, Business $business)
    {
        $this->authorize('update', $business);

        $validated = $request->validate([
            'business_name' => 'sometimes|string|max:255',
            'business_email' => 'sometimes|email|max:255',
            'business_phone' => 'sometimes|string|max:20',
            'business_address' => 'sometimes|string|max:500',
            'timezone' => 'sometimes|string|max:100',
            'date_format' => 'sometimes|string|max:20',
            'currency' => 'sometimes|string|max:10',
        ]);

        $this->settingsService->updateSettings($business, $validated);

        return redirect()->route('business.settings', $business->slug)
            ->with('success', 'General settings updated successfully.');
    }

    /**
     * Update appearance settings (colors, logo)
     */
    public function updateAppearance(Request $request, Business $business)
    {
        $this->authorize('update', $business);

        $validated = $request->validate([
            'primary_color' => 'sometimes|string|max:7',
            'secondary_color' => 'sometimes|string|max:7',
            'accent_color' => 'sometimes|string|max:7',
            'font_family' => 'sometimes|string|max:100',
            'logo' => 'sometimes|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            'favicon' => 'sometimes|image|mimes:ico,png|max:1024',
        ]);

        // Handle logo upload
        if ($request->hasFile('logo')) {
            $path = $request->file('logo')->store('business-logos', 'public');
            $validated['logo_path'] = $path;
            
            // Delete old logo if exists
            $oldSettings = $this->settingsService->getSettings($business);
            if (!empty($oldSettings['logo_path'])) {
                Storage::disk('public')->delete($oldSettings['logo_path']);
            }
        }

        // Handle favicon upload
        if ($request->hasFile('favicon')) {
            $path = $request->file('favicon')->store('business-favicons', 'public');
            $validated['favicon_path'] = $path;
            
            // Delete old favicon if exists
            $oldSettings = $this->settingsService->getSettings($business);
            if (!empty($oldSettings['favicon_path'])) {
                Storage::disk('public')->delete($oldSettings['favicon_path']);
            }
        }

        $this->settingsService->updateSettings($business, $validated);

        return redirect()->route('business.settings', $business->slug)
            ->with('success', 'Appearance settings updated successfully.');
    }

    /**
     * Update notification settings
     */
    public function updateNotifications(Request $request, Business $business)
    {
        $this->authorize('update', $business);

        $validated = $request->validate([
            'email_notifications' => 'sometimes|boolean',
            'sms_notifications' => 'sometimes|boolean',
            'push_notifications' => 'sometimes|boolean',
            'notification_events' => 'sometimes|array',
        ]);

        $this->settingsService->updateSettings($business, $validated);

        return redirect()->route('business.settings', $business->slug)
            ->with('success', 'Notification settings updated successfully.');
    }

    /**
     * Update invoice settings
     */
    public function updateInvoice(Request $request, Business $business)
    {
        $this->authorize('update', $business);

        $validated = $request->validate([
            'invoice_prefix' => 'sometimes|string|max:20',
            'invoice_logo' => 'sometimes|image|mimes:jpeg,png,jpg|max:2048',
            'invoice_footer' => 'sometimes|string|max:500',
            'invoice_terms' => 'sometimes|string|max:1000',
            'tax_rate' => 'sometimes|numeric|min:0|max:100',
            'tax_name' => 'sometimes|string|max:50',
        ]);

        // Handle invoice logo upload
        if ($request->hasFile('invoice_logo')) {
            $path = $request->file('invoice_logo')->store('business-invoice-logos', 'public');
            $validated['invoice_logo_path'] = $path;
            
            // Delete old invoice logo if exists
            $oldSettings = $this->settingsService->getSettings($business);
            if (!empty($oldSettings['invoice_logo_path'])) {
                Storage::disk('public')->delete($oldSettings['invoice_logo_path']);
            }
        }

        $this->settingsService->updateSettings($business, $validated);

        return redirect()->route('business.settings', $business->slug)
            ->with('success', 'Invoice settings updated successfully.');
    }

    /**
     * Remove logo
     */
    public function removeLogo(Request $request, Business $business)
    {
        $this->authorize('update', $business);

        $type = $request->input('type', 'logo'); // logo, favicon, invoice_logo
        
        $settings = $this->settingsService->getSettings($business);
        
        $pathKey = $type . '_path';
        if (!empty($settings[$pathKey])) {
            Storage::disk('public')->delete($settings[$pathKey]);
            unset($settings[$pathKey]);
            
            $business->settings = $settings;
            $business->save();
        }

        return response()->json(['success' => true]);
    }
}