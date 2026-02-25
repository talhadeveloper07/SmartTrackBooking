<?php

namespace App\Http\Controllers\Business;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Service;
use App\Models\Business;
use Illuminate\Support\Facades\DB;
class ServiceController extends Controller
{
    public function index(Business $business)
    {
        $services = Service::with(['durations' => function ($q) {
                $q->orderBy('duration_minutes');
            }])
            ->where('business_id', $business->id)
            ->latest()
            ->get();

        return view('business.admin.services.index', compact('business', 'services'));
    }
    public function add_service(Business $business)
    {
        return view('business.admin.services.create', compact('business'));
    }
    public function store(Request $request, Business $business)
    {

        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'status' => 'required|in:active,inactive',
            'durations' => 'required|array|min:1',
            'durations.*.duration_minutes' => 'required|integer|min:1',
            'durations.*.price' => 'required|numeric|min:0',
            'durations.*.status' => 'required|in:active,inactive'
        ]);

        DB::beginTransaction();

        try {

            $service = Service::create([
                'business_id' => $business->id,
                'name' => $request->name,
                'slug' => \Str::slug($request->name),
                'description' => $request->description,
                'status' => $request->status,
            ]);

            foreach ($request->durations as $duration) {
                $service->durations()->create([
                    'duration_name' => $duration['duration_name'] ?? null,
                    'duration_minutes' => $duration['duration_minutes'],
                    'price' => $duration['price'],
                    'deposit' => $duration['deposit'] ?? 0,
                    'status' => $duration['status']
                ]);
            }

            DB::commit();

            return redirect()
                ->route('business.services.dashboard', $business->slug)
                ->with('success', 'Service created successfully');

        } catch (\Exception $e) {
            DB::rollback();
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

    public function update(Request $request, Business $business, Service $service)
    {
        abort_if($service->business_id !== $business->id, 404);

        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'status' => 'required|in:active,inactive',

            // durations array
            'durations' => 'required|array|min:1',
            'durations.*.duration_minutes' => 'required|integer|min:1',
            'durations.*.price' => 'required|numeric|min:0',
            'durations.*.deposit' => 'nullable|numeric|min:0',
            'durations.*.duration_name' => 'nullable|string|max:255',
            'durations.*.status' => 'required|in:active,inactive',

        ]);

        DB::beginTransaction();

        try {
            // 1) Update service basic details
            $service->update([
                'name' => $request->name,
                'slug' => $service->name !== $request->name ? Str::slug($request->name) : $service->slug,
                'description' => $request->description,
                'status' => $request->status,
            ]);

            // 2) Sync durations
            // keep IDs sent from form
            $incomingIds = [];

            foreach ($request->durations as $row) {
                // if duration has id => update
                if (!empty($row['id'])) {
                    $incomingIds[] = (int) $row['id'];

                    $duration = $service->durations()
                        ->where('id', $row['id'])
                        ->first();

                    // extra safety
                    if ($duration) {
                        $duration->update([
                            'duration_name' => $row['duration_name'] ?? null,
                            'duration_minutes' => $row['duration_minutes'],
                            'price' => $row['price'],
                            'deposit' => $row['deposit'] ?? 0,
                            'status' => $row['status'],
                        ]);
                    }
                } else {
                    // new duration
                    $new = $service->durations()->create([
                        'duration_name' => $row['duration_name'] ?? null,
                        'duration_minutes' => $row['duration_minutes'],
                        'price' => $row['price'],
                        'deposit' => $row['deposit'] ?? 0,
                        'status' => $row['status'],
                    ]);

                    $incomingIds[] = $new->id;
                }
            }

            // 3) Delete removed durations (existing but not in incoming)
            $service->durations()
                ->whereNotIn('id', $incomingIds)
                ->delete();

            DB::commit();

            return redirect()
                ->route('business.services', $business->slug)
                ->with('success', 'Service updated successfully');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Something went wrong');
        }
    }
}
