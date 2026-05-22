<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ProductsController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->input('search', '');
        $filter = $request->input('filter', 'all');

        $query = DB::table('products_products')->whereNull('deleted_at');

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('reference', 'like', "%{$search}%");
            });
        }

        if ($filter !== 'all') {
            $query->where('type', $filter);
        }

        $products = $query->orderByDesc('created_at')->paginate(20)->withQueryString();

        // Attach category names
        $categoryIds = $products->pluck('category_id')->filter()->unique()->values()->toArray();
        $categories  = [];
        if ($categoryIds) {
            $cats = DB::table('products_categories')->whereIn('id', $categoryIds)->get();
            foreach ($cats as $cat) {
                $categories[$cat->id] = $cat->name;
            }
        }

        $total    = DB::table('products_products')->whereNull('deleted_at')->count();
        $forSale  = DB::table('products_products')->whereNull('deleted_at')->where('sales_ok', 1)->count();
        $forPurch = DB::table('products_products')->whereNull('deleted_at')->where('purchase_ok', 1)->count();
        $services = DB::table('products_products')->whereNull('deleted_at')->where('type', 'service')->count();

        return view('app.products.index', compact('products', 'search', 'filter', 'total', 'forSale', 'forPurch', 'services', 'categories'));
    }

    public function show($id)
    {
        $product = DB::table('products_products')->whereNull('deleted_at')->where('id', $id)->first();
        abort_if(!$product, 404);

        $category = null;
        if ($product->category_id) {
            $category = DB::table('products_categories')->where('id', $product->category_id)->first();
        }

        return view('app.products.show', compact('product', 'category'));
    }

    public function create()
    {
        $categories = DB::table('products_categories')->orderBy('name')->get();
        $uoms = DB::table('unit_of_measures')->whereNull('deleted_at')->orderBy('name')->get();
        return view('app.products.create', compact('categories', 'uoms'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name'                 => 'required|string|max:255',
            'type'                 => 'required|in:consu,service,storable,combo',
            'reference'            => 'nullable|string|max:100',
            'barcode'              => 'nullable|string|max:100',
            'price'                => 'nullable|numeric|min:0',
            'cost'                 => 'nullable|numeric|min:0',
            'uom_id'               => 'required|integer|exists:unit_of_measures,id',
            'uom_po_id'            => 'nullable|integer|exists:unit_of_measures,id',
            'category_id'          => 'required|integer|exists:products_categories,id',
            'description'          => 'nullable|string',
            'description_sale'     => 'nullable|string',
            'description_purchase' => 'nullable|string',
        ]);

        $validated['sales_ok']        = $request->boolean('sales_ok');
        $validated['purchase_ok']     = $request->boolean('purchase_ok');
        $validated['enable_sales']    = $request->boolean('enable_sales');
        $validated['enable_purchase'] = $request->boolean('enable_purchase');
        $validated['uom_po_id']       = $validated['uom_po_id'] ?? $validated['uom_id'];
        $validated['created_at']      = now();
        $validated['updated_at']      = now();

        DB::table('products_products')->insert($validated);

        session()->flash('success', 'Product created successfully.');
        return redirect()->route('products.index');
    }

    public function edit($id)
    {
        $product    = DB::table('products_products')->whereNull('deleted_at')->where('id', $id)->first();
        abort_if(!$product, 404);
        $categories = DB::table('products_categories')->orderBy('name')->get();
        return view('app.products.edit', compact('product', 'categories'));
    }

    public function update(Request $request, $id)
    {
        $product = DB::table('products_products')->whereNull('deleted_at')->where('id', $id)->first();
        abort_if(!$product, 404);

        $validated = $request->validate([
            'name'                 => 'required|string|max:255',
            'type'                 => 'required|in:consu,service,storable,combo',
            'reference'            => 'nullable|string|max:100',
            'barcode'              => 'nullable|string|max:100',
            'price'                => 'nullable|numeric|min:0',
            'cost'                 => 'nullable|numeric|min:0',
            'description'          => 'nullable|string',
            'description_sale'     => 'nullable|string',
            'description_purchase' => 'nullable|string',
        ]);

        $validated['sales_ok']        = $request->boolean('sales_ok');
        $validated['purchase_ok']     = $request->boolean('purchase_ok');
        $validated['enable_sales']    = $request->boolean('enable_sales');
        $validated['enable_purchase'] = $request->boolean('enable_purchase');
        $validated['updated_at']      = now();

        DB::table('products_products')->where('id', $id)->update($validated);

        session()->flash('success', 'Product updated successfully.');
        return redirect()->route('products.show', $id);
    }

    public function destroy($id)
    {
        $product = DB::table('products_products')->whereNull('deleted_at')->where('id', $id)->first();
        abort_if(!$product, 404);

        DB::table('products_products')->where('id', $id)->update(['deleted_at' => now()]);

        session()->flash('success', 'Product deleted successfully.');
        return redirect()->route('products.index');
    }
}
