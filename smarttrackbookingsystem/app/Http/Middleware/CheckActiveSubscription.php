<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckActiveSubscription
{
    public function handle(Request $request, Closure $next): Response
    {
        $user = auth()->user();

        // ❌ Not logged in
        if (!$user) {
            return redirect()->route('login');
        }

        // ❌ No business linked
        if (!$user->businessAdmin || !$user->businessAdmin->business) {
            return redirect()->route('plans')->with('error', 'No business found.');
        }

        $business = $user->businessAdmin->business;

        // ❌ No subscription
        $subscription = $business->subscription;

        // ❌ No subscription
        if (!$subscription) {
            return redirect()->route('plans')->with('error', 'Please subscribe.');
        }

        // ✅ Trial active
        if ($subscription->status === 'trial' && $subscription->trial_ends_at > now()) {
            return $next($request);
        }

        // ❌ Trial expired
        if ($subscription->status === 'trial' && $subscription->trial_ends_at <= now()) {
            return redirect()->route('plans')->with('error', 'Trial expired. Please subscribe.');
        }

        // ✅ Active subscription
        if ($subscription->status === 'active' && $subscription->ends_at > now()) {
            return $next($request);
        }

        // ❌ Everything else
        return redirect()->route('plans')->with('error', 'Subscription required.');
    }
}