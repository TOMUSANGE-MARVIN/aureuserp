<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class EmployeesController extends Controller
{
    public function index(Request $request)
    {
        $search     = $request->input('search', '');
        $deptFilter = $request->input('department', '');

        $query = DB::table('employees_employees')->whereNull('deleted_at');

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('work_email', 'like', "%{$search}%");
            });
        }

        if ($deptFilter) {
            $query->where('department_id', $deptFilter);
        }

        $employees = $query->orderByDesc('created_at')->paginate(20)->withQueryString();

        // Load departments for filter dropdown
        $departments = DB::table('employees_departments')->orderBy('name')->get();

        // Department name map
        $deptMap = [];
        foreach ($departments as $dept) {
            $deptMap[$dept->id] = $dept->name;
        }

        $total  = DB::table('employees_employees')->whereNull('deleted_at')->count();
        $active = DB::table('employees_employees')->whereNull('deleted_at')->where('is_active', 1)->count();
        $deptCount = DB::table('employees_employees')->whereNull('deleted_at')->whereNotNull('department_id')->distinct('department_id')->count('department_id');

        return view('app.employees.index', compact('employees', 'search', 'deptFilter', 'departments', 'deptMap', 'total', 'active', 'deptCount'));
    }

    public function show($id)
    {
        $employee = DB::table('employees_employees')->whereNull('deleted_at')->where('id', $id)->first();
        abort_if(!$employee, 404);

        $department = null;
        if ($employee->department_id) {
            $department = DB::table('employees_departments')->where('id', $employee->department_id)->first();
        }

        $manager = null;
        if ($employee->parent_id) {
            $manager = DB::table('employees_employees')->where('id', $employee->parent_id)->first();
        }

        return view('app.employees.show', compact('employee', 'department', 'manager'));
    }

    public function create()
    {
        $departments = DB::table('employees_departments')->orderBy('name')->get();
        return view('app.employees.create', compact('departments'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name'          => 'required|string|max:255',
            'job_title'     => 'nullable|string|max:255',
            'work_email'    => 'nullable|email|max:255',
            'work_phone'    => 'nullable|string|max:50',
            'mobile_phone'  => 'nullable|string|max:50',
            'gender'        => 'nullable|in:male,female,other',
            'marital'       => 'nullable|in:single,married,cohabitant,widower,divorced',
            'birthday'      => 'nullable|date',
            'employee_type' => 'nullable|in:employee,student,freelance,external',
            'department_id' => 'nullable|integer',
        ]);

        $validated['is_active']  = $request->boolean('is_active', true);
        $validated['created_at'] = now();
        $validated['updated_at'] = now();

        DB::table('employees_employees')->insert($validated);

        session()->flash('success', 'Employee created successfully.');
        return redirect()->route('employees.index');
    }

    public function edit($id)
    {
        $employee    = DB::table('employees_employees')->whereNull('deleted_at')->where('id', $id)->first();
        abort_if(!$employee, 404);
        $departments = DB::table('employees_departments')->orderBy('name')->get();
        return view('app.employees.edit', compact('employee', 'departments'));
    }

    public function update(Request $request, $id)
    {
        $employee = DB::table('employees_employees')->whereNull('deleted_at')->where('id', $id)->first();
        abort_if(!$employee, 404);

        $validated = $request->validate([
            'name'          => 'required|string|max:255',
            'job_title'     => 'nullable|string|max:255',
            'work_email'    => 'nullable|email|max:255',
            'work_phone'    => 'nullable|string|max:50',
            'mobile_phone'  => 'nullable|string|max:50',
            'gender'        => 'nullable|in:male,female,other',
            'marital'       => 'nullable|in:single,married,cohabitant,widower,divorced',
            'birthday'      => 'nullable|date',
            'employee_type' => 'nullable|in:employee,student,freelance,external',
            'department_id' => 'nullable|integer',
        ]);

        $validated['is_active']  = $request->boolean('is_active');
        $validated['updated_at'] = now();

        DB::table('employees_employees')->where('id', $id)->update($validated);

        session()->flash('success', 'Employee updated successfully.');
        return redirect()->route('employees.show', $id);
    }

    public function destroy($id)
    {
        $employee = DB::table('employees_employees')->whereNull('deleted_at')->where('id', $id)->first();
        abort_if(!$employee, 404);

        DB::table('employees_employees')->where('id', $id)->update(['deleted_at' => now()]);

        session()->flash('success', 'Employee deleted successfully.');
        return redirect()->route('employees.index');
    }
}
