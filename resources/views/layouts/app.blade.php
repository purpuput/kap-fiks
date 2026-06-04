<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ ($attributes->get('title') ?? ($title ?? '')) ? ($attributes->get('title') ?? $title) . ' - ' : '' }}{{ config('app.name', 'KAP EKO PRASETYO') }}</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

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
        <div class="flex-1 min-h-screen transition-all duration-300"
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
            toast: true,
            position: 'top-end',
            icon: 'success',
            title: "{{ session('success') }}",
            showConfirmButton: false,
            timer: 3000,
            timerProgressBar: true
        });
    </script>
    @endif

    @if(session('error'))
    <script>
        Swal.fire({
            toast: true,
            position: 'top-end',
            icon: 'error',
            title: "{{ session('error') }}",
            showConfirmButton: false,
            timer: 3000,
            timerProgressBar: true
        });
    </script>
    @endif

    @if($errors->any())
    <script>
        Swal.fire({
            toast: true,
            position: 'top-end',
            icon: 'error',
            title: "Ada kesalahan pada input form!",
            text: "{{ $errors->first() }}",
            showConfirmButton: false,
            timer: 4000,
            timerProgressBar: true
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