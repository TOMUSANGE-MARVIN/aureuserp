<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Services\NotificationService;
use Carbon\Carbon;

class PayrollController extends Controller
{
    // ─── Dashboard / Payslips List ───────────────────────────────────────────

    public function index(Request $request)
    {
        $month  = $request->input('month', Carbon::now()->format('Y-m'));
        $empId  = $request->input('employee_id');
        $status = $request->input('status');

        [$year, $mon] = explode('-', $month);
        $periodStart = "{$year}-{$mon}-01";
        $periodEnd   = Carbon::create($year, $mon)->endOfMonth()->format('Y-m-d');

        $query = DB::table('payroll_payslips as ps')
            ->join('employees_employees as e', 'ps.employee_id', '=', 'e.id')
            ->leftJoin('payroll_salary_structures as ss', 'ps.structure_id', '=', 'ss.id')
            ->select('ps.*', 'e.name as employee_name', 'e.job_title', 'ss.name as structure_name')
            ->whereBetween('ps.period_start', [$periodStart, $periodEnd]);

        if ($empId) $query->where('ps.employee_id', $empId);
        if ($status) $query->where('ps.status', $status);

        $payslips = $query->orderBy('e.name')->paginate(20)->withQueryString();

        $stats = DB::table('payroll_payslips')
            ->whereBetween('period_start', [$periodStart, $periodEnd])
            ->selectRaw('COUNT(*) as total, SUM(net_salary) as total_net, SUM(gross_salary) as total_gross, SUM(total_deductions) as total_deductions')
            ->first();

        $employees  = DB::table('employees_employees')->whereNull('deleted_at')->orderBy('name')->get();
        $totalStaff = $employees->count();

        return view('app.payroll.index', compact('payslips', 'stats', 'employees', 'totalStaff', 'month', 'empId', 'status'));
    }

    // ─── Create Payslip ──────────────────────────────────────────────────────

    public function createPayslip()
    {
        $employees  = DB::table('employees_employees')->whereNull('deleted_at')->orderBy('name')->get();
        $structures = DB::table('payroll_salary_structures')->where('is_active', 1)->orderBy('name')->get();

        return view('app.payroll.create-payslip', compact('employees', 'structures'));
    }

    public function storePayslip(Request $request)
    {
        $request->validate([
            'employee_id'   => 'required|exists:employees_employees,id',
            'structure_id'  => 'required|exists:payroll_salary_structures,id',
            'period_start'  => 'required|date',
            'period_end'    => 'required|date|after_or_equal:period_start',
            'basic_salary'  => 'required|numeric|min:0',
        ]);

        $rules = DB::table('payroll_salary_rules')
            ->where('structure_id', $request->structure_id)
            ->orderBy('sequence')
            ->get();

        $basicSalary      = (float) $request->basic_salary;
        $totalEarnings    = $basicSalary;
        $totalDeductions  = 0;
        $lines            = [];

        $lines[] = [
            'name'     => 'Basic Salary',
            'code'     => 'BASIC',
            'type'     => 'earning',
            'amount'   => $basicSalary,
            'sequence' => 1,
            'rule_id'  => null,
        ];

        foreach ($rules as $rule) {
            $amount = $rule->amount_type === 'percentage'
                ? round(($rule->amount / 100) * $basicSalary, 2)
                : (float) $rule->amount;

            if ($rule->type === 'earning') {
                $totalEarnings += $amount;
            } else {
                $totalDeductions += $amount;
            }

            $lines[] = [
                'name'     => $rule->name,
                'code'     => $rule->code,
                'type'     => $rule->type,
                'amount'   => $amount,
                'sequence' => $rule->sequence,
                'rule_id'  => $rule->id,
            ];
        }

        $grossSalary = $totalEarnings;
        $netSalary   = $grossSalary - $totalDeductions;

        $payslipId = DB::table('payroll_payslips')->insertGetId([
            'employee_id'      => $request->employee_id,
            'structure_id'     => $request->structure_id,
            'period_start'     => $request->period_start,
            'period_end'       => $request->period_end,
            'basic_salary'     => $basicSalary,
            'gross_salary'     => $grossSalary,
            'total_deductions' => $totalDeductions,
            'net_salary'       => $netSalary,
            'status'           => 'draft',
            'note'             => $request->note,
            'created_at'       => now(),
            'updated_at'       => now(),
        ]);

        $now = now();
        foreach ($lines as &$line) {
            $line['payslip_id'] = $payslipId;
            $line['created_at'] = $now;
            $line['updated_at'] = $now;
        }

        DB::table('payroll_payslip_lines')->insert($lines);

        return redirect()->route('payroll.show', $payslipId)->with('success', 'Payslip created successfully.');
    }

    // ─── View / Actions on Payslip ───────────────────────────────────────────

