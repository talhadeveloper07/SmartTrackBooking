<?php

namespace App\Http\Controllers\Business\Frontend;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Plan;

class PriceController extends Controller
{
    public function index()
    {
        $plans = Plan::where('active', true)->get();

        return view('frontend.plans', compact('plans'));
    }
}
