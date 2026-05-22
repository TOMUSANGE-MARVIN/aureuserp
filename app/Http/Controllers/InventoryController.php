<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class InventoryController extends Controller
{
    public function index(Request $request)
    {
        $filter = $request->input('filter', 'all');

        $query = DB::table('inventories_operations')
            ->leftJoin('partners_partners', 'inventories_operations.partner_id', '=', 'partners_partners.id')
            ->leftJoin('inventories_operation_types', 'inventories_operations.operation_type_id', '=', 'inventories_operation_types.id')
            ->select(
                'inventories_operations.*',
                'partners_partners.name as partner_name',
                'inventories_operation_types.name as operation_type_name'
            );

        if ($filter === 'receipts') {
            $query->where('inventories_operations.move_type', 'incoming');
        } elseif ($filter === 'deliveries') {
            $query->where('inventories_operations.move_type', 'outgoing');
        } elseif ($filter === 'internal') {
            $query->where('inventories_operations.move_type', 'internal');
        } elseif ($filter === 'done') {
            $query->where('inventories_operations.state', 'done');
        }

        $operations = $query->orderByDesc('inventories_operations.created_at')->paginate(20)->withQueryString();

        $total      = DB::table('inventories_operations')->count();
        $receipts   = DB::table('inventories_operations')->where('move_type', 'incoming')->count();
        $deliveries = DB::table('inventories_operations')->where('move_type', 'outgoing')->count();
        $done       = DB::table('inventories_operations')->where('state', 'done')->count();

        return view('app.inventory.index', compact('operations', 'filter', 'total', 'receipts', 'deliveries', 'done'));
    }

    public function show($id)
    {
        $operation = DB::table('inventories_operations')
            ->leftJoin('partners_partners', 'inventories_operations.partner_id', '=', 'partners_partners.id')
            ->leftJoin('inventories_operation_types', 'inventories_operations.operation_type_id', '=', 'inventories_operation_types.id')
            ->select(
                'inventories_operations.*',
                'partners_partners.name as partner_name',
                'inventories_operation_types.name as operation_type_name'
            )
            ->where('inventories_operations.id', $id)
            ->first();
        abort_if(!$operation, 404);

        $sourceLocation = null;
        $destLocation   = null;
        if (Schema::hasTable('inventories_locations')) {
            if ($operation->source_location_id) {
                $sourceLocation = DB::table('inventories_locations')->where('id', $operation->source_location_id)->first();
            }
            if ($operation->destination_location_id) {
                $destLocation = DB::table('inventories_locations')->where('id', $operation->destination_location_id)->first();
            }
        }

        $lines = [];
        if (Schema::hasTable('inventories_move_lines')) {
            $lines = DB::table('inventories_move_lines')->where('operation_id', $id)->get();
        }

        return view('app.inventory.show', compact('operation', 'sourceLocation', 'destLocation', 'lines'));
    }
}