    public function showPayslip($id)
    {
        $payslip = DB::table('payroll_payslips as ps')
            ->join('employees_employees as e', 'ps.employee_id', '=', 'e.id')
            ->leftJoin('payroll_salary_structures as ss', 'ps.structure_id', '=', 'ss.id')
            ->select('ps.*', 'e.name as employee_name', 'e.job_title', 'e.work_email', 'ss.name as structure_name')
            ->where('ps.id', $id)
            ->firstOrFail();

        $lines   = DB::table('payroll_payslip_lines')
            ->where('payslip_id', $id)
            ->orderBy('sequence')
            ->get();

        $company = DB::table('companies')->first();

        return view('app.payroll.show-payslip', compact('payslip', 'lines', 'company'));
    }

    public function confirmPayslip($id)
    {
        $payslip = DB::table('payroll_payslips')->where('id', $id)->first();
        if (!$payslip || $payslip->status !== 'draft') {
            return back()->with('error', 'Payslip cannot be confirmed.');
        }
        DB::table('payroll_payslips')->where('id', $id)->update(['status' => 'confirmed', 'updated_at' => now()]);

        // Notify the employee's linked user if available
        $payslip = DB::table('payroll_payslips as p')
            ->leftJoin('employees_employees as e', 'p.employee_id', '=', 'e.id')
            ->leftJoin('users as u', 'u.email', '=', 'e.work_email')
            ->select('p.*', 'e.name as emp_name', 'u.id as user_id')
            ->where('p.id', $id)
            ->first();
        if ($payslip && $payslip->user_id) {
            NotificationService::notify(
                (int) $payslip->user_id,
                'Your payslip has been confirmed',
                'Period: ' . $payslip->period_start . ' – ' . $payslip->period_end,
                '/app/payroll/' . $id,
                'payroll', 'payroll'
            );
        }
        NotificationService::notifyAdmins(
            'Payslip confirmed',
            ($payslip->emp_name ?? 'Employee') . ' — ' . ($payslip->period_start ?? ''),
            '/app/payroll/' . $id,
            'payroll', 'payroll'
        );

        return back()->with('success', 'Payslip confirmed.');
    }

    public function markPaid($id)
    {
        $payslip = DB::table('payroll_payslips')->where('id', $id)->first();
        if (!$payslip || $payslip->status !== 'confirmed') {
            return back()->with('error', 'Payslip must be confirmed before marking as paid.');
        }
        DB::table('payroll_payslips')->where('id', $id)->update([
            'status'     => 'paid',
            'paid_at'    => now()->toDateString(),
            'updated_at' => now(),
        ]);

        // Notify admins of payment
        $ps = DB::table('payroll_payslips as p')
            ->leftJoin('employees_employees as e', 'p.employee_id', '=', 'e.id')
            ->select('p.*', 'e.name as emp_name')
            ->where('p.id', $id)->first();
        NotificationService::notifyAdmins(
            'Payslip marked as paid',
            ($ps->emp_name ?? 'Employee') . ' — Net: ' . number_format((float)($ps->net_salary ?? 0), 2),
            '/app/payroll/' . $id,
            'payroll', 'payroll'
        );

        return back()->with('success', 'Payslip marked as paid.');
    }

    public function deletePayslip($id)
    {
        $payslip = DB::table('payroll_payslips')->where('id', $id)->first();
        if ($payslip && $payslip->status === 'draft') {
            DB::table('payroll_payslips')->where('id', $id)->delete();
            return redirect()->route('payroll.index')->with('success', 'Payslip deleted.');
        }
        return back()->with('error', 'Only draft payslips can be deleted.');
    }

    // ─── Run Payroll (bulk generate) ─────────────────────────────────────────

