<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\Dashboard\DashboardSettingService;
use App\Models\Organization;
use Illuminate\Support\Facades\Auth;


class OrganizationController extends Controller
{
    public function index()
    {
        return view('organization.dashboard');
    }
    public function edit(Organization $organization)
    {
         $organization = Organization::firstOrFail();

        $setting = $organization->dashboardSetting()->firstOrCreate([]);

        return view('common.dashboard-settings', [
            'layout' => 'organization.layouts.app',
            'content' => 'organization_content',
            'setting' => $setting,
            'updateRoute' => route('org.settings.update'),
        ]);
    }

    public function update(Request $request, Organization $organization, DashboardSettingService $service)
    {
         $organization = Organization::firstOrFail();

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

    $service->update($organization, $validated);

    return back()->with('success','Organization settings updated.');
    }
}
