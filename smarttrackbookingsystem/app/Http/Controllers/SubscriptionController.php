<?php

namespace App\Http\Controllers;

use App\Models\Plan;
use Illuminate\Http\Request;

class SubscriptionController extends Controller
{

public function index()
{
    $user = auth()->user();

    $business = $user->businessAdmin->business ?? null;
    $subscription = $business ? $business->subscription : null;

    $plans = Plan::all();

    return view('business.admin.subscription.index', compact('subscription', 'plans'));
}

public function upgradePlan(Request $request, $businessSlug)
{
    $request->validate([
        'plan_id' => 'required|exists:plans,id',
    ]);

    $user = auth()->user();

    // ✅ Get business using slug (IMPORTANT for your routing)
    $business = $user->businessAdmin->business;

    if (!$business || $business->slug !== $businessSlug) {
        return back()->with('error', 'Invalid business.');
    }

    $subscription = $business->subscription;

    if (!$subscription) {
        return back()->with('error', 'No active subscription found.');
    }

    $plan = Plan::findOrFail($request->plan_id);

    // ❌ Prevent same plan
    if ($subscription->plan_id == $plan->id) {
        return back()->with('error', 'You are already on this plan.');
    }

    \Stripe\Stripe::setApiKey(config('services.stripe.secret'));

    try {
        // Get current Stripe subscription
        $stripeSubscription = \Stripe\Subscription::retrieve(
            $subscription->stripe_subscription_id
        );

        $updatedSubscription = \Stripe\Subscription::update(
    $subscription->stripe_subscription_id,
    [
        'items' => [[
            'id' => $stripeSubscription->items->data[0]->id,
            'price' => $plan->stripe_price_id,
        ]],

        'proration_behavior' => 'create_prorations',

        // 🔥 THIS IS THE MAIN FIX
        'billing_cycle_anchor' => 'now',

        // 🔥 OPTIONAL BUT IMPORTANT
        'cancel_at_period_end' => false,
    ]
);

      if (isset($updatedSubscription->current_period_start)) {
    $start = \Carbon\Carbon::createFromTimestamp($updatedSubscription->current_period_start);
    $end   = \Carbon\Carbon::createFromTimestamp($updatedSubscription->current_period_end);
} else {
    $start = now();
    $end = now()->addMonth();
}

        // ✅ Update DB
        $subscription->update([
            'plan_id' => $plan->id,
            'stripe_price_id' => $plan->stripe_price_id,
            'status' => $updatedSubscription->status,
            'starts_at' => $start,
            'ends_at' => $end,
        ]);

        return back()->with('success', 'Plan upgraded successfully 🚀');

    } catch (\Exception $e) {
        return back()->with('error', $e->getMessage());
    }
}
public function billingPortal($businessSlug)
{
    $user = auth()->user();
    $business = $user->businessAdmin->business;

    if (!$business || $business->slug !== $businessSlug) {
        return back()->with('error', 'Invalid business.');
    }

    $subscription = $business->subscription;

    if (!$subscription || !$subscription->stripe_customer_id) {
        return back()->with('error', 'No billing account found.');
    }

    \Stripe\Stripe::setApiKey(config('services.stripe.secret'));

    $session = \Stripe\BillingPortal\Session::create([
        'customer' => $subscription->stripe_customer_id,
        'return_url' => route('business.subscription.index', $business->slug),
    ]);

    return redirect($session->url);
}
}
