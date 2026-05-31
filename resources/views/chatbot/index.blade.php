<x-app-layout title="AURA AI (Audit Reporting Asisten)">

    <style>
        .chat-container {
            height: calc(100vh - 130px);
            min-height: 400px;
        }

        .message-bubble {
            max-width: 80%;
            word-break: break-word;
        }

        .typing-dot {
            width: 5px;
            height: 5px;
            border-radius: 50%;
            background: #2563eb;
            animation: typingBounce 1.2s infinite;
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
            transition: all 0.15s ease;
        }

        .quick-prompt:hover {
            background: #eff6ff;
            border-color: #bfdbfe;
            color: #2563eb;
            transform: translateY(-1px);
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

        #chat-messages::-webkit-scrollbar-thumb:hover {
            background: #94a3b8;
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

        /* Pengaturan Gaya Markdown */
        .prose-ai p {
            margin-bottom: 0.5rem;
            line-height: 1.5;
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
            font-weight: 700;
            color: #1e3a8a;
        }

        .prose-ai code {
            background: #f1f5f9;
            padding: 0.1rem 0.3rem;
            border-radius: 4px;
            font-size: 0.85em;
            color: #0f172a;
            border: 1px solid #e2e8f0;
        }

        .prose-ai h3 {
            font-weight: 700;
            font-size: 0.95rem;
            margin-top: 0.75rem;
            margin-bottom: 0.35rem;
            color: #1e3a8a;
        }

        .prose-ai blockquote {
            border-left: 3px solid #3b82f6;
            padding-left: 0.75rem;
            color: #4b5563;
            margin: 0.5rem 0;
            font-style: italic;
        }

        .msg-enter {
            animation: msgSlideIn 0.25s cubic-bezier(0.16, 1, 0.3, 1);
        }

        @keyframes msgSlideIn {
            from {
                opacity: 0;
                transform: translateY(8px);
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
        <div class="w-full md:w-60 bg-white rounded-xl border border-gray-200/80 flex flex-col overflow-hidden flex-shrink-0 h-48 md:h-full shadow-sm">
            <div class="p-3 border-b border-gray-100 bg-gray-50/50">
                <a href="{{ route('chatbot.index') }}" class="w-full flex items-center justify-center gap-1.5 bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded-lg shadow-sm transition text-xs">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"></path>
                    </svg>
                    Chat Baru
                </a>
            </div>

            <div class="flex-1 overflow-y-auto history-list p-2" id="history-list-wrapper">
                @if($sessions->isEmpty())
                <div id="no-history-placeholder" class="text-center py-6 px-4">
                    <div class="w-10 h-10 bg-blue-50 border border-blue-100 rounded-lg flex items-center justify-center mx-auto mb-2">
                        <svg class="w-5 h-5 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"></path>
                        </svg>
                    </div>
                    <p class="text-[10px] text-gray-400 font-semibold">Belum ada riwayat</p>
                </div>
                <div id="session-list-container" class="space-y-0.5 hidden">
                    <p class="text-[9px] font-bold text-gray-400 uppercase tracking-wider mb-2 mt-1.5 px-2" id="history-header">Riwayat Anda</p>
                </div>
                @else
                <div id="session-list-container" class="space-y-0.5">
                    <p class="text-[9px] font-bold text-gray-400 uppercase tracking-wider mb-2 mt-1.5 px-2" id="history-header">Riwayat Anda</p>
                    @foreach($sessions as $s)
                    <div class="group relative flex items-center justify-between {{ ($currentSession && $currentSession->id == $s->id) ? 'bg-blue-50 text-blue-700 border-l-2 border-blue-600' : 'text-slate-600 hover:bg-slate-50' }} rounded-lg transition-colors">
                        <a href="{{ route('chatbot.index', ['session_id' => $s->id]) }}" class="flex-1 truncate px-2.5 py-2 text-xs font-semibold">
                            {{ $s->title }}
                        </a>

                        {{-- Tombol Hapus --}}
                        <form action="{{ route('chatbot.destroy_session', $s->id) }}" method="POST" class="absolute right-1 opacity-0 group-hover:opacity-100 transition-opacity"
                            onsubmit="event.preventDefault(); confirmDelete(this, 'Percakapan ini beserta semua pesan riwayat di dalamnya akan dihapus secara permanen!')">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="p-1 text-blue-400 hover:text-red-500 hover:bg-red-50 rounded-md transition-all">
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
        <div class="flex-1 flex flex-col bg-white rounded-xl border border-gray-200/80 overflow-hidden h-full relative shadow-sm">

            {{-- Area Header --}}
            <div class="bg-white border-b border-gray-100 px-5 py-3.5 flex items-center justify-between z-20 shadow-sm relative">
                <div class="flex items-center gap-3">
                    <div class="w-8 h-8 rounded-lg bg-blue-50 text-blue-600 flex items-center justify-center border border-blue-100 shadow-sm">
                        <svg class="w-4.5 h-4.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z" />
                        </svg>
                    </div>
                    <div>
                        <h2 class="text-gray-800 font-bold text-sm leading-tight" id="chat-title">
                            {{ $currentSession ? $currentSession->title : 'AURA AI (Audit Reporting Asisten)' }}
                        </h2>
                        <div class="flex items-center gap-1.5 mt-0.5">
                            <span class="relative flex h-1.5 w-1.5">
                                <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-emerald-400 opacity-75"></span>
                                <span class="relative inline-flex rounded-full h-1.5 w-1.5 bg-emerald-500"></span>
                            </span>
                            <span class="text-gray-400 text-[10px] font-semibold">Groq Engine (<span id="model-badge">llama-3.3-70b</span>)</span>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Panel Pengaturan API --}}
            @if(auth()->user()->role === 'admin')
            <div id="api-settings-panel" class="hidden bg-blue-50 border-b border-blue-100 px-5 py-3 absolute w-full z-30 shadow-md top-14">
                <h3 class="text-xs font-bold text-blue-800 mb-1">Konfigurasi API Groq</h3>
                <p class="text-[10px] text-blue-600 mb-2">Atur API Key untuk mengubah model AI. Buka file <code class="bg-white px-1 py-0.5 rounded border border-blue-200">.env</code> Anda dan pastikan nilai <code class="bg-white px-1 py-0.5 rounded border border-blue-200">GROQ_API_KEY</code> telah diset.</p>
                <div class="flex gap-2 items-center">
                    <input type="password" value="{{ env('GROQ_API_KEY') ? '************************' : '' }}" disabled class="bg-white border border-blue-200 text-gray-500 text-xs rounded-lg block w-full p-2 max-w-md" placeholder="API Key tidak diset di .env">
                    <a href="https://console.groq.com/keys" target="_blank" class="text-[10px] bg-blue-600 text-white px-3 py-2 rounded-lg hover:bg-blue-700 transition font-bold">Dapatkan API Key</a>
                </div>
                <button onclick="toggleSettings()" class="absolute top-4 right-4 text-blue-400 hover:text-blue-600">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </button>
            </div>
            @endif

            {{-- Pola Latar Belakang --}}
            <div class="absolute inset-0 opacity-[0.015] pointer-events-none z-0" style="background-image: radial-gradient(#3b82f6 1px, transparent 1px); background-size: 16px 16px;"></div>

            {{-- Area Tampilan Pesan --}}
            <div id="chat-messages" class="flex-1 overflow-y-auto p-4 space-y-4 relative z-10">

                @if(!$currentSession || count($messages) === 0)
                {{-- Pesan Sambutan --}}
                <div class="flex items-start gap-3 msg-enter">
                    <div class="w-8 h-8 rounded-lg bg-blue-50 text-blue-600 border border-blue-100 flex items-center justify-center flex-shrink-0 shadow-sm">
                        <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                            <path d="M10 2a8 8 0 100 16A8 8 0 0010 2zm0 14a6 6 0 110-12 6 6 0 010 12zm-1-9h2v4H9V7zm0 6h2v2H9v-2z" />
                        </svg>
                    </div>
                    <div class="message-bubble bg-white border border-gray-200/80 text-gray-700 rounded-xl rounded-tl-none px-4 py-3 shadow-sm">
                        <p class="font-bold text-blue-700 text-xs mb-1">AURA AI (Audit Reporting Asisten)</p>
                        <p class="text-xs leading-relaxed text-gray-800">
                            Halo, <strong class="text-gray-900">{{ Auth::user()->name }}</strong>! 👋
                        </p>
                        <p class="text-xs leading-relaxed mt-1 text-gray-600">
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
                        <div class="message-bubble inline-block bg-blue-600 text-white rounded-xl rounded-br-none px-4 py-2.5 shadow-sm text-xs leading-relaxed text-left">{{ $msg->message }}</div>
                        <p class="text-[10px] text-gray-400 mt-0.5 pr-0.5 font-semibold">{{ $msg->created_at->format('H:i') }}</p>
                    </div>
                    <div class="w-8 h-8 rounded-lg bg-blue-50 border border-blue-100 text-blue-600 flex items-center justify-center flex-shrink-0 font-bold text-xs shadow-sm">
                        {{ strtoupper(substr(Auth::user()->name, 0, 1)) }}
                    </div>
                </div>

                {{-- Balon Pesan Asisten AI --}}
                <div class="flex items-start gap-3 msg-enter">
                    <div class="w-8 h-8 rounded-lg bg-blue-50 text-blue-600 border border-blue-100 flex items-center justify-center flex-shrink-0 shadow-sm">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707" />
                        </svg>
                    </div>
                    <div>
                        <div class="message-bubble bg-white border border-gray-200/80 text-gray-700 rounded-xl rounded-tl-none px-4 py-3 shadow-sm text-xs leading-relaxed prose-ai">
                            {!! Str::markdown($msg->reply) !!}
                        </div>
                        <p class="text-[10px] text-gray-400 mt-0.5 pl-0.5 font-semibold">{{ $msg->created_at->format('H:i') }}</p>
                    </div>
                </div>
                @endforeach
                @endif

                {{-- Jangkar untuk Gulir Otomatis --}}
                <div id="chat-bottom"></div>

            </div>

            {{-- Indikator Mengetik --}}
            <div id="typing-indicator" class="hidden px-4 pb-2 relative z-10">
                <div class="flex items-center gap-3">
                    <div class="w-8 h-8 rounded-lg bg-blue-50 text-blue-600 border border-blue-100 flex items-center justify-center flex-shrink-0 shadow-sm">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3" />
                        </svg>
                    </div>
                    <div class="bg-slate-50 border border-slate-100 rounded-xl rounded-tl-none px-3.5 py-2.5 flex items-center gap-1 shadow-sm">
                        <div class="typing-dot"></div>
                        <div class="typing-dot"></div>
                        <div class="typing-dot"></div>
                    </div>
                </div>
            </div>

            {{-- Topik Cepat (Hanya tampil jika tidak ada riwayat pada sesi aktif) --}}
            @if(!$currentSession || count($messages) === 0)
            <div id="quick-prompts" class="px-4 pb-3 bg-white relative z-10 pt-1.5">
                <p class="text-[10px] text-gray-400 mb-1.5 font-semibold uppercase tracking-wider">Topik Cepat</p>
                <div class="flex flex-wrap gap-1.5">
                    @foreach([
                    '📊 Berapa total laporan audit saat ini?',
                    '🏢 Berapa jumlah klien yang terdaftar?',
                    '📋 Tampilkan daftar laporan audit terbaru',
                    '⚠️ Apakah ada kendala yang dilaporkan oleh tim?',
                    '📅 Bagaimana ringkasan absensi kehadiran hari ini?',
                    ] as $prompt)
                    <button class="quick-prompt text-xs font-semibold text-slate-600 bg-white border border-gray-200 px-3.5 py-1.5 rounded-lg shadow-sm" onclick="setQuickPrompt(this)">
                        {{ $prompt }}
                    </button>
                    @endforeach
                </div>
            </div>
            @endif

            {{-- Area Input Pesan --}}
            <div class="px-4 py-3 bg-gray-50 border-t border-gray-200/80 relative z-20">
                <div class="flex items-end gap-2.5">
                    <div class="flex-1 bg-white border border-gray-200 rounded-lg px-3 py-2 flex items-end gap-2 shadow-sm transition-colors relative focus-within:border-blue-400">
                        <textarea id="message-input" rows="1"
                            placeholder="Ketik pesan Anda di sini..."
                            class="flex-1 bg-transparent text-gray-800 placeholder-gray-400 text-xs resize-none border-none p-0 focus:ring-0 max-h-32"
                            style="min-height: 20px; overflow-y: hidden;"></textarea>
                        <div class="text-[9px] text-gray-400 whitespace-nowrap self-end pb-0.5 font-medium" id="char-count">0/2000</div>
                    </div>
                    <button id="send-btn"
                        class="w-9 h-9 bg-blue-600 hover:bg-blue-700 disabled:bg-blue-300 text-white rounded-lg flex items-center justify-center transition-all send-btn flex-shrink-0"
                        onclick="sendMessage()">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8" />
                        </svg>
                    </button>
                </div>
                <div class="flex justify-between items-center mt-2 px-1">
                    <p class="text-[10px] text-gray-400">
                        <kbd class="px-1.5 py-0.5 bg-gray-200 rounded border border-gray-300 text-gray-600 font-sans text-[9px]">Enter</kbd> kirim ·
                        <kbd class="px-1.5 py-0.5 bg-gray-200 rounded border border-gray-300 text-gray-600 font-sans text-[9px]">Shift+Enter</kbd> baris baru
                    </p>
                    <p class="text-[9px] text-gray-400 uppercase tracking-widest font-bold">Max Output 2048 Tokens</p>
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
                'text-[9px] text-red-500 whitespace-nowrap self-end pb-0.5 font-medium' :
                'text-[9px] text-gray-400 whitespace-nowrap self-end pb-0.5 font-medium';
        });

        textarea.addEventListener('keydown', function(e) {
            if (e.key === 'Enter' && !e.shiftKey) {
                e.preventDefault();
                sendMessage();
            }
        });

        function setQuickPrompt(btn) {
            textarea.value = btn.textContent.trim().replace(/^[\u{1F300}-\u{1FFFF}\u2600-\u27BF]\s*/u, '');
            textarea.dispatchEvent(new Event('input'));
            textarea.focus();
        }

        // Pemformat Markdown sederhana untuk JavaScript (karena riwayat menggunakan Laravel Str::markdown)
        function formatText(text) {
            return text
                .replace(/\*\*(.*?)\*\*/g, '<strong>$1</strong>')
                .replace(/\*(.*?)\*/g, '<em>$1</em>')
                .replace(/`(.*?)`/g, '<code>$1</code>')
                .replace(/^### (.*$)/gim, '<h3 class="font-bold text-blue-900 mt-2 mb-1.5 text-xs">$1</h3>')
                .replace(/^## (.*$)/gim, '<h2 class="font-bold text-blue-900 mt-2.5 mb-1.5 text-sm">$1</h2>')
                .replace(/^# (.*$)/gim, '<h1 class="font-bold text-blue-900 mt-3 mb-2 text-base">$1</h1>')
                .replace(/^\s*[-*]\s+(.*)/gim, '<li class="ml-4 list-disc text-gray-700 text-xs">$1</li>')
                .replace(/(<li class="ml-4 list-disc.*<\/li>\n?)+/g, '<ul class="my-1.5 space-y-1">$&</ul>')
                .replace(/^\d+\.\s+(.*)/gim, '<li class="ml-4 list-decimal text-gray-700 text-xs">$1</li>')
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
                            <div class="message-bubble inline-block bg-blue-600 text-white rounded-xl rounded-br-none px-4 py-2.5 shadow-sm text-xs leading-relaxed text-left">${escapeHtml(text)}</div>
                            <p class="text-[10px] text-gray-400 mt-0.5 pr-0.5 font-semibold">${time}</p>
                        </div>
                        <div class="w-8 h-8 rounded-lg bg-blue-50 border border-blue-100 text-blue-600 flex items-center justify-center flex-shrink-0 font-bold text-xs shadow-sm">
                            {{ strtoupper(substr(Auth::user()->name, 0, 1)) }}
                        </div>
                    </div>`;
            } else {
                msgs.innerHTML += `
                    <div class="flex items-start gap-3 msg-enter mb-4">
                        <div class="w-8 h-8 rounded-lg bg-blue-50 border border-blue-100 text-blue-600 flex items-center justify-center flex-shrink-0 shadow-sm">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707"/></svg>
                        </div>
                        <div>
                            <div class="message-bubble bg-white border border-gray-200/80 text-gray-700 rounded-xl rounded-tl-none px-4 py-3 shadow-sm text-xs leading-relaxed prose-ai">${formatText(text)}</div>
                            <p class="text-[10px] text-gray-400 mt-0.5 pl-0.5 font-semibold">${time}</p>
                        </div>
                    </div>`;
            }
            scrollToBottom();
        }

        function appendError(msg) {
            const msgs = document.getElementById('chat-messages');
            msgs.innerHTML += `
                <div class="flex justify-center msg-enter mb-4">
                    <div class="bg-red-50 border border-red-200 text-red-600 text-xs px-3.5 py-2 rounded-lg max-w-md text-center shadow-sm flex items-center gap-1.5 font-bold">
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
                                el.className = "group relative flex items-center justify-between text-slate-600 hover:bg-slate-50 rounded-lg transition-colors";
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
                                <div id="session-list-container" class="space-y-0.5">
                                    <p class="text-[9px] font-bold text-gray-400 uppercase tracking-wider mb-2 mt-1.5 px-2" id="history-header">Riwayat Anda</p>
                                </div>
                            `;
                            container = document.getElementById('session-list-container');
                        }

                        if (container) {
                            container.classList.remove('hidden');
                            
                            const destroyUrl = `/chatbot/session/${data.session_id}`;
                            const detailUrl = `/chatbot?session_id=${data.session_id}`;
                            
                            const newSessionHtml = `
                                <div class="group relative flex items-center justify-between bg-blue-50 text-blue-700 border-l-2 border-blue-600 rounded-lg transition-colors">
                                    <a href="${detailUrl}" class="flex-1 truncate px-2.5 py-2 text-xs font-semibold">
                                        ${escapeHtml(data.title)}
                                    </a>
                                    <form action="${destroyUrl}" method="POST" class="absolute right-1 opacity-0 group-hover:opacity-100 transition-opacity"
                                        onsubmit="event.preventDefault(); confirmDelete(this, 'Percakapan ini beserta semua pesan riwayat di dalamnya akan dihapus secara permanen!')">
                                        <input type="hidden" name="_token" value="${CSRF}">
                                        <input type="hidden" name="_method" value="DELETE">
                                        <button type="submit" class="p-1 text-blue-400 hover:text-red-500 hover:bg-red-50 rounded-md transition-all">
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