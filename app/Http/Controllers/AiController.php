<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use GuzzleHttp\Client;

class AiController extends Controller
{
    private function getErpContext(): array
    {
        $now = Carbon::now();

        try {
            $employees    = DB::table('employees_employees')->whereNull('deleted_at')->count();
            $openTickets  = DB::table('helpdesk_tickets')->whereIn('status', ['open', 'in_progress'])->count();
            $draftPayslips = DB::table('payroll_payslips')->where('status', 'draft')->count();
            $totalPayslipsMonth = DB::table('payroll_payslips')
                ->whereMonth('period_start', $now->month)
                ->whereYear('period_start', $now->year)
                ->count();
            $netPayrollMonth = DB::table('payroll_payslips')
                ->whereMonth('period_start', $now->month)
                ->whereYear('period_start', $now->year)
                ->sum('net_salary');
            $urgentTickets = DB::table('helpdesk_tickets')
                ->where('priority', 'urgent')
                ->whereIn('status', ['open', 'in_progress'])
                ->count();
            $salaryStructures = DB::table('payroll_salary_structures')->where('is_active', 1)->count();
            $teams            = DB::table('helpdesk_teams')->where('is_active', 1)->count();
            $users            = DB::table('users')->count();
            $company          = DB::table('companies')->first();

            return [
                'company_name'          => $company->name ?? 'the company',
                'total_employees'       => $employees,
                'total_users'           => $users,
                'open_tickets'          => $openTickets,
                'urgent_tickets'        => $urgentTickets,
                'draft_payslips'        => $draftPayslips,
                'payslips_this_month'   => $totalPayslipsMonth,
                'net_payroll_month'     => number_format($netPayrollMonth, 2),
                'salary_structures'     => $salaryStructures,
                'helpdesk_teams'        => $teams,
                'current_month'         => $now->format('F Y'),
                'current_date'          => $now->format('l, F j Y'),
            ];
        } catch (\Throwable $e) {
            return ['note' => 'ERP data could not be fetched: ' . $e->getMessage()];
        }
    }

    private function buildSystemPrompt(array $context): string
    {
        $ctx = json_encode($context, JSON_PRETTY_PRINT);

        return <<<PROMPT
You are Aura, the intelligent AI assistant embedded inside AureusERP — a modern SaaS ERP platform. You help organisation admins and team members work smarter.

Today is {$context['current_date']}.

## Live ERP Snapshot
{$ctx}

## Your Capabilities
1. **Answer questions** about the live ERP data above (employees, tickets, payroll, etc.)
2. **Draft documents**: HR letters, payslip notes, ticket responses, onboarding emails, job descriptions
3. **Give business insights**: spot risks, anomalies, and opportunities from the ERP data
4. **Suggest actions**: when relevant, mention where in the ERP to take action (e.g., "Go to Payroll → Run Payroll")
5. **Explain modules**: help users understand how to use payroll, helpdesk, HR, inventory, accounting, etc.

## Personality
- Concise, professional, friendly
- Use bullet points for lists
- When you spot something important (e.g., urgent tickets, unconfirmed payslips), proactively mention it
- Never make up data — only refer to the ERP snapshot above
- When asked to draft something, produce the full draft immediately

## Response Format
- Keep responses focused and scannable
- Use **bold** for key figures
- For action suggestions, prefix with 👉
- For warnings, prefix with ⚠️
- For insights, prefix with 💡
PROMPT;
    }

    public function chat(Request $request)
    {
        $request->validate(['message' => 'required|string|max:2000']);

        $apiKey = env('OPENAI_API_KEY');
        if (!$apiKey) {
            return response()->json([
                'reply' => "⚙️ **AI not configured yet.**\n\nTo enable Aura, add your OpenAI API key:\n1. Open your `.env` file\n2. Set `OPENAI_API_KEY=your-key-here`\n3. Restart the server\n\nYou can get a key at [platform.openai.com](https://platform.openai.com).",
            ]);
        }

        $history = $request->input('history', []);
        $userMessage = $request->input('message');
        $context = $this->getErpContext();

        $messages = [['role' => 'system', 'content' => $this->buildSystemPrompt($context)]];

        foreach (array_slice($history, -10) as $turn) {
            if (!empty($turn['role']) && !empty($turn['content'])) {
                $messages[] = ['role' => $turn['role'], 'content' => $turn['content']];
            }
        }
        $messages[] = ['role' => 'user', 'content' => $userMessage];

        try {
            $client = new Client(['timeout' => 30]);
            $response = $client->post('https://api.openai.com/v1/chat/completions', [
                'headers' => [
                    'Authorization' => "Bearer {$apiKey}",
                    'Content-Type'  => 'application/json',
                ],
                'json' => [
                    'model'       => env('OPENAI_MODEL', 'gpt-4o-mini'),
                    'messages'    => $messages,
                    'max_tokens'  => 800,
                    'temperature' => 0.7,
                ],
            ]);

            $data  = json_decode($response->getBody(), true);
            $reply = $data['choices'][0]['message']['content'] ?? 'No response.';

            return response()->json(['reply' => $reply]);
        } catch (\GuzzleHttp\Exception\ClientException $e) {
            $body = json_decode($e->getResponse()->getBody(), true);
            $msg  = $body['error']['message'] ?? $e->getMessage();
            return response()->json(['reply' => "⚠️ API error: {$msg}"], 200);
        } catch (\Throwable $e) {
            return response()->json(['reply' => "⚠️ Could not reach AI: " . $e->getMessage()], 200);
        }
    }

    public function insights()
    {
        $context = $this->getErpContext();
        $insights = [];

        if (($context['urgent_tickets'] ?? 0) > 0) {
            $insights[] = ['type' => 'warning', 'text' => "{$context['urgent_tickets']} urgent ticket(s) need immediate attention", 'link' => '/app/helpdesk?priority=urgent', 'label' => 'View'];
        }
        if (($context['draft_payslips'] ?? 0) > 0) {
            $insights[] = ['type' => 'warning', 'text' => "{$context['draft_payslips']} payslip(s) are still in draft — confirm or pay them", 'link' => '/app/payroll?status=draft', 'label' => 'Review'];
        }
        if (($context['open_tickets'] ?? 0) > 5) {
            $insights[] = ['type' => 'info', 'text' => "{$context['open_tickets']} open tickets in helpdesk queue", 'link' => '/app/helpdesk?status=open', 'label' => 'View'];
        }
        if (($context['payslips_this_month'] ?? 0) === 0) {
            $insights[] = ['type' => 'info', 'text' => "No payslips generated for {$context['current_month']} yet", 'link' => '/app/payroll', 'label' => 'Run Payroll'];
        }

        return response()->json(['insights' => $insights, 'context' => $context]);
    }
}
