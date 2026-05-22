<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class TimeOffController extends Controller
{
    public function index(Request $request)
    {
        $filter = $request->get('filter', 'all');
        $search = $request->get('search', '');

        $query = DB::table('time_off_leaves')
            ->leftJoin('employees_employees', 'employees_employees.id', '=', 'time_off_leaves.employee_id')
            ->leftJoin('time_off_leave_types', 'time_off_leave_types.id', '=', 'time_off_leaves.holiday_status_id')
            ->select(
                'time_off_leaves.*',
                'employees_employees.name as employee_name',
                'time_off_leave_types.name as leave_type_name'
            );

        if ($filter !== 'all') {
            $query->where('time_off_leaves.state', $filter);
        }

        if ($search) {
            $query->where('employees_employees.name', 'like', "%$search%");
        }

        $leaves = $query->orderByDesc('time_off_leaves.created_at')->paginate(20)->withQueryString();

        $stats = [
            'total'    => DB::table('time_off_leaves')->count(),
            'draft'    => DB::table('time_off_leaves')->where('state', 'draft')->count(),
            'pending'  => DB::table('time_off_leaves')->whereIn('state', ['confirm', 'validate1'])->count(),
            'approved' => DB::table('time_off_leaves')->where('state', 'validate')->count(),
        ];

        return view('app.time-off.index', compact('leaves', 'stats', 'filter', 'search'));
    }

    public function create()
    {
        $employees  = DB::table('employees_employees')->whereNull('deleted_at')->orderBy('name')->get();
        $leaveTypes = DB::table('time_off_leave_types')->orderBy('name')->get();
        return view('app.time-off.create', compact('employees', 'leaveTypes'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'employee_id'       => 'required|integer',
            'holiday_status_id' => 'required|integer',
            'date_from'         => 'required|date',
            'date_to'           => 'required|date|after_or_equal:date_from',
        ]);

        $days = Carbon::parse($request->date_from)->diffInDays(Carbon::parse($request->date_to)) + 1;

        DB::table('time_off_leaves')->insert([
            'employee_id'       => $request->employee_id,
            'holiday_status_id' => $request->holiday_status_id,
            'date_from'         => $request->date_from,
            'date_to'           => $request->date_to,
            'number_of_days'    => $days,
            'state'             => 'draft',
            'holiday_type'      => 'employee',
            'description'       => $request->description,
            'created_at'        => now(),
            'updated_at'        => now(),
        ]);

        session()->flash('success', 'Leave request created successfully.');
        return redirect('/app/time-off');
    }

    public function show($id)
    {
        $leave = DB::table('time_off_leaves')
            ->leftJoin('employees_employees', 'employees_employees.id', '=', 'time_off_leaves.employee_id')
            ->leftJoin('time_off_leave_types', 'time_off_leave_types.id', '=', 'time_off_leaves.holiday_status_id')
            ->select(
                'time_off_leaves.*',
                'employees_employees.name as employee_name',
                'time_off_leave_types.name as leave_type_name'
            )
            ->where('time_off_leaves.id', $id)
            ->first();

        abort_if(!$leave, 404);
        return view('app.time-off.show', compact('leave'));
    }

    public function approve($id)
    {
        DB::table('time_off_leaves')->where('id', $id)->update(['state' => 'validate', 'updated_at' => now()]);
        session()->flash('success', 'Leave approved.');
        return redirect('/app/time-off/' . $id);
    }

    public function refuse($id)
    {
        DB::table('time_off_leaves')->where('id', $id)->update(['state' => 'refuse', 'updated_at' => now()]);
        session()->flash('success', 'Leave refused.');
        return redirect('/app/time-off/' . $id);
    }

    public function destroy($id)
    {
        DB::table('time_off_leaves')->where('id', $id)->delete();
        session()->flash('success', 'Leave request deleted.');
        return redirect('/app/time-off');
    }
}
