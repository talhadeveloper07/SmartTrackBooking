<?php

namespace App\Http\Controllers\Business;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Business;
use App\Models\Appointment;
use App\Models\Customer;
use App\Models\Employee;
use App\Models\BusinessAdmin;
use App\Services\Dashboard\DashboardSettingService;
use App\Services\Business\Profile\ProfileService;
class AdminController extends Controller
{
    public function index(Business $business)
    {
    $totalAppointments = Appointment::where('business_id', $business->id)->count();

    $pendingAppointments = Appointment::where('business_id', $business->id)
        ->whereIn('status', ['pending']) // add 'confirmed' if you want as pending-like
        ->count();

    $totalCustomers = Customer::where('business_id', $business->id)->count();

    $totalEmployees = Employee::where('business_id', $business->id)->count();
    
    $recentCustomers = Customer::where('business_id', $business->id)
        ->with('user:id,name,email')
        ->latest()
        ->limit(4)
        ->get();

  
    $user = auth()->user();

    $business = $user->businessAdmin->business ?? null;

    $subscription = $business ? $business->subscription : null;

    return view('business.admin.dashboard', compact(
        'business',
        'totalAppointments',
        'pendingAppointments',
        'totalCustomers',
        'totalEmployees',
        'recentCustomers',
        'subscription'
    ));
    }
    public function edit(Business $business)
    {
        $setting = $business->dashboardSetting()->firstOrCreate([]);

        return view('common.dashboard-settings', [
            'layout' => 'business.layouts.app', // ✅ business layout
            'content' => 'business_content', // ✅ business layout
            'setting' => $setting,
            'updateRoute' => route('business.settings.update', $business->slug),
        ]);
    }
    public function update(Request $request, Business $business, DashboardSettingService $service)
    {
        $validated = $request->validate([
            'logo' => ['nullable','image','mimes:png,jpg,jpeg,webp,svg','max:2048'],
            'favicon' => ['nullable','image','mimes:png,ico,jpg,jpeg,webp','max:1024'],
            'primary_color' => ['nullable','regex:/^#([A-Fa-f0-9]{6}|[A-Fa-f0-9]{3})$/'],
            'secondary_color' => ['nullable','regex:/^#([A-Fa-f0-9]{6}|[A-Fa-f0-9]{3})$/'],
            'sidebar_bg' => ['nullable','regex:/^#([A-Fa-f0-9]{6}|[A-Fa-f0-9]{3})$/'],
            'sidebar_text' => ['nullable','regex:/^#([A-Fa-f0-9]{6}|[A-Fa-f0-9]{3})$/'],
            'topbar_bg' => ['nullable','regex:/^#([A-Fa-f0-9]{6}|[A-Fa-f0-9]{3})$/'],
            'topbar_text' => ['nullable','regex:/^#([A-Fa-f0-9]{6}|[A-Fa-f0-9]{3})$/'],
        ]);

        $validated['logo'] = $request->file('logo');
        $validated['favicon'] = $request->file('favicon');

        $service->update($business, $validated);

        return back()->with('success', 'Business dashboard settings updated.');
    }

      public function edit_profile(Business $business)
    {
        return view('business.admin.profile', compact('business'));
    }

    public function update_profile(Request $request, Business $business, ProfileService $profileService)
    {
        $user = auth()->user();

        $validated = $request->validate([
            'name'    => ['required', 'string', 'max:255'],
            'phone'   => ['nullable', 'string', 'max:255'],
            'address' => ['nullable', 'string'],
            'avatar'  => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
        ]);

        // pass file as UploadedFile instance
        $validated['avatar'] = $request->file('avatar');

        try {
            $profileService->updateBasicInfo($user, $validated);

            return back()->with('success', 'Profile updated successfully.');
        } catch (\Throwable $e) {
            return back()->withInput()->with('error', $e->getMessage());
        }
    }

    public function sendPasswordResetLink(Business $business, ProfileService $passwordResetService)
    {
        $user = auth()->user();

        try {
            $status = $passwordResetService->sendResetLink($user);

            if ($status === \Illuminate\Support\Facades\Password::RESET_LINK_SENT) {
                return back()->with('success', 'Password reset link sent to your email.');
            }

            return back()->with('error', __($status));
        } catch (\Throwable $e) {
            return back()->with('error', $e->getMessage());
        }
    }
}
