<!-- Sidebar -->
<div
    :class="sidebarOpen ? 'translate-x-0 w-64' : '-translate-x-full md:translate-x-0 md:w-20'"
    class="fixed top-0 left-0 h-screen bg-slate-900 text-white
           transition-all duration-300 flex flex-col z-50 shadow-xl overflow-hidden">

    <!-- Logo & Toggle -->
    <div class="flex items-center justify-between h-16 px-4 border-b border-slate-700/60 flex-shrink-0">
        <div class="flex items-center gap-3 min-w-0">
            <div class="w-8 h-8 rounded-lg bg-blue-600 flex items-center justify-center flex-shrink-0">
                <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/>
                </svg>
            </div>
            <span x-show="sidebarOpen" x-transition:enter="transition-opacity duration-200"
                x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
                class="text-base font-bold tracking-wide whitespace-nowrap">
                KAP Eko Prasetyo
            </span>
        </div>
        <button @click="sidebarOpen = !sidebarOpen; localStorage.setItem('sidebarOpen', sidebarOpen)"
            class="p-1.5 rounded-lg hover:bg-slate-700 transition flex-shrink-0 ml-2">
            <svg class="w-4 h-4 transition-transform duration-300" :class="sidebarOpen ? '' : 'rotate-180'"
                fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
            </svg>
        </button>
    </div>

    <!-- Navigation -->
    <nav class="flex-1 px-3 py-4 space-y-1 overflow-y-auto overflow-x-hidden">

        @php
            $role = auth()->user()->role;
            $navLink = 'flex items-center gap-3 px-3 py-2.5 rounded-xl transition-all duration-150 text-slate-300 hover:bg-slate-700/70 hover:text-white group';
            $navActive = 'flex items-center gap-3 px-3 py-2.5 rounded-xl bg-blue-600 text-white shadow-sm';
            $iconClass = 'w-5 h-5 flex-shrink-0';
        @endphp

        <!-- Label Grup -->
        <p x-show="sidebarOpen" class="text-xs font-semibold text-slate-500 uppercase tracking-widest px-3 pt-1 pb-1">
            Menu Utama
        </p>

        <!-- Dashboard -->
        <a href="{{ route('dashboard') }}"
            class="{{ request()->routeIs('dashboard') || request()->routeIs('*.dashboard') ? $navActive : $navLink }}">
            <svg class="{{ $iconClass }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/>
            </svg>
            <span x-show="sidebarOpen" class="text-sm font-medium whitespace-nowrap">Dashboard</span>
        </a>

        <!-- Absensi -->
        <a href="{{ route('attendances.index') }}"
            class="{{ request()->routeIs('attendances.*') ? $navActive : $navLink }}">
            <svg class="{{ $iconClass }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
            </svg>
            <span x-show="sidebarOpen" class="text-sm font-medium whitespace-nowrap">Absensi</span>
        </a>

        <!-- Logbook -->
        <a href="{{ route('logbooks.index') }}"
            class="{{ request()->routeIs('logbooks.*') ? $navActive : $navLink }}">
            <svg class="{{ $iconClass }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
            </svg>
            <span x-show="sidebarOpen" class="text-sm font-medium whitespace-nowrap">Logbook</span>
        </a>

        <!-- Divider -->
        <div class="border-t border-slate-700/50 my-2"></div>
        <p x-show="sidebarOpen" class="text-xs font-semibold text-slate-500 uppercase tracking-widest px-3 pb-1">
            Audit
        </p>

        <!-- Data Klien -->
        <a href="{{ route('clients.index') }}"
            class="{{ request()->routeIs('clients.*') ? $navActive : $navLink }}">
            <svg class="{{ $iconClass }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/>
            </svg>
            <span x-show="sidebarOpen" class="text-sm font-medium whitespace-nowrap">Data Klien</span>
        </a>

        <!-- Laporan Audit -->
        <a href="{{ route('audit_reports.index') }}"
            class="{{ request()->routeIs('audit_reports.*') ? $navActive : $navLink }}">
            <svg class="{{ $iconClass }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
            </svg>
            <span x-show="sidebarOpen" class="text-sm font-medium whitespace-nowrap">Laporan Audit</span>
        </a>

        <!-- Manajemen Dokumen -->
        <a href="{{ route('document_managements.index') }}"
            class="{{ request()->routeIs('document_managements.*') ? $navActive : $navLink }}">
            <svg class="{{ $iconClass }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M5 8h14M5 8a2 2 0 110-4h14a2 2 0 110 4M5 8v10a2 2 0 002 2h10a2 2 0 002-2V8m-9 4h4"/>
            </svg>
            <span x-show="sidebarOpen" class="text-sm font-medium whitespace-nowrap">Manajemen Dokumen</span>
        </a>

        <!-- Divider -->
        <div class="border-t border-slate-700/50 my-2"></div>
        <p x-show="sidebarOpen" class="text-xs font-semibold text-slate-500 uppercase tracking-widest px-3 pb-1">
            Tools
        </p>

        <!-- AI Chatbot -->
        <a href="{{ route('chatbot.index') }}"
            class="{{ request()->routeIs('chatbot.*') ? $navActive : $navLink }}">
            <svg class="{{ $iconClass }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-4 4v-4z"/>
            </svg>
            <span x-show="sidebarOpen" class="text-sm font-medium whitespace-nowrap">AURA AI</span>
        </a>

        <!-- Admin: Manajemen User -->
        @if($role === 'admin')
        <div class="border-t border-slate-700/50 my-2"></div>
        <p x-show="sidebarOpen" class="text-xs font-semibold text-slate-500 uppercase tracking-widest px-3 pb-1">
            Admin
        </p>
        <a href="{{ route('admin.users.index') }}"
            class="{{ request()->routeIs('admin.users.*') ? $navActive : $navLink }}">
            <svg class="{{ $iconClass }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/>
            </svg>
            <span x-show="sidebarOpen" class="text-sm font-medium whitespace-nowrap">Manajemen User</span>
        </a>
        @endif

    </nav>

    <!-- Profile & Logout -->
    <div class="border-t border-slate-700/60 p-3 flex-shrink-0">
        <a href="{{ route('profile.edit') }}" title="Pengaturan Profil & Password" class="flex items-center gap-3 px-2 py-2 rounded-xl mb-1 hover:bg-slate-700/50 transition-colors group cursor-pointer">
            <!-- Avatar -->
            <div class="w-9 h-9 rounded-full flex-shrink-0 flex items-center justify-center font-bold text-sm
                {{ $role === 'admin' ? 'bg-blue-600' : ($role === 'pimpinan' ? 'bg-purple-600' : 'bg-teal-600') }} group-hover:ring-2 ring-slate-500 transition-all">
                {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}
            </div>
            <!-- Info -->
            <div x-show="sidebarOpen" class="flex-1 min-w-0">
                <p class="text-sm font-semibold text-white truncate group-hover:text-blue-400 transition-colors">{{ auth()->user()->name }}</p>
                <p class="text-xs text-slate-400 capitalize">{{ auth()->user()->role }}</p>
            </div>
        </a>

        <!-- Logout -->
        <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button type="button" onclick="confirmLogout(this.form)"
                class="flex items-center gap-3 w-full px-3 py-2 rounded-xl hover:bg-red-600/20 text-slate-400 hover:text-red-400 transition-all duration-150">
                <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/>
                </svg>
                <span x-show="sidebarOpen" class="text-sm font-medium whitespace-nowrap">Logout</span>
            </button>
        </form>
    </div>

</div>