<?php

namespace App\Http\Controllers;

use App\Models\SubscriptionPlan;
use App\Services\TenantProvisioningService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class TenantRegistrationController extends Controller
{
    public function __construct(private readonly TenantProvisioningService $provisioning) {}

    public function show(): View
    {
        $plans = SubscriptionPlan::where('is_active', true)->orderBy('sort')->get();

        return view('saas.register', compact('plans'));
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'company_name' => 'required|string|max:255',
            'admin_name'   => 'required|string|max:255',
            'email'        => 'required|email|unique:users,email',
            'password'     => 'required|string|min:8|confirmed',
            'plan_id'      => 'nullable|exists:subscription_plans,id',
            'phone'        => 'nullable|string|max:30',
        ]);

        $company = $this->provisioning->provision($validated);

        return redirect()->route('saas.register.success', ['company' => $company->slug]);
    }

    public function success(string $company): View
    {
        return view('saas.success', compact('company'));
    }
}
