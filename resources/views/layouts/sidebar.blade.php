<!-- Sidebar -->
<div
    :class="sidebarOpen ? 'translate-x-0 w-64' : '-translate-x-full md:translate-x-0 md:w-20'"
    class="fixed top-0 left-0 h-screen bg-slate-950/95 text-white
           transition-all duration-300 flex flex-col z-50 shadow-2xl border-r border-slate-800/80 backdrop-blur-md overflow-hidden">

    <!-- Logo & Toggle -->
    <div class="flex items-center justify-between h-16 px-4 border-b border-slate-800/60 flex-shrink-0">
        <div class="flex items-center gap-3 min-w-0">
            <div class="w-8 h-8 rounded-lg bg-gradient-to-tr from-blue-500 to-indigo-600 flex items-center justify-center flex-shrink-0 shadow-md shadow-blue-500/30">
                <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/>
                </svg>
            </div>
            <span x-show="sidebarOpen" x-transition:enter="transition-opacity duration-200"
                x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
                class="text-base font-extrabold tracking-wide whitespace-nowrap bg-gradient-to-r from-white via-slate-100 to-slate-300 bg-clip-text text-transparent">
                KAP Eko Prasetyo
            </span>
        </div>
        <button @click="sidebarOpen = !sidebarOpen; localStorage.setItem('sidebarOpen', sidebarOpen)"
            class="p-1.5 rounded-lg hover:bg-slate-800 transition flex-shrink-0 ml-2 border border-transparent hover:border-slate-700/50">
            <svg class="w-4 h-4 transition-transform duration-300" :class="sidebarOpen ? '' : 'rotate-180'"
                fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
            </svg>
        </button>
    </div>

    <!-- Navigation -->
    <nav class="flex-1 px-3 py-4 space-y-1.5 overflow-y-auto overflow-x-hidden">

        @php
            $role = auth()->user()->role;
            $navLink = 'flex items-center gap-3 px-3.5 py-3 rounded-xl transition-all duration-300 text-slate-400 hover:bg-slate-900/60 hover:text-blue-400 hover:translate-x-1 group relative';
            $navActive = 'flex items-center gap-3 px-3.5 py-3 rounded-xl bg-gradient-to-r from-blue-600 to-indigo-600 text-white shadow-lg shadow-blue-500/25 relative';
            $iconClass = 'w-5 h-5 flex-shrink-0 transition-transform duration-300 group-hover:scale-110';
        @endphp

        <!-- Label Grup -->
        <p x-show="sidebarOpen" class="text-[10px] font-extrabold text-slate-500 uppercase tracking-widest px-3 pt-2 pb-1.5">
            Menu Utama
        </p>

        <!-- Dashboard -->
        @php $isDashboard = request()->routeIs('dashboard') || request()->routeIs('*.dashboard'); @endphp
        <a href="{{ route('dashboard') }}"
            class="{{ $isDashboard ? $navActive : $navLink }}">
            @if($isDashboard)
                <span class="absolute left-0 top-3 bottom-3 w-1 bg-white rounded-r-md"></span>
            @endif
            <svg class="{{ $iconClass }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/>
            </svg>
            <span x-show="sidebarOpen" class="text-sm font-semibold whitespace-nowrap">Dashboard</span>
        </a>

        <!-- Absensi -->
        @php $isAttendance = request()->routeIs('attendances.*'); @endphp
        <a href="{{ route('attendances.index') }}"
            class="{{ $isAttendance ? $navActive : $navLink }}">
            @if($isAttendance)
                <span class="absolute left-0 top-3 bottom-3 w-1 bg-white rounded-r-md"></span>
            @endif
            <svg class="{{ $iconClass }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
            </svg>
            <span x-show="sidebarOpen" class="text-sm font-semibold whitespace-nowrap">Absensi</span>
        </a>

        <!-- Logbook -->
        @php $isLogbook = request()->routeIs('logbooks.*'); @endphp
        <a href="{{ route('logbooks.index') }}"
            class="{{ $isLogbook ? $navActive : $navLink }}">
            @if($isLogbook)
                <span class="absolute left-0 top-3 bottom-3 w-1 bg-white rounded-r-md"></span>
            @endif
            <svg class="{{ $iconClass }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
            </svg>
            <span x-show="sidebarOpen" class="text-sm font-semibold whitespace-nowrap">Logbook</span>
        </a>

        <!-- Divider -->
        <div class="border-t border-slate-800/60 my-3"></div>
        <p x-show="sidebarOpen" class="text-[10px] font-extrabold text-slate-500 uppercase tracking-widest px-3 pb-1.5">
            Audit
        </p>

        <!-- Data Klien -->
        @php $isClient = request()->routeIs('clients.*'); @endphp
        <a href="{{ route('clients.index') }}"
            class="{{ $isClient ? $navActive : $navLink }}">
            @if($isClient)
                <span class="absolute left-0 top-3 bottom-3 w-1 bg-white rounded-r-md"></span>
            @endif
            <svg class="{{ $iconClass }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/>
            </svg>
            <span x-show="sidebarOpen" class="text-sm font-semibold whitespace-nowrap">Data Klien</span>
        </a>

        <!-- Laporan Audit -->
        @php $isAuditReport = request()->routeIs('audit_reports.*'); @endphp
        <a href="{{ route('audit_reports.index') }}"
            class="{{ $isAuditReport ? $navActive : $navLink }}">
            @if($isAuditReport)
                <span class="absolute left-0 top-3 bottom-3 w-1 bg-white rounded-r-md"></span>
            @endif
            <svg class="{{ $iconClass }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
            </svg>
            <span x-show="sidebarOpen" class="text-sm font-semibold whitespace-nowrap">Laporan Audit</span>
        </a>

        <!-- Manajemen Dokumen -->
        @php $isDoc = request()->routeIs('document_managements.*'); @endphp
        <a href="{{ route('document_managements.index') }}"
            class="{{ $isDoc ? $navActive : $navLink }}">
            @if($isDoc)
                <span class="absolute left-0 top-3 bottom-3 w-1 bg-white rounded-r-md"></span>
            @endif
            <svg class="{{ $iconClass }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M5 8h14M5 8a2 2 0 110-4h14a2 2 0 110 4M5 8v10a2 2 0 002 2h10a2 2 0 002-2V8m-9 4h4"/>
            </svg>
            <span x-show="sidebarOpen" class="text-sm font-semibold whitespace-nowrap">Manajemen Dokumen</span>
        </a>

        <!-- Divider -->
        <div class="border-t border-slate-800/60 my-3"></div>
        <p x-show="sidebarOpen" class="text-[10px] font-extrabold text-slate-500 uppercase tracking-widest px-3 pb-1.5">
            Tools
        </p>

        <!-- AI Chatbot -->
        @php $isChat = request()->routeIs('chatbot.*'); @endphp
        <a href="{{ route('chatbot.index') }}"
            class="{{ $isChat ? $navActive : $navLink }}">
            @if($isChat)
                <span class="absolute left-0 top-3 bottom-3 w-1 bg-white rounded-r-md"></span>
            @endif
            <svg class="{{ $iconClass }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-4 4v-4z"/>
            </svg>
            <span x-show="sidebarOpen" class="text-sm font-semibold whitespace-nowrap">AURA AI</span>
        </a>

        <!-- Admin: Manajemen User -->
        @if($role === 'admin')
        <div class="border-t border-slate-800/60 my-3"></div>
        <p x-show="sidebarOpen" class="text-[10px] font-extrabold text-slate-500 uppercase tracking-widest px-3 pb-1.5">
            Admin
        </p>
        @php $isUserMgmt = request()->routeIs('admin.users.*'); @endphp
        <a href="{{ route('admin.users.index') }}"
            class="{{ $isUserMgmt ? $navActive : $navLink }}">
            @if($isUserMgmt)
                <span class="absolute left-0 top-3 bottom-3 w-1 bg-white rounded-r-md"></span>
            @endif
            <svg class="{{ $iconClass }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/>
            </svg>
            <span x-show="sidebarOpen" class="text-sm font-semibold whitespace-nowrap">Manajemen User</span>
        </a>
        @endif

    </nav>

    <!-- Profile & Logout -->
    <div class="border-t border-slate-800/80 p-4 flex-shrink-0 bg-slate-950/40">
        <a href="{{ route('profile.edit') }}" title="Pengaturan Profil & Password" class="flex items-center gap-3 p-2 rounded-2xl mb-2 hover:bg-slate-800/50 transition-all duration-300 group cursor-pointer border border-transparent hover:border-slate-800/60">
            <!-- Avatar -->
            <div class="w-10 h-10 rounded-xl flex-shrink-0 flex items-center justify-center font-bold text-sm text-white shadow-md
                {{ $role === 'admin' ? 'bg-gradient-to-tr from-blue-600 to-indigo-650' : ($role === 'pimpinan' ? 'bg-gradient-to-tr from-purple-600 to-indigo-600' : 'bg-gradient-to-tr from-emerald-600 to-teal-600') }} group-hover:ring-2 ring-blue-500/50 transition-all">
                {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}
            </div>
            <!-- Info -->
            <div x-show="sidebarOpen" class="flex-1 min-w-0">
                <p class="text-sm font-bold text-slate-100 truncate group-hover:text-blue-400 transition-colors">{{ auth()->user()->name }}</p>
                <span class="inline-flex text-[10px] px-2.5 py-0.5 rounded-full font-extrabold uppercase tracking-wider mt-1
                    {{ $role === 'admin' ? 'bg-blue-500/10 text-blue-400 border border-blue-500/25' : ($role === 'pimpinan' ? 'bg-purple-500/10 text-purple-400 border border-purple-500/25' : 'bg-emerald-500/10 text-emerald-400 border border-emerald-500/25') }}">
                    {{ auth()->user()->role }}
                </span>
            </div>
        </a>

        <!-- Logout -->
        <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button type="button" onclick="confirmLogout(this.form)"
                class="flex items-center gap-3 w-full px-3.5 py-2.5 rounded-xl hover:bg-red-500/10 text-slate-400 hover:text-red-400 transition-all duration-200 border border-transparent hover:border-red-500/20">
                <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/>
                </svg>
                <span x-show="sidebarOpen" class="text-sm font-semibold whitespace-nowrap">Logout</span>
            </button>
        </form>
    </div>

</div>