<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use App\Models\BusinessAdmin;

class LoginController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Login Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles authenticating users for the application and
    | redirecting them to your home screen. The controller uses a trait
    | to conveniently provide its functionality to your applications.
    |
    */

    use AuthenticatesUsers;

    /**
     * Where to redirect users after login.
     *
     * @var string
     */
     protected function redirectTo(): string
    {
        $userType = auth()->user()->user_type;
        $user = auth()->user();
        return match ($userType) {
            'org_admin' => '/org/dashboard',
            'business_admin' => $this->getBusinessAdminRedirect($user->id),
            'employee'           => '/employee/dashboard',
            'customer'           => '/customer/dashboard',
            default              => '/dashboard', // fallback
        };
    }

    private function getBusinessAdminRedirect($userId): string
    {
        $businessAdmin = BusinessAdmin::with('business')
                            ->where('user_id', $userId)
                            ->first();

        // safety fallback
        if (!$businessAdmin || !$businessAdmin->business) {
            return '/login';
        }

        return '/' . $businessAdmin->business->slug . '/admin/dashboard';
    }

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest')->except('logout');
        $this->middleware('auth')->only('logout');
    }
}
