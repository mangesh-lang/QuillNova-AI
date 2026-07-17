@extends('layouts.app')
@section('title', 'AI Interactive Chat')

@section('styles')
<!-- Prism.js Tomorrow Theme for code syntax highlighting -->
<link href="https://cdn.jsdelivr.net/npm/prismjs@1.29.0/themes/prism-tomorrow.min.css" rel="stylesheet">
<style>
    /* Chat layout styling */
    .chat-container {
        height: calc(100vh - 140px);
        display: flex;
        gap: 20px;
    }

    .chat-sidebar {
        width: 280px;
        flex-shrink: 0;
        display: flex;
        flex-direction: column;
        justify-content: space-between;
    }

    .chat-main {
        flex-grow: 1;
        display: flex;
        flex-direction: column;
        justify-content: space-between;
        min-width: 0;
    }

    /* Enhanced Glassmorphism styling for panels */
    .chat-sidebar, .chat-main {
        backdrop-filter: blur(20px) saturate(190%) !important;
        -webkit-backdrop-filter: blur(20px) saturate(190%) !important;
        border: 1px solid var(--glass-border) !important;
        background: rgba(255, 255, 255, 0.45) !important; /* light mode glass */
    }
    .dark-theme .chat-sidebar, .dark-theme .chat-main {
        background: rgba(15, 23, 42, 0.45) !important; /* dark mode glass */
    }

    .session-list {
        overflow-y: auto;
        flex-grow: 1;
        max-height: calc(100vh - 280px);
    }

    .session-item {
        padding: 12px 15px;
        border-radius: 10px;
        border: 1px solid var(--glass-border);
        background: rgba(255, 255, 255, 0.1);
        transition: var(--transition-smooth);
        cursor: pointer;
        display: flex;
        align-items: center;
        justify-content: space-between;
        margin-bottom: 8px;
        text-decoration: none;
    }
    .dark-theme .session-item {
        background: rgba(255, 255, 255, 0.02);
    }
    .session-item:hover, .session-item.active {
        background: rgba(99, 102, 241, 0.12) !important;
        border-color: var(--accent-primary) !important;
    }
    .session-item.active .session-title {
        color: var(--accent-primary) !important;
        font-weight: 600;
    }

    .chat-messages {
        flex-grow: 1;
        overflow-y: auto;
        padding: 20px;
        display: flex;
        flex-direction: column;
        gap: 20px;
        max-height: calc(100vh - 270px);
    }

    /* Message bubbles with Glassmorphic visual look */
    .msg-row {
        display: flex;
        width: 100%;
    }
    .msg-row.user {
        justify-content: flex-end;
    }
    .msg-row.assistant {
        justify-content: flex-start;
    }

    .msg-bubble {
        max-width: 80%;
        padding: 16px 20px;
        border-radius: 16px;
        box-shadow: var(--shadow-sm);
        position: relative;
        line-height: 1.6;
    }
    .msg-row.user .msg-bubble {
        background: linear-gradient(135deg, rgba(99, 102, 241, 0.85), rgba(129, 140, 248, 0.85)) !important;
        backdrop-filter: blur(10px);
        -webkit-backdrop-filter: blur(10px);
        color: #ffffff;
        border-bottom-right-radius: 4px;
        border: 1px solid rgba(255, 255, 255, 0.15) !important;
    }
    .msg-row.assistant .msg-bubble {
        background: rgba(255, 255, 255, 0.5) !important;
        backdrop-filter: blur(10px);
        -webkit-backdrop-filter: blur(10px);
        border: 1px solid var(--glass-border) !important;
        border-bottom-left-radius: 4px;
        color: var(--text-primary);
    }
    .dark-theme .msg-row.assistant .msg-bubble {
        background: rgba(30, 41, 59, 0.45) !important;
    }

    /* Markdown styling inside bubbles */
    .msg-bubble p:last-child {
        margin-bottom: 0;
    }
    .msg-bubble code {
        font-size: 0.85rem;
        background: rgba(0, 0, 0, 0.25);
        padding: 2px 6px;
        border-radius: 4px;
        color: #f87171;
    }
    .msg-bubble pre {
        margin: 12px 0;
        border-radius: 8px;
        overflow: hidden;
    }
    .msg-bubble pre code {
        background: transparent !important;
        padding: 0;
        color: inherit;
    }

    .chat-input-area {
        background: transparent !important;
        border-top: 1px solid var(--glass-border);
        padding: 15px 0 0 0;
    }

    /* Glassy Input field styling */
    .chat-input-area textarea {
        background: rgba(255, 255, 255, 0.3) !important;
        backdrop-filter: blur(8px);
        -webkit-backdrop-filter: blur(8px);
        border: 1px solid var(--glass-border) !important;
        border-radius: 14px !important;
        resize: none;
        height: 52px;
        transition: var(--transition-smooth);
        padding: 12px 16px;
    }
    .dark-theme .chat-input-area textarea {
        background: rgba(15, 23, 42, 0.3) !important;
    }
    .chat-input-area textarea:focus {
        background: rgba(255, 255, 255, 0.5) !important;
        border-color: var(--accent-primary) !important;
        box-shadow: 0 0 0 2px rgba(99, 102, 241, 0.15) !important;
    }
    .dark-theme .chat-input-area textarea:focus {
        background: rgba(15, 23, 42, 0.5) !important;
    }

    .btn-message-send {
        background-color: var(--accent-primary);
        border: none;
        border-radius: 10px;
        width: 48px;
        height: 48px;
        display: flex;
        align-items: center;
        justify-content: center;
        color: #ffffff;
        transition: var(--transition-smooth);
    }
    .btn-message-send:hover {
        background-color: #6366f1;
        transform: scale(1.05);
    }

    /* Scrollbars customization */
    .chat-messages::-webkit-scrollbar, .session-list::-webkit-scrollbar {
        width: 6px;
    }
    .chat-messages::-webkit-scrollbar-track, .session-list::-webkit-scrollbar-track {
        background: transparent;
    }
    .chat-messages::-webkit-scrollbar-thumb, .session-list::-webkit-scrollbar-thumb {
        background: rgba(99, 102, 241, 0.2);
        border-radius: 10px;
    }
    .chat-messages::-webkit-scrollbar-thumb:hover, .session-list::-webkit-scrollbar-thumb:hover {
        background: rgba(99, 102, 241, 0.4);
    }

    /* Typing bubble */
    .typing-indicator {
        display: flex;
        align-items: center;
        gap: 4px;
        padding: 6px 0;
    }
    .typing-indicator span {
        width: 8px;
        height: 8px;
        background-color: var(--text-secondary);
        border-radius: 50%;
        display: inline-block;
        animation: bounce 1.4s infinite ease-in-out both;
    }
    .typing-indicator span:nth-child(1) { animation-delay: -0.32s; }
    .typing-indicator span:nth-child(2) { animation-delay: -0.16s; }
    @keyframes bounce {
        0%, 80%, 100% { transform: scale(0); }
        40% { transform: scale(1.0); }
    }
