@extends('app.layouts.app')
@section('title', 'Notifications')

@section('content')
<div class="max-w-3xl mx-auto space-y-4">

    <!-- Header -->
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-xl font-bold text-gray-900 dark:text-white">Notifications</h1>
            <p class="text-sm text-gray-500 dark:text-gray-400 mt-0.5">All your recent alerts and updates</p>
        </div>
        <div class="flex gap-2">
            <button onclick="clearReadNotifications()"
                    class="text-sm px-3 py-1.5 rounded-lg bg-gray-100 dark:bg-white/5 text-gray-600 dark:text-gray-300 hover:bg-gray-200 dark:hover:bg-white/10 transition-colors">
                Clear read
            </button>
        </div>
    </div>

    <!-- List -->
    <div class="bg-white dark:bg-[#13131f] rounded-2xl border border-gray-100 dark:border-white/8 divide-y divide-gray-50 dark:divide-white/5" id="notif-list">
        @forelse ($notifications as $notif)
            <div class="flex items-start gap-4 px-5 py-4 {{ is_null($notif->read_at) ? 'bg-brand-50/40 dark:bg-violet-900/10' : '' }} hover:bg-gray-50 dark:hover:bg-white/3 transition-colors group"
                 id="notif-{{ $notif->id }}">

                <!-- Icon -->
                <div class="w-9 h-9 rounded-full flex items-center justify-center flex-shrink-0 mt-0.5
                    {{ match($notif->type) {
                        'ticket'   => 'bg-blue-100 dark:bg-blue-900/30 text-blue-600 dark:text-blue-400',
                        'payroll'  => 'bg-green-100 dark:bg-green-900/30 text-green-600 dark:text-green-400',
                        'hr'       => 'bg-purple-100 dark:bg-purple-900/30 text-purple-600 dark:text-purple-400',
                        'alert'    => 'bg-red-100 dark:bg-red-900/30 text-red-600 dark:text-red-400',
                        default    => 'bg-gray-100 dark:bg-white/8 text-gray-500 dark:text-gray-400',
                    } }}">
                    @if($notif->type === 'ticket')
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 5v2m0 4v2m0 4v2M5 5a2 2 0 00-2 2v3a2 2 0 110 4v3a2 2 0 002 2h14a2 2 0 002-2v-3a2 2 0 110-4V7a2 2 0 00-2-2H5z"/></svg>
                    @elseif($notif->type === 'payroll')
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1"/></svg>
                    @elseif($notif->type === 'hr')
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                    @elseif($notif->type === 'alert')
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                    @else
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/></svg>
                    @endif
                </div>

                <!-- Content -->
                <div class="flex-1 min-w-0">
                    <div class="flex items-start justify-between gap-2">
                        <p class="text-sm font-semibold text-gray-900 dark:text-white {{ is_null($notif->read_at) ? '' : 'font-medium' }} truncate">
                            {{ $notif->title }}
                        </p>
                        <div class="flex items-center gap-1.5 flex-shrink-0">
                            @if(is_null($notif->read_at))
                                <span class="w-2 h-2 rounded-full bg-brand-500 flex-shrink-0"></span>
                            @endif
                            <span class="text-xs text-gray-400">
                                {{ \Carbon\Carbon::parse($notif->created_at)->diffForHumans() }}
                            </span>
                        </div>
                    </div>
                    @if($notif->body)
                        <p class="text-sm text-gray-500 dark:text-gray-400 mt-0.5 line-clamp-2">{{ $notif->body }}</p>
                    @endif
                    @if($notif->url)
                        <a href="{{ $notif->url }}" class="text-xs text-brand-600 dark:text-brand-400 hover:underline mt-1 inline-block">View →</a>
                    @endif
                </div>

                <!-- Delete btn -->
                <button onclick="deleteNotification({{ $notif->id }})"
                        class="opacity-0 group-hover:opacity-100 p-1 rounded hover:bg-gray-100 dark:hover:bg-white/10 text-gray-400 hover:text-red-500 transition-all flex-shrink-0">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>
        @empty
            <div class="text-center py-16">
                <svg class="w-12 h-12 mx-auto text-gray-300 dark:text-gray-600 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/>
                </svg>
                <p class="text-gray-400 dark:text-gray-500 text-sm">No notifications yet</p>
            </div>
        @endforelse
    </div>

    <!-- Pagination -->
    @if($notifications->hasPages())
        <div class="flex justify-center">
            {{ $notifications->links() }}
        </div>
    @endif

</div>
@endsection

@section('scripts')
<script>
function deleteNotification(id) {
    fetch('/app/notifications/' + id, {
        method: 'DELETE',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
            'Accept': 'application/json',
        }
    }).then(() => {
        const el = document.getElementById('notif-' + id);
        if (el) el.remove();
    });
}

function clearReadNotifications() {
    fetch('/app/notifications/clear-all', {
        method: 'DELETE',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
            'Accept': 'application/json',
        }
    }).then(() => location.reload());
}
</script>
@endsection
