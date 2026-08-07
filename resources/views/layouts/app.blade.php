<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ ($attributes->get('title') ?? ($title ?? '')) ? ($attributes->get('title') ?? $title) . ' - ' : '' }}{{ config('app.name', 'KAP EKO PRASETYO') }}</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:ital,wght@0,300;0,400;0,500;0,600;0,700;0,800;1,400&display=swap" rel="stylesheet">

    <!-- Logo -->
    <link rel="icon" type="image/svg+xml" href="data:image/svg+xml,<svg xmlns='http://www.w3.org/2000/svg' fill='none' stroke='%232563eb' stroke-width='2' viewBox='0 0 24 24'><path stroke-linecap='round' stroke-linejoin='round' d='M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z'/></svg>">

    <!-- Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <!-- Darkmode -->
    <script>
        if (localStorage.getItem('theme') === 'dark' || (!('theme' in localStorage) && window.matchMedia('(prefers-color-scheme: dark)').matches)) {
            document.documentElement.classList.add('dark');
        } else {
            document.documentElement.classList.remove('dark');
        }
    </script>
</head>

<body class="font-sans antialiased bg-gray-100 dark:bg-slate-900 text-gray-800 dark:text-gray-100 transition-colors duration-200">

    <div class="flex flex-col md:flex-row" x-data="{ sidebarOpen: window.innerWidth >= 768 ? (localStorage.getItem('sidebarOpen') !== 'false') : false }">

        <!-- Mobile Sidebar Overlay -->
        <div x-show="sidebarOpen" @click="sidebarOpen = false"
             x-transition:enter="transition-opacity ease-linear duration-300"
             x-transition:enter-start="opacity-0"
             x-transition:enter-end="opacity-100"
             x-transition:leave="transition-opacity ease-linear duration-300"
             x-transition:leave-start="opacity-100"
             x-transition:leave-end="opacity-0"
             class="fixed inset-0 z-40 bg-slate-900/50 backdrop-blur-sm md:hidden"></div>

        <!-- Sidebar -->
        @include('layouts.sidebar')

        <!-- Main Content -->
        <div class="flex-1 min-w-0 min-h-screen transition-all duration-300"
            :class="sidebarOpen ? 'md:ml-64' : 'md:ml-20'">

            <!-- Header -->
            <header class="sticky top-0 z-30 bg-white/90 dark:bg-slate-800/90 backdrop-blur-sm shadow-sm border-b border-gray-200 dark:border-slate-700 px-6 py-4">
                <div class="flex items-center justify-between">
                    <div class="flex items-center">
                        <button @click="sidebarOpen = !sidebarOpen" class="mr-3 md:hidden text-gray-500 hover:text-gray-700 dark:text-gray-400 focus:outline-none">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                            </svg>
                        </button>
                        @if(!request()->routeIs('dashboard', '*.dashboard'))
                        <div>
                            <h1 class="text-xl font-bold text-gray-800 dark:text-gray-100">
                                {{ $attributes->get('title') ?? ($title ?? ($header ?? 'Dashboard')) }}
                            </h1>
                            @isset($subtitle)
                            <p class="text-sm text-gray-500 dark:text-gray-400 mt-0.5">{{ $subtitle }}</p>
                            @else
                            <p class="text-sm text-gray-500 dark:text-gray-400 mt-0.5">
                                Selamat datang, <span class="font-semibold text-blue-600 dark:text-blue-400">{{ auth()->user()->name }}</span>
                            </p>
                            @endisset
                        </div>
                        @endif
                    </div>
                    
                    <div class="flex items-center gap-4">
                        <div class="hidden sm:flex flex-col text-right">
                            <div id="realtime-clock" class="text-xs font-semibold text-gray-600 dark:text-gray-300"></div>
                            <div id="realtime-date" class="text-[10px] text-gray-500 dark:text-gray-400"></div>
                        </div>

                        <!-- Notification Bell Dropdown -->
                        <div x-data="{
                            open: false,
                            unreadCount: 0,
                            notifications: [],
                            fetchNotifications() {
                                fetch('{{ route('notifications.unread_data') }}')
                                    .then(res => res.json())
                                    .then(data => {
                                        this.unreadCount = data.unread_count;
                                        this.notifications = data.recent;
                                    })
                                    .catch(err => console.error(err));
                            }
                        }" x-init="fetchNotifications(); setInterval(() => fetchNotifications(), 30000)" class="relative">
                            <button @click="open = !open" type="button" class="relative text-gray-500 dark:text-gray-400 hover:bg-gray-100 dark:hover:bg-slate-700 focus:outline-none rounded-lg text-sm p-2.5 transition-colors">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" />
                                </svg>
                                <span x-show="unreadCount > 0" x-text="unreadCount > 99 ? '99+' : unreadCount" class="absolute -top-1 -right-1 inline-flex items-center justify-center px-1.5 py-0.5 text-xs font-bold leading-none text-white bg-red-600 rounded-full animate-pulse"></span>
                            </button>

                            <!-- Dropdown menu -->
                            <div x-show="open" @click.away="open = false" x-transition class="absolute right-0 mt-2 w-80 sm:w-96 bg-white dark:bg-slate-800 rounded-xl shadow-xl border border-gray-200 dark:border-slate-700 z-50 overflow-hidden" style="display: none;">
                                <div class="p-3 bg-gray-50 dark:bg-slate-700/50 border-b border-gray-200 dark:border-slate-700 flex items-center justify-between">
                                    <span class="font-semibold text-sm text-gray-800 dark:text-gray-200 flex items-center gap-2">
                                        📢 Notifikasi
                                        <span x-show="unreadCount > 0" class="text-xs bg-blue-100 dark:bg-blue-900/40 text-blue-600 dark:text-blue-400 font-bold px-2 py-0.5 rounded-full" x-text="unreadCount + ' Baru'"></span>
                                    </span>
                                    <template x-if="unreadCount > 0">
                                        <form action="{{ route('notifications.read_all') }}" method="POST" class="inline">
                                            @csrf
                                            <button type="submit" class="text-xs text-blue-600 dark:text-blue-400 hover:underline">Tandai Dibaca</button>
                                        </form>
                                    </template>
                                </div>

                                <div class="max-h-80 overflow-y-auto divide-y divide-gray-100 dark:divide-slate-700">
                                    <template x-if="notifications.length === 0">
                                        <div class="p-6 text-center text-xs text-gray-500 dark:text-gray-400">
                                            Tidak ada notifikasi.
                                        </div>
                                    </template>

                                    <template x-for="item in notifications" :key="item.id">
                                        <a :href="item.url" class="block p-3 hover:bg-gray-50 dark:hover:bg-slate-700/40 transition" :class="item.read ? 'opacity-70' : 'bg-blue-50/40 dark:bg-slate-700/20'">
                                            <div class="flex items-start gap-3">
                                                <div class="mt-0.5 shrink-0">
                                                    <template x-if="item.icon === 'check-circle' || item.icon === 'check-badge'">
                                                        <span class="p-1.5 bg-green-100 dark:bg-green-900/40 text-green-600 dark:text-green-400 rounded-lg inline-block">
                                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                                                        </span>
                                                    </template>
                                                    <template x-if="item.icon === 'exclamation-circle' || item.icon === 'exclamation-triangle'">
                                                        <span class="p-1.5 bg-red-100 dark:bg-red-900/40 text-red-600 dark:text-red-400 rounded-lg inline-block">
                                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                                                        </span>
                                                    </template>
                                                    <template x-if="item.icon !== 'check-circle' && item.icon !== 'check-badge' && item.icon !== 'exclamation-circle' && item.icon !== 'exclamation-triangle'">
                                                        <span class="p-1.5 bg-blue-100 dark:bg-blue-900/40 text-blue-600 dark:text-blue-400 rounded-lg inline-block">
                                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                                        </span>
                                                    </template>
                                                </div>
                                                <div class="flex-1 min-w-0">
                                                    <p class="text-xs font-semibold text-gray-800 dark:text-gray-200 truncate" x-text="item.title"></p>
                                                    <p class="text-xs text-gray-600 dark:text-gray-300 line-clamp-2 mt-0.5" x-text="item.message"></p>
                                                    <span class="text-[10px] text-gray-400 dark:text-gray-500 mt-1 block" x-text="item.created_at"></span>
                                                </div>
                                            </div>
                                        </a>
                                    </template>
                                </div>

                                <div class="p-2 bg-gray-50 dark:bg-slate-700/50 border-t border-gray-200 dark:border-slate-700 text-center">
                                    <a href="{{ route('notifications.index') }}" class="text-xs font-medium text-blue-600 dark:text-blue-400 hover:underline">Lihat Semua Notifikasi &rarr;</a>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Dark Mode Toggle Button -->
                        <button id="theme-toggle" type="button" class="text-gray-500 dark:text-gray-400 hover:bg-gray-100 dark:hover:bg-slate-700 focus:outline-none focus:ring-4 focus:ring-gray-200 dark:focus:ring-slate-700 rounded-lg text-sm p-2.5 transition-colors">
                            <svg id="theme-toggle-dark-icon" class="hidden w-5 h-5" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg"><path d="M17.293 13.293A8 8 0 016.707 2.707a8.001 8.001 0 1010.586 10.586z"></path></svg>
                            <svg id="theme-toggle-light-icon" class="hidden w-5 h-5" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg"><path d="M10 2a1 1 0 011 1v1a1 1 0 11-2 0V3a1 1 0 011-1zm4 8a4 4 0 11-8 0 4 4 0 018 0zm-.464 4.95l.707.707a1 1 0 001.414-1.414l-.707-.707a1 1 0 00-1.414 1.414zm2.12-10.607a1 1 0 010 1.414l-.706.707a1 1 0 11-1.414-1.414l.707-.707a1 1 0 011.414 0zM17 11a1 1 0 100-2h-1a1 1 0 100 2h1zm-7 4a1 1 0 011 1v1a1 1 0 11-2 0v-1a1 1 0 011-1zM5.05 6.464A1 1 0 106.46 5.05l-.707-.707a1 1 0 00-1.414 1.414l.707.707zm1.414 8.486l-.707.707a1 1 0 01-1.414-1.414l.707-.707a1 1 0 011.414 1.414zM4 11a1 1 0 100-2H3a1 1 0 000 2h1z" fill-rule="evenodd" clip-rule="evenodd"></path></svg>
                        </button>
                    </div>
                </div>
            </header>

            <!-- Content -->
            <main class="p-6">
                {{ $slot ?? '' }}
            </main>

        </div>

    </div>

    <!-- SweetAlert -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    @if(session('success'))
    <script>
        Swal.fire({
            icon: 'success',
            title: 'Berhasil!',
            text: {!! json_encode(session('success')) !!},
            showConfirmButton: false,
            timer: 2000,
            timerProgressBar: true
        });
    </script>
    @endif

    @if(session('error'))
    <script>
        Swal.fire({
            icon: 'error',
            title: 'Gagal!',
            text: {!! json_encode(session('error')) !!},
            showConfirmButton: true,
            confirmButtonColor: '#3085d6'
        });
    </script>
    @endif

    @if($errors->any())
    <script>
        Swal.fire({
            icon: 'error',
            title: 'Ada Kesalahan!',
            html: {!! json_encode('<ul class="text-left list-disc list-inside">' . implode('', array_map(fn($err) => '<li>' . e($err) . '</li>', $errors->all())) . '</ul>') !!},
            showConfirmButton: true,
            confirmButtonColor: '#3085d6'
        });
    </script>
    @endif

    <script>
        function confirmLogout(form) {
            Swal.fire({
                title: 'Yakin ingin logout?',
                text: 'Sesi login akan berakhir',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#dc2626',
                cancelButtonColor: '#6b7280',
                confirmButtonText: 'Ya, logout!',
                cancelButtonText: 'Batal'
            }).then((result) => {
                if (result.isConfirmed) {
                    form.submit();
                }
            });
        }

        function confirmDelete(form, message = 'Data yang dihapus tidak dapat dikembalikan!') {
            Swal.fire({
                title: 'Yakin ingin menghapus?',
                text: message,
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#dc2626',
                cancelButtonColor: '#6b7280',
                confirmButtonText: 'Ya, hapus!',
                cancelButtonText: 'Batal'
            }).then((result) => {
                if (result.isConfirmed) {
                    form.submit();
                }
            });
        }
    </script>

    <script>
        // dark mode toggle
        const themeToggle = document.getElementById('theme-toggle');
        const themeToggleDarkIcon = document.getElementById('theme-toggle-dark-icon');
        const themeToggleLightIcon = document.getElementById('theme-toggle-light-icon');
        const html = document.documentElement;

        // Set initial icon state based on class 'dark'
        if (html.classList.contains('dark')) {
            themeToggleLightIcon?.classList.remove('hidden');
            themeToggleDarkIcon?.classList.add('hidden');
        } else {
            themeToggleDarkIcon?.classList.remove('hidden');
            themeToggleLightIcon?.classList.add('hidden');
        }

        themeToggle?.addEventListener('click', () => {
            // Toggle classes
            html.classList.toggle('dark');
            
            // Toggle icons
            themeToggleDarkIcon?.classList.toggle('hidden');
            themeToggleLightIcon?.classList.toggle('hidden');
            
            // Save preference to localStorage
            if (html.classList.contains('dark')) {
                localStorage.setItem('theme', 'dark');
            } else {
                localStorage.setItem('theme', 'light');
            }
        });
    </script>

    @stack('scripts')
</body>

</html>