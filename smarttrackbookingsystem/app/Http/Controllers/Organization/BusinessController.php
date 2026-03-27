<?php

namespace App\Http\Controllers\Organization;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\Business;
use App\Models\BusinessSubscription;
use App\Models\Plan;
use DataTables;
use Illuminate\Support\Str;

class BusinessController extends Controller
{
    public function all_business_accounts()
    {
        return view('organization.business.index');
    }
    public function getBusinesses(Request $request)
    {
        if ($request->ajax()) {

            $data = Business::latest();

            return DataTables::of($data)
                ->addIndexColumn()

                ->editColumn('logo', function ($row) {
                    if ($row->logo) {
                        return '<img src="' . asset('storage/' . $row->logo) . '" width="40">';
                    }
                    return '-';
                })

                ->editColumn('status', function ($row) {
                    if ($row->status == 'active') {
                        return '<span class="badge bg-success">Active</span>';
                    }
                    return '<span class="badge bg-danger">Inactive</span>';
                })

                ->addColumn('action', function ($row) {
                    return '
                        <a href="'.route('org.business.show',$row->slug).'" class="btn btn-warning shadow btn-xs sharp me-1"><i class="fa fa-eye"></i></a>
                        <a href="'.route('org.business.edit',$row->slug).'" class="btn btn-primary shadow btn-xs sharp me-1"><i class="fa fa-pencil"></i></a>
                        <a href="#" class="btn btn-danger shadow btn-xs sharp"><i class="fa fa-trash"></i></a>
                    ';
                })

                ->rawColumns(['logo', 'status', 'action'])
                ->make(true);
        }
    }

    public function add_new_business()
    {
        $plans = Plan::where('active', true)->get();
        return view('organization.business.add', compact('plans'));
    }

    public function store_business(Request $request)
{
    $request->validate([
        'name' => 'required|unique:businesses,name',
        'email' => 'nullable|email',
        'logo' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        'cover_image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        'plan_id' => 'required|exists:plans,id',
    ]);

    // Use a transaction to ensure both records are created together
    DB::transaction(function () use ($request) {
        
        // 1. Prepare Business Data
        $businessData = $request->except(['logo', 'cover_image', 'plan_id']);
        $businessData['slug'] = Str::slug($request->name) . '-' . rand(100, 999); // Added rand to avoid slug collisions

        if ($request->hasFile('logo')) {
            $businessData['logo'] = $request->file('logo')->store('business', 'public');
        }

        if ($request->hasFile('cover_image')) {
            $businessData['cover_image'] = $request->file('cover_image')->store('business', 'public');
        }

        // 2. Create Business
        $business = Business::create($businessData);

        // 3. Create Subscription Record
        // We fetch the plan to see if we need to calculate dates or prices
        $plan = Plan::find($request->plan_id);

        BusinessSubscription::create([
            'business_id'            => $business->id,
            'plan_id'                => $plan->id,
            'status'                 => 'active',
            'starts_at'              => now(),
            'ends_at'                => now()->addMonth(), // Defaulting to 1 month; adjust as needed
            'stripe_customer_id'     => null, // Admin created, so likely no Stripe ID yet
            'stripe_subscription_id' => null,
        ]);
    });

    return redirect()->route('org.business-accounts')->with('success', 'Business and Subscription Plan created successfully');
}

    public function edit(Business $business)
    {
        return view('organization.business.edit', compact('business'));
    }

    public function update(Request $request, Business $business)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'business_type' => 'nullable|string|max:255',
            'email' => 'nullable|email|max:255',
            'phone' => 'nullable|string|max:50',
            'address' => 'nullable|string|max:255',
            'city' => 'nullable|string|max:100',
            'state' => 'nullable|string|max:100',
            'country' => 'nullable|string|max:100',
            'postal_code' => 'nullable|string|max:20',
            'status' => 'required|in:active,inactive',
            'description' => 'nullable|string',
            'business_hours' => 'nullable|string',
            'logo' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
            'cover_image' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:4096',
        ]);

        // Auto update slug if name changed (optional)
        if ($business->name !== $validated['name']) {
            $validated['slug'] = Str::slug($validated['name']);
        }

        // Upload logo (optional)
        if ($request->hasFile('logo')) {
            $validated['logo'] = $request->file('logo')->store('business', 'public');
        }

        // Upload cover (optional)
        if ($request->hasFile('cover_image')) {
            $validated['cover_image'] = $request->file('cover_image')->store('business', 'public');
        }

        $business->update($validated);

        return redirect()->route('org.business.index')->with('success', 'Business Updated Successfully');
    }

    public function show(Business $business)
    {
        // eager load admins + users
        $business->load('admins');

        return view('organization.business.show', compact('business'));
    }
}
