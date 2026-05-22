<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Services\NotificationService;

class HelpdeskController extends Controller
{
    // ─── Dashboard / Tickets List ─────────────────────────────────────────────

    public function index(Request $request)
    {
        $status   = $request->input('status');
        $priority = $request->input('priority');
        $teamId   = $request->input('team_id');
        $search   = $request->input('search');

        $query = DB::table('helpdesk_tickets as t')
            ->leftJoin('helpdesk_teams as tm', 't.team_id', '=', 'tm.id')
            ->leftJoin('users as a', 't.assigned_to', '=', 'a.id')
            ->leftJoin('users as c', 't.created_by', '=', 'c.id')
            ->select('t.*', 'tm.name as team_name', 'a.name as assignee_name', 'c.name as creator_name');

        if ($status) $query->where('t.status', $status);
        if ($priority) $query->where('t.priority', $priority);
        if ($teamId) $query->where('t.team_id', $teamId);
        if ($search) $query->where(function ($q) use ($search) {
            $q->where('t.title', 'like', "%{$search}%")
              ->orWhere('t.customer_email', 'like', "%{$search}%")
              ->orWhere('t.customer_name', 'like', "%{$search}%");
        });

        $tickets = $query->orderByRaw("FIELD(t.priority,'urgent','high','medium','low')")
                         ->orderBy('t.created_at', 'desc')
                         ->paginate(20)->withQueryString();

        $stats = DB::table('helpdesk_tickets')->selectRaw("
            COUNT(*) as total,
            SUM(status='open') as open,
            SUM(status='in_progress') as in_progress,
            SUM(status='resolved') as resolved,
            SUM(status='closed') as closed
        ")->first();

        $teams = DB::table('helpdesk_teams')->where('is_active', 1)->orderBy('name')->get();
        $users = DB::table('users')->orderBy('name')->get();

        return view('app.helpdesk.index', compact('tickets', 'stats', 'teams', 'users', 'status', 'priority', 'teamId', 'search'));
    }

    // ─── Create Ticket ────────────────────────────────────────────────────────

    public function createTicket()
    {
        $teams = DB::table('helpdesk_teams')->where('is_active', 1)->orderBy('name')->get();
        $users = DB::table('users')->orderBy('name')->get();
        return view('app.helpdesk.create-ticket', compact('teams', 'users'));
    }

    public function storeTicket(Request $request)
    {
        $request->validate([
            'title'          => 'required|string|max:255',
            'description'    => 'nullable|string',
            'customer_name'  => 'nullable|string|max:255',
            'customer_email' => 'nullable|email|max:255',
            'priority'       => 'required|in:low,medium,high,urgent',
            'type'           => 'nullable|string|max:100',
            'team_id'        => 'nullable|exists:helpdesk_teams,id',
            'assigned_to'    => 'nullable|exists:users,id',
        ]);

        $ticketId = DB::table('helpdesk_tickets')->insertGetId([
            'title'          => $request->title,
            'description'    => $request->description,
            'customer_name'  => $request->customer_name,
            'customer_email' => $request->customer_email,
            'priority'       => $request->priority,
            'type'           => $request->type,
            'team_id'        => $request->team_id,
            'assigned_to'    => $request->assigned_to,
            'created_by'     => Auth::id(),
            'status'         => 'open',
            'created_at'     => now(),
            'updated_at'     => now(),
        ]);

        // Notify assigned user if set
        if ($request->assigned_to) {
            NotificationService::notify(
                (int) $request->assigned_to,
                'New ticket assigned to you',
                $request->title,
                '/app/helpdesk/' . $ticketId,
                'ticket',
                'ticket'
            );
        }
        // Notify admins of new ticket
        NotificationService::notifyAdmins(
            'New support ticket: ' . $request->title,
            'Priority: ' . $request->priority . ($request->customer_name ? ' — ' . $request->customer_name : ''),
            '/app/helpdesk/' . $ticketId,
            'ticket',
            'ticket'
        );

        return redirect()->route('helpdesk.show', $ticketId)->with('success', 'Ticket created.');
    }

    // ─── View Ticket ──────────────────────────────────────────────────────────

    public function showTicket($id)
    {
        $ticket = DB::table('helpdesk_tickets as t')
            ->leftJoin('helpdesk_teams as tm', 't.team_id', '=', 'tm.id')
            ->leftJoin('users as a', 't.assigned_to', '=', 'a.id')
            ->leftJoin('users as c', 't.created_by', '=', 'c.id')
            ->select('t.*', 'tm.name as team_name', 'a.name as assignee_name', 'c.name as creator_name')
            ->where('t.id', $id)
            ->firstOrFail();

        $messages = DB::table('helpdesk_ticket_messages as m')
            ->leftJoin('users as u', 'm.user_id', '=', 'u.id')
            ->select('m.*', 'u.name as author_name')
            ->where('m.ticket_id', $id)
            ->orderBy('m.created_at')
            ->get();

        $teams = DB::table('helpdesk_teams')->where('is_active', 1)->orderBy('name')->get();
        $users = DB::table('users')->orderBy('name')->get();

        $tags = DB::table('helpdesk_ticket_tags as tt')
            ->join('helpdesk_tags as tg', 'tt.tag_id', '=', 'tg.id')
            ->where('tt.ticket_id', $id)
            ->pluck('tg.name');

        return view('app.helpdesk.show-ticket', compact('ticket', 'messages', 'teams', 'users', 'tags'));
    }

    public function updateTicket(Request $request, $id)
    {
        $request->validate([
            'status'      => 'nullable|in:open,in_progress,resolved,closed',
            'priority'    => 'nullable|in:low,medium,high,urgent',
            'assigned_to' => 'nullable|exists:users,id',
            'team_id'     => 'nullable|exists:helpdesk_teams,id',
        ]);

        $data = ['updated_at' => now()];
        if ($request->has('status')) {
            $data['status'] = $request->status;
            if ($request->status === 'resolved') $data['resolved_at'] = now();
            if ($request->status === 'closed')   $data['closed_at']   = now();
        }
        if ($request->has('priority'))    $data['priority']    = $request->priority;
        if ($request->has('assigned_to')) $data['assigned_to'] = $request->assigned_to ?: null;
        if ($request->has('team_id'))     $data['team_id']     = $request->team_id ?: null;

        DB::table('helpdesk_tickets')->where('id', $id)->update($data);

        // Notify ticket creator on status change
        if ($request->has('status')) {
            $ticket = DB::table('helpdesk_tickets')->where('id', $id)->first();
            if ($ticket && $ticket->created_by && $ticket->created_by !== Auth::id()) {
                $label = ['open' => 'Open', 'in_progress' => 'In Progress', 'resolved' => 'Resolved', 'closed' => 'Closed'][$request->status] ?? $request->status;
                NotificationService::notify(
                    (int) $ticket->created_by,
                    'Ticket status changed to ' . $label,
                    $ticket->title,
                    '/app/helpdesk/' . $id,
                    'ticket',
                    'ticket'
                );
            }
            // Notify newly assigned user
            if ($request->has('assigned_to') && $request->assigned_to && $request->assigned_to !== Auth::id()) {
                NotificationService::notify(
                    (int) $request->assigned_to,
                    'Ticket assigned to you',
                    $ticket->title ?? '',
                    '/app/helpdesk/' . $id,
                    'ticket',
                    'ticket'
                );
            }
        }

        return back()->with('success', 'Ticket updated.');
    }

    public function deleteTicket($id)
    {
        DB::table('helpdesk_tickets')->where('id', $id)->delete();
        return redirect()->route('helpdesk.index')->with('success', 'Ticket deleted.');
    }

    // ─── Messages / Replies ───────────────────────────────────────────────────

    public function storeMessage(Request $request, $id)
    {
        $request->validate(['body' => 'required|string']);

        DB::table('helpdesk_ticket_messages')->insert([
            'ticket_id'   => $id,
            'user_id'     => Auth::id(),
            'body'        => $request->body,
            'is_internal' => $request->boolean('is_internal'),
            'created_at'  => now(),
            'updated_at'  => now(),
        ]);

        // Auto-move to in_progress on first reply
        $ticket = DB::table('helpdesk_tickets')->where('id', $id)->first();
        if ($ticket && $ticket->status === 'open' && !$request->boolean('is_internal')) {
            DB::table('helpdesk_tickets')->where('id', $id)->update(['status' => 'in_progress', 'updated_at' => now()]);
        }

        return back()->with('success', 'Reply sent.');
    }

    // ─── Teams ────────────────────────────────────────────────────────────────

    public function teams()
    {
        $teams = DB::table('helpdesk_teams')->orderBy('name')->get();
        foreach ($teams as $team) {
            $team->member_count  = DB::table('helpdesk_team_members')->where('team_id', $team->id)->count();
            $team->ticket_count  = DB::table('helpdesk_tickets')->where('team_id', $team->id)->count();
            $team->open_tickets  = DB::table('helpdesk_tickets')->where('team_id', $team->id)->whereIn('status', ['open','in_progress'])->count();
        }
        $users = DB::table('users')->orderBy('name')->get();
        return view('app.helpdesk.teams', compact('teams', 'users'));
    }

    public function storeTeam(Request $request)
    {
        $request->validate(['name' => 'required|string|max:255']);
        $id = DB::table('helpdesk_teams')->insertGetId([
            'name'        => $request->name,
            'description' => $request->description,
            'is_active'   => 1,
            'created_at'  => now(),
            'updated_at'  => now(),
        ]);
        return redirect()->route('helpdesk.teams.edit', $id)->with('success', 'Team created.');
    }

    public function editTeam($id)
    {
        $team    = DB::table('helpdesk_teams')->where('id', $id)->firstOrFail();
        $members = DB::table('helpdesk_team_members as tm')
            ->join('users as u', 'tm.user_id', '=', 'u.id')
            ->select('u.id', 'u.name', 'u.email')
            ->where('tm.team_id', $id)
            ->get();
        $memberIds = $members->pluck('id')->toArray();
        $users     = DB::table('users')->orderBy('name')->get();
        return view('app.helpdesk.edit-team', compact('team', 'members', 'memberIds', 'users'));
    }

    public function updateTeam(Request $request, $id)
    {
        $request->validate(['name' => 'required|string|max:255']);
        DB::table('helpdesk_teams')->where('id', $id)->update([
            'name'        => $request->name,
            'description' => $request->description,
            'is_active'   => $request->has('is_active') ? 1 : 0,
            'updated_at'  => now(),
        ]);
        return back()->with('success', 'Team updated.');
    }

    public function deleteTeam($id)
    {
        DB::table('helpdesk_teams')->where('id', $id)->delete();
        return redirect()->route('helpdesk.teams')->with('success', 'Team deleted.');
    }

    public function addTeamMember(Request $request, $id)
    {
        $request->validate(['user_id' => 'required|exists:users,id']);
        DB::table('helpdesk_team_members')->insertOrIgnore([
            'team_id' => $id,
            'user_id' => $request->user_id,
        ]);
        return back()->with('success', 'Member added.');
    }

    public function removeTeamMember($teamId, $userId)
    {
        DB::table('helpdesk_team_members')
            ->where('team_id', $teamId)
            ->where('user_id', $userId)
            ->delete();
        return back()->with('success', 'Member removed.');
    }
}
