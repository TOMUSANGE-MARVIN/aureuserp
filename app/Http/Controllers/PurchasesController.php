<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class PurchasesController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->input('search', '');
        $filter = $request->input('filter', 'all');

        $query = DB::table('purchases_orders')
            ->leftJoin('partners_partners', 'purchases_orders.partner_id', '=', 'partners_partners.id')
            ->select('purchases_orders.*', 'partners_partners.name as partner_name');

        if ($search) {
            $query->where('purchases_orders.name', 'like', "%{$search}%");
        }

        if ($filter !== 'all') {
            $query->where('purchases_orders.state', $filter);
        }

        $orders = $query->orderByDesc('purchases_orders.created_at')->paginate(20)->withQueryString();

        $total     = DB::table('purchases_orders')->count();
        $draft     = DB::table('purchases_orders')->where('state', 'draft')->count();
        $confirmed = DB::table('purchases_orders')->where('state', 'purchase')->count();
        $spend     = DB::table('purchases_orders')->whereIn('state', ['purchase', 'done'])->sum('total_amount');

        return view('app.purchases.index', compact('orders', 'search', 'filter', 'total', 'draft', 'confirmed', 'spend'));
    }

    public function show($id)
    {
        $order = DB::table('purchases_orders')
            ->leftJoin('partners_partners', 'purchases_orders.partner_id', '=', 'partners_partners.id')
            ->select('purchases_orders.*', 'partners_partners.name as partner_name')
            ->where('purchases_orders.id', $id)
            ->first();
        abort_if(!$order, 404);

        $lines = [];
        if (Schema::hasTable('purchases_order_lines')) {
            $lines = DB::table('purchases_order_lines')->where('order_id', $id)->get();
        }

        return view('app.purchases.show', compact('order', 'lines'));
    }

    public function create()
    {
        $partners = DB::table('partners_partners')->orderBy('name')->limit(100)->get();
        return view('app.purchases.create', compact('partners'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'partner_id' => 'nullable|integer',
            'priority'   => 'nullable|in:normal,urgent',
            'notes'      => 'nullable|string',
        ]);

        $name = 'PO-' . time();
        DB::table('purchases_orders')->insert([
            'name'           => $name,
            'state'          => 'draft',
            'partner_id'     => $validated['partner_id'] ?? null,
            'ordered_at'     => now(),
            'priority'       => $validated['priority'] ?? 'normal',
            'untaxed_amount' => 0,
            'tax_amount'     => 0,
            'total_amount'   => 0,
            'created_at'     => now(),
            'updated_at'     => now(),
        ]);

        $inserted = DB::table('purchases_orders')->where('name', $name)->orderByDesc('id')->first();
        session()->flash('success', "Purchase Order {$name} created successfully.");
        return redirect()->route('purchases.show', $inserted->id);
    }

    public function edit($id)
    {
        $order    = DB::table('purchases_orders')->where('id', $id)->first();
        abort_if(!$order, 404);
        $partners = DB::table('partners_partners')->orderBy('name')->limit(100)->get();
        return view('app.purchases.edit', compact('order', 'partners'));
    }

    public function update(Request $request, $id)
    {
        $order = DB::table('purchases_orders')->where('id', $id)->first();
        abort_if(!$order, 404);

        $validated = $request->validate([
            'partner_id' => 'nullable|integer',
            'priority'   => 'nullable|in:normal,urgent',
            'notes'      => 'nullable|string',
            'state'      => 'nullable|in:draft,purchase,cancel',
        ]);

        DB::table('purchases_orders')->where('id', $id)->update([
            'partner_id' => $validated['partner_id'] ?? null,
            'priority'   => $validated['priority'] ?? 'normal',
            'state'      => $validated['state'] ?? $order->state,
            'updated_at' => now(),
        ]);

        session()->flash('success', 'Purchase Order updated successfully.');
        return redirect()->route('purchases.show', $id);
    }
}
