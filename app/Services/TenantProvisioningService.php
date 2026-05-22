<?php

namespace App\Services;

use App\Models\Subscription;
use App\Models\SubscriptionPlan;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Webkul\Security\Models\User;
use Webkul\Support\Models\Company;

class TenantProvisioningService
{
    public function provision(array $data): Company
    {
        return DB::transaction(function () use ($data): Company {
            // Create the company/tenant
            $company = Company::create([
                'name'      => $data['company_name'],
                'slug'      => Str::slug($data['company_name']),
                'email'     => $data['email'],
                'phone'     => $data['phone'] ?? null,
                'website'   => $data['website'] ?? null,
                'subdomain' => Str::slug($data['company_name']),
                'is_active' => true,
                'is_tenant' => true,
            ]);

            // Create admin user for the tenant
            $user = User::create([
                'name'              => $data['admin_name'],
                'email'             => $data['email'],
                'password'          => Hash::make($data['password']),
                'is_active'         => true,
                'default_company_id' => $company->id,
            ]);

            // Attach user to company
            $user->allowedCompanies()->attach($company->id);

            // Assign trial subscription if a plan is provided
            if (! empty($data['plan_id'])) {
                $plan = SubscriptionPlan::find($data['plan_id']);

                if ($plan) {
                    Subscription::create([
                        'company_id'    => $company->id,
                        'plan_id'       => $plan->id,
                        'status'        => 'trial',
                        'billing_cycle' => 'monthly',
                        'amount'        => $plan->price_monthly,
                        'currency'      => $plan->currency,
                        'trial_ends_at' => now()->addDays($plan->trial_days),
                        'starts_at'     => now(),
                    ]);

                    $company->update(['trial_ends_at' => now()->addDays($plan->trial_days)]);
                }
            }

            return $company;
        });
    }
}