    public function runPayroll(Request $request)
    {
        $request->validate([
            'structure_id' => 'required|exists:payroll_salary_structures,id',
            'period_start' => 'required|date',
            'period_end'   => 'required|date|after_or_equal:period_start',
            'basic_salary' => 'required|numeric|min:0',
        ]);

        $employees = DB::table('employees_employees')->whereNull('deleted_at')->get();
        $rules     = DB::table('payroll_salary_rules')
            ->where('structure_id', $request->structure_id)
            ->orderBy('sequence')->get();

        $created = 0;
        foreach ($employees as $emp) {
            $exists = DB::table('payroll_payslips')
                ->where('employee_id', $emp->id)
                ->where('period_start', $request->period_start)
                ->exists();
            if ($exists) continue;

            $basicSalary     = (float) $request->basic_salary;
            $totalEarnings   = $basicSalary;
            $totalDeductions = 0;
            $lines = [[
                'name' => 'Basic Salary', 'code' => 'BASIC', 'type' => 'earning',
                'amount' => $basicSalary, 'sequence' => 1, 'rule_id' => null,
            ]];

            foreach ($rules as $rule) {
                $amount = $rule->amount_type === 'percentage'
                    ? round(($rule->amount / 100) * $basicSalary, 2)
                    : (float) $rule->amount;
                if ($rule->type === 'earning') $totalEarnings += $amount;
                else $totalDeductions += $amount;
                $lines[] = ['name' => $rule->name, 'code' => $rule->code, 'type' => $rule->type,
                    'amount' => $amount, 'sequence' => $rule->sequence, 'rule_id' => $rule->id];
            }

            $payslipId = DB::table('payroll_payslips')->insertGetId([
                'employee_id'      => $emp->id,
                'structure_id'     => $request->structure_id,
                'period_start'     => $request->period_start,
                'period_end'       => $request->period_end,
                'basic_salary'     => $basicSalary,
                'gross_salary'     => $totalEarnings,
                'total_deductions' => $totalDeductions,
                'net_salary'       => $totalEarnings - $totalDeductions,
                'status'           => 'draft',
                'created_at'       => now(),
                'updated_at'       => now(),
            ]);

            $now = now();
            foreach ($lines as &$line) {
                $line['payslip_id'] = $payslipId;
                $line['created_at'] = $now;
                $line['updated_at'] = $now;
            }
            DB::table('payroll_payslip_lines')->insert($lines);
            $created++;
        }

        NotificationService::notifyAdmins(
            "{$created} payslips generated",
            'Period: ' . $request->period_start . ' – ' . $request->period_end,
            '/app/payroll',
            'payroll', 'payroll'
        );

        return redirect()->route('payroll.index')->with('success', "{$created} payslips generated.");
    }

    // ─── Salary Structures ───────────────────────────────────────────────────

    public function structures()
    {
        $structures = DB::table('payroll_salary_structures')->orderBy('name')->get();
        foreach ($structures as $s) {
            $s->rules_count   = DB::table('payroll_salary_rules')->where('structure_id', $s->id)->count();
            $s->payslip_count = DB::table('payroll_payslips')->where('structure_id', $s->id)->count();
        }
        return view('app.payroll.structures', compact('structures'));
    }

    public function storeStructure(Request $request)
    {
        $request->validate(['name' => 'required|string|max:255']);
        $id = DB::table('payroll_salary_structures')->insertGetId([
            'name'        => $request->name,
            'description' => $request->description,
            'is_active'   => 1,
            'created_at'  => now(),
            'updated_at'  => now(),
        ]);
        return redirect()->route('payroll.structures.edit', $id)->with('success', 'Structure created.');
    }

    public function editStructure($id)
    {
        $structure = DB::table('payroll_salary_structures')->where('id', $id)->firstOrFail();
        $rules     = DB::table('payroll_salary_rules')
            ->where('structure_id', $id)->orderBy('sequence')->get();
        return view('app.payroll.edit-structure', compact('structure', 'rules'));
    }

    public function updateStructure(Request $request, $id)
    {
        $request->validate(['name' => 'required|string|max:255']);
        DB::table('payroll_salary_structures')->where('id', $id)->update([
            'name'        => $request->name,
            'description' => $request->description,
            'is_active'   => $request->has('is_active') ? 1 : 0,
            'updated_at'  => now(),
        ]);
        return back()->with('success', 'Structure updated.');
    }

    public function deleteStructure($id)
    {
        if (DB::table('payroll_payslips')->where('structure_id', $id)->exists()) {
            return back()->with('error', 'Cannot delete a structure that is used in payslips.');
        }
        DB::table('payroll_salary_structures')->where('id', $id)->delete();
        return redirect()->route('payroll.structures')->with('success', 'Structure deleted.');
    }

    // ─── Salary Rules ─────────────────────────────────────────────────────────

    public function storeRule(Request $request, $structureId)
    {
        $request->validate([
            'name'        => 'required|string|max:255',
            'code'        => 'required|string|max:50',
            'type'        => 'required|in:earning,deduction',
            'amount_type' => 'required|in:fixed,percentage',
            'amount'      => 'required|numeric|min:0',
            'sequence'    => 'nullable|integer',
        ]);
        DB::table('payroll_salary_rules')->insert([
            'structure_id' => $structureId,
            'name'         => $request->name,
            'code'         => strtoupper($request->code),
            'type'         => $request->type,
            'amount_type'  => $request->amount_type,
            'amount'       => $request->amount,
            'sequence'     => $request->sequence ?? 10,
            'created_at'   => now(),
            'updated_at'   => now(),
        ]);
        return back()->with('success', 'Rule added.');
    }

    public function deleteRule($structureId, $ruleId)
    {
        DB::table('payroll_salary_rules')
            ->where('id', $ruleId)
            ->where('structure_id', $structureId)
            ->delete();
        return back()->with('success', 'Rule deleted.');
    }
}
