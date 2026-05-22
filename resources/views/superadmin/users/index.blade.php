@extends('superadmin.layouts.app')
@section('title', 'Platform Users')
@section('breadcrumb', 'Users')

@section('content')
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-xl font-bold text-gray-900 dark:text-white">Platform Users</h1>
            <p class="text-sm text-gray-500 mt-1">{{ $total }} total users across all organizations</p>
        </div>
    </div>

    <form method="GET" action="/superadmin/users" class="flex flex-wrap gap-3">
        <input type="text" name="search" value="{{ $search }}" placeholder="Search by name or email..."
               class="flex-1 min-w-48 px-3 py-2 rounded-xl border border-gray-200 dark:border-white/10 bg-white dark:bg-[#0f0f1a] text-gray-900 dark:text-white placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-indigo-500 text-sm">
        @if($search)
            <a href="/superadmin/users" class="px-3 py-2 rounded-xl border border-gray-200 dark:border-white/10 text-gray-500 text-sm hover:bg-gray-50 dark:hover:bg-white/5 transition-colors">Clear</a>
        @endif
    </form>

    <div class="bg-white dark:bg-[#0f0f1a] rounded-2xl border border-gray-100 dark:border-white/5 overflow-hidden">
        <table class="w-full text-sm">
            <thead>
                <tr class="border-b border-gray-100 dark:border-white/5">
                    <th class="px-5 py-3 text-left text-xs font-semibold text-gray-400 uppercase tracking-wide">User</th>
                    <th class="px-5 py-3 text-left text-xs font-semibold text-gray-400 uppercase tracking-wide">Email</th>
                    <th class="px-5 py-3 text-left text-xs font-semibold text-gray-400 uppercase tracking-wide">Organization</th>
                    <th class="px-5 py-3 text-left text-xs font-semibold text-gray-400 uppercase tracking-wide">Role</th>
                    <th class="px-5 py-3 text-left text-xs font-semibold text-gray-400 uppercase tracking-wide">Joined</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-50 dark:divide-white/3">
                @forelse($users as $user)
                    @php $company = collect($companies)->firstWhere('id', $user->default_company_id ?? null); @endphp
                    <tr class="hover:bg-gray-50 dark:hover:bg-white/2 transition-colors">
                        <td class="px-5 py-3">
                            <div class="flex items-center gap-2.5">
                                <div class="w-7 h-7 rounded-full bg-indigo-100 dark:bg-indigo-900/30 flex items-center justify-center text-indigo-600 dark:text-indigo-400 text-xs font-bold flex-shrink-0">
                                    {{ strtoupper(substr($user->name, 0, 1)) }}
                                </div>
                                <div>
                                    <p class="font-medium text-gray-900 dark:text-white">{{ $user->name }}</p>
                                    @if(property_exists($user, 'is_superadmin') && $user->is_superadmin)
                                        <span class="text-xs text-indigo-500 font-medium">Super Admin</span>
                                    @endif
                                </div>
                            </div>
                        </td>
                        <td class="px-5 py-3 text-gray-500 dark:text-gray-400 text-xs">{{ $user->email }}</td>
                        <td class="px-5 py-3 text-gray-500 dark:text-gray-400 text-xs">{{ $company?->name ?? '—' }}</td>
                        <td class="px-5 py-3 text-gray-400 text-xs capitalize">
                            @if(isset($user->is_superadmin) && $user->is_superadmin)
                                <span class="inline-flex px-2 py-0.5 rounded-full text-xs bg-indigo-100 text-indigo-700 dark:bg-indigo-900/30 dark:text-indigo-400 font-medium">Super Admin</span>
                            @else
                                <span class="text-gray-400">Tenant User</span>
                            @endif
                        </td>
                        <td class="px-5 py-3 text-gray-400 text-xs">{{ \Carbon\Carbon::parse($user->created_at)->format('M d, Y') }}</td>
                    </tr>
                @empty
                    <tr><td colspan="5" class="px-5 py-8 text-center text-gray-400">No users found.</td></tr>
                @endforelse
            </tbody>
        </table>
        @if($users->hasPages())
            <div class="px-5 py-3 border-t border-gray-100 dark:border-white/5">{{ $users->links() }}</div>
        @endif
    </div>
@endsection
