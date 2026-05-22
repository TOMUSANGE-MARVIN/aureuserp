<?php

namespace App\Http\Controllers;

use App\Models\SubscriptionPlan;

class LandingController extends Controller
{
    public function index()
    {
        $plans = SubscriptionPlan::where('is_active', true)->orderBy('sort')->get();

        return view('landing', compact('plans'));
    }
}
