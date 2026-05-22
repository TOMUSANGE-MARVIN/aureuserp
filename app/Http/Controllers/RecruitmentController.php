<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class RecruitmentController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->get('search', '');
        $stageFilter = $request->get('stage', 'all');

        $query = DB::table('recruitments_applicants')
            ->whereNull('recruitments_applicants.deleted_at')
            ->leftJoin('recruitments_candidates', 'recruitments_candidates.id', '=', 'recruitments_applicants.candidate_id')
            ->leftJoin('recruitments_stages', 'recruitments_stages.id', '=', 'recruitments_applicants.stage_id')
            ->leftJoin('employees_departments', 'employees_departments.id', '=', 'recruitments_applicants.department_id')
            ->select(
                'recruitments_applicants.*',
                'recruitments_candidates.name as candidate_name',
                'recruitments_candidates.email_from as candidate_email',
                'recruitments_stages.name as stage_name',
                'employees_departments.name as department_name'
            );

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('recruitments_candidates.name', 'like', "%$search%")
                  ->orWhere('recruitments_candidates.email_from', 'like', "%$search%");
            });
        }

        if ($stageFilter !== 'all') {
            $query->where('recruitments_applicants.stage_id', $stageFilter);
        }

        $applicants = $query->orderByDesc('recruitments_applicants.created_at')->paginate(20)->withQueryString();

        $stages = DB::table('recruitments_stages')->orderBy('id')->get();

        $stats = [
            'total'       => DB::table('recruitments_applicants')->whereNull('deleted_at')->count(),
            'new'         => DB::table('recruitments_applicants')->whereNull('deleted_at')->where('state', 'new')->count(),
            'in_progress' => DB::table('recruitments_applicants')->whereNull('deleted_at')->where('state', 'in_progress')->count(),
            'done'        => DB::table('recruitments_applicants')->whereNull('deleted_at')->where('state', 'done')->count(),
        ];

        return view('app.recruitment.index', compact('applicants', 'stages', 'stats', 'search', 'stageFilter'));
    }

    public function create()
    {
        $stages = DB::table('recruitments_stages')->orderBy('id')->get();
        $departments = DB::table('employees_departments')->whereNull('deleted_at')->orderBy('name')->get();
        return view('app.recruitment.create', compact('stages', 'departments'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name'             => 'required|string|max:255',
            'email'            => 'nullable|email',
            'mobile'           => 'nullable|string|max:50',
            'stage_id'         => 'nullable|integer',
            'salary_expected'  => 'nullable|numeric',
        ]);

        $candidateId = DB::table('recruitments_candidates')->insertGetId([
            'name'       => $request->name,
            'email_from' => $request->email,
            'phone'      => $request->mobile,
            'is_active'  => 1,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        DB::table('recruitments_applicants')->insert([
            'candidate_id'    => $candidateId,
            'stage_id'        => $request->stage_id ?: DB::table('recruitments_stages')->orderBy('id')->value('id'),
            'department_id'   => $request->department_id ?: null,
            'priority'        => $request->priority ?? 0,
            'state'           => 'new',
            'salary_expected' => $request->salary_expected,
            'is_active'       => 1,
            'date_opened'     => now(),
            'deleted_at'      => null,
            'created_at'      => now(),
            'updated_at'      => now(),
        ]);

        session()->flash('success', 'Application created successfully.');
        return redirect('/app/recruitment');
    }

    public function show($id)
    {
        $applicant = DB::table('recruitments_applicants')
            ->whereNull('recruitments_applicants.deleted_at')
            ->leftJoin('recruitments_candidates', 'recruitments_candidates.id', '=', 'recruitments_applicants.candidate_id')
            ->leftJoin('recruitments_stages', 'recruitments_stages.id', '=', 'recruitments_applicants.stage_id')
            ->leftJoin('employees_departments', 'employees_departments.id', '=', 'recruitments_applicants.department_id')
            ->select(
                'recruitments_applicants.*',
                'recruitments_candidates.name as candidate_name',
                'recruitments_candidates.email_from as candidate_email',
                'recruitments_candidates.phone as candidate_phone',
                'recruitments_candidates.linkedin_profile',
                'recruitments_stages.name as stage_name',
                'employees_departments.name as department_name'
            )
            ->where('recruitments_applicants.id', $id)
            ->first();

        abort_if(!$applicant, 404);
        return view('app.recruitment.show', compact('applicant'));
    }

    public function edit($id)
    {
        $applicant = DB::table('recruitments_applicants')
            ->leftJoin('recruitments_candidates', 'recruitments_candidates.id', '=', 'recruitments_applicants.candidate_id')
            ->select('recruitments_applicants.*', 'recruitments_candidates.name as candidate_name',
                     'recruitments_candidates.email_from as candidate_email', 'recruitments_candidates.phone as candidate_phone')
            ->where('recruitments_applicants.id', $id)->first();
        abort_if(!$applicant, 404);

        $stages = DB::table('recruitments_stages')->orderBy('id')->get();
        $departments = DB::table('employees_departments')->whereNull('deleted_at')->orderBy('name')->get();
        return view('app.recruitment.edit', compact('applicant', 'stages', 'departments'));
    }

    public function update(Request $request, $id)
    {
        $applicant = DB::table('recruitments_applicants')->where('id', $id)->first();
        abort_if(!$applicant, 404);

        DB::table('recruitments_applicants')->where('id', $id)->update([
            'stage_id'        => $request->stage_id,
            'department_id'   => $request->department_id ?: null,
            'priority'        => $request->priority ?? 0,
            'state'           => $request->state ?? 'new',
            'salary_expected' => $request->salary_expected,
            'salary_proposed' => $request->salary_proposed,
            'updated_at'      => now(),
        ]);

        if ($applicant->candidate_id) {
            DB::table('recruitments_candidates')->where('id', $applicant->candidate_id)->update([
                'name'       => $request->name,
                'email_from' => $request->email,
                'phone'      => $request->mobile,
                'updated_at' => now(),
            ]);
        }

        session()->flash('success', 'Application updated.');
        return redirect('/app/recruitment/' . $id);
    }

    public function destroy($id)
    {
        DB::table('recruitments_applicants')->where('id', $id)->update(['deleted_at' => now()]);
        session()->flash('success', 'Application removed.');
        return redirect('/app/recruitment');
    }
}
