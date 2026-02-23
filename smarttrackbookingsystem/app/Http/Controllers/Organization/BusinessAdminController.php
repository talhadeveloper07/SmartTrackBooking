<?php

namespace App\Http\Controllers\Organization;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Business;
use App\Models\BusinessAdmin;
use App\Models\User;
use Illuminate\Support\Facades\Hash;


class BusinessAdminController extends Controller
{
     public function create(Business $business)
    {
        return view('organization.business_admins.create', compact('business'));
    }
     public function store(Request $request, Business $business)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255|unique:users,email',
            'password' => 'required|min:6',
            'position' => 'nullable|string|max:255',
            'status' => 'required|in:active,inactive',
            'permissions' => 'nullable|array',
        ]);

        // create user
        $user = User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => Hash::make($data['password']),
            'user_type' => 'business_admin', // or your numeric user_type
        ]);

        // create business_admin record
       $business->admins()->attach($user->id, [
            'position' => $data['position'] ?? null,
            'permissions' => json_encode($data['permissions'] ?? []),
            'status' => $data['status'],
        ]);

        return redirect()->route('org.dashboard')->with('success', 'Business Admin Created');
    }
}
