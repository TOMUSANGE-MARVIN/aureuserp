<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class DashboardController extends Controller
{
    public function index()
    {
        $months = collect(range(1, 12))->map(fn($m) => date('M', mktime(0, 0, 0, $m, 1)));
        $currentYear = date('Y');
        $hasTable = fn(string $table) => Schema::hasTable($table);

        // ── Revenue (monthly invoices) ──
        $revenueRaw = $hasTable('accounts_account_moves')
            ? DB::table('accounts_account_moves')
                ->selectRaw('MONTH(created_at) as m, COALESCE(SUM(amount_total),0) as total')
                ->whereYear('created_at', $currentYear)
                ->whereIn('move_type', ['out_invoice', 'out_receipt'])
                ->groupBy('m')
                ->pluck('total', 'm')
            : collect();

        $revenueData = $months->mapWithKeys(fn($_, $i) => [
            $i + 1 => (float)($revenueRaw->get($i + 1, 0))
        ]);

        // ── Sales Orders (monthly) ──
        $salesRaw = $hasTable('sales_orders')
            ? DB::table('sales_orders')
                ->selectRaw('MONTH(created_at) as m, COUNT(*) as cnt')
                ->whereYear('created_at', $currentYear)
                ->groupBy('m')
                ->pluck('cnt', 'm')
            : collect();

        $salesData = $months->mapWithKeys(fn($_, $i) => [
            $i + 1 => (int)($salesRaw->get($i + 1, 0))
        ]);

        // ── Order Status distribution ──
        $orderStatuses = $hasTable('sales_orders')
            ? DB::table('sales_orders')
                ->selectRaw('state, COUNT(*) as cnt')
                ->groupBy('state')
                ->get()
                ->pluck('cnt', 'state')
            : collect();

        // ── Partners by month (new customers) ──
        $partnersRaw = $hasTable('partners_partners')
            ? DB::table('partners_partners')
                ->selectRaw('MONTH(created_at) as m, COUNT(*) as cnt')
                ->whereYear('created_at', $currentYear)
                ->groupBy('m')
                ->pluck('cnt', 'm')
            : collect();

        $partnersData = $months->mapWithKeys(fn($_, $i) => [
            $i + 1 => (int)($partnersRaw->get($i + 1, 0))
        ]);

        // ── Top-level stats ──
        $totalRevenue   = $hasTable('accounts_account_moves')
            ? (DB::table('accounts_account_moves')->whereIn('move_type', ['out_invoice', 'out_receipt'])->sum('amount_total') ?? 0)
            : 0;

        $totalOrders    = $hasTable('sales_orders') ? DB::table('sales_orders')->count() : 0;
        $totalCustomers = $hasTable('partners_partners') ? DB::table('partners_partners')->count() : 0;
        $totalEmployees = $hasTable('employees_employees') ? DB::table('employees_employees')->count() : 0;
        $totalProducts  = $hasTable('products_products') ? DB::table('products_products')->count() : 0;
        $totalProjects  = $hasTable('projects_projects') ? DB::table('projects_projects')->count() : 0;
        $openTasks      = $hasTable('projects_tasks')
            ? DB::table('projects_tasks')->whereNotIn('state', ['1_done', 'cancelled'])->count()
            : 0;

        // ── Recent orders ──
        $recentOrders = $hasTable('sales_orders')
            ? DB::table('sales_orders as o')
                ->leftJoin('partners_partners as p', 'o.partner_id', '=', 'p.id')
                ->select('o.name', 'o.state', 'o.amount_total', 'o.created_at', 'p.name as partner_name')
                ->orderByDesc('o.created_at')
                ->limit(6)
                ->get()
            : collect();

        // ── Demo fallback (when no real data yet) ──
        $hasRealData = ($totalOrders + $totalCustomers) > 0;

        if (!$hasRealData) {
            $revenueData   = collect([1=>12400,2=>18200,3=>15600,4=>22100,5=>19800,6=>28300,7=>24900,8=>31200,9=>27400,10=>33800,11=>29600,12=>38500]);
            $salesData     = collect([1=>34,2=>47,3=>42,4=>58,5=>51,6=>73,7=>65,8=>81,9=>69,10=>88,11=>76,12=>95]);
            $partnersData  = collect([1=>8,2=>12,3=>9,4=>15,5=>11,6=>18,7=>14,8=>20,9=>16,10=>22,11=>19,12=>26]);
            $orderStatuses = collect(['draft'=>28,'sent'=>45,'sale'=>112,'done'=>87,'cancel'=>9]);
            $totalRevenue  = 301800;
            $totalOrders   = 281;
            $totalCustomers= 14;
            $totalEmployees= 10;
            $totalProducts = 0;
            $totalProjects = 0;
            $openTasks     = 0;
        }

        // ── Tasks by stage ──
        $taskStages = ($hasTable('projects_tasks') && $hasTable('projects_task_stages'))
            ? DB::table('projects_tasks as t')
                ->join('projects_task_stages as s', 't.stage_id', '=', 's.id')
                ->selectRaw('s.name, COUNT(t.id) as cnt')
                ->groupBy('s.name')
                ->get()
                ->pluck('cnt', 'name')
            : collect();

        if ($taskStages->isEmpty()) {
            $taskStages = collect(['Backlog' => 24, 'In Progress' => 18, 'In Review' => 9, 'Done' => 41]);
        }

        // ── Inventory by category ──
        $inventoryCategories = ($hasTable('products_categories') && $hasTable('products_products'))
            ? DB::table('products_categories as c')
                ->join('products_products as p', 'p.category_id', '=', 'c.id')
                ->selectRaw('c.name, COUNT(p.id) as cnt')
                ->groupBy('c.name')
                ->limit(5)
                ->get()
                ->pluck('cnt', 'name')
            : collect();

        if ($inventoryCategories->isEmpty()) {
            $inventoryCategories = collect(['Electronics' => 45, 'Office Supplies' => 32, 'Software' => 28, 'Hardware' => 19, 'Services' => 15]);
        }

        return view('app.dashboard', compact(
            'months', 'revenueData', 'salesData', 'partnersData', 'orderStatuses',
            'totalRevenue', 'totalOrders', 'totalCustomers', 'totalEmployees',
            'totalProducts', 'totalProjects', 'openTasks', 'recentOrders',
            'taskStages', 'inventoryCategories', 'hasRealData'
        ));
    }
}
