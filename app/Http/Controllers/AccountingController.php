<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class AccountingController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->input('search', '');
        $filter = $request->input('filter', 'all');

        $query = DB::table('accounts_account_moves')
            ->leftJoin('partners_partners', 'accounts_account_moves.partner_id', '=', 'partners_partners.id')
            ->select('accounts_account_moves.*', 'partners_partners.name as partner_name');

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('accounts_account_moves.name', 'like', "%{$search}%")
                  ->orWhere('accounts_account_moves.invoice_origin', 'like', "%{$search}%");
            });
        }

        if ($filter === 'invoices') {
            $query->where('accounts_account_moves.move_type', 'out_invoice');
        } elseif ($filter === 'bills') {
            $query->where('accounts_account_moves.move_type', 'in_invoice');
        } elseif ($filter === 'credit_notes') {
            $query->where('accounts_account_moves.move_type', 'out_refund');
        } elseif ($filter === 'entries') {
            $query->where('accounts_account_moves.move_type', 'entry');
        }

        $moves = $query->orderByDesc('accounts_account_moves.created_at')->paginate(20)->withQueryString();

        $totalPosted = DB::table('accounts_account_moves')->where('state', 'posted')->count();
        $unpaidInv   = DB::table('accounts_account_moves')->where('move_type', 'out_invoice')->where('state', 'posted')->where('payment_state', 'not_paid')->count();
        $unpaidBills = DB::table('accounts_account_moves')->where('move_type', 'in_invoice')->where('state', 'posted')->where('payment_state', 'not_paid')->count();
        $totalAR     = DB::table('accounts_account_moves')->where('move_type', 'out_invoice')->where('state', 'posted')->sum('amount_residual');

        return view('app.accounting.index', compact('moves', 'search', 'filter', 'totalPosted', 'unpaidInv', 'unpaidBills', 'totalAR'));
    }

    public function show($id)
    {
        $move = DB::table('accounts_account_moves')
            ->leftJoin('partners_partners', 'accounts_account_moves.partner_id', '=', 'partners_partners.id')
            ->select('accounts_account_moves.*', 'partners_partners.name as partner_name')
            ->where('accounts_account_moves.id', $id)
            ->first();
        abort_if(!$move, 404);

        $lines = [];
        if (Schema::hasTable('accounts_account_move_lines')) {
            $lines = DB::table('accounts_account_move_lines')->where('move_id', $id)->get();
        }

        return view('app.accounting.show', compact('move', 'lines'));
    }
}
