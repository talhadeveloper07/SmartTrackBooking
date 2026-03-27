<?php

namespace App\Http\Controllers\Organization;

use App\Http\Controllers\Controller;
use App\Models\Business;
use App\Models\Plan;
use App\Models\BusinessSubscription;
use Illuminate\Http\Request;

class BusinessSubscriptionController extends Controller
{
    public function store(Request $request, Business $business)
    {
        $request->validate([
            'plan_id' => 'required|exists:plans,id',
        ]);

        $plan = Plan::findOrFail($request->plan_id);

        // expire old subscription
        BusinessSubscription::where('business_id', $business->id)
            ->update(['status' => 'expired']);

        $subscription = BusinessSubscription::create([
            'business_id' => $business->id,
            'plan_id' => $plan->id,
            'status' => 'active',
            'starts_at' => now(),
            'ends_at' => $plan->isFree() ? null : now()->addMonth(),
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Subscription activated',
            'data' => $subscription
        ]);
    }

    public function show(Business $business)
    {
        $subscription = $business->subscription()->with('plan')->first();

        return response()->json([
            'data' => $subscription
        ]);
    }

    public function cancel(Business $business)
    {
        $subscription = $business->subscription;

        if (!$subscription) {
            return response()->json(['message' => 'No subscription found'], 404);
        }

        $subscription->update([
            'status' => 'cancelled',
            'ends_at' => now(),
        ]);

        return response()->json([
            'message' => 'Subscription cancelled'
        ]);
    }
}