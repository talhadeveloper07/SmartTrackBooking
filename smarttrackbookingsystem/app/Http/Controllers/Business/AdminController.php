<?php

namespace App\Http\Controllers\Business;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Business;
use App\Models\BusinessAdmin;

class AdminController extends Controller
{
    public function index(Business $business)
    {
         return view('business.admin.dashboard', compact('business'));
    }
}
