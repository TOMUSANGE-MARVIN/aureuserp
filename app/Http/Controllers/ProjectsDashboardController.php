<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Collection;

class ProjectsDashboardController extends Controller
{
    public function index()
    {
        // ── Real data ──
        $totalProjects   = DB::table('projects_projects')->whereNull('deleted_at')->count();
        $totalTasks      = DB::table('projects_tasks')->whereNull('deleted_at')->count();
        $openTasks       = DB::table('projects_tasks')->whereNull('deleted_at')->whereNotIn('state', ['1_done', 'cancelled'])->count();
        $doneTasks       = DB::table('projects_tasks')->whereNull('deleted_at')->where('state', '1_done')->count();
        $overdueTasks    = DB::table('projects_tasks')->whereNull('deleted_at')->whereNotNull('deadline')
            ->where('deadline', '<', now())->whereNotIn('state', ['1_done', 'cancelled'])->count();

        // Tasks by state
        $tasksByState = DB::table('projects_tasks')->whereNull('deleted_at')
            ->selectRaw('state, count(*) as cnt')->groupBy('state')->pluck('cnt', 'state');

        // Tasks by stage
        $tasksByStage = DB::table('projects_tasks as t')
            ->leftJoin('projects_task_stages as s', 't.stage_id', '=', 's.id')
            ->whereNull('t.deleted_at')
            ->selectRaw('COALESCE(s.name, "Unassigned") as stage_name, count(*) as cnt')
            ->groupBy('stage_name')->orderByDesc('cnt')->limit(8)->pluck('cnt', 'stage_name');

        // Projects list
        $projects = DB::table('projects_projects as p')
            ->leftJoin('users as u', 'p.user_id', '=', 'u.id')
            ->whereNull('p.deleted_at')
            ->selectRaw('p.id, p.name, p.color, p.start_date, p.end_date, p.is_active, u.name as manager_name')
            ->orderByDesc('p.created_at')->limit(10)->get();

        // Enrich projects with task counts
        $projects = $projects->map(function ($project) {
            $counts = DB::table('projects_tasks')
                ->where('project_id', $project->id)->whereNull('deleted_at')
                ->selectRaw('count(*) as total, sum(state = "1_done") as done')->first();
            $project->total_tasks = $counts->total ?? 0;
            $project->done_tasks  = $counts->done ?? 0;
            $project->progress    = $project->total_tasks > 0
                ? round(($project->done_tasks / $project->total_tasks) * 100) : 0;
            return $project;
        });

        // Recent tasks
        $recentTasks = DB::table('projects_tasks as t')
            ->leftJoin('projects_projects as p', 't.project_id', '=', 'p.id')
            ->leftJoin('projects_task_stages as s', 't.stage_id', '=', 's.id')
            ->whereNull('t.deleted_at')
            ->selectRaw('t.id, t.title, t.state, t.priority, t.deadline, t.progress, p.name as project_name, s.name as stage_name')
            ->orderByDesc('t.created_at')->limit(8)->get();

        // Monthly tasks created
        $monthlyTasks = DB::table('projects_tasks')
            ->whereNull('deleted_at')
            ->where('created_at', '>=', now()->startOfYear())
            ->selectRaw('MONTH(created_at) as month, count(*) as cnt')
            ->groupBy('month')->orderBy('month')->pluck('cnt', 'month');

        $hasRealData = $totalProjects > 0 || $totalTasks > 0;

        // ── Demo data fallback ──
        $months = collect(['Jan','Feb','Mar','Apr','May','Jun','Jul','Aug','Sep','Oct','Nov','Dec']);

        if (!$hasRealData) {
            $totalProjects  = 12;
            $totalTasks     = 84;
            $openTasks      = 38;
            $doneTasks      = 32;
            $overdueTasks   = 7;

            $tasksByState = collect([
                'in_progress' => 24, '1_done' => 32, 'changes_requested' => 8,
                'approved' => 12, 'cancelled' => 8,
            ]);

            $tasksByStage = collect([
                'Backlog' => 18, 'In Progress' => 24, 'In Review' => 12,
                'Done' => 30, 'Testing' => 8,
            ]);

            $tasksCreatedData = collect([3,5,8,6,9,11,7,12,10,8,6,4]);

            $projects = collect([
                (object)['id'=>1,'name'=>'Website Redesign','color'=>'purple','manager_name'=>'Alice K.','total_tasks'=>12,'done_tasks'=>8,'progress'=>67,'start_date'=>'2025-01-10','end_date'=>'2025-06-30','is_active'=>true],
                (object)['id'=>2,'name'=>'ERP Integration','color'=>'blue','manager_name'=>'Bob M.','total_tasks'=>20,'done_tasks'=>14,'progress'=>70,'start_date'=>'2025-02-01','end_date'=>'2025-08-31','is_active'=>true],
                (object)['id'=>3,'name'=>'Mobile App v2','color'=>'green','manager_name'=>'Carol N.','total_tasks'=>16,'done_tasks'=>4,'progress'=>25,'start_date'=>'2025-03-15','end_date'=>'2025-09-30','is_active'=>true],
                (object)['id'=>4,'name'=>'Customer Portal','color'=>'orange','manager_name'=>'Dave S.','total_tasks'=>9,'done_tasks'=>9,'progress'=>100,'start_date'=>'2024-11-01','end_date'=>'2025-02-28','is_active'=>false],
                (object)['id'=>5,'name'=>'Data Analytics','color'=>'cyan','manager_name'=>'Eve T.','total_tasks'=>14,'done_tasks'=>6,'progress'=>43,'start_date'=>'2025-04-01','end_date'=>'2025-10-31','is_active'=>true],
            ]);

            $recentTasks = collect([
                (object)['id'=>1,'title'=>'Design new landing page','state'=>'1_done','priority'=>'0','deadline'=>null,'progress'=>100,'project_name'=>'Website Redesign','stage_name'=>'Done'],
                (object)['id'=>2,'title'=>'Set up CI/CD pipeline','state'=>'in_progress','priority'=>'1','deadline'=>'2025-06-15','progress'=>60,'project_name'=>'ERP Integration','stage_name'=>'In Progress'],
                (object)['id'=>3,'title'=>'Write unit tests for API','state'=>'in_progress','priority'=>'0','deadline'=>'2025-06-20','progress'=>30,'project_name'=>'Mobile App v2','stage_name'=>'In Progress'],
                (object)['id'=>4,'title'=>'User acceptance testing','state'=>'changes_requested','priority'=>'1','deadline'=>'2025-05-30','progress'=>80,'project_name'=>'Website Redesign','stage_name'=>'In Review'],
                (object)['id'=>5,'title'=>'Database schema migration','state'=>'1_done','priority'=>'0','deadline'=>null,'progress'=>100,'project_name'=>'ERP Integration','stage_name'=>'Done'],
            ]);
        } else {
            $rawMonthly = $monthlyTasks;
            $tasksCreatedData = $months->keys()->map(fn($i) => $rawMonthly->get($i + 1, 0));
        }

        if ($hasRealData) {
            $tasksCreatedData = $months->keys()->map(fn($i) => $monthlyTasks->get($i + 1, 0));
        }

        return view('app.projects', compact(
            'totalProjects','totalTasks','openTasks','doneTasks','overdueTasks',
            'tasksByState','tasksByStage','projects','recentTasks',
            'months','tasksCreatedData','hasRealData'
        ));
    }
}