</style>
@endsection

@section('content')
<div class="chat-container" x-data="chatComponent({{ $activeSession ? $activeSession->id : 'null' }})">
    <!-- Conversations Sidebar Left -->
    <div class="chat-sidebar glass-card p-3">
        <div>
            <button class="btn btn-custom w-100 mb-3 fw-semibold py-2 d-flex align-items-center justify-content-center gap-2" @click="createSession()">
                <i class="bi bi-chat-left-text-fill"></i> New Conversation
            </button>
            
            <h6 class="text-muted-custom fw-bold px-2 mb-3" style="font-size: 0.8rem;">CHAT HISTORY</h6>
            
            <div class="session-list">
                @forelse($sessions as $s)
                    <div class="session-item text-primary {{ $activeSession && $activeSession->id === $s->id ? 'active' : '' }}">
                        <a href="{{ route('chat.show', $s->id) }}" class="text-decoration-none text-primary text-truncate flex-grow-1 me-2">
                            <i class="bi bi-chat-left text-muted-custom me-2" style="font-size: 0.9rem;"></i>
                            <span id="session-title-{{ $s->id }}">{{ $s->title }}</span>
                        </a>
                        <div class="d-flex gap-1.5 opacity-75">
                            <button class="btn btn-sm btn-link text-muted-custom p-0 border-0" @click.stop="renameSession({{ $s->id }}, '{{ addslashes($s->title) }}')"><i class="bi bi-pencil-square" style="font-size: 0.85rem;"></i></button>
                            <button class="btn btn-sm btn-link text-danger p-0 border-0" @click.stop="deleteSession({{ $s->id }})"><i class="bi bi-trash" style="font-size: 0.85rem;"></i></button>
                        </div>
                    </div>
                @empty
                    <div class="text-center py-4 text-muted-custom small">No past sessions.</div>
                @endforelse
            </div>
        </div>

        <div class="p-2 border-top border-custom text-muted-custom text-center" style="font-size: 0.75rem;">
            Usage applies to daily limits.
        </div>
    </div>

    <!-- Active Chat Panel Right -->
    <div class="chat-main glass-card p-4">
        @if($activeSession)
            <!-- Chat Header -->
            <div class="d-flex justify-content-between align-items-center pb-3 border-bottom border-custom">
                <div>
                    <h5 class="fw-bold mb-0 text-primary">{{ $activeSession->title }}</h5>
                    <span class="text-muted-custom" style="font-size: 0.8rem;">Active Session #{{ $activeSession->id }}</span>
                </div>
                
                <div class="d-flex align-items-center gap-2">
                    <span class="text-muted-custom small fw-semibold">Model:</span>
                    <!-- Simple selection logic, model triggers session update if changed -->
                    <select class="form-select form-select-sm text-primary border-custom rounded-pill px-3" style="background: var(--bg-secondary); width: 130px;"
                        @change="fetch('/chat-api/session/{{ $activeSession->id }}', {
                            method: 'PUT',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]').getAttribute('content')
                            },
                            body: JSON.stringify({ title: '{{ $activeSession->title }}', model: $event.target.value })
                        }).then(r => r.json())">
                        <option value="openai" {{ $activeSession->model === 'openai' ? 'selected' : '' }}>OpenAI</option>
                        <option value="gemini" {{ $activeSession->model === 'gemini' ? 'selected' : '' }}>Gemini</option>
                    </select>
                </div>
            </div>

            <!-- Messages Window -->
            <div class="chat-messages" id="chat-messages-container">
                @foreach($messages as $msg)
                    <div class="msg-row {{ $msg->role }}">
                        <div class="msg-bubble">
                            @if($msg->role === 'assistant')
                                <!-- Server-side markdown rendering check or handled in layout window.onload -->
                                <div class="markdown-body text-primary">{!! $msg->content !!}</div>
                                <button class="btn btn-sm btn-link text-muted-custom p-0 border-0 bg-transparent mt-2 d-block small" 
                                    onclick="navigator.clipboard.writeText(`{{ addslashes($msg->content) }}`); Swal.fire({ toast: true, position: 'top-end', icon: 'success', title: 'Copied!', showConfirmButton: false, timer: 1500 });">
                                    <i class="bi bi-clipboard me-1"></i> Copy Response
                                </button>
                            @else
                                <div class="text-white">{{ $msg->content }}</div>
                            @endif
                        </div>
                    </div>
                @endforeach
            </div>

            <!-- Messages Inputs -->
            <div class="chat-input-area">
                <form @submit.prevent="sendMessage()" class="d-flex gap-2">
                    <textarea id="chat-input" class="form-control text-primary border-custom" placeholder="Type your message here... (Enter to send, Shift + Enter for new line)" 
                        @keydown.enter="if(!$event.shiftKey) { $event.preventDefault(); sendMessage(); }"></textarea>
                    
                    <button type="submit" class="btn-message-send" x-show="!loading" title="Send Message">
                        <i class="bi bi-arrow-up-short fs-4"></i>
                    </button>
                    <button type="button" class="btn btn-danger rounded-3 p-0" style="width: 48px; height: 48px; display: flex; align-items: center; justify-content: center; background-color: #ef4444; border: none; box-shadow: 0 4px 12px rgba(239, 68, 68, 0.25);" x-show="loading" @click="stopGeneration()" title="Stop Generation">
                        <i class="bi bi-stop-fill fs-4"></i>
                    </button>
                </form>
            </div>
        @else
            <!-- Empty Chat Welcome -->
            <div class="d-flex flex-column align-items-center justify-content-center h-100 text-center text-muted-custom py-5">
                <div class="bg-primary bg-opacity-10 text-primary rounded-circle d-flex align-items-center justify-content-center mb-4" style="width: 80px; height: 80px; background-color: rgba(99, 102, 241, 0.1);">
                    <i class="bi bi-chat-left-text-fill fs-1 text-primary"></i>
                </div>
                <h4 class="fw-bold text-primary">Interactive AI Chat</h4>
                <p class="mb-4 small" style="max-width: 320px;">Launch a new conversational session to brainstorm ideas, check code logic, or analyze facts.</p>
                <button class="btn btn-custom px-4 fw-semibold rounded-3 py-2" @click="createSession()">
                    Start Chat Session
                </button>
            </div>
        @endif
    </div>
