<?php

namespace App\Http\Controllers\Organization\Frontend;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Plan;
use App\Models\User;
use App\Models\Business;
use App\Models\BusinessAdmin;
use App\Models\BusinessSubscription;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use App\Mail\BusinessRegisteredMail;
use App\Mail\InvoiceMail;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Str;

use Stripe\Stripe;
use Stripe\Checkout\Session as StripeSession;

class BusinessAuthController extends Controller
{
    public function showRegister(Plan $plan)
    {
        return view('frontend.business.add', compact('plan'));
    }

    /**
     * STEP 1: Create Stripe Checkout Session
     */
    public function register(Request $request)
    {
        $request->validate([
            'business_name' => 'required',
            'owner_name' => 'required',
            'email' => 'required|email|unique:users,email',
            'plan_id' => 'required|exists:plans,id',
        ]);

        $plan = Plan::findOrFail($request->plan_id);

        Stripe::setApiKey(config('services.stripe.secret'));

     $session = \Stripe\Checkout\Session::create([
                    'payment_method_types' => ['card'],
                    'mode' => 'subscription',

                    'customer_email' => $request->email,

                    'line_items' => [[
                        'price' => $plan->stripe_price_id,
                        'quantity' => 1,
                    ]],

                    'success_url' => route('stripe.success') . '?session_id={CHECKOUT_SESSION_ID}',
                    'cancel_url' => route('stripe.cancel'),

                    'metadata' => [
                        'business_name' => $request->business_name,
                        'owner_name' => $request->owner_name,
                        'email' => $request->email,
                        'plan_id' => $plan->id,
                    ],
                ]);

        return redirect($session->url);
    }

    /**
     * STEP 2: Handle Success & Create Account
     */
public function success(Request $request)
{
    if (!$request->has('session_id')) {
        return redirect()->route('login')->with('error', 'Invalid session.');
    }

    Stripe::setApiKey(config('services.stripe.secret'));

    try {
        $session = \Stripe\Checkout\Session::retrieve($request->session_id);
    } catch (\Exception $e) {
        return redirect()->route('login')->with('error', 'Invalid Stripe session.');
    }

    // ❌ Payment not completed
    if ($session->payment_status !== 'paid') {
        return redirect()->route('login')->with('error', 'Payment not completed.');
    }

    $data = $session->metadata;

    // ✅ Prevent duplicate account
    if (User::where('email', $data->email)->exists()) {
        return redirect()->route('login')->with('success', 'Account already created. Please login.');
    }

    DB::beginTransaction();

    try {

        // ✅ Get plan
        $plan = Plan::findOrFail($data->plan_id);

        // ✅ Create user
        $user = User::create([
            'name' => $data->owner_name,
            'email' => $data->email,
            'password' => bcrypt(Str::random(12)),
            'user_type' => 'business_admin',
        ]);

        // ✅ Create business
        $business = Business::create([
            'name' => $data->business_name,
            'slug' => $this->generateUniqueSlug($data->business_name),
            'email' => $data->email,
            'phone' => $data->phone ?? null,
            'address' => $data->address ?? null,
            'city' => $data->city ?? null,
            'state' => $data->state ?? null,
            'country' => $data->country ?? null,
            'postal_code' => $data->postal_code ?? null,
            'business_type' => $data->business_type ?? null,
            'description' => $data->description ?? null,
        ]);

        // ✅ Attach admin
        BusinessAdmin::create([
            'business_id' => $business->id,
            'user_id' => $user->id,
            'position' => 'owner',
            'permissions' => ['all'],
            'status' => 'active',
        ]);

        // ✅ Stripe data
        $customerId = $session->customer;
        $subscriptionId = $session->subscription;

        // ✅ Get subscription
        $stripeSubscription = \Stripe\Subscription::retrieve($subscriptionId);

        // ✅ Safe timestamp handling
        $start = !empty($stripeSubscription->current_period_start)
            ? \Carbon\Carbon::createFromTimestamp($stripeSubscription->current_period_start)
            : now();

        $end = !empty($stripeSubscription->current_period_end)
            ? \Carbon\Carbon::createFromTimestamp($stripeSubscription->current_period_end)
            : now()->addMonth();

        // ✅ Save subscription
        BusinessSubscription::create([
            'business_id' => $business->id,
            'plan_id' => $plan->id,
            'stripe_customer_id' => $customerId,
            'stripe_subscription_id' => $subscriptionId,
            'stripe_price_id' => $stripeSubscription->items->data[0]->price->id,
            'trial_ends_at' => now()->addDays(7),
            'status' => $stripeSubscription->status,
            'starts_at' => $start,
            'ends_at' => $end,
        ]);

        // ✅ Send password setup email
        Password::sendResetLink([
            'email' => $user->email
        ]);

        // =========================
        // 🔥 EMAILS SECTION
        // =========================

       \Log::info('Sending admin registration email');

        try {
            Mail::to('admin@yourapp.com')
                ->send(new BusinessRegisteredMail($business, $user));
        } catch (\Exception $e) {
            \Log::error('Admin registration email failed: '.$e->getMessage());
        }

        \Log::info('Sending admin invoice');

        try {
            Mail::to('admin@yourapp.com')
                ->send(new InvoiceMail($business, $plan, $plan->price));
        } catch (\Exception $e) {
            \Log::error('Admin invoice failed: '.$e->getMessage());
        }

        \Log::info('Sending customer invoice');

        try {
            Mail::to($user->email)
                ->send(new InvoiceMail($business, $plan, $plan->price));
        } catch (\Exception $e) {
            \Log::error('Customer invoice failed: '.$e->getMessage());
        }
        DB::commit();

        return redirect()->route('login')->with('success', 'Account created successfully! Please check your email.');

    } catch (\Exception $e) {
        DB::rollBack();
        return redirect()->route('login')->with('error', $e->getMessage());
    }
}

