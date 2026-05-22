<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class SuperAdminController extends Controller
{
    /* ─────────────────────────────────────────── dashboard ─── */
    public function dashboard()
    {
        $stats = [];
        $stats['total_orgs']     = DB::table('companies')->where('is_tenant', 1)->count();
        $stats['active_orgs']    = DB::table('companies')->where('is_tenant', 1)->where('is_active', 1)->whereNull('suspended_at')->count();
        $stats['trial_orgs']     = DB::table('companies')->where('is_tenant', 1)->whereNotNull('trial_ends_at')->where('trial_ends_at', '>', now())->count();
        $stats['suspended_orgs'] = DB::table('companies')->where('is_tenant', 1)->whereNotNull('suspended_at')->count();
        $stats['total_users']    = DB::table('users')->count();
        $stats['total_subs']     = DB::table('subscriptions')->count();
        $stats['active_subs']    = DB::table('subscriptions')->where('status', 'active')->count();
        $stats['trial_subs']     = DB::table('subscriptions')->where('status', 'trial')->count();
        $stats['cancelled_subs'] = DB::table('subscriptions')->where('status', 'cancelled')->count();
        $stats['mrr']            = DB::table('subscriptions')
            ->where('status', 'active')->where('billing_cycle', 'monthly')
            ->sum('amount');
        $stats['arr']            = $stats['mrr'] * 12;

        $recentOrgs = DB::table('companies')->where('is_tenant', 1)
            ->orderByDesc('created_at')->limit(8)->get();

        $subscriptions = DB::table('subscriptions')
            ->whereIn('company_id', $recentOrgs->pluck('id'))->get();

        $plans = DB::table('subscription_plans')->get();

        $planDistribution = DB::table('subscriptions')
            ->join('subscription_plans', 'subscriptions.plan_id', '=', 'subscription_plans.id')
            ->select('subscription_plans.name as plan_name', DB::raw('count(*) as count'))
            ->groupBy('subscription_plans.id', 'subscription_plans.name')
            ->get();

        $trialsExpiring = DB::table('companies')
            ->where('is_tenant', 1)
            ->whereNotNull('trial_ends_at')
            ->whereBetween('trial_ends_at', [now(), now()->addDays(7)])
            ->get();

        // Chart: new orgs per month (last 6 months)
        $orgGrowthRaw = DB::select(
            "SELECT DATE_FORMAT(created_at,'%Y-%m') as month, COUNT(*) as cnt
             FROM companies WHERE is_tenant=1
             GROUP BY month ORDER BY month ASC"
        );
        $orgGrowth = $this->fillMonths($orgGrowthRaw, 6, 'cnt');

        // Chart: MRR snapshots (approximate from created_at of subscriptions)
        $mrrRaw = DB::select(
            "SELECT DATE_FORMAT(created_at,'%Y-%m') as month, SUM(amount) as total
             FROM subscriptions WHERE status='active' AND billing_cycle='monthly'
             GROUP BY month ORDER BY month ASC"
        );
        $mrrGrowth = $this->fillMonths($mrrRaw, 6, 'total');

        return view('superadmin.index', compact(
            'stats', 'recentOrgs', 'subscriptions', 'plans',
            'planDistribution', 'trialsExpiring', 'orgGrowth', 'mrrGrowth'
        ));
    }

    /* ─────────────────────────────────────── organizations ─── */
    public function organizations(Request $request)
    {
        $search = $request->get('search', '');
        $filter = $request->get('status', '');

        $query = DB::table('companies')->where('is_tenant', 1);
        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%$search%")
                  ->orWhere('subdomain', 'like', "%$search%")
                  ->orWhere('email', 'like', "%$search%");
            });
        }
        if ($filter === 'active')    $query->where('is_active', 1)->whereNull('suspended_at');
        if ($filter === 'inactive')  $query->where('is_active', 0);
        if ($filter === 'suspended') $query->whereNotNull('suspended_at');
        if ($filter === 'trial')     $query->whereNotNull('trial_ends_at')->where('trial_ends_at', '>', now());

        $orgs = $query->orderByDesc('created_at')->paginate(20)->withQueryString();

        $total         = DB::table('companies')->where('is_tenant', 1)->count();
        $activeCount   = DB::table('companies')->where('is_tenant', 1)->where('is_active', 1)->whereNull('suspended_at')->count();
        $trialCount    = DB::table('companies')->where('is_tenant', 1)->whereNotNull('trial_ends_at')->where('trial_ends_at', '>', now())->count();
        $suspendedCount = DB::table('companies')->where('is_tenant', 1)->whereNotNull('suspended_at')->count();
        $subscriptions = DB::table('subscriptions')->get();
        $plans         = DB::table('subscription_plans')->get();

        return view('superadmin.organizations.index', compact(
            'orgs', 'search', 'filter', 'total', 'activeCount', 'trialCount', 'suspendedCount', 'subscriptions', 'plans'
        ));
    }

    public function showOrganization($id)
    {
        $org  = DB::table('companies')->find($id);
        abort_if(!$org, 404);
        $sub  = DB::table('subscriptions')->where('company_id', $id)->orderByDesc('created_at')->first();
        $plan = $sub ? DB::table('subscription_plans')->find($sub->plan_id) : null;

        $status = $org->suspended_at ? 'suspended'
                : (!$org->is_active  ? 'inactive'
                : ($sub?->status === 'trial' ? 'trial' : 'active'));

        return view('superadmin.organizations.show', compact('org', 'sub', 'plan', 'status'));
    }

    public function suspendOrganization(Request $request, $id)
    {
        $reason = $request->input('reason', '');
        DB::table('companies')->where('id', $id)->update([
            'suspended_at'      => now(),
            'suspension_reason' => $reason,
            'updated_at'        => now(),
        ]);
        return redirect("/superadmin/organizations/$id")->with('success', 'Organization suspended.');
    }

    public function unsuspendOrganization($id)
    {
        DB::table('companies')->where('id', $id)->update([
            'suspended_at'      => null,
            'suspension_reason' => null,
            'updated_at'        => now(),
        ]);
        return redirect("/superadmin/organizations/$id")->with('success', 'Organization restored.');
    }

    public function activateOrganization($id)
    {
        DB::table('companies')->where('id', $id)->update([
            'is_active'  => 1,
            'updated_at' => now(),
        ]);
        return redirect("/superadmin/organizations/$id")->with('success', 'Organization activated.');
    }

    /* ─────────────────────────────────────── subscriptions ─── */
    public function subscriptions(Request $request)
    {
        $filter = $request->get('status', '');
        $query  = DB::table('subscriptions');
        if ($filter) $query->where('status', $filter);

        $subscriptions = $query->orderByDesc('created_at')->paginate(25)->withQueryString();
        $orgs   = DB::table('companies')->get();
        $plans  = DB::table('subscription_plans')->get();
        $mrr    = DB::table('subscriptions')->where('status', 'active')->where('billing_cycle', 'monthly')->sum('amount');
        $total         = DB::table('subscriptions')->count();
        $activeCount   = DB::table('subscriptions')->where('status', 'active')->count();
        $trialCount    = DB::table('subscriptions')->where('status', 'trial')->count();
        $cancelledCount= DB::table('subscriptions')->where('status', 'cancelled')->count();

        return view('superadmin.subscriptions.index', compact(
            'subscriptions', 'orgs', 'plans', 'filter',
            'mrr', 'total', 'activeCount', 'trialCount', 'cancelledCount'
        ));
    }

    /* ─────────────────────────────────────────────── plans ─── */
    public function plans()
    {
        $plans = DB::table('subscription_plans')->orderBy('sort')->get();
        $planCounts = DB::table('subscriptions')
            ->select('plan_id', DB::raw('count(*) as cnt'))
            ->groupBy('plan_id')
            ->pluck('cnt', 'plan_id');

        return view('superadmin.plans.index', compact('plans', 'planCounts'));
    }

    /* ─────────────────────────────────────────────── users ─── */
    public function users(Request $request)
    {
        $search = $request->get('search', '');
        $query  = DB::table('users');
        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%$search%")
                  ->orWhere('email', 'like', "%$search%");
            });
        }
        $users     = $query->orderByDesc('created_at')->paginate(30)->withQueryString();
        $total     = DB::table('users')->count();
        $companies = DB::table('companies')->get();

        return view('superadmin.users.index', compact('users', 'total', 'search', 'companies'));
    }

    /* ───────────────────────────────────────────── analytics ── */
    public function analytics()
    {
        // Monthly new orgs (last 12 months)
        $orgGrowthRaw = DB::select(
            "SELECT DATE_FORMAT(created_at,'%Y-%m') as month, COUNT(*) as cnt
             FROM companies WHERE is_tenant=1
             GROUP BY month ORDER BY month ASC"
        );
        $orgGrowth = $this->fillMonths($orgGrowthRaw, 12, 'cnt');

        // MRR trend
        $mrrRaw = DB::select(
            "SELECT DATE_FORMAT(created_at,'%Y-%m') as month, SUM(amount) as total
             FROM subscriptions WHERE status='active' AND billing_cycle='monthly'
             GROUP BY month ORDER BY month ASC"
        );
        $mrrGrowth = $this->fillMonths($mrrRaw, 12, 'total');

        // Plan distribution (pie)
        $planDistribution = DB::table('subscriptions')
            ->join('subscription_plans', 'subscriptions.plan_id', '=', 'subscription_plans.id')
            ->select('subscription_plans.name as plan_name', DB::raw('count(*) as count'))
            ->groupBy('subscription_plans.id', 'subscription_plans.name')
            ->get();

        // Status distribution
        $statusDist = DB::table('subscriptions')
            ->select('status', DB::raw('count(*) as count'))
            ->groupBy('status')->get();

        $totalOrgs  = DB::table('companies')->where('is_tenant', 1)->count();
        $totalUsers = DB::table('users')->count();
        $mrr        = DB::table('subscriptions')->where('status', 'active')->where('billing_cycle', 'monthly')->sum('amount');

        return view('superadmin.analytics.index', compact(
            'orgGrowth', 'mrrGrowth', 'planDistribution', 'statusDist',
            'totalOrgs', 'totalUsers', 'mrr'
        ));
    }

    /* ─────────────────────────────────────────── helpers ──── */
    private function fillMonths(array $rows, int $count, string $field): array
    {
        $map = [];
        foreach ($rows as $r) $map[$r->month] = (float)$r->$field;

        $labels = $values = [];
        for ($i = $count - 1; $i >= 0; $i--) {
            $m = Carbon::now()->subMonths($i)->format('Y-m');
            $labels[] = Carbon::now()->subMonths($i)->format('M Y');
            $values[] = $map[$m] ?? 0;
        }
        return ['labels' => $labels, 'values' => $values];
    }
}
