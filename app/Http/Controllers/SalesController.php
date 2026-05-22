<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class SalesController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->input('search', '');
        $filter = $request->input('filter', 'all');
        $hasSalesTable = Schema::hasTable('sales_orders');
        $hasPartnersTable = Schema::hasTable('partners_partners');

        if (! $hasSalesTable) {
            session()->flash('error', 'Sales module tables are not initialized yet.');
            $orders = new LengthAwarePaginator(
                collect(),
                0,
                20,
                null,
                ['path' => $request->url(), 'query' => $request->query()]
            );

            $total = 0;
            $confirmed = 0;
            $draft = 0;
            $revenue = 0;

            return view('app.sales.index', compact('orders', 'search', 'filter', 'total', 'confirmed', 'draft', 'revenue'));
        }

        $query = DB::table('sales_orders')
            ->whereNull('sales_orders.deleted_at')
            ->select('sales_orders.*');

        if ($hasPartnersTable) {
            $query->leftJoin('partners_partners', 'sales_orders.partner_id', '=', 'partners_partners.id')
                ->addSelect('partners_partners.name as partner_name');
        }

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('sales_orders.name', 'like', "%{$search}%")
                  ->orWhere('sales_orders.client_order_ref', 'like', "%{$search}%");
            });
        }

        if ($filter !== 'all') {
            $query->where('sales_orders.state', $filter);
        }

        $orders = $query->orderByDesc('sales_orders.created_at')->paginate(20)->withQueryString();

        $total     = DB::table('sales_orders')->whereNull('deleted_at')->count();
        $confirmed = DB::table('sales_orders')->whereNull('deleted_at')->where('state', 'sale')->count();
        $draft     = DB::table('sales_orders')->whereNull('deleted_at')->where('state', 'draft')->count();
        $revenue   = DB::table('sales_orders')->whereNull('deleted_at')->whereIn('state', ['sale', 'done'])->sum('amount_total');

        return view('app.sales.index', compact('orders', 'search', 'filter', 'total', 'confirmed', 'draft', 'revenue'));
    }

    public function show($id)
    {
        if (! Schema::hasTable('sales_orders')) {
            return redirect()->route('sales.index')->with('error', 'Sales module tables are not initialized yet.');
        }

        $order = DB::table('sales_orders')
            ->whereNull('sales_orders.deleted_at')
            ->where('sales_orders.id', $id)
            ->select('sales_orders.*');

        if (Schema::hasTable('partners_partners')) {
            $order->leftJoin('partners_partners', 'sales_orders.partner_id', '=', 'partners_partners.id')
                ->addSelect('partners_partners.name as partner_name');
        }

        $order = $order->first();

        abort_if(!$order, 404);

        $lines = [];
        if (Schema::hasTable('sales_order_lines')) {
            $lines = DB::table('sales_order_lines')->where('order_id', $id)->get();
        }

        return view('app.sales.show', compact('order', 'lines'));
    }

    public function create()
    {
        if (! Schema::hasTable('sales_orders')) {
            return redirect()->route('sales.index')->with('error', 'Sales module tables are not initialized yet.');
        }

        $partners = Schema::hasTable('partners_partners')
            ? DB::table('partners_partners')->orderBy('name')->limit(100)->get()
            : collect();

        return view('app.sales.create', compact('partners'));
    }

    public function store(Request $request)
    {
        if (! Schema::hasTable('sales_orders')) {
            return redirect()->route('sales.index')->with('error', 'Sales module tables are not initialized yet.');
        }

        $validated = $request->validate([
            'partner_id'       => 'nullable|integer',
            'note'             => 'nullable|string',
            'validity_date'    => 'nullable|date',
            'client_order_ref' => 'nullable|string|max:255',
        ]);

        $name = 'SO-' . time();
        DB::table('sales_orders')->insert([
            'name'             => $name,
            'state'            => 'draft',
            'partner_id'       => $validated['partner_id'] ?? null,
            'date_order'       => now(),
            'note'             => $validated['note'] ?? null,
            'validity_date'    => $validated['validity_date'] ?? null,
            'client_order_ref' => $validated['client_order_ref'] ?? null,
            'amount_untaxed'   => 0,
            'amount_tax'       => 0,
            'amount_total'     => 0,
            'created_at'       => now(),
            'updated_at'       => now(),
        ]);

        $inserted = DB::table('sales_orders')->where('name', $name)->orderByDesc('id')->first();
        session()->flash('success', "Sales Order {$name} created successfully.");
        return redirect()->route('sales.show', $inserted->id);
    }

    public function edit($id)
    {
        if (! Schema::hasTable('sales_orders')) {
            return redirect()->route('sales.index')->with('error', 'Sales module tables are not initialized yet.');
        }

        $order    = DB::table('sales_orders')->whereNull('deleted_at')->where('id', $id)->first();
        abort_if(!$order, 404);
        $partners = Schema::hasTable('partners_partners')
            ? DB::table('partners_partners')->orderBy('name')->limit(100)->get()
            : collect();

        return view('app.sales.edit', compact('order', 'partners'));
    }

    public function update(Request $request, $id)
    {
        if (! Schema::hasTable('sales_orders')) {
            return redirect()->route('sales.index')->with('error', 'Sales module tables are not initialized yet.');
        }

        $order = DB::table('sales_orders')->whereNull('deleted_at')->where('id', $id)->first();
        abort_if(!$order, 404);

        $validated = $request->validate([
            'partner_id'       => 'nullable|integer',
            'note'             => 'nullable|string',
            'validity_date'    => 'nullable|date',
            'client_order_ref' => 'nullable|string|max:255',
            'state'            => 'nullable|in:draft,sale,cancel',
        ]);

        DB::table('sales_orders')->where('id', $id)->update([
            'partner_id'       => $validated['partner_id'] ?? null,
            'note'             => $validated['note'] ?? null,
            'validity_date'    => $validated['validity_date'] ?? null,
            'client_order_ref' => $validated['client_order_ref'] ?? null,
            'state'            => $validated['state'] ?? $order->state,
            'updated_at'       => now(),
        ]);

        session()->flash('success', 'Sales Order updated successfully.');
        return redirect()->route('sales.show', $id);
    }
}
