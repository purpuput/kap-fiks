<!-- Sidebar -->
<div x-data="{ open: true }" class="flex h-screen bg-gray-100">
    <!-- Sidebar -->
    <div :class="open ? 'w-64' : 'w-20'" class="bg-gray-800 text-white transition-all duration-300 flex flex-col">
        <!-- Header Sidebar -->
        <div class="flex items-center justify-between h-16 px-4 border-b border-gray-700">
            <div x-show="open" class="flex items-center">
                <x-application-logo class="block h-8 w-auto fill-current text-white" />
                <span class="ml-2 font-bold">KAP</span>
            </div>
            <button @click="open = !open" class="text-gray-300 hover:text-white">
                <svg x-show="open" class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                </svg>
                <svg x-show="!open" class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                </svg>
            </button>
        </div>

        <!-- Navigation Links -->
        <nav class="flex-1 px-2 py-4 space-y-2">
            <!-- Dashboard Link -->
            <a href="{{ route('dashboard') }}" class="flex items-center px-4 py-2 rounded-lg transition duration-200 {{ request()->routeIs('dashboard') ? 'bg-gray-700' : 'hover:bg-gray-700' }}">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-3m0 0l7-4 7 4M5 9v10a1 1 0 001 1h12a1 1 0 001-1V9m-9 11l4-2m0 0l4 2m-4-2V5" />
                </svg>
                <span x-show="open" class="ml-4">Dashboard</span>
            </a>

            <!-- Absensi - Semua Role -->
            <a href="{{ route('attendances.index') }}" class="flex items-center px-4 py-2 rounded-lg transition duration-200 {{ request()->routeIs('attendances.*') ? 'bg-gray-700' : 'hover:bg-gray-700' }}">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01" />
                </svg>
                <span x-show="open" class="ml-4">Absensi</span>
            </a>

            <!-- Logbook - Admin, Auditor, Pimpinan -->
            @if(in_array(auth()->user()->role, ['admin', 'auditor', 'pimpinan']))
            <a href="{{ route('logbooks.index') }}" class="flex items-center px-4 py-2 rounded-lg transition duration-200 {{ request()->routeIs('logbooks.*') ? 'bg-gray-700' : 'hover:bg-gray-700' }}">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                </svg>
                <span x-show="open" class="ml-4">Logbook</span>
            </a>
            @endif

            <!-- Data Klien - Admin, Auditor, Pimpinan -->
            @if(in_array(auth()->user()->role, ['admin', 'auditor', 'pimpinan']))
            <a href="{{ route('clients.index') }}" class="flex items-center px-4 py-2 rounded-lg transition duration-200 {{ request()->routeIs('clients.*') ? 'bg-gray-700' : 'hover:bg-gray-700' }}">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6V4m0 2a2 2 0 100 4m0-4a2 2 0 110 4m-6 8a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4m6 6v10m6-2a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4" />
                </svg>
                <span x-show="open" class="ml-4">Data Klien</span>
            </a>
            @endif

            <!-- Laporan Audit - Admin, Auditor, Pimpinan -->
            @if(in_array(auth()->user()->role, ['admin', 'auditor', 'pimpinan']))
            <a href="{{ route('audit_reports.index') }}" class="flex items-center px-4 py-2 rounded-lg transition duration-200 {{ request()->routeIs('audit_reports.*') ? 'bg-gray-700' : 'hover:bg-gray-700' }}">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4M7 12a5 5 0 1110 0 5 5 0 01-10 0z" />
                </svg>
                <span x-show="open" class="ml-4">Laporan Audit</span>
            </a>
            @endif

            <!-- Manajemen Dokumen - Admin, Auditor, Pimpinan -->
            @if(in_array(auth()->user()->role, ['admin', 'auditor', 'pimpinan']))
            <a href="{{ route('documents.index') }}" class="flex items-center px-4 py-2 rounded-lg transition duration-200 {{ request()->routeIs('documents.*') ? 'bg-gray-700' : 'hover:bg-gray-700' }}">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                </svg>
                <span x-show="open" class="ml-4">Dokumen</span>
            </a>
            @endif

            <!-- AI Audit Assistant -->
            @if(in_array(auth()->user()->role, ['admin', 'auditor', 'pimpinan']))
            <a href="{{ route('chatbot.index') }}"
                class="flex items-center px-4 py-2 rounded-lg transition duration-200 {{ request()->routeIs('chatbot.*') ? 'bg-gray-700' : 'hover:bg-gray-700' }}">

                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round"
                        stroke-linejoin="round"
                        stroke-width="2"
                        d="M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-4 4v-4z" />
                </svg>

                <span x-show="open" class="ml-4">
                    AI Audit Assistant
                </span>
            </a>
            @endif

            <!-- Admin Links -->
            @if(auth()->user()->role === 'admin')
            <div x-data="{ adminOpen: false }" class="space-y-2">
                <button @click="adminOpen = !adminOpen" class="w-full flex items-center px-4 py-2 rounded-lg hover:bg-gray-700 transition duration-200">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6V4m0 2a2 2 0 100 4m0-4a2 2 0 110 4m-6 8a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4m6 6v10m6-2a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4" />
                    </svg>
                    <span x-show="open" class="ml-4">Settings</span>
                    <svg x-show="open" :class="adminOpen ? 'rotate-90' : ''" class="ml-auto w-4 h-4 transition duration-200" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                    </svg>
                </button>
                <div x-show="adminOpen" class="pl-4 space-y-1">
                    <a href="{{ route('admin.users.index') }}" class="block px-4 py-2 text-sm rounded hover:bg-gray-700 transition duration-200 {{ request()->routeIs('admin.users.*') ? 'bg-gray-700' : '' }}">
                        <span x-show="open">👥 Manajemen User</span>
                        <svg x-show="!open" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-2a6 6 0 0112 0v2h12v-2a6 6 0 01-9-5.656v-.344" />
                        </svg>
                    </a>
                </div>
            </div>
            @endif
        </nav>

        <!-- User Profile Section -->
        <div class="border-t border-gray-700 p-4">
            <button @click="$dispatch('open-profile-menu')" class="flex items-center w-full px-4 py-2 rounded-lg hover:bg-gray-700 transition duration-200">
                <div class="w-8 h-8 bg-blue-500 rounded-full flex items-center justify-center text-sm font-bold">
                    {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}
                </div>
                <div x-show="open" class="ml-3 text-left">
                    <div class="text-sm font-medium">{{ auth()->user()->name }}</div>
                    <div class="text-xs text-gray-400">{{ ucfirst(auth()->user()->role) }}</div>
                </div>
            </button>

            <!-- Logout Button -->
            <form method="POST" action="{{ route('logout') }}" class="mt-2">
                @csrf
                <button type="submit" class="flex items-center w-full px-4 py-2 rounded-lg hover:bg-gray-700 transition duration-200 text-red-400">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />
                    </svg>
                    <span x-show="open" class="ml-4">Logout</span>
                </button>
            </form>
        </div>
    </div>

    <!-- Main Content -->
    <div class="flex-1 flex flex-col overflow-hidden">
        <!-- Top Bar -->
        <div class="bg-white border-b border-gray-200 px-6 py-4">
            <h1 class="text-2xl font-bold text-gray-800">{{ $title ?? 'Dashboard' }}</h1>
        </div>

        <!-- Content Area -->
        <main class="flex-1 overflow-auto p-6">
            {{ $slot }}
        </main>
    </div>
</div>