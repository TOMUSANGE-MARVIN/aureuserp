<?php

namespace App\Filament\Superadmin\Widgets;

use App\Models\Subscription;
use App\Models\SubscriptionPlan;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Webkul\Support\Models\Company;

class StatsOverview extends BaseWidget
{
    protected function getStats(): array
    {
        $totalTenants = Company::where('is_tenant', true)->count();
        $activeTenants = Company::where('is_tenant', true)->whereNull('suspended_at')->count();
        $trialTenants = Subscription::where('status', 'trial')->count();
        $activeSubscriptions = Subscription::where('status', 'active')->count();
        $mrr = Subscription::where('status', 'active')
            ->where('billing_cycle', 'monthly')
            ->sum('amount');
        $newThisMonth = Company::where('is_tenant', true)
            ->whereMonth('created_at', now()->month)
            ->whereYear('created_at', now()->year)
            ->count();

        return [
            Stat::make('Total Organisations', $totalTenants)
                ->description('All registered tenants')
                ->descriptionIcon('heroicon-m-building-office-2')
                ->color('primary'),

            Stat::make('Active Tenants', $activeTenants)
                ->description("{$trialTenants} on trial")
                ->descriptionIcon('heroicon-m-check-circle')
                ->color('success'),

            Stat::make('Active Subscriptions', $activeSubscriptions)
                ->description('Paying customers')
                ->descriptionIcon('heroicon-m-credit-card')
                ->color('info'),

            Stat::make('MRR', number_format($mrr, 2) . ' USD')
                ->description('Monthly recurring revenue')
                ->descriptionIcon('heroicon-m-currency-dollar')
                ->color('warning'),

            Stat::make('New This Month', $newThisMonth)
                ->description('New signups')
                ->descriptionIcon('heroicon-m-user-plus')
                ->color('success'),

            Stat::make('Plans', SubscriptionPlan::where('is_active', true)->count())
                ->description('Active pricing plans')
                ->descriptionIcon('heroicon-m-squares-2x2')
                ->color('primary'),
        ];
    }
}
