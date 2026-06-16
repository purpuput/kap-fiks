<x-app-layout title="AURA AI (Audit Reporting Asisten)">

    <style>
        .chat-container {
            height: calc(100vh - 130px);
            min-height: 400px;
        }

        .message-bubble {
            word-break: break-word;
        }

        .typing-dot {
            width: 5px;
            height: 5px;
            border-radius: 50%;
            background: #2563eb;
            animation: typingBounce 1.2s infinite;
        }

        html.dark .typing-dot {
            background: #60a5fa;
        }

        .typing-dot:nth-child(2) {
            animation-delay: 0.2s;
        }

        .typing-dot:nth-child(3) {
            animation-delay: 0.4s;
        }

        @keyframes typingBounce {
            0%, 60%, 100% {
                transform: translateY(0);
                opacity: 0.4;
            }
            30% {
                transform: translateY(-5px);
                opacity: 1;
            }
        }

        .quick-prompt {
            transition: all 0.2s ease;
        }

        .quick-prompt:hover {
            background: #eff6ff;
            border-color: #bfdbfe;
            color: #2563eb;
            transform: translateY(-1px);
        }

        html.dark .quick-prompt:hover {
            background: rgba(30, 58, 138, 0.3) !important;
            border-color: #1e40af !important;
            color: #60a5fa !important;
        }

        #chat-messages::-webkit-scrollbar {
            width: 5px;
        }

        #chat-messages::-webkit-scrollbar-track {
            background: transparent;
        }

        #chat-messages::-webkit-scrollbar-thumb {
            background: #cbd5e1;
            border-radius: 6px;
        }

        html.dark #chat-messages::-webkit-scrollbar-thumb {
            background: #334155;
        }

        #chat-messages::-webkit-scrollbar-thumb:hover {
            background: #94a3b8;
        }

        html.dark #chat-messages::-webkit-scrollbar-thumb:hover {
            background: #475569;
        }

        .history-list::-webkit-scrollbar {
            width: 3px;
        }

        .history-list::-webkit-scrollbar-track {
            background: transparent;
        }

        .history-list::-webkit-scrollbar-thumb {
            background: #e2e8f0;
            border-radius: 4px;
        }

        html.dark .history-list::-webkit-scrollbar-thumb {
            background: #1e293b;
        }

        /* Pengaturan Gaya Markdown */
        .prose-ai p {
            margin-bottom: 0.5rem;
            line-height: 1.6;
        }

        .prose-ai p:last-child {
            margin-bottom: 0;
        }

        .prose-ai ul {
            list-style: disc;
            padding-left: 1.25rem;
            margin-bottom: 0.5rem;
        }

        .prose-ai ol {
            list-style: decimal;
            padding-left: 1.25rem;
            margin-bottom: 0.5rem;
        }

        .prose-ai strong {
            font-weight: 750;
            color: #1e3a8a;
        }

        html.dark .prose-ai strong {
            color: #93c5fd;
        }

        .prose-ai code {
            background: #f1f5f9;
            padding: 0.15rem 0.35rem;
            border-radius: 6px;
            font-size: 0.85em;
            color: #0f172a;
            border: 1px solid #e2e8f0;
        }

        html.dark .prose-ai code {
            background: #1e293b;
            color: #cbd5e1;
            border: 1px solid #334155;
        }

        .prose-ai h1 {
            font-weight: 800;
            font-size: 1.1rem;
            margin-top: 0.75rem;
            margin-bottom: 0.35rem;
            color: #1e3a8a;
        }

        html.dark .prose-ai h1 {
            color: #93c5fd;
        }

        .prose-ai h2 {
            font-weight: 750;
            font-size: 1rem;
            margin-top: 0.75rem;
            margin-bottom: 0.35rem;
            color: #1e3a8a;
        }

        html.dark .prose-ai h2 {
            color: #93c5fd;
        }

        .prose-ai h3 {
            font-weight: 700;
            font-size: 0.95rem;
            margin-top: 0.75rem;
            margin-bottom: 0.35rem;
            color: #1e3a8a;
        }

        html.dark .prose-ai h3 {
            color: #93c5fd;
        }

        .prose-ai li {
            color: #4b5563;
        }

        html.dark .prose-ai li {
            color: #cbd5e1;
        }

        .prose-ai blockquote {
            border-left: 3px solid #3b82f6;
            padding-left: 0.75rem;
            color: #4b5563;
            margin: 0.5rem 0;
            font-style: italic;
        }

        html.dark .prose-ai blockquote {
            border-left: 3px solid #60a5fa;
            color: #94a3b8;
        }

        .msg-enter {
            animation: msgSlideIn 0.3s cubic-bezier(0.16, 1, 0.3, 1);
        }

        @keyframes msgSlideIn {
            from {
                opacity: 0;
                transform: translateY(10px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        #message-input:focus {
            outline: none;
        }
    </style>

    <div class="flex flex-col md:flex-row gap-4 chat-container">

        {{-- ──────────────────────────────────────────────────────────
             RIWAYAT PERCAKAPAN (SIDEBAR)
        ────────────────────────────────────────────────────────── --}}
        <div class="w-full md:w-64 bg-white dark:bg-slate-900 rounded-3xl border border-slate-100 dark:border-slate-800/80 flex flex-col overflow-hidden flex-shrink-0 h-52 md:h-full shadow-sm transition-all duration-200">
            <div class="p-4 border-b border-slate-50 dark:border-slate-800/60 bg-slate-50/50 dark:bg-slate-900/50">
                <a href="{{ route('chatbot.index') }}" class="w-full flex items-center justify-center gap-1.5 bg-gradient-to-r from-blue-600 to-indigo-650 hover:from-blue-700 hover:to-indigo-700 dark:from-blue-500 dark:to-indigo-600 dark:hover:from-blue-600 dark:hover:to-indigo-700 text-white font-bold py-2.5 px-4 rounded-xl shadow-sm hover:shadow transition-all text-sm">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"></path>
                    </svg>
                    Chat Baru
                </a>
            </div>

            <div class="flex-1 overflow-y-auto history-list p-2" id="history-list-wrapper">
                @if($sessions->isEmpty())
                <div id="no-history-placeholder" class="text-center py-6 px-4">
                    <div class="w-10 h-10 bg-blue-50 dark:bg-blue-950/30 border border-blue-100 dark:border-blue-900/40 rounded-xl flex items-center justify-center mx-auto mb-2">
                        <svg class="w-5 h-5 text-blue-500 dark:text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"></path>
                        </svg>
                    </div>
                    <p class="text-xs text-slate-400 dark:text-slate-500 font-semibold">Belum ada riwayat</p>
                </div>
                <div id="session-list-container" class="space-y-1 hidden">
                    <p class="text-[10px] font-bold text-slate-400 dark:text-slate-500 uppercase tracking-wider mb-2 mt-1.5 px-2.5" id="history-header">Riwayat Anda</p>
                </div>
                @else
                <div id="session-list-container" class="space-y-1">
                    <p class="text-[10px] font-bold text-slate-400 dark:text-slate-500 uppercase tracking-wider mb-2 mt-1.5 px-2.5" id="history-header">Riwayat Anda</p>
                    @foreach($sessions as $s)
                    <div class="group relative flex items-center justify-between {{ ($currentSession && $currentSession->id == $s->id) ? 'bg-blue-50/70 dark:bg-blue-950/40 text-blue-700 dark:text-blue-400 border-l-2 border-blue-600 dark:border-blue-500' : 'text-slate-650 dark:text-slate-400 hover:bg-slate-50/80 dark:hover:bg-slate-800/40' }} rounded-xl transition-all duration-200">
                        <a href="{{ route('chatbot.index', ['session_id' => $s->id]) }}" class="flex-1 truncate px-3 py-2.5 text-xs font-bold">
                            {{ $s->title }}
                        </a>

                        {{-- Tombol Hapus --}}
                        <form action="{{ route('chatbot.destroy_session', $s->id) }}" method="POST" class="absolute right-2.5 opacity-0 group-hover:opacity-100 transition-opacity"
                            onsubmit="event.preventDefault(); confirmDelete(this, 'Percakapan ini beserta semua pesan riwayat di dalamnya akan dihapus secara permanen!')">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="p-1.5 text-slate-400 hover:text-rose-600 hover:bg-rose-50 dark:hover:bg-rose-950/30 rounded-lg transition-all">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                </svg>
                            </button>
                        </form>
                    </div>
                    @endforeach
                </div>
                @endif
            </div>
        </div>

        {{-- ──────────────────────────────────────────────────────────
             AREA PERCAKAPAN UTAMA
        ────────────────────────────────────────────────────────── --}}
        <div class="flex-1 flex flex-col bg-white dark:bg-slate-900 rounded-3xl border border-slate-100 dark:border-slate-800/80 overflow-hidden h-full relative shadow-sm transition-all duration-200">

            {{-- Area Header --}}
            <div class="bg-white dark:bg-slate-900 border-b border-slate-100 dark:border-slate-800/60 px-6 py-4 flex items-center justify-between z-20 shadow-sm relative transition-all duration-200">
                <div class="flex items-center gap-3">
                    <div class="w-9 h-9 rounded-xl bg-blue-50 dark:bg-blue-950/40 text-blue-600 dark:text-blue-400 flex items-center justify-center border border-blue-100 dark:border-blue-900/40 shadow-sm transition-colors duration-200">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z" />
                        </svg>
                    </div>
                    <div>
                        <h2 class="text-slate-800 dark:text-slate-100 font-bold text-sm leading-tight" id="chat-title">
                            {{ $currentSession ? $currentSession->title : 'AURA AI (Audit Reporting Asisten)' }}
                        </h2>
                        <div class="flex items-center gap-1.5 mt-0.5">
                            <span class="relative flex h-1.5 w-1.5">
                                <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-emerald-400 opacity-75"></span>
                                <span class="relative inline-flex rounded-full h-1.5 w-1.5 bg-emerald-500"></span>
                            </span>
                            <span class="text-slate-400 dark:text-slate-500 text-xs font-semibold">Groq Engine (<span id="model-badge">llama-3.3-70b</span>)</span>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Pola Latar Belakang --}}
            <div class="absolute inset-0 opacity-[0.015] dark:opacity-[0.02] pointer-events-none z-0" style="background-image: radial-gradient(#3b82f6 1px, transparent 1px); background-size: 16px 16px;"></div>

            {{-- Area Tampilan Pesan --}}
            <div id="chat-messages" class="flex-1 overflow-y-auto p-5 space-y-5 relative z-10">

                @if(!$currentSession || count($messages) === 0)
                {{-- Pesan Sambutan --}}
                <div class="flex items-start gap-3 msg-enter">
                    <div class="w-9 h-9 rounded-xl bg-blue-50 dark:bg-blue-950/40 text-blue-600 dark:text-blue-400 border border-blue-100 dark:border-blue-900/50 flex items-center justify-center flex-shrink-0 shadow-sm transition-colors duration-200">
                        <svg class="w-4.5 h-4.5" fill="currentColor" viewBox="0 0 20 20">
                            <path d="M10 2a8 8 0 100 16A8 8 0 0010 2zm0 14a6 6 0 110-12 6 6 0 010 12zm-1-9h2v4H9V7zm0 6h2v2H9v-2z" />
                        </svg>
                    </div>
                    <div class="message-bubble bg-slate-50/50 dark:bg-slate-850/50 border border-slate-100 dark:border-slate-800 text-slate-700 dark:text-slate-300 rounded-2xl rounded-tl-none px-5 py-4 shadow-sm leading-relaxed max-w-[85%] transition-colors duration-200">
                        <p class="font-bold text-blue-600 dark:text-blue-400 text-sm mb-1.5">AURA AI (Audit Reporting Asisten)</p>
                        <p class="text-sm leading-relaxed text-slate-800 dark:text-slate-200">
                            Halo, <strong class="text-slate-900 dark:text-white">{{ Auth::user()->name }}</strong>! 👋
                        </p>
                        <p class="text-sm leading-relaxed mt-1 text-slate-650 dark:text-slate-400">
                            Saya adalah <strong>AURA AI (Audit Reporting Asisten)</strong>, asisten cerdas yang siap membantu Anda dalam memantau, menganalisis, dan melaporkan kegiatan audit, status klien, serta data operasional KAP secara cepat dan akurat langsung dari database sistem.
                        </p>
                    </div>
                </div>
                @else
                {{-- Render riwayat pesan dari database --}}
                @foreach($messages as $msg)
                {{-- Balon Pesan Pengguna --}}
                <div class="flex items-start justify-end gap-3 msg-enter mb-4">
                    <div class="text-right">
                        <div class="message-bubble inline-block bg-blue-600 dark:bg-blue-600 text-white rounded-2xl rounded-br-none px-5 py-3 shadow-sm hover:shadow text-sm leading-relaxed text-left max-w-[80%] transition-all duration-200">{{ $msg->message }}</div>
                        <p class="text-[10px] text-slate-400 dark:text-slate-500 mt-1 pr-0.5 font-semibold">{{ $msg->created_at->format('H:i') }}</p>
                    </div>
                    <div class="w-9 h-9 rounded-xl bg-blue-50 dark:bg-blue-950/40 border border-blue-100 dark:border-blue-900/50 text-blue-650 dark:text-blue-400 flex items-center justify-center flex-shrink-0 font-bold text-sm shadow-sm transition-colors duration-200">
                        {{ strtoupper(substr(Auth::user()->name, 0, 1)) }}
                    </div>
                </div>

                {{-- Balon Pesan Asisten AI --}}
                <div class="flex items-start gap-3 msg-enter">
                    <div class="w-9 h-9 rounded-xl bg-blue-50 dark:bg-blue-950/40 text-blue-600 dark:text-blue-400 border border-blue-100 dark:border-blue-900/50 flex items-center justify-center flex-shrink-0 shadow-sm transition-colors duration-200">
                        <svg class="w-4.5 h-4.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3" />
                        </svg>
                    </div>
                    <div>
                        <div class="message-bubble bg-white dark:bg-slate-850 border border-slate-100 dark:border-slate-800 text-slate-700 dark:text-slate-300 rounded-2xl rounded-tl-none px-5 py-4 shadow-sm text-xs leading-relaxed prose-ai max-w-[85%] transition-colors duration-200">
                            {!! Str::markdown($msg->reply) !!}
                        </div>
                        <p class="text-[10px] text-slate-400 dark:text-slate-500 mt-1 pl-0.5 font-semibold">{{ $msg->created_at->format('H:i') }}</p>
                    </div>
                </div>
                @endforeach
                @endif

                {{-- Jangkar untuk Gulir Otomatis --}}
                <div id="chat-bottom"></div>

            </div>

            {{-- Indikator Mengetik --}}
            <div id="typing-indicator" class="hidden px-5 pb-3 relative z-10">
                <div class="flex items-center gap-3">
                    <div class="w-9 h-9 rounded-xl bg-blue-50 dark:bg-blue-950/40 text-blue-600 dark:text-blue-400 border border-blue-100 dark:border-blue-900/50 flex items-center justify-center flex-shrink-0 shadow-sm transition-colors duration-200">
                        <svg class="w-4.5 h-4.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3" />
                        </svg>
                    </div>
                    <div class="bg-slate-50/80 dark:bg-slate-850/80 border border-slate-100 dark:border-slate-800 rounded-xl rounded-tl-none px-4 py-3 flex items-center gap-1 shadow-sm transition-colors duration-200">
                        <div class="typing-dot"></div>
                        <div class="typing-dot"></div>
                        <div class="typing-dot"></div>
                    </div>
                </div>
            </div>

            {{-- Topik Cepat (Hanya tampil jika tidak ada riwayat pada sesi aktif) --}}
            @if(!$currentSession || count($messages) === 0)
            <div id="quick-prompts" class="px-5 pb-4 bg-white dark:bg-slate-900 relative z-10 pt-2 transition-colors duration-200">
                <p class="text-[10px] text-slate-400 dark:text-slate-500 mb-2 font-bold uppercase tracking-wider">Topik Cepat</p>
                <div class="flex flex-wrap gap-2">
                    @foreach([
                    '📊 Berapa total laporan audit saat ini?',
                    '🏢 Berapa jumlah klien yang terdaftar?',
                    '📋 Tampilkan daftar laporan audit terbaru',
                    '⚠️ Apakah ada kendala yang dilaporkan oleh tim?',
                    '📅 Bagaimana ringkasan absensi kehadiran hari ini?',
                    ] as $prompt)
                    <button class="quick-prompt text-xs font-semibold text-slate-600 dark:text-slate-400 bg-slate-50 dark:bg-slate-850/50 border border-slate-200/60 dark:border-slate-800 px-3.5 py-2 rounded-xl shadow-sm transition-all duration-200" onclick="setQuickPrompt(this)">
                        {{ $prompt }}
                    </button>
                    @endforeach
                </div>
            </div>
            @endif

            {{-- Area Input Pesan --}}
            <div class="px-5 py-4 bg-slate-50/50 dark:bg-slate-900/80 border-t border-slate-100 dark:border-slate-800/80 relative z-20 transition-colors duration-200">
                <div class="flex items-end gap-3">
                    <div class="flex-1 bg-white dark:bg-slate-850 border border-slate-200/80 dark:border-slate-800 rounded-2xl px-4 py-3 flex items-end gap-3 shadow-sm transition-all duration-200 focus-within:border-blue-500 dark:focus-within:border-blue-600 focus-within:ring-2 focus-within:ring-blue-500/10 relative">
                        <textarea id="message-input" rows="1"
                            placeholder="Ketik pesan Anda di sini..."
                            class="flex-1 bg-transparent text-slate-800 dark:text-slate-100 placeholder-slate-400 dark:placeholder-slate-500 text-sm resize-none border-none p-0 focus:ring-0 max-h-32 focus:outline-none focus:border-none focus:ring-transparent"
                            style="min-height: 20px; overflow-y: hidden;"></textarea>
                        <div class="text-[10px] text-slate-400 dark:text-slate-500 whitespace-nowrap self-end pb-0.5 font-medium" id="char-count">0/2000</div>
                    </div>
                    <button id="send-btn"
                        class="w-11 h-11 bg-gradient-to-r from-blue-600 to-indigo-650 hover:from-blue-700 hover:to-indigo-700 dark:from-blue-500 dark:to-indigo-600 dark:hover:from-blue-600 dark:hover:to-indigo-700 disabled:from-slate-100 disabled:to-slate-100 dark:disabled:from-slate-800 dark:disabled:to-slate-800 disabled:text-slate-400 dark:disabled:text-slate-600 text-white rounded-2xl flex items-center justify-center transition-all shadow-sm hover:shadow flex-shrink-0"
                        onclick="sendMessage()">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8" />
                        </svg>
                    </button>
                </div>
                <div class="flex justify-between items-center mt-2 px-1 text-[11px] text-slate-400 dark:text-slate-500">
                    <p>
                        <kbd class="px-1.5 py-0.5 bg-slate-100 dark:bg-slate-800 rounded border border-slate-200 dark:border-slate-700 text-slate-600 dark:text-slate-400 font-sans text-[10px]">Enter</kbd> kirim ·
                        <kbd class="px-1.5 py-0.5 bg-slate-100 dark:bg-slate-800 rounded border border-slate-200 dark:border-slate-700 text-slate-600 dark:text-slate-400 font-sans text-[10px]">Shift+Enter</kbd> baris baru
                    </p>
                    <p class="uppercase tracking-widest font-bold text-[9px]">Max Output 2048 Tokens</p>
                </div>
            </div>

        </div>
    </div>

    <script>
        const CSRF = document.querySelector('meta[name="csrf-token"]').content;
        const chatEndpoint = "{{ route('chatbot.send') }}";

        // Ambil ID sesi saat ini jika ada
        let sessionIdStr = "{{ $currentSession ? $currentSession->id : '' }}";
        let currentSessionId = sessionIdStr !== '' ? parseInt(sessionIdStr) : null;
        let isNewSession = "{{ $currentSession ? 'false' : 'true' }}" === 'true';

        // Gulir ke bagian bawah saat halaman dimuat
        function scrollToBottom() {
            const chatMessages = document.getElementById('chat-messages');
            chatMessages.scrollTop = chatMessages.scrollHeight;
        }
        window.onload = scrollToBottom;

        function toggleSettings() {
            const panel = document.getElementById('api-settings-panel');
            if (panel) panel.classList.toggle('hidden');
        }

        // Menyesuaikan tinggi textarea secara otomatis
        const textarea = document.getElementById('message-input');
        const charCount = document.getElementById('char-count');
        textarea.addEventListener('input', function() {
            this.style.height = 'auto';
            this.style.height = Math.min(this.scrollHeight, 128) + 'px';
            charCount.textContent = this.value.length + '/2000';
            charCount.className = this.value.length > 1800 ?
                'text-[10px] text-rose-500 dark:text-rose-450 whitespace-nowrap self-end pb-0.5 font-medium' :
                'text-[10px] text-slate-400 dark:text-slate-500 whitespace-nowrap self-end pb-0.5 font-medium';
        });

        textarea.addEventListener('keydown', function(e) {
            if (e.key === 'Enter' && !e.shiftKey) {
                e.preventDefault();
                sendMessage();
            }
        });

        // Quick prompts setting text
        function setQuickPrompt(btn) {
            textarea.value = btn.textContent.trim().replace(/^[\u{1F300}-\u{1FFFF}\u2600-\u27BF]\s*/u, '');
            textarea.dispatchEvent(new Event('input'));
            textarea.focus();
        }

        // Markdown formatter for JS (dynamic output)
        function formatText(text) {
            return text
                .replace(/\*\*(.*?)\*\*/g, '<strong>$1</strong>')
                .replace(/\*(.*?)\*/g, '<em>$1</em>')
                .replace(/`(.*?)`/g, '<code>$1</code>')
                .replace(/^### (.*$)/gim, '<h3>$1</h3>')
                .replace(/^## (.*$)/gim, '<h2>$1</h2>')
                .replace(/^# (.*$)/gim, '<h1>$1</h1>')
                .replace(/^\s*[-*]\s+(.*)/gim, '<li class="ml-4 list-disc text-xs">$1</li>')
                .replace(/(<li class="ml-4 list-disc.*<\/li>\n?)+/g, '<ul class="my-1.5 space-y-1">$&</ul>')
                .replace(/^\d+\.\s+(.*)/gim, '<li class="ml-4 list-decimal text-xs">$1</li>')
                .replace(/(<li class="ml-4 list-decimal.*<\/li>\n?)+/g, '<ol class="my-1.5 space-y-1">$&</ol>')
                .replace(/^> (.*$)/gim, '<blockquote>$1</blockquote>')
                .replace(/\n/g, '<br>');
        }

        function escapeHtml(str) {
            return str.replace(/&/g, '&amp;').replace(/</g, '&lt;').replace(/>/g, '&gt;').replace(/"/g, '&quot;');
        }

        function appendMessage(role, text, timestamp) {
            const msgs = document.getElementById('chat-messages');
            const time = timestamp || new Date().toLocaleTimeString('id-ID', {
                hour: '2-digit',
                minute: '2-digit'
            });

            if (role === 'user') {
                msgs.innerHTML += `
                    <div class="flex items-start justify-end gap-3 msg-enter mb-4">
                        <div class="text-right">
                            <div class="message-bubble inline-block bg-blue-600 dark:bg-blue-600 text-white rounded-2xl rounded-br-none px-5 py-3 shadow-sm hover:shadow text-sm leading-relaxed text-left max-w-[80%] transition-all duration-200">${escapeHtml(text)}</div>
                            <p class="text-[10px] text-slate-400 dark:text-slate-500 mt-1 pr-0.5 font-semibold">${time}</p>
                        </div>
                        <div class="w-9 h-9 rounded-xl bg-blue-50 dark:bg-blue-950/40 border border-blue-100 dark:border-blue-900/50 text-blue-650 dark:text-blue-400 flex items-center justify-center flex-shrink-0 font-bold text-sm shadow-sm transition-colors duration-200">
                            {{ strtoupper(substr(Auth::user()->name, 0, 1)) }}
                        </div>
                    </div>`;
            } else {
                msgs.innerHTML += `
                    <div class="flex items-start gap-3 msg-enter mb-4">
                        <div class="w-9 h-9 rounded-xl bg-blue-50 dark:bg-blue-950/40 text-blue-600 dark:text-blue-400 border border-blue-100 dark:border-blue-900/50 flex items-center justify-center flex-shrink-0 shadow-sm transition-colors duration-200">
                            <svg class="w-4.5 h-4.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3"/></svg>
                        </div>
                        <div>
                            <div class="message-bubble bg-white dark:bg-slate-850 border border-slate-100 dark:border-slate-800 text-slate-700 dark:text-slate-300 rounded-2xl rounded-tl-none px-5 py-4 shadow-sm text-xs leading-relaxed prose-ai max-w-[85%] transition-colors duration-200">${formatText(text)}</div>
                            <p class="text-[10px] text-slate-400 dark:text-slate-500 mt-1 pl-0.5 font-semibold">${time}</p>
                        </div>
                    </div>`;
            }
            scrollToBottom();
        }

        function appendError(msg) {
            const msgs = document.getElementById('chat-messages');
            msgs.innerHTML += `
                <div class="flex justify-center msg-enter mb-4">
                    <div class="bg-rose-50 dark:bg-rose-950/30 border border-rose-200 dark:border-rose-900/50 text-rose-600 dark:text-rose-450 text-xs px-4 py-2.5 rounded-2xl max-w-md text-center shadow-sm flex items-center gap-1.5 font-bold">
                        <svg class="w-4.5 h-4.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                        ${escapeHtml(msg)}
                    </div>
                </div>`;
            scrollToBottom();
        }

        async function sendMessage() {
            const input = document.getElementById('message-input');
            const sendBtn = document.getElementById('send-btn');
            const message = input.value.trim();
            if (!message) return;

            // Sembunyikan topik cepat jika ada
            const quickPrompts = document.getElementById('quick-prompts');
            if (quickPrompts) quickPrompts.classList.add('hidden');

            appendMessage('user', message);
            input.value = '';
            input.style.height = 'auto';
            charCount.textContent = '0/2000';

            sendBtn.disabled = true;
            document.getElementById('typing-indicator').classList.remove('hidden');
            scrollToBottom();

            try {
                const payload = {
                    message: message,
                    session_id: currentSessionId
                };

                const res = await fetch(chatEndpoint, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': CSRF,
                        'Accept': 'application/json',
                    },
                    body: JSON.stringify(payload),
                });

                const data = await res.json();

                if (!res.ok || data.error) {
                    appendError(data.error || 'Terjadi kesalahan tak terduga.');
                } else {
                    if (data.model) {
                        document.getElementById('model-badge').textContent = data.model.split('-').slice(0, 3).join('-');
                    }

                    // Perbarui ID sesi jika sebelumnya bernilai null (sesi baru)
                    if (currentSessionId === null && data.session_id) {
                        currentSessionId = data.session_id;
                        isNewSession = false;
                        window.history.pushState({}, '', '?session_id=' + data.session_id);
                        document.getElementById('chat-title').textContent = data.title;

                        // Atur ulang kelas CSS sesi riwayat sebelumnya (hapus status aktif)
                        const sessionListContainer = document.getElementById('session-list-container');
                        if (sessionListContainer) {
                            sessionListContainer.querySelectorAll('.group').forEach(el => {
                                el.className = "group relative flex items-center justify-between text-slate-650 dark:text-slate-400 hover:bg-slate-50/80 dark:hover:bg-slate-800/40 rounded-xl transition-all duration-200";
                            });
                        }

                        // Tambahkan item sesi baru ke sidebar secara dinamis
                        const noHistoryPlaceholder = document.getElementById('no-history-placeholder');
                        if (noHistoryPlaceholder) {
                            noHistoryPlaceholder.classList.add('hidden');
                        }
                        
                        const wrapper = document.getElementById('history-list-wrapper');
                        let container = document.getElementById('session-list-container');
                        
                        if (!container && wrapper) {
                            // Jika riwayat sebelumnya kosong, buat kontainer secara dinamis
                            wrapper.innerHTML = `
                                <div id="session-list-container" class="space-y-1">
                                    <p class="text-[10px] font-bold text-slate-400 dark:text-slate-550 uppercase tracking-wider mb-2 mt-1.5 px-2.5" id="history-header">Riwayat Anda</p>
                                </div>
                            `;
                            container = document.getElementById('session-list-container');
                        }

                        if (container) {
                            container.classList.remove('hidden');
                            
                            const destroyUrl = `/chatbot/session/${data.session_id}`;
                            const detailUrl = `/chatbot?session_id=${data.session_id}`;
                            
                            const newSessionHtml = `
                                <div class="group relative flex items-center justify-between bg-blue-50/70 dark:bg-blue-950/40 text-blue-700 dark:text-blue-400 border-l-2 border-blue-600 dark:border-blue-500 rounded-xl transition-all duration-200">
                                    <a href="${detailUrl}" class="flex-1 truncate px-3 py-2.5 text-xs font-bold">
                                        ${escapeHtml(data.title)}
                                    </a>
                                    <form action="${destroyUrl}" method="POST" class="absolute right-2.5 opacity-0 group-hover:opacity-100 transition-opacity"
                                        onsubmit="event.preventDefault(); confirmDelete(this, 'Percakapan ini beserta semua pesan riwayat di dalamnya akan dihapus secara permanen!')">
                                        <input type="hidden" name="_token" value="${CSRF}">
                                        <input type="hidden" name="_method" value="DELETE">
                                        <button type="submit" class="p-1.5 text-slate-400 hover:text-rose-600 hover:bg-rose-50 dark:hover:bg-rose-950/30 rounded-lg transition-all">
                                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                            </svg>
                                        </button>
                                    </form>
                                </div>
                            `;
                            
                            const header = document.getElementById('history-header');
                            if (header) {
                                header.insertAdjacentHTML('afterend', newSessionHtml);
                            } else {
                                container.insertAdjacentHTML('beforeend', newSessionHtml);
                            }
                        }
                    }

                    appendMessage('assistant', data.reply);
                }
            } catch (err) {
                appendError('Tidak dapat terhubung ke server. Periksa koneksi Anda.');
            } finally {
                sendBtn.disabled = false;
                document.getElementById('typing-indicator').classList.add('hidden');
                textarea.focus();
            }
        }
    </script>
</x-app-layout>