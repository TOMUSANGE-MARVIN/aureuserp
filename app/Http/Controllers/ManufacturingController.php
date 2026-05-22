<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ManufacturingController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->get('search', '');
        $filter = $request->get('filter', 'all');

        $query = DB::table('manufacturing_orders')
            ->leftJoin('products_products', 'products_products.id', '=', 'manufacturing_orders.product_id')
            ->select('manufacturing_orders.*', 'products_products.name as product_name');

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('manufacturing_orders.name', 'like', "%$search%")
                  ->orWhere('manufacturing_orders.reference', 'like', "%$search%");
            });
        }

        if ($filter !== 'all') {
            $query->where('manufacturing_orders.state', $filter);
        }

        $orders = $query->orderByDesc('manufacturing_orders.created_at')->paginate(20)->withQueryString();

        $stats = [
            'total'       => DB::table('manufacturing_orders')->count(),
            'draft'       => DB::table('manufacturing_orders')->where('state', 'draft')->count(),
            'in_progress' => DB::table('manufacturing_orders')->where('state', 'in_progress')->count(),
            'done'        => DB::table('manufacturing_orders')->where('state', 'done')->count(),
        ];

        return view('app.manufacturing.index', compact('orders', 'stats', 'search', 'filter'));
    }

    public function show($id)
    {
        $order = DB::table('manufacturing_orders')
            ->leftJoin('products_products', 'products_products.id', '=', 'manufacturing_orders.product_id')
            ->select('manufacturing_orders.*', 'products_products.name as product_name')
            ->where('manufacturing_orders.id', $id)
            ->first();

        abort_if(!$order, 404);

        $workOrders = DB::table('manufacturing_work_orders')
            ->leftJoin('manufacturing_work_centers', 'manufacturing_work_centers.id', '=', 'manufacturing_work_orders.work_center_id')
            ->select('manufacturing_work_orders.*', 'manufacturing_work_centers.name as work_center_name')
            ->where('manufacturing_work_orders.manufacturing_order_id', $id)
            ->get();

        return view('app.manufacturing.show', compact('order', 'workOrders'));
    }

    public function workOrders(Request $request)
    {
        $orders = DB::table('manufacturing_work_orders')
            ->leftJoin('manufacturing_orders', 'manufacturing_orders.id', '=', 'manufacturing_work_orders.manufacturing_order_id')
            ->leftJoin('manufacturing_work_centers', 'manufacturing_work_centers.id', '=', 'manufacturing_work_orders.work_center_id')
            ->leftJoin('products_products', 'products_products.id', '=', 'manufacturing_work_orders.product_id')
            ->select(
                'manufacturing_work_orders.*',
                'manufacturing_orders.name as mo_name',
                'manufacturing_work_centers.name as work_center_name',
                'products_products.name as product_name'
            )
            ->orderByDesc('manufacturing_work_orders.created_at')
            ->paginate(20);

        return view('app.manufacturing.work-orders', compact('orders'));
    }

    public function billsOfMaterials(Request $request)
    {
        $boms = DB::table('manufacturing_bills_of_materials')
            ->whereNull('manufacturing_bills_of_materials.deleted_at')
            ->leftJoin('products_products', 'products_products.id', '=', 'manufacturing_bills_of_materials.product_id')
            ->select('manufacturing_bills_of_materials.*', 'products_products.name as product_name')
            ->orderByDesc('manufacturing_bills_of_materials.created_at')
            ->paginate(20);

        return view('app.manufacturing.bom', compact('boms'));
    }
}
