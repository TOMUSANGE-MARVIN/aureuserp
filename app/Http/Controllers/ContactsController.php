<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ContactsController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->input('search', '');
        $filter = $request->input('filter', 'all');

        $query = DB::table('partners_partners')->whereNull('deleted_at');

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%");
            });
        }

        if ($filter === 'customer') {
            $query->where('customer_rank', '>', 0);
        } elseif ($filter === 'supplier') {
            $query->where('supplier_rank', '>', 0);
        } elseif ($filter === 'company') {
            $query->where('account_type', 'company');
        }

        $contacts = $query->orderByDesc('created_at')->paginate(20)->withQueryString();

        $total      = DB::table('partners_partners')->whereNull('deleted_at')->count();
        $customers  = DB::table('partners_partners')->whereNull('deleted_at')->where('customer_rank', '>', 0)->count();
        $suppliers  = DB::table('partners_partners')->whereNull('deleted_at')->where('supplier_rank', '>', 0)->count();
        $companies  = DB::table('partners_partners')->whereNull('deleted_at')->where('account_type', 'company')->count();

        return view('app.contacts.index', compact('contacts', 'search', 'filter', 'total', 'customers', 'suppliers', 'companies'));
    }

    public function show($id)
    {
        $contact = DB::table('partners_partners')->whereNull('deleted_at')->where('id', $id)->first();
        abort_if(!$contact, 404);
        return view('app.contacts.show', compact('contact'));
    }

    public function create()
    {
        return view('app.contacts.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name'         => 'required|string|max:255',
            'account_type' => 'required|in:individual,company',
            'email'        => 'nullable|email|max:255',
            'phone'        => 'nullable|string|max:50',
            'mobile'       => 'nullable|string|max:50',
            'job_title'    => 'nullable|string|max:255',
            'website'      => 'nullable|string|max:255',
            'street1'      => 'nullable|string|max:255',
            'street2'      => 'nullable|string|max:255',
            'city'         => 'nullable|string|max:100',
            'zip'          => 'nullable|string|max:20',
        ]);

        $validated['is_active']  = $request->boolean('is_active', true);
        $validated['created_at'] = now();
        $validated['updated_at'] = now();

        DB::table('partners_partners')->insert($validated);

        session()->flash('success', 'Contact created successfully.');
        return redirect()->route('contacts.index');
    }

    public function edit($id)
    {
        $contact = DB::table('partners_partners')->whereNull('deleted_at')->where('id', $id)->first();
        abort_if(!$contact, 404);
        return view('app.contacts.edit', compact('contact'));
    }

    public function update(Request $request, $id)
    {
        $contact = DB::table('partners_partners')->whereNull('deleted_at')->where('id', $id)->first();
        abort_if(!$contact, 404);

        $validated = $request->validate([
            'name'         => 'required|string|max:255',
            'account_type' => 'required|in:individual,company',
            'email'        => 'nullable|email|max:255',
            'phone'        => 'nullable|string|max:50',
            'mobile'       => 'nullable|string|max:50',
            'job_title'    => 'nullable|string|max:255',
            'website'      => 'nullable|string|max:255',
            'street1'      => 'nullable|string|max:255',
            'street2'      => 'nullable|string|max:255',
            'city'         => 'nullable|string|max:100',
            'zip'          => 'nullable|string|max:20',
        ]);

        $validated['is_active']  = $request->boolean('is_active');
        $validated['updated_at'] = now();

        DB::table('partners_partners')->where('id', $id)->update($validated);

        session()->flash('success', 'Contact updated successfully.');
        return redirect()->route('contacts.show', $id);
    }

    public function destroy($id)
    {
        $contact = DB::table('partners_partners')->whereNull('deleted_at')->where('id', $id)->first();
        abort_if(!$contact, 404);

        DB::table('partners_partners')->where('id', $id)->update(['deleted_at' => now()]);

        session()->flash('success', 'Contact deleted successfully.');
        return redirect()->route('contacts.index');
    }
}