</div>
@endsection

@section('scripts')
<!-- Marked.js for markdown parsing -->
<script src="https://cdn.jsdelivr.net/npm/marked/marked.min.js"></script>
<!-- Prism.js for syntax highlighting -->
<script src="https://cdn.jsdelivr.net/npm/prismjs@1.29.0/prism.min.js"></script>

<script>
    document.addEventListener('alpine:init', () => {
        Alpine.data('chatComponent', (sessionId) => ({
            activeSessionId: sessionId,
            loading: false,
            abortController: null,

            createSession() {
                fetch('{{ route('chat.session.create') }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]').getAttribute('content')
                    },
                    body: JSON.stringify({ model: 'openai' })
                })
                .then(res => res.json())
                .then(data => {
                    if(data.success) {
                        window.location.href = '/chat/' + data.session_id;
                    }
                });
            },

            deleteSession(id) {
                Swal.fire({
                    title: 'Delete Conversation?',
                    text: 'This will permanently remove all message logs for this session.',
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#ef4444',
                    confirmButtonText: 'Yes, delete it'
                }).then((result) => {
                    if (result.isConfirmed) {
                        fetch('/chat-api/session/' + id, {
                            method: 'DELETE',
                            headers: {
                                'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]').getAttribute('content')
                            }
                        })
                        .then(res => res.json())
                        .then(data => {
                            if (data.success) {
                                window.location.href = '/chat';
                            }
                        });
                    }
                });
            },

            renameSession(id, currentTitle) {
                Swal.fire({
                    title: 'Rename Conversation',
                    input: 'text',
                    inputValue: currentTitle,
                    showCancelButton: true,
                    confirmButtonText: 'Save',
                    inputValidator: (value) => {
                        if (!value || value.trim() === '') {
                            return 'Please enter a valid title!';
                        }
                    }
                }).then((result) => {
                    if (result.isConfirmed && result.value) {
                        fetch('/chat-api/session/' + id, {
                            method: 'PUT',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]').getAttribute('content')
                            },
                            body: JSON.stringify({ title: result.value })
                        })
                        .then(res => res.json())
                        .then(data => {
                            if (data.success) {
                                window.location.reload();
                            }
                        });
                    }
                });
            },

            sendMessage() {
                const input = document.getElementById('chat-input');
                const content = input.value.trim();
                if (!content || this.loading) return;

                input.value = '';
                this.loading = true;
                this.abortController = new AbortController();

                this.appendMessage('user', content);
                this.scrollToBottom();

                const typingId = this.appendTypingBubble();
                this.scrollToBottom();

                fetch('/chat-api/session/' + this.activeSessionId + '/message', {
                    method: 'POST',
                    signal: this.abortController.signal,
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]').getAttribute('content')
                    },
                    body: JSON.stringify({ content: content })
                })
                .then(res => res.json().then(data => ({ status: res.status, body: data })))
                .then(res => {
                    document.getElementById(typingId).remove();
                    this.loading = false;

                    if (res.status === 200) {
                        this.appendMessage('assistant', res.body.assistant_message.content);
                        const sideItem = document.getElementById('session-title-' + this.activeSessionId);
                        if (sideItem) {
                            sideItem.textContent = res.body.session_title;
                        }
                        this.scrollToBottom();
                    } else {
                        Swal.fire({
                            icon: 'error',
                            title: 'Limits / Model Error',
                            text: res.body.error || 'Failed to generate response.'
                        });
                    }
                })
                .catch(err => {
                    if (err.name === 'AbortError') {
                        document.getElementById(typingId).remove();
                        this.appendMessage('assistant', '*Generation cancelled by user.*');
                    } else {
                        document.getElementById(typingId).remove();
                        Swal.fire({ icon: 'error', title: 'Connection Error', text: 'Failed to communicate with AI server.' });
                    }
                    this.loading = false;
                    this.scrollToBottom();
                });
            },

            stopGeneration() {
                if (this.abortController) {
                    this.abortController.abort();
                }
            },

            appendMessage(role, text) {
                const container = document.getElementById('chat-messages-container');
                const row = document.createElement('div');
                row.className = 'msg-row ' + role;
                
                let bubble = document.createElement('div');
                bubble.className = 'msg-bubble';
                
                if (role === 'assistant') {
                    bubble.innerHTML = marked.parse(text);
                    
                    let copyBtn = document.createElement('button');
                    copyBtn.className = 'btn btn-sm btn-link text-muted-custom p-0 border-0 bg-transparent mt-2 d-block small';
                    copyBtn.innerHTML = '<i class="bi bi-clipboard me-1"></i> Copy Response';
                    copyBtn.onclick = () => {
                        navigator.clipboard.writeText(text);
                        Swal.fire({ toast: true, position: 'top-end', icon: 'success', title: 'Copied!', showConfirmButton: false, timer: 1500 });
                    };
                    bubble.appendChild(copyBtn);
                } else {
                    bubble.textContent = text;
                }

                row.appendChild(bubble);
                container.appendChild(row);
                
                if (role === 'assistant') {
                    Prism.highlightAllUnder(bubble);
                }
            },

            appendTypingBubble() {
                const id = 'typing-' + Date.now();
                const container = document.getElementById('chat-messages-container');
                const row = document.createElement('div');
                row.className = 'msg-row assistant';
                row.id = id;

                row.innerHTML = `
                    <div class="msg-bubble">
                        <div class="typing-indicator">
                            <span></span><span></span><span></span>
                        </div>
                    </div>
                `;

                container.appendChild(row);
                return id;
            },

            scrollToBottom() {
                const container = document.getElementById('chat-messages-container');
                if (container) {
                    container.scrollTop = container.scrollHeight;
                }
            }
        }));
    });

    document.addEventListener('DOMContentLoaded', function () {
        document.querySelectorAll('.markdown-body').forEach(function(el) {
            const raw = el.textContent || el.innerText;
            el.innerHTML = marked.parse(raw);
            Prism.highlightAllUnder(el);
        });

        const container = document.getElementById('chat-messages-container');
        if (container) {
            container.scrollTop = container.scrollHeight;
        }
    });
</script>
@endsection