    /**
     * Cancel URL
     */
    public function cancel()
    {
        return redirect()->route('login')->with('error', 'Payment cancelled.');
    }

    /**
     * Slug Generator
     */
    private function generateUniqueSlug($name)
    {
        $baseSlug = Str::slug($name);
        $slug = $baseSlug;
        $counter = 2;

        while (Business::where('slug', $slug)->exists()) {
            $slug = $baseSlug . '-' . $counter;
            $counter++;
        }

        return $slug;
    }

    public function upgradePlan(Request $request)
{
    $request->validate([
        'plan_id' => 'required|exists:plans,id'
    ]);

    $user = auth()->user();

    $business = $user->businessAdmin->business;
    $subscription = $business->subscription;

    if (!$subscription) {
        return back()->with('error', 'No active subscription found.');
    }

    $plan = Plan::findOrFail($request->plan_id);

    \Stripe\Stripe::setApiKey(config('services.stripe.secret'));

    try {
        // Get current subscription from Stripe
        $stripeSubscription = \Stripe\Subscription::retrieve($subscription->stripe_subscription_id);

        // Update Stripe subscription
        $updatedSubscription = \Stripe\Subscription::update(
            $subscription->stripe_subscription_id,
            [
                'items' => [[
                    'id' => $stripeSubscription->items->data[0]->id,
                    'price' => $plan->stripe_price_id,
                ]],
                'proration_behavior' => 'create_prorations',
                'billing_cycle_anchor' => 'now', // 🔥 RESET CYCLE
            ]
        );

        // Get new billing period
        $start = \Carbon\Carbon::createFromTimestamp($updatedSubscription->current_period_start);
        $end   = \Carbon\Carbon::createFromTimestamp($updatedSubscription->current_period_end);

        // ✅ Update DB
        $subscription->update([
            'plan_id' => $plan->id,
            'stripe_price_id' => $plan->stripe_price_id,
            'status' => $updatedSubscription->status,
            'starts_at' => $start,
            'ends_at' => $end,
        ]);

        return back()->with('success', 'Plan updated successfully.');

    } catch (\Exception $e) {
        return back()->with('error', $e->getMessage());
    }
}
public function cancelSubscription($subscriptionId)
{
    \Stripe\Subscription::update($subscriptionId, [
        'cancel_at_period_end' => true,
    ]);
}
}