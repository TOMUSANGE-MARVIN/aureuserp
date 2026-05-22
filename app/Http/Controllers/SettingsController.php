<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Webkul\Security\Models\Invitation;
use Webkul\Security\Mail\UserInvitationMail;

class SettingsController extends Controller
{
    /**
     * All available ERP modules with labels and sidebar match keys.
     */
    public static function modules(): array
    {
        return [
            'dashboard'     => ['label' => 'Dashboard Overview',  'group' => 'General'],
            'contacts'      => ['label' => 'Contacts',            'group' => 'Sales'],
            'sales'         => ['label' => 'Sales',               'group' => 'Sales'],
            'purchases'     => ['label' => 'Purchases',           'group' => 'Sales'],
            'products'      => ['label' => 'Products',            'group' => 'Operations'],
            'inventory'     => ['label' => 'Inventory',           'group' => 'Operations'],
            'manufacturing' => ['label' => 'Manufacturing',       'group' => 'Operations'],
            'employees'     => ['label' => 'Employees',           'group' => 'People'],
            'projects'      => ['label' => 'Projects',            'group' => 'People'],
            'recruitment'   => ['label' => 'Recruitment',         'group' => 'People'],
            'time_off'      => ['label' => 'Time Off',            'group' => 'People'],
            'accounting'    => ['label' => 'Accounting',          'group' => 'Finance'],
            'payroll'       => ['label' => 'Payroll',             'group' => 'Finance'],
            'website'       => ['label' => 'Website',             'group' => 'Digital'],
            'helpdesk'      => ['label' => 'Helpdesk',            'group' => 'Digital'],
            'settings'      => ['label' => 'Settings (Admin)',    'group' => 'System'],
        ];
    }

    public function index()
    {
        $company = DB::table('companies')->first();
        $currencies = DB::table('currencies')->orderBy('name')->get();
        $stats = [
            'users'      => DB::table('users')->count(),
            'roles'      => DB::table('roles')->count(),
            'currencies' => DB::table('currencies')->where('active', 1)->count(),
        ];
        return view('app.settings.index', compact('stats', 'company', 'currencies'));
    }

    public function updateGeneral(Request $request)
    {
        $request->validate(['default_currency_id' => 'required|exists:currencies,id']);
        // Activate the selected currency if not already active
        DB::table('currencies')->where('id', $request->default_currency_id)->update(['active' => 1]);
        DB::table('companies')->where('id', 1)->update([
            'currency_id' => $request->default_currency_id,
            'updated_at'  => now(),
        ]);
        session()->flash('success', 'Default currency updated.');
        return redirect('/app/settings');
    }

    public function users(Request $request)
    {
        $search = $request->get('search', '');
        $query = DB::table('users')
            ->leftJoin('partners_partners', 'partners_partners.id', '=', 'users.partner_id')
            ->select('users.*', 'partners_partners.name as partner_name');

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('users.name', 'like', "%$search%")
                  ->orWhere('users.email', 'like', "%$search%");
            });
        }

        $users = $query->orderByDesc('users.created_at')->paginate(20)->withQueryString();
        return view('app.settings.users', compact('users', 'search'));
    }

    public function createUser()
    {
        $modules = self::modules();
        return view('app.settings.create-user', compact('modules'));
    }

    public function storeUser(Request $request)
    {
        $request->validate([
            'name'     => 'required|string|max:255',
            'email'    => 'required|email|unique:users,email',
            'password' => 'required|string|min:8|confirmed',
        ]);

        $userId = DB::table('users')->insertGetId([
            'name'       => $request->name,
            'email'      => $request->email,
            'password'   => Hash::make($request->password),
            'is_active'  => 1,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $this->syncModuleAccess($userId, $request->input('modules', []));

        session()->flash('success', 'User created successfully.');
        return redirect('/app/settings/users');
    }

    public function editUser($id)
    {
        $user = DB::table('users')->where('id', $id)->first();
        abort_if(!$user, 404);

        $userModules = DB::table('user_module_access')
            ->where('user_id', $id)
            ->pluck('module')
            ->toArray();

        $modules = self::modules();
        return view('app.settings.edit-user', compact('user', 'userModules', 'modules'));
    }

    public function updateUser(Request $request, $id)
    {
        $user = DB::table('users')->where('id', $id)->first();
        abort_if(!$user, 404);

        $request->validate([
            'name'     => 'required|string|max:255',
            'email'    => 'required|email|unique:users,email,'. $id,
            'password' => 'nullable|string|min:8|confirmed',
        ]);

        $data = [
            'name'       => $request->name,
            'email'      => $request->email,
            'updated_at' => now(),
        ];
        if ($request->filled('password')) {
            $data['password'] = Hash::make($request->password);
        }

        DB::table('users')->where('id', $id)->update($data);
        $this->syncModuleAccess($id, $request->input('modules', []));

        session()->flash('success', 'User updated successfully.');
        return redirect('/app/settings/users');
    }

    public function destroyUser($id)
    {
        DB::table('user_module_access')->where('user_id', $id)->delete();
        DB::table('users')->where('id', $id)->delete();
        session()->flash('success', 'User deleted.');
        return redirect('/app/settings/users');
    }

    public function inviteUser(Request $request)
    {
        $request->validate([
            'email' => 'required|email|unique:users,email|unique:user_invitations,email',
        ], [
            'email.unique' => 'An invitation has already been sent to this email address, or a user with this email already exists.',
        ]);

        $invitation = Invitation::create(['email' => $request->email]);

        try {
            Mail::to($invitation->email)->send(new UserInvitationMail($invitation));
            session()->flash('success', "Invitation sent to {$request->email}.");
        } catch (\Exception $e) {
            $invitation->delete();
            session()->flash('error', 'Failed to send invitation email. Please check your mail configuration.');
        }

        return redirect('/app/settings/users/create?tab=invite');
    }

    private function syncModuleAccess(int $userId, array $modules): void
    {
        DB::table('user_module_access')->where('user_id', $userId)->delete();
        $rows = array_map(fn($m) => [
            'user_id'    => $userId,
            'module'     => $m,
            'created_at' => now(),
            'updated_at' => now(),
        ], array_filter($modules, fn($m) => array_key_exists($m, self::modules())));

        if ($rows) {
            DB::table('user_module_access')->insert(array_values($rows));
        }
    }

    public function roles()
    {
        $roles = DB::table('roles')->orderBy('name')->get();
        return view('app.settings.roles', compact('roles'));
    }

    public function activityTypes()
    {
        $types = DB::table('activity_types')->orderBy('name')->get();
        return view('app.settings.activity-types', compact('types'));
    }

    public function currencies()
    {
        $currencies = DB::table('currencies')->orderBy('name')->get();
        return view('app.settings.currencies', compact('currencies'));
    }

    public function editCurrency($id)
    {
        $currency = DB::table('currencies')->where('id', $id)->first();
        abort_if(!$currency, 404);
        return view('app.settings.edit-currency', compact('currency'));
    }

    public function updateCurrency(Request $request, $id)
    {
        $currency = DB::table('currencies')->where('id', $id)->first();
        abort_if(!$currency, 404);

        $request->validate([
            'rounding' => 'nullable|numeric|min:0',
        ]);

        DB::table('currencies')->where('id', $id)->update([
            'active'     => $request->boolean('active'),
            'rounding'   => $request->input('rounding', 0.01),
            'updated_at' => now(),
        ]);

        session()->flash('success', 'Currency updated.');
        return redirect()->route('settings.currencies');
    }
}
