<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Auth;

class RedirectAuthenticatedUserFromHome
{
     public function handle($request, Closure $next)
    {
        if (!Auth::check()) return $next($request);

        $user = Auth::user();

        if ($user->user_type === 'business_admin') {

            // Get the first business this admin belongs to
            $bizAdmin = $user->businessAdminOf()->with('business')->first();

            if ($bizAdmin?->business) {
                return redirect()->route('business.dashboard', $bizAdmin->business->slug);
            }

            // fallback if no business mapped
            return redirect()->route('org.dashboard'); // or a safe page
        }

        // other user types...
        if ($user->user_type === 'org_admin') {
            return redirect()->route('org.dashboard');
        }

        if ($user->user_type === 'employee') {
            return redirect()->route('employee.dashboard');
        }

        if ($user->user_type === 'customer') {
            return redirect()->route('customer.dashboard');
        }

        return $next($request);
    }
}