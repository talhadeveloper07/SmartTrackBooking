<?php
namespace App\Http\Controllers\Business;

use App\Http\Controllers\Controller;
use App\Models\Business;
use App\Services\BusinessSettingsService;
use App\Helpers\BusinessSettingsHelper;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class BusinessSettingsController extends Controller
{
    protected $settingsService;

    public function __construct(BusinessSettingsService $settingsService)
    {
        $this->settingsService = $settingsService;
    }

    public function index(Business $business)
    {
        $this->authorize('update', $business);
        $settings = $this->settingsService->getSettings($business);
        return view('business.admin.settings.index', compact('business', 'settings'));
    }

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
            'time_format' => 'sometimes|string|max:20',
            'currency' => 'sometimes|string|max:10',
            'week_start' => 'sometimes|string|max:10',
        ]);

        // Update business model fields
        if (isset($validated['business_name'])) {
            $business->name = $validated['business_name'];
        }
        if (isset($validated['business_email'])) {
            $business->email = $validated['business_email'];
        }
        if (isset($validated['business_phone'])) {
            $business->phone = $validated['business_phone'];
        }
        if (isset($validated['business_address'])) {
            $business->address = $validated['business_address'];
        }
        $business->save();

        // Update settings
        $settingsData = array_diff_key($validated, array_flip(['business_name', 'business_email', 'business_phone', 'business_address']));
        $this->settingsService->updateSettings($business, $settingsData);

        return redirect()->route('business.settings', $business->slug)
            ->with('success', 'General settings updated successfully.');
    }

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
            
            $oldSettings = $this->settingsService->getSettings($business);
            if (!empty($oldSettings['logo_path'])) {
                Storage::disk('public')->delete($oldSettings['logo_path']);
            }
        }

        // Handle favicon upload
        if ($request->hasFile('favicon')) {
            $path = $request->file('favicon')->store('business-favicons', 'public');
            $validated['favicon_path'] = $path;
            
            $oldSettings = $this->settingsService->getSettings($business);
            if (!empty($oldSettings['favicon_path'])) {
                Storage::disk('public')->delete($oldSettings['favicon_path']);
            }
        }

        $this->settingsService->updateSettings($business, $validated);

        return redirect()->route('business.settings', $business->slug)
            ->with('success', 'Appearance settings updated successfully.');
    }

    public function removeLogo(Request $request, Business $business)
    {
        $this->authorize('update', $business);

        $type = $request->input('type', 'logo');
        $settings = $this->settingsService->getSettings($business);
        
        $pathKey = $type . '_path';
        if (!empty($settings[$pathKey])) {
            Storage::disk('public')->delete($settings[$pathKey]);
            unset($settings[$pathKey]);
            
            $business->settings = $settings;
            $business->save();
            
            BusinessSettingsHelper::clearRequestCache($business);
            $business->touch();
        }

        return response()->json(['success' => true]);
    }
}