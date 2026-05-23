<x-app-layout>
    <x-slot name="title">AI Audit Assistant</x-slot>

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
            width: 6px;
            height: 6px;
            border-radius: 50%;
            background: #94a3b8;
            animation: typingBounce 1.2s infinite;
        }

        .typing-dot:nth-child(2) {
            animation-delay: 0.2s;
        }

        .typing-dot:nth-child(3) {
            animation-delay: 0.4s;
        }

        @keyframes typingBounce {

            0%,
            60%,
            100% {
                transform: translateY(0);
                opacity: 0.4;
            }

            30% {
                transform: translateY(-6px);
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

        #chat-messages::-webkit-scrollbar {
            width: 6px;
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
            width: 4px;
        }

        .history-list::-webkit-scrollbar-track {
            background: transparent;
        }

        .history-list::-webkit-scrollbar-thumb {
            background: #e2e8f0;
            border-radius: 4px;
        }

        /* Markdown Styling */
        .prose-ai p {
            margin-bottom: 0.5rem;
            line-height: 1.6;
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
            font-weight: 600;
            color: #1e293b;
        }

        .prose-ai code {
            background: #f1f5f9;
            padding: 0.15rem 0.4rem;
            border-radius: 6px;
            font-size: 0.85em;
            color: #0f172a;
            border: 1px solid #e2e8f0;
        }

        .prose-ai h3 {
            font-weight: 600;
            font-size: 1.05rem;
            margin-top: 1rem;
            margin-bottom: 0.5rem;
            color: #0f172a;
        }

        .prose-ai blockquote {
            border-left: 3px solid #3b82f6;
            padding-left: 0.75rem;
            color: #64748b;
            margin: 0.5rem 0;
            font-style: italic;
        }

        .msg-enter {
            animation: msgSlideIn 0.3s cubic-bezier(0.16, 1, 0.3, 1);
        }

        @keyframes msgSlideIn {
            from {
                opacity: 0;
                transform: translateY(12px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        #message-input:focus {
            outline: none;
            box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.15);
            border-color: #93c5fd;
        }
    </style>

    <div class="flex flex-col md:flex-row gap-4 chat-container">

        {{-- ──────────────────────────────────────────────────────────
             SIDEBAR HISTORY
        ────────────────────────────────────────────────────────── --}}
        <div class="w-full md:w-64 bg-white rounded-2xl shadow-sm border border-gray-200 flex flex-col overflow-hidden flex-shrink-0 h-48 md:h-full">
            <div class="p-4 border-b border-gray-100 bg-gray-50/50">
                <a href="{{ route('chatbot.index') }}" class="w-full flex items-center justify-center gap-2 bg-blue-600 hover:bg-blue-700 text-white font-medium py-2.5 px-4 rounded-xl shadow-sm transition-colors text-sm">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                    </svg>
                    Chat Baru
                </a>
            </div>

            <div class="flex-1 overflow-y-auto history-list p-2">
                @if($sessions->isEmpty())
                <div class="text-center py-6 px-4">
                    <div class="w-12 h-12 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-2">
                        <svg class="w-6 h-6 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"></path>
                        </svg>
                    </div>
                    <p class="text-xs text-gray-500">Belum ada riwayat percakapan.</p>
                </div>
                @else
                <p class="text-[10px] font-bold text-gray-400 uppercase tracking-wider mb-2 mt-2 px-2">Riwayat Anda</p>
                <div class="space-y-1">
                    @foreach($sessions as $s)
                    <div class="group relative flex items-center justify-between {{ ($currentSession && $currentSession->id == $s->id) ? 'bg-blue-50 text-blue-700' : 'text-gray-600 hover:bg-gray-50' }} rounded-lg transition-colors">
                        <a href="{{ route('chatbot.index', ['session_id' => $s->id]) }}" class="flex-1 truncate px-3 py-2.5 text-sm font-medium">
                            {{ $s->title }}
                        </a>

                        {{-- Tombol Hapus --}}
                        <form action="{{ route('chatbot.destroy_session', $s->id) }}" method="POST" class="absolute right-1 opacity-0 group-hover:opacity-100 transition-opacity" onsubmit="return confirm('Hapus percakapan ini?');">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="p-1.5 text-gray-400 hover:text-red-500 hover:bg-red-50 rounded-md">
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
             MAIN CHAT AREA
        ────────────────────────────────────────────────────────── --}}
        <div class="flex-1 flex flex-col bg-white rounded-2xl shadow-sm border border-gray-200 overflow-hidden h-full relative">

            {{-- Header Area --}}
            <div class="bg-white border-b border-gray-200 px-6 py-4 flex items-center justify-between z-20 shadow-sm relative">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-xl bg-blue-50 text-blue-600 flex items-center justify-center border border-blue-100 shadow-sm">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z" />
                        </svg>
                    </div>
                    <div>
                        <h2 class="text-gray-800 font-bold text-lg leading-tight" id="chat-title">
                            {{ $currentSession ? $currentSession->title : 'AI Audit Assistant' }}
                        </h2>
                        <div class="flex items-center gap-1.5 mt-0.5">
                            <span class="relative flex h-2 w-2">
                                <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-emerald-400 opacity-75"></span>
                                <span class="relative inline-flex rounded-full h-2 w-2 bg-emerald-500"></span>
                            </span>
                            <span class="text-gray-500 text-xs font-medium">Groq Engine (<span id="model-badge">llama-3.3-70b</span>)</span>
                        </div>
                    </div>
                </div>

                <div class="flex items-center gap-3">
                    @if(auth()->user()->role === 'admin')
                    <button onclick="toggleSettings()" title="Pengaturan Token API" class="flex items-center gap-2 text-gray-500 hover:text-blue-600 bg-gray-50 hover:bg-blue-50 px-3 py-2 rounded-lg text-sm border border-gray-200 transition-all duration-200">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"></path>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                        </svg>
                        <span class="hidden xl:inline font-medium">API Settings</span>
                    </button>
                    @endif
                </div>
            </div>

            {{-- API Settings Panel --}}
            @if(auth()->user()->role === 'admin')
            <div id="api-settings-panel" class="hidden bg-blue-50 border-b border-blue-100 px-6 py-4 absolute w-full z-30 shadow-md top-20">
                <h3 class="text-sm font-bold text-blue-800 mb-2">Konfigurasi API Groq</h3>
                <p class="text-xs text-blue-600 mb-3">Atur API Key untuk mengubah model AI. Buka file <code class="bg-white px-1 py-0.5 rounded border border-blue-200">.env</code> Anda dan pastikan nilai <code class="bg-white px-1 py-0.5 rounded border border-blue-200">GROQ_API_KEY</code> telah diset.</p>
                <div class="flex gap-2 items-center">
                    <input type="password" value="{{ env('GROQ_API_KEY') ? '************************' : '' }}" disabled class="bg-white border border-blue-200 text-gray-500 text-sm rounded-lg block w-full p-2 max-w-md" placeholder="API Key tidak diset di .env">
                    <a href="https://console.groq.com/keys" target="_blank" class="text-xs bg-white text-blue-600 border border-blue-200 px-3 py-2 rounded-lg hover:bg-blue-600 hover:text-white transition font-medium">Dapatkan API Key</a>
                </div>
                <button onclick="toggleSettings()" class="absolute top-4 right-4 text-blue-400 hover:text-blue-600">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </button>
            </div>
            @endif

            {{-- Background Pattern --}}
            <div class="absolute inset-0 opacity-[0.02] pointer-events-none z-0" style="background-image: radial-gradient(#3b82f6 1px, transparent 1px); background-size: 20px 20px;"></div>

            {{-- Messages Area --}}
            <div id="chat-messages" class="flex-1 overflow-y-auto p-5 space-y-6 relative z-10">

                @if(!$currentSession || count($messages) === 0)
                {{-- Welcome Message --}}
                <div class="flex items-start gap-3 msg-enter">
                    <div class="w-9 h-9 rounded-full bg-blue-600 flex items-center justify-center flex-shrink-0 shadow-md ring-4 ring-blue-50">
                        <svg class="w-5 h-5 text-white" fill="currentColor" viewBox="0 0 20 20">
                            <path d="M10 2a8 8 0 100 16A8 8 0 0010 2zm0 14a6 6 0 110-12 6 6 0 010 12zm-1-9h2v4H9V7zm0 6h2v2H9v-2z" />
                        </svg>
                    </div>
                    <div class="message-bubble bg-gray-50 border border-gray-200 text-gray-700 rounded-2xl rounded-tl-sm px-5 py-4 shadow-sm">
                        <p class="font-bold text-blue-700 text-sm mb-1">AI Audit Assistant</p>
                        <p class="text-sm leading-relaxed text-gray-800">
                            Halo, <strong class="text-gray-900">{{ Auth::user()->name }}</strong>! 👋
                        </p>
                        <p class="text-sm leading-relaxed mt-1 text-gray-600">
                            Saya telah dihubungkan dengan <strong>data sistem KAP</strong> ini. Anda bisa bertanya mengenai jumlah laporan audit, status klien, maupun standar ISA/SPAP secara umum.
                        </p>
                    </div>
                </div>
                @else
                {{-- Render riwayat pesan dari database --}}
                @foreach($messages as $msg)
                {{-- User Bubble --}}
                <div class="flex items-end justify-end gap-3 msg-enter mb-6">
                    <div class="text-right">
                        <div class="message-bubble inline-block bg-blue-600 text-white rounded-2xl rounded-br-sm px-5 py-3 shadow-md text-sm leading-relaxed text-left">{{ $msg->message }}</div>
                        <p class="text-[11px] text-gray-400 mt-1 pr-1 font-medium">{{ $msg->created_at->format('H:i') }}</p>
                    </div>
                    <div class="w-9 h-9 rounded-full bg-indigo-100 border-2 border-white flex items-center justify-center flex-shrink-0 text-indigo-700 font-bold text-sm shadow-sm">
                        {{ strtoupper(substr(Auth::user()->name, 0, 1)) }}
                    </div>
                </div>

                {{-- AI Bubble --}}
                <div class="flex items-start gap-3 msg-enter">
                    <div class="w-9 h-9 rounded-full bg-blue-600 flex items-center justify-center flex-shrink-0 shadow-md ring-4 ring-blue-50">
                        <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707" />
                        </svg>
                    </div>
                    <div>
                        <div class="message-bubble bg-white border border-gray-200 text-gray-700 rounded-2xl rounded-tl-sm px-5 py-4 shadow-sm text-sm leading-relaxed prose-ai">
                            {!! Str::markdown($msg->reply) !!}
                        </div>
                        <p class="text-[11px] text-gray-400 mt-1 pl-1 font-medium">{{ $msg->created_at->format('H:i') }}</p>
                    </div>
                </div>
                @endforeach
                @endif

                {{-- Anchor for scrolling --}}
                <div id="chat-bottom"></div>

            </div>

            {{-- Typing Indicator --}}
            <div id="typing-indicator" class="hidden px-5 pb-2 relative z-10">
                <div class="flex items-center gap-3">
                    <div class="w-9 h-9 rounded-full bg-blue-100 text-blue-600 flex items-center justify-center flex-shrink-0">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3" />
                        </svg>
                    </div>
                    <div class="bg-gray-100 border border-gray-200 rounded-2xl rounded-tl-sm px-4 py-3 flex items-center gap-1.5 shadow-sm">
                        <div class="typing-dot"></div>
                        <div class="typing-dot"></div>
                        <div class="typing-dot"></div>
                    </div>
                </div>
            </div>

            {{-- Quick Prompts (Only show if no history in current view) --}}
            @if(!$currentSession || count($messages) === 0)
            <div id="quick-prompts" class="px-5 pb-3 bg-white relative z-10 pt-2">
                <p class="text-xs text-gray-400 mb-2 font-semibold uppercase tracking-wider">Topik Cepat</p>
                <div class="flex flex-wrap gap-2">
                    @foreach([
                    '📊 Berapa total laporan audit saat ini?',
                    '📋 Apa itu opini WTP?',
                    '⚠️ Cara menilai risiko material',
                    '✅ Standar ISA 315',
                    ] as $prompt)
                    <button class="quick-prompt text-xs font-medium text-gray-600 bg-white border border-gray-200 px-3 py-1.5 rounded-full shadow-sm" onclick="setQuickPrompt(this)">
                        {{ $prompt }}
                    </button>
                    @endforeach
                </div>
            </div>
            @endif

            {{-- Input Area --}}
            <div class="px-5 py-4 bg-gray-50 border-t border-gray-200 relative z-20">
                <div class="flex items-end gap-3">
                    <div class="flex-1 bg-white border border-gray-300 rounded-xl px-4 py-3 flex items-end gap-2 shadow-sm transition-colors relative focus-within:border-blue-400 focus-within:ring-1 focus-within:ring-blue-400">
                        <textarea id="message-input" rows="1"
                            placeholder="Ketik pesan Anda di sini..."
                            class="flex-1 bg-transparent text-gray-800 placeholder-gray-400 text-sm resize-none border-none p-0 focus:ring-0 max-h-32"
                            style="min-height: 24px; overflow-y: hidden;"></textarea>
                        <div class="text-[10px] text-gray-400 whitespace-nowrap self-end pb-0.5 font-medium" id="char-count">0/2000</div>
                    </div>
                    <button id="send-btn"
                        class="w-12 h-12 bg-blue-600 hover:bg-blue-700 disabled:bg-blue-300 text-white rounded-xl flex items-center justify-center shadow-md shadow-blue-500/30 transition-all duration-200 send-btn flex-shrink-0"
                        onclick="sendMessage()">
                        <svg class="w-5 h-5 ml-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8" />
                        </svg>
                    </button>
                </div>
                <div class="flex justify-between items-center mt-2 px-1">
                    <p class="text-xs text-gray-400">
                        <kbd class="px-1.5 py-0.5 bg-gray-200 rounded border border-gray-300 text-gray-600 font-sans text-[10px]">Enter</kbd> kirim ·
                        <kbd class="px-1.5 py-0.5 bg-gray-200 rounded border border-gray-300 text-gray-600 font-sans text-[10px]">Shift+Enter</kbd> baris baru
                    </p>
                    <p class="text-[10px] text-gray-400 uppercase tracking-widest font-bold">Max Output 2048 Tokens</p>
                </div>
            </div>

        </div>
    </div>

    <script>
        const CSRF = document.querySelector('meta[name="csrf-token"]').content;
        const chatEndpoint = "{{ route('chatbot.send') }}";

        // Pass current session ID if it exists
        let sessionIdStr = "{{ $currentSession ? $currentSession->id : '' }}";
        let currentSessionId = sessionIdStr !== '' ? parseInt(sessionIdStr) : null;
        let isNewSession = "{{ $currentSession ? 'false' : 'true' }}" === 'true';

        // Scroll to bottom on load
        function scrollToBottom() {
            const chatMessages = document.getElementById('chat-messages');
            chatMessages.scrollTop = chatMessages.scrollHeight;
        }
        window.onload = scrollToBottom;

        function toggleSettings() {
            const panel = document.getElementById('api-settings-panel');
            if (panel) panel.classList.toggle('hidden');
        }

        // Auto-resize textarea
        const textarea = document.getElementById('message-input');
        const charCount = document.getElementById('char-count');
        textarea.addEventListener('input', function() {
            this.style.height = 'auto';
            this.style.height = Math.min(this.scrollHeight, 128) + 'px';
            charCount.textContent = this.value.length + '/2000';
            charCount.className = this.value.length > 1800 ?
                'text-[10px] text-red-500 whitespace-nowrap self-end pb-0.5 font-medium' :
                'text-[10px] text-gray-400 whitespace-nowrap self-end pb-0.5 font-medium';
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

        // Simple Markdown formatter for JS (since we use Laravel Str::markdown for history)
        function formatText(text) {
            return text
                .replace(/\*\*(.*?)\*\*/g, '<strong>$1</strong>')
                .replace(/\*(.*?)\*/g, '<em>$1</em>')
                .replace(/`(.*?)`/g, '<code>$1</code>')
                .replace(/^### (.*$)/gim, '<h3 class="font-bold text-gray-800 mt-3 mb-2 text-base">$1</h3>')
                .replace(/^## (.*$)/gim, '<h2 class="font-bold text-gray-900 mt-3 mb-2 text-lg">$1</h2>')
                .replace(/^# (.*$)/gim, '<h1 class="font-bold text-gray-900 mt-3 mb-2 text-xl">$1</h1>')
                .replace(/^\s*[-*]\s+(.*)/gim, '<li class="ml-4 list-disc text-gray-700">$1</li>')
                .replace(/(<li class="ml-4 list-disc.*<\/li>\n?)+/g, '<ul class="my-2 space-y-1">$&</ul>')
                .replace(/^\d+\.\s+(.*)/gim, '<li class="ml-4 list-decimal text-gray-700">$1</li>')
                .replace(/(<li class="ml-4 list-decimal.*<\/li>\n?)+/g, '<ol class="my-2 space-y-1">$&</ol>')
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
                    <div class="flex items-end justify-end gap-3 msg-enter mb-6">
                        <div class="text-right">
                            <div class="message-bubble inline-block bg-blue-600 text-white rounded-2xl rounded-br-sm px-5 py-3 shadow-md text-sm leading-relaxed text-left">${escapeHtml(text)}</div>
                            <p class="text-[11px] text-gray-400 mt-1 pr-1 font-medium">${time}</p>
                        </div>
                        <div class="w-9 h-9 rounded-full bg-indigo-100 border-2 border-white flex items-center justify-center flex-shrink-0 text-indigo-700 font-bold text-sm shadow-sm">
                            {{ strtoupper(substr(Auth::user()->name, 0, 1)) }}
                        </div>
                    </div>`;
            } else {
                msgs.innerHTML += `
                    <div class="flex items-start gap-3 msg-enter mb-6">
                        <div class="w-9 h-9 rounded-full bg-blue-600 flex items-center justify-center flex-shrink-0 shadow-md ring-4 ring-blue-50">
                            <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707"/></svg>
                        </div>
                        <div>
                            <div class="message-bubble bg-white border border-gray-200 text-gray-700 rounded-2xl rounded-tl-sm px-5 py-4 shadow-sm text-sm leading-relaxed prose-ai">${formatText(text)}</div>
                            <p class="text-[11px] text-gray-400 mt-1 pl-1 font-medium">${time}</p>
                        </div>
                    </div>`;
            }
            scrollToBottom();
        }

        function appendError(msg) {
            const msgs = document.getElementById('chat-messages');
            msgs.innerHTML += `
                <div class="flex justify-center msg-enter mb-6">
                    <div class="bg-red-50 border border-red-200 text-red-600 text-sm px-4 py-2.5 rounded-xl max-w-md text-center shadow-sm flex items-center gap-2 font-medium">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
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

            // Hide quick prompts if exist
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

                    // Update session ID if it was null
                    if (currentSessionId === null && data.session_id) {
                        currentSessionId = data.session_id;
                        // Reload page quietly to show history, or just change URL
                        window.history.pushState({}, '', '?session_id=' + data.session_id);
                        document.getElementById('chat-title').textContent = data.title;

                        // We could manually append the new session to the sidebar, 
                        // but a simple reload is robust enough for now if they click history
                        setTimeout(() => {
                            // Optional: reload to refresh the sidebar list for a completely new session
                            if (isNewSession) location.reload();
                        }, 2000);
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