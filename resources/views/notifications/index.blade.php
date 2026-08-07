<x-app-layout title="Daftar Notifikasi">
    <div class="py-4 max-w-5xl mx-auto space-y-6">
        <!-- Header -->
        <div class="bg-white dark:bg-slate-800 rounded-xl p-6 shadow-sm border border-gray-200 dark:border-slate-700 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <h2 class="text-xl font-bold text-gray-800 dark:text-gray-100 flex items-center gap-2">
                    🔔 Pusat Notifikasi
                </h2>
                <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">
                    Daftar pemberitahuan aktivitas review logbook, laporan audit, dan penugasan tim.
                </p>
            </div>
            @if(auth()->user()->unreadNotifications->count() > 0)
                <form action="{{ route('notifications.read_all') }}" method="POST">
                    @csrf
                    <button type="submit" class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white font-medium text-xs rounded-lg transition-colors shadow-sm flex items-center gap-2">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                        Tandai Semua Dibaca
                    </button>
                </form>
            @endif
        </div>

        <!-- List Notifikasi -->
        <div class="bg-white dark:bg-slate-800 rounded-xl shadow-sm border border-gray-200 dark:border-slate-700 overflow-hidden divide-y divide-gray-100 dark:divide-slate-700">
            @forelse($notifications as $notification)
                @php
                    $data = $notification->data;
                    $isRead = $notification->read_at !== null;
                    $icon = $data['icon'] ?? 'bell';
                @endphp
                <div class="p-4 sm:p-5 flex items-start gap-4 transition hover:bg-gray-50 dark:hover:bg-slate-700/40 {{ $isRead ? 'opacity-75' : 'bg-blue-50/50 dark:bg-slate-700/30' }}">
                    <div class="mt-1 shrink-0">
                        @if(in_array($icon, ['check-circle', 'check-badge']))
                            <span class="p-2.5 bg-green-100 dark:bg-green-900/50 text-green-600 dark:text-green-400 rounded-xl inline-flex items-center justify-center">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                            </span>
                        @elseif(in_array($icon, ['exclamation-circle', 'exclamation-triangle']))
                            <span class="p-2.5 bg-red-100 dark:bg-red-900/50 text-red-600 dark:text-red-400 rounded-xl inline-flex items-center justify-center">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                            </span>
                        @elseif($icon === 'user-group')
                            <span class="p-2.5 bg-purple-100 dark:bg-purple-900/50 text-purple-600 dark:text-purple-400 rounded-xl inline-flex items-center justify-center">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5-3.87M9 20H4v-2a3 3 0 015-3.87m6-4a4 4 0 11-8 0 4 4 0 018 0z"/></svg>
                            </span>
                        @else
                            <span class="p-2.5 bg-blue-100 dark:bg-blue-900/50 text-blue-600 dark:text-blue-400 rounded-xl inline-flex items-center justify-center">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                            </span>
                        @endif
                    </div>

                    <div class="flex-1 min-w-0">
                        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-1">
                            <h3 class="text-sm font-bold text-gray-800 dark:text-gray-100 flex items-center gap-2">
                                {{ $data['title'] ?? 'Notifikasi' }}
                                @if(!$isRead)
                                    <span class="inline-block w-2 h-2 rounded-full bg-blue-600"></span>
                                @endif
                            </h3>
                            <span class="text-xs text-gray-400 dark:text-gray-500">
                                {{ $notification->created_at->diffForHumans() }}
                            </span>
                        </div>
                        <p class="text-sm text-gray-600 dark:text-gray-300 mt-1">
                            {{ $data['message'] ?? '' }}
                        </p>

                        @if(!empty($data['url']))
                            <div class="mt-3">
                                <a href="{{ route('notifications.read', $notification->id) }}" class="inline-flex items-center gap-1.5 text-xs font-semibold text-blue-600 dark:text-blue-400 hover:text-blue-800 dark:hover:text-blue-300">
                                    Buka Halaman Terkait
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"/></svg>
                                </a>
                            </div>
                        @endif
                    </div>
                </div>
            @empty
                <div class="p-12 text-center">
                    <svg class="w-12 h-12 mx-auto text-gray-400 dark:text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" />
                    </svg>
                    <p class="text-gray-500 dark:text-gray-400 mt-3 text-sm font-medium">Belum ada notifikasi.</p>
                </div>
            @endforelse
        </div>

        <!-- Pagination -->
        @if($notifications->hasPages())
            <div class="pt-2">
                {{ $notifications->links() }}
            </div>
        @endif
    </div>
</x-app-layout>
