<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ ($attributes->get('title') ?? ($title ?? '')) ? ($attributes->get('title') ?? $title) . ' - ' : '' }}{{ config('app.name', 'KAP SISTEM') }}</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

    <!-- Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="font-sans antialiased bg-gray-100">

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
            <header class="bg-white shadow-sm border-b border-gray-200 px-6 py-4">
                <div class="flex items-center justify-between">
                    <div class="flex items-center">
                        <button @click="sidebarOpen = !sidebarOpen" class="mr-3 md:hidden text-gray-500 hover:text-gray-700 focus:outline-none">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                            </svg>
                        </button>
                        <div>
                            <h1 class="text-xl font-bold text-gray-800">
                                {{ $attributes->get('title') ?? ($title ?? ($header ?? 'Dashboard')) }}
                            </h1>
                            @isset($subtitle)
                            <p class="text-sm text-gray-500 mt-0.5">{{ $subtitle }}</p>
                            @else
                            <p class="text-sm text-gray-500 mt-0.5">
                                Selamat datang, <span class="font-semibold text-blue-600">{{ auth()->user()->name }}</span>
                                — <span class="capitalize">{{ auth()->user()->role }}</span>
                            </p>
                            @endisset
                        </div>
                    </div>
                    <div id="realtime-clock" class="hidden sm:block text-xs text-gray-400"></div>
                    <div id="realtime-date" class="hidden sm:block text-xs text-gray-400"></div>

<script>
    function updateDate() {
        const now = new Date();

        const tanggal = now.toLocaleDateString('id-ID', {
            weekday: 'long',
            day: '2-digit',
            month: 'long',
            year: 'numeric'
        });

        document.getElementById('realtime-date').innerHTML = tanggal;
    }

    updateDate();
</script>
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

    @stack('scripts')
</body>

</html>