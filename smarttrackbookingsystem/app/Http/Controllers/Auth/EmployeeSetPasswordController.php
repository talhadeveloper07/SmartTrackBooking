<?php
namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;

class EmployeeSetPasswordController extends Controller
{
    public function show(User $user, string $token)
    {
        return view('auth.set-password', compact('user', 'token'));
    }

    public function update(Request $request, User $user, string $token)
    {
        $request->validate([
            'password' => 'required|min:6|confirmed',
        ]);

        // Validate token
        $status = Password::broker()->reset(
            [
                'email' => $user->email,
                'password' => $request->password,
                'password_confirmation' => $request->password_confirmation,
                'token' => $token,
            ],
            function ($user) use ($request) {
                $user->password = Hash::make($request->password);
                $user->save();
            }
        );

        if ($status !== Password::PASSWORD_RESET) {
            return back()->with('error', 'Link is invalid or expired.');
        }

        return redirect()->route('login')->with('success', 'Password set successfully. You can login now.');
    }
}