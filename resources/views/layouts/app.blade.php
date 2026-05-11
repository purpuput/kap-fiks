<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Laravel') }}</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

    <!-- Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="font-sans antialiased bg-gray-100">

    <div class="flex flex-col md:flex-row">

        <!-- Sidebar -->
        @include('layouts.sidebar')

        <!-- Main Content -->
        <div class="flex-1 md:ml-64 min-h-screen transition-all duration-300">

            <!-- Header -->
            <header class="bg-white shadow-sm border-b border-gray-200 px-6 py-4">
                <div class="flex items-center justify-between">
                    <div>
                        <h1 class="text-xl font-bold text-gray-800">
                            {{ $title ?? ($header ?? 'Dashboard') }}
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
                    <div id="realtime-clock" class="text-xs text-gray-400"></div>

<div id="realtime-date" class="text-xs text-gray-400"></div>

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
    </script>

</body>

</html>