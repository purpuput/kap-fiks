<x-app-layout>
    <x-slot name="title">AI Audit Assistant</x-slot>

    <style>
        .chat-container {
            height: calc(100vh - 220px);
            min-height: 400px;
        }
        .message-bubble {
            max-width: 75%;
            word-break: break-word;
        }
        .typing-dot {
            width: 8px; height: 8px;
            border-radius: 50%;
            background: #6b7280;
            animation: typingBounce 1.2s infinite;
        }
        .typing-dot:nth-child(2) { animation-delay: 0.2s; }
        .typing-dot:nth-child(3) { animation-delay: 0.4s; }
        @keyframes typingBounce {
            0%, 60%, 100% { transform: translateY(0); opacity: 0.4; }
            30% { transform: translateY(-8px); opacity: 1; }
        }
        .quick-prompt:hover {
            background: #1e3a5f;
            border-color: #3b82f6;
            color: #93c5fd;
        }
        #chat-messages::-webkit-scrollbar { width: 5px; }
        #chat-messages::-webkit-scrollbar-track { background: #1e293b; }
        #chat-messages::-webkit-scrollbar-thumb { background: #334155; border-radius: 5px; }
        .prose-ai p { margin-bottom: 0.5rem; }
        .prose-ai ul { list-style: disc; padding-left: 1.25rem; margin-bottom: 0.5rem; }
        .prose-ai ol { list-style: decimal; padding-left: 1.25rem; margin-bottom: 0.5rem; }
        .prose-ai strong { font-weight: 700; color: #e2e8f0; }
        .prose-ai code { background: #1e293b; padding: 0.1rem 0.3rem; border-radius: 4px; font-size: 0.85em; }
        .msg-enter { animation: msgSlideIn 0.25s ease-out; }
        @keyframes msgSlideIn {
            from { opacity: 0; transform: translateY(10px); }
            to   { opacity: 1; transform: translateY(0); }
        }
        #message-input:focus { outline: none; box-shadow: 0 0 0 2px #3b82f6; }
        .send-btn:disabled { opacity: 0.5; cursor: not-allowed; }
    </style>

    <div class="flex flex-col h-full" style="height: calc(100vh - 80px);">

        {{-- Header --}}
        <div class="bg-gradient-to-r from-blue-900 to-indigo-900 rounded-2xl shadow-xl mb-4 px-6 py-4 flex items-center justify-between">
            <div class="flex items-center gap-3">
                <div class="w-11 h-11 rounded-full bg-blue-500 flex items-center justify-center shadow-lg">
                    <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z"/>
                    </svg>
                </div>
                <div>
                    <h2 class="text-white font-bold text-lg leading-tight">AI Audit Assistant</h2>
                    <div class="flex items-center gap-1.5 mt-0.5">
                        <span class="w-2 h-2 rounded-full bg-emerald-400 animate-pulse"></span>
                        <span class="text-emerald-300 text-xs font-medium">Online · Groq AI (<span id="model-badge">llama-3.3-70b</span>)</span>
                    </div>
                </div>
            </div>
            <button id="clear-btn" title="Hapus percakapan"
                class="flex items-center gap-2 text-slate-300 hover:text-red-400 hover:bg-red-900/30 px-3 py-2 rounded-xl text-sm transition-all duration-200">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                </svg>
                <span class="hidden sm:inline">Hapus Chat</span>
            </button>
        </div>

        {{-- Chat Area --}}
        <div class="flex-1 bg-slate-900 rounded-2xl shadow-xl flex flex-col overflow-hidden border border-slate-700">

            {{-- Messages --}}
            <div id="chat-messages" class="flex-1 overflow-y-auto p-5 space-y-4">

                {{-- Welcome Message --}}
                <div id="welcome-msg" class="flex items-start gap-3 msg-enter">
                    <div class="w-9 h-9 rounded-full bg-blue-600 flex items-center justify-center flex-shrink-0 shadow">
                        <svg class="w-5 h-5 text-white" fill="currentColor" viewBox="0 0 20 20">
                            <path d="M10 2a8 8 0 100 16A8 8 0 0010 2zm0 14a6 6 0 110-12 6 6 0 010 12zm-1-9h2v4H9V7zm0 6h2v2H9v-2z"/>
                        </svg>
                    </div>
                    <div class="message-bubble bg-slate-800 text-slate-200 rounded-2xl rounded-tl-sm px-4 py-3 shadow border border-slate-700">
                        <p class="font-semibold text-blue-300 text-sm mb-1">AI Audit Assistant</p>
                        <p class="text-sm leading-relaxed">
                            Halo, <strong class="text-white">{{ Auth::user()->name }}</strong>! 👋 Saya siap membantu proses audit Anda.
                        </p>
                        <p class="text-sm leading-relaxed mt-1 text-slate-400">
                            Tanyakan apa saja seputar <span class="text-blue-300">standar audit (ISA/SPAP)</span>, prosedur audit, opini, atau regulasi akuntan publik.
                        </p>
                    </div>
                </div>

            </div>

            {{-- Typing Indicator --}}
            <div id="typing-indicator" class="hidden px-5 pb-2">
                <div class="flex items-center gap-3">
                    <div class="w-9 h-9 rounded-full bg-blue-600 flex items-center justify-center flex-shrink-0">
                        <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3"/>
                        </svg>
                    </div>
                    <div class="bg-slate-800 border border-slate-700 rounded-2xl rounded-tl-sm px-4 py-3 flex items-center gap-1.5">
                        <div class="typing-dot"></div>
                        <div class="typing-dot"></div>
                        <div class="typing-dot"></div>
                    </div>
                </div>
            </div>

            {{-- Quick Prompts --}}
            <div id="quick-prompts" class="px-5 py-3 border-t border-slate-700 bg-slate-900">
                <p class="text-xs text-slate-500 mb-2 font-medium uppercase tracking-wider">Pertanyaan Cepat</p>
                <div class="flex flex-wrap gap-2">
                    @foreach([
                        '📋 Apa itu opini WTP?',
                        '🔍 Prosedur audit piutang',
                        '⚠️ Cara menilai risiko material',
                        '📄 Isi laporan auditor independen',
                        '✅ Standar ISA 315 itu apa?',
                        '💡 Sampling audit statistik',
                    ] as $prompt)
                    <button class="quick-prompt text-xs text-slate-300 bg-slate-800 border border-slate-600 px-3 py-1.5 rounded-full transition-all duration-200"
                        onclick="setQuickPrompt(this)">
                        {{ $prompt }}
                    </button>
                    @endforeach
                </div>
            </div>

            {{-- Input Area --}}
            <div class="px-5 py-4 bg-slate-850 border-t border-slate-700 bg-slate-900">
                <div class="flex items-end gap-3">
                    <div class="flex-1 bg-slate-800 border border-slate-600 rounded-2xl px-4 py-3 flex items-end gap-2 focus-within:border-blue-500 transition-colors">
                        <textarea id="message-input" rows="1"
                            placeholder="Tanyakan sesuatu tentang audit..."
                            class="flex-1 bg-transparent text-slate-200 placeholder-slate-500 text-sm resize-none outline-none max-h-32"
                            style="min-height: 24px; overflow-y: hidden;"></textarea>
                        <div class="text-xs text-slate-600 whitespace-nowrap self-end pb-0.5" id="char-count">0/2000</div>
                    </div>
                    <button id="send-btn"
                        class="w-12 h-12 bg-blue-600 hover:bg-blue-500 disabled:opacity-50 text-white rounded-2xl flex items-center justify-center shadow-lg transition-all duration-200 hover:scale-105 active:scale-95 send-btn flex-shrink-0"
                        onclick="sendMessage()">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"/>
                        </svg>
                    </button>
                </div>
                <p class="text-xs text-slate-600 mt-2 text-center">
                    Enter untuk kirim · Shift+Enter untuk baris baru · Didukung oleh <span class="text-blue-400">Groq AI</span>
                </p>
            </div>

        </div>
    </div>

    <script>
        const CSRF = document.querySelector('meta[name="csrf-token"]').content;
        const chatEndpoint = "{{ route('chatbot.send') }}";
        let conversationHistory = [];

        // Auto-resize textarea
        const textarea = document.getElementById('message-input');
        const charCount = document.getElementById('char-count');
        textarea.addEventListener('input', function () {
            this.style.height = 'auto';
            this.style.height = Math.min(this.scrollHeight, 128) + 'px';
            charCount.textContent = this.value.length + '/2000';
            charCount.className = this.value.length > 1800
                ? 'text-xs text-red-400 whitespace-nowrap self-end pb-0.5'
                : 'text-xs text-slate-600 whitespace-nowrap self-end pb-0.5';
        });

        // Enter to send
        textarea.addEventListener('keydown', function (e) {
            if (e.key === 'Enter' && !e.shiftKey) {
                e.preventDefault();
                sendMessage();
            }
        });

        // Quick prompt
        function setQuickPrompt(btn) {
            textarea.value = btn.textContent.trim().replace(/^[\u{1F300}-\u{1FFFF}\u2600-\u27BF]\s*/u, '');
            textarea.dispatchEvent(new Event('input'));
            textarea.focus();
        }

        // Clear chat
        document.getElementById('clear-btn').addEventListener('click', function () {
            if (!confirm('Hapus seluruh percakapan?')) return;
            conversationHistory = [];
            const msgs = document.getElementById('chat-messages');
            msgs.innerHTML = `
                <div class="flex items-start gap-3 msg-enter">
                    <div class="w-9 h-9 rounded-full bg-blue-600 flex items-center justify-center flex-shrink-0 shadow">
                        <svg class="w-5 h-5 text-white" fill="currentColor" viewBox="0 0 20 20">
                            <path d="M10 2a8 8 0 100 16A8 8 0 0010 2zm0 14a6 6 0 110-12 6 6 0 010 12zm-1-9h2v4H9V7zm0 6h2v2H9v-2z"/>
                        </svg>
                    </div>
                    <div class="message-bubble bg-slate-800 text-slate-200 rounded-2xl rounded-tl-sm px-4 py-3 shadow border border-slate-700">
                        <p class="font-semibold text-blue-300 text-sm mb-1">AI Audit Assistant</p>
                        <p class="text-sm">Percakapan telah direset. Ada yang bisa saya bantu? 😊</p>
                    </div>
                </div>`;
            document.getElementById('quick-prompts').classList.remove('hidden');
        });

        // Format markdown-like text
        function formatText(text) {
            return text
                .replace(/\*\*(.*?)\*\*/g, '<strong>$1</strong>')
                .replace(/\*(.*?)\*/g, '<em>$1</em>')
                .replace(/`(.*?)`/g, '<code>$1</code>')
                .replace(/^### (.*$)/gim, '<h3 class="font-bold text-blue-300 mt-2 mb-1">$1</h3>')
                .replace(/^## (.*$)/gim,  '<h2 class="font-bold text-blue-200 mt-2 mb-1 text-base">$1</h2>')
                .replace(/^# (.*$)/gim,   '<h1 class="font-bold text-blue-100 mt-2 mb-1 text-lg">$1</h1>')
                .replace(/^\s*[-*]\s+(.*)/gim, '<li class="ml-4 list-disc">$1</li>')
                .replace(/(<li.*<\/li>\n?)+/g, '<ul class="my-1">$&</ul>')
                .replace(/^\d+\.\s+(.*)/gim, '<li class="ml-4 list-decimal">$1</li>')
                .replace(/\n/g, '<br>');
        }

        // Append message bubble
        function appendMessage(role, text, timestamp) {
            const msgs = document.getElementById('chat-messages');
            const time = timestamp || new Date().toLocaleTimeString('id-ID', {hour:'2-digit', minute:'2-digit'});

            if (role === 'user') {
                msgs.innerHTML += `
                    <div class="flex items-end justify-end gap-2 msg-enter">
                        <div class="text-right">
                            <div class="message-bubble bg-blue-600 text-white rounded-2xl rounded-br-sm px-4 py-3 shadow-lg text-sm leading-relaxed">${escapeHtml(text)}</div>
                            <p class="text-xs text-slate-500 mt-1 pr-1">${time}</p>
                        </div>
                        <div class="w-9 h-9 rounded-full bg-blue-700 border-2 border-blue-500 flex items-center justify-center flex-shrink-0 text-white font-bold text-sm shadow">
                            {{ strtoupper(substr(Auth::user()->name, 0, 1)) }}
                        </div>
                    </div>`;
            } else {
                msgs.innerHTML += `
                    <div class="flex items-start gap-3 msg-enter">
                        <div class="w-9 h-9 rounded-full bg-blue-600 flex items-center justify-center flex-shrink-0 shadow">
                            <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707"/>
                            </svg>
                        </div>
                        <div>
                            <div class="message-bubble bg-slate-800 border border-slate-700 text-slate-200 rounded-2xl rounded-tl-sm px-4 py-3 shadow text-sm leading-relaxed prose-ai">${formatText(text)}</div>
                            <p class="text-xs text-slate-500 mt-1 pl-1">${time}</p>
                        </div>
                    </div>`;
            }
            msgs.scrollTop = msgs.scrollHeight;
        }

        // Append error bubble
        function appendError(msg) {
            const msgs = document.getElementById('chat-messages');
            msgs.innerHTML += `
                <div class="flex justify-center msg-enter">
                    <div class="bg-red-900/40 border border-red-700 text-red-300 text-sm px-4 py-2 rounded-xl max-w-md text-center">
                        ⚠️ ${escapeHtml(msg)}
                    </div>
                </div>`;
            msgs.scrollTop = msgs.scrollHeight;
        }

        function escapeHtml(str) {
            return str.replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;').replace(/"/g,'&quot;');
        }

        async function sendMessage() {
            const input = document.getElementById('message-input');
            const sendBtn = document.getElementById('send-btn');
            const message = input.value.trim();
            if (!message) return;

            // Hide quick prompts after first message
            document.getElementById('quick-prompts').classList.add('hidden');

            appendMessage('user', message);
            input.value = '';
            input.style.height = 'auto';
            charCount.textContent = '0/2000';

            // Show typing indicator
            sendBtn.disabled = true;
            document.getElementById('typing-indicator').classList.remove('hidden');
            document.getElementById('chat-messages').scrollTop = 99999;

            try {
                const res = await fetch(chatEndpoint, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': CSRF,
                        'Accept': 'application/json',
                    },
                    body: JSON.stringify({
                        message: message,
                        history: conversationHistory,
                    }),
                });

                const data = await res.json();

                if (!res.ok || data.error) {
                    appendError(data.error || 'Terjadi kesalahan tak terduga.');
                } else {
                    // Update model badge
                    if (data.model) {
                        document.getElementById('model-badge').textContent = data.model.split('-').slice(0,3).join('-');
                    }
                    // Save to history
                    conversationHistory.push({ role: 'user', content: message });
                    conversationHistory.push({ role: 'assistant', content: data.reply });
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