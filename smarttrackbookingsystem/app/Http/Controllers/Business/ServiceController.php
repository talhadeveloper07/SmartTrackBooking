<?php

namespace App\Http\Controllers\Business;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Service;
use App\Models\Business;
use Illuminate\Support\Facades\DB;
use App\Services\Business\Service\ServiceManagementService;

class ServiceController extends Controller
{
    public function index(Business $business)
    {
       $services = Service::with(['durations' => fn($q) => $q->orderBy('duration_minutes')])
    ->withCount('employees')
    ->where('business_id', $business->id)
    ->latest()
    ->get();

    return view('business.admin.services.index', compact('business', 'services'));
    }
    public function add_service(Business $business)
    {
        return view('business.admin.services.create', compact('business'));
    }
    public function store(Request $request, Business $business, ServiceManagementService $serviceManagementService)
    {

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'status' => 'required|in:active,inactive',
            'durations' => 'required|array|min:1',
            'durations.*.duration_minutes' => 'required|integer|min:1',
            'durations.*.price' => 'required|numeric|min:0',
            'durations.*.status' => 'required|in:active,inactive'
        ]);

        try {

            $serviceManagementService->create($business, $validated);

            return redirect()
                ->route('business.services.dashboard', $business->slug)
                ->with('success', 'Service created successfully');

        } catch (\Throwable $e) {

            return back()->with('error', 'Something went wrong');
        }
    }
    public function destroy(Business $business, Service $service)
    {
        // Security: ensure service belongs to current business
        abort_if($service->business_id !== $business->id, 404);

        $service->delete();

        return back()->with('success', 'Service deleted successfully');
    }

    public function edit(Business $business, Service $service)
    {
        // Security: service must belong to this business
        abort_if($service->business_id !== $business->id, 404);

        $service->load('durations');

        return view('business.admin.services.edit', compact('business', 'service'));
    }

    public function update(Request $request,Business $business,Service $service,ServiceManagementService $serviceManagementService) {

    $validated = $request->validate([
        'name' => 'required|string|max:255',
        'description' => 'nullable|string',
        'status' => 'required|in:active,inactive',

        'durations' => 'required|array|min:1',
        'durations.*.duration_minutes' => 'required|integer|min:1',
        'durations.*.price' => 'required|numeric|min:0',
        'durations.*.deposit' => 'nullable|numeric|min:0',
        'durations.*.duration_name' => 'nullable|string|max:255',
        'durations.*.status' => 'required|in:active,inactive',
    ]);

    try {

        $serviceManagementService->update($business, $service, $validated);

        return redirect()
            ->route('business.services', $business->slug)
            ->with('success', 'Service updated successfully');

    } catch (\Throwable $e) {

        return back()->with('error', 'Something went wrong');
    }
}
}
