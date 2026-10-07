<!DOCTYPE html>
<html lang="bn">
<head>
    <meta charset="UTF-8">
    <title>Laravel AI Chat</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <style>

        * {
            box-sizing: border-box;
        }

        body {
            margin: 0;
            font-family:
                Inter,
                "Noto Sans Bengali",
                "Segoe UI",
                sans-serif;

            background: #f1f5f9;
            color: #1e293b;
        }


        /* =========================
        MAIN APP
        ========================= */

        .chat-app {
            width: 100%;
            max-width: 1200px;
            height: 90vh;

            margin: 5vh auto;

            display: flex;

            background: #ffffff;

            border-radius: 18px;

            overflow: hidden;

            box-shadow:
                0 20px 50px rgba(15, 23, 42, 0.12);
        }


        /* =========================
        SIDEBAR
        ========================= */

        #sidebar {
            width: 260px;

            display: flex;
            flex-direction: column;

            background: #0f172a;
            color: white;
        }


        .sidebar-header {
            padding: 20px;
            border-bottom: 1px solid rgba(255,255,255,0.08);
        }


        .logo {
            font-size: 19px;
            font-weight: 700;

            margin-bottom: 18px;

            display: flex;
            align-items: center;
            gap: 8px;
        }


        .new-chat-btn {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 7px;

            width: 100%;

            padding: 11px 14px;

            border-radius: 9px;

            background: #2563eb;

            color: white;

            text-decoration: none;

            font-size: 14px;
            font-weight: 600;

            transition: 0.2s;
        }


        .new-chat-btn:hover {
            background: #1d4ed8;
        }


        .history-title {
            padding: 18px 20px 10px;

            font-size: 12px;

            color: #94a3b8;

            text-transform: uppercase;

            letter-spacing: 0.5px;
        }


        .conversation-list {
            flex: 1;

            padding: 5px 10px;

            overflow-y: auto;
        }


        .sidebar-footer {
            padding: 15px 20px;

            font-size: 11px;

            color: #64748b;

            border-top: 1px solid rgba(255,255,255,0.08);
        }

        .edit-title-input {
            flex: 1;
            padding: 2px 4px;
            border: 1px solid #2563eb;
            border-radius: 4px;
            font-size: inherit;
        }


        /* =========================
        MAIN CHAT
        ========================= */

        .chat-main {
            flex: 1;

            display: flex;
            flex-direction: column;

            min-width: 0;

            background: #f8fafc;
        }


        /* =========================
        HEADER
        ========================= */

        .chat-header {
            height: 75px;

            display: flex;
            align-items: center;

            padding: 0 25px;

            background: white;

            border-bottom: 1px solid #e2e8f0;
        }


        .bot-info {
            display: flex;
            align-items: center;

            gap: 12px;
        }


        .bot-avatar {
            width: 42px;
            height: 42px;

            display: flex;
            align-items: center;
            justify-content: center;

            background: #dbeafe;

            border-radius: 50%;

            font-size: 22px;
        }


        .bot-info h2 {
            margin: 0;

            font-size: 17px;
            font-weight: 700;

            color: #0f172a;
        }


        .bot-info span {
            display: flex;
            align-items: center;
            gap: 5px;

            margin-top: 3px;

            font-size: 12px;

            color: #64748b;
        }


        .online-dot {
            width: 7px;
            height: 7px;

            background: #22c55e;

            border-radius: 50%;
        }


        /* =========================
        CHAT BOX
        ========================= */

        #chat-box {
            flex: 1;

            padding: 25px;

            overflow-y: auto;

            scroll-behavior: smooth;
        }


        /* =========================
        MESSAGE ROW
        ========================= */

        .message-row {
            display: flex;

            align-items: flex-end;

            gap: 9px;

            margin-bottom: 18px;
        }


        .user-row {
            justify-content: flex-end;
        }


        .ai-row {
            justify-content: flex-start;
        }


        /* =========================
        AVATAR
        ========================= */

        .message-avatar {
            width: 32px;
            height: 32px;

            flex-shrink: 0;

            display: flex;
            align-items: center;
            justify-content: center;

            border-radius: 50%;

            background: #e2e8f0;

            font-size: 16px;
        }


        .user-avatar {
            background: #dbeafe;
        }


        /* =========================
        MESSAGE
        ========================= */

        .msg {
            max-width: 70%;

            padding: 12px 16px;

            border-radius: 14px;

            font-size: 14px;

            line-height: 1.6;

            word-wrap: break-word;

            white-space: pre-wrap;
        }


        .msg.user {
            background: #2563eb;

            color: white;

            border-bottom-right-radius: 4px;
        }


        .msg.ai {
            background: white;

            color: #334155;

            border: 1px solid #e2e8f0;

            border-bottom-left-radius: 4px;

            box-shadow: 0 2px 5px rgba(0,0,0,0.03);
        }


        /* =========================
        INPUT AREA
        ========================= */

        .chat-input-area {
            padding: 15px 20px;

            background: white;

            border-top: 1px solid #e2e8f0;
        }


        #chat-form {
            display: flex;

            gap: 10px;

            width: 100%;
        }


        #prompt {
            flex: 1;

            min-width: 0;

            padding: 13px 16px;

            border: 1px solid #cbd5e1;

            border-radius: 10px;

            outline: none;

            font-size: 14px;

            transition: 0.2s;
        }


        #prompt:focus {
            border-color: #2563eb;

            box-shadow:
                0 0 0 3px rgba(37,99,235,0.1);
        }


        #send-btn {
            display: flex;
            align-items: center;
            justify-content: center;

            gap: 7px;

            padding: 0 20px;

            border: none;

            border-radius: 10px;

            background: #2563eb;

            color: white;

            font-size: 14px;
            font-weight: 600;

            cursor: pointer;

            transition: 0.2s;
        }


        #send-btn:hover {
            background: #1d4ed8;
        }


        #send-btn:disabled {
            background: #93c5fd;

            cursor: not-allowed;
        }


        .input-hint {
            margin-top: 8px;

            text-align: center;

            font-size: 10px;

            color: #94a3b8;
        }


        /* =========================
        SCROLLBAR
        ========================= */

        #chat-box::-webkit-scrollbar,
        .conversation-list::-webkit-scrollbar {
            width: 5px;
        }


        #chat-box::-webkit-scrollbar-thumb,
        .conversation-list::-webkit-scrollbar-thumb {
            background: #cbd5e1;

            border-radius: 10px;
        }

        .conv-item {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 6px 8px;
            border-radius: 6px;
            margin-bottom: 4px;
        }
        .conv-item:hover {
            background: #f0f0f0;
        }
        .conv-item a {
            flex: 1;
            overflow: hidden;
            text-overflow: ellipsis;
            white-space: nowrap;
            text-decoration: none;
            color: #333;
        }
        .conv-item button {
            background: none;
            border: none;
            cursor: pointer;
            color: #888;
            padding: 0 4px;
        }
        .conv-item button:hover {
            color: red;
        }


        /* =========================
        MOBILE
        ========================= */

        @media (max-width: 768px) {

            .chat-app {
                height: 100vh;

                margin: 0;

                border-radius: 0;
            }


            #sidebar {
                width: 200px;
            }


            .msg {
                max-width: 82%;
            }

        }


        @media (max-width: 600px) {

            #sidebar {
                display: none;
            }


            .chat-header {
                padding: 0 15px;
            }


            #chat-box {
                padding: 15px;
            }


            .chat-input-area {
                padding: 12px;
            }


            #send-btn {
                padding: 0 15px;
            }


            #send-btn span {
                display: none;
            }

        }

    </style>

 
</head>
<body>
    <div class="chat-app">

    {{-- Sidebar --}}
    <aside id="sidebar">

        <div class="sidebar-header">
            <div class="logo">
                🤖 <span>Laravel AI</span>
            </div>

            <a href="{{ route('chat') }}" class="new-chat-btn">
                <span>＋</span> নতুন চ্যাট
            </a>
        </div>

        <div class="history-title">
            <span>চ্যাট হিস্টোরি</span>
        </div>

        <div id="conv-list" class="conversation-list">
            {{-- Conversation history will come here --}}
        </div>

        <div class="sidebar-footer">
            Laravel AI Assistant
        </div>

    </aside>


    {{-- Main Chat --}}
    <main class="chat-main">

        {{-- Header --}}
        <header class="chat-header">
            <div class="bot-info">
                <div class="bot-avatar">🤖</div>

                <div>
                    <h2>Laravel AI Chat</h2>
                    <span>
                        <i class="online-dot"></i>
                        AI Assistant Online
                    </span>
                </div>
            </div>
        </header>


        {{-- Chat Messages --}}
        <div id="chat-box">

            @foreach($messages as $m)

                <div class="message-row {{ $m->role === 'user' ? 'user-row' : 'ai-row' }}">

                    @if($m->role !== 'user')
                        <div class="message-avatar">
                            🤖
                        </div>
                    @endif

                    <div class="msg {{ $m->role === 'user' ? 'user' : 'ai' }}">
                        {{ $m->content }}
                    </div>

                    @if($m->role === 'user')
                        <div class="message-avatar user-avatar">
                            👤
                        </div>
                    @endif

                </div>

            @endforeach

        </div>


        {{-- Input --}}
        <div class="chat-input-area">

            <form id="chat-form">

                <input
                    type="text"
                    id="prompt"
                    placeholder="আপনার প্রশ্ন লিখুন..."
                    autocomplete="off"
                    required
                >

                <button type="submit" id="send-btn">
                    <span>➤</span>
                    পাঠান
                </button>

            </form>

            <div class="input-hint">
                AI ভুল তথ্য দিতে পারে। গুরুত্বপূর্ণ তথ্য যাচাই করে নিন।
            </div>

        </div>

    </main>

</div>


<style>

    * {
        box-sizing: border-box;
    }

    body {
        margin: 0;
        font-family:
            Inter,
            "Noto Sans Bengali",
            "Segoe UI",
            sans-serif;

        background: #f1f5f9;
        color: #1e293b;
    }


    /* =========================
       MAIN APP
    ========================= */

    .chat-app {
        width: 100%;
        max-width: 1200px;
        height: 90vh;

        margin: 5vh auto;

        display: flex;

        background: #ffffff;

        border-radius: 18px;

        overflow: hidden;

        box-shadow:
            0 20px 50px rgba(15, 23, 42, 0.12);
    }


    /* =========================
       SIDEBAR
    ========================= */

    #sidebar {
        width: 260px;

        display: flex;
        flex-direction: column;

        background: #0f172a;
        color: white;
    }


    .sidebar-header {
        padding: 20px;
        border-bottom: 1px solid rgba(255,255,255,0.08);
    }


    .logo {
        font-size: 19px;
        font-weight: 700;

        margin-bottom: 18px;

        display: flex;
        align-items: center;
        gap: 8px;
    }


    .new-chat-btn {
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 7px;

        width: 100%;

        padding: 11px 14px;

        border-radius: 9px;

        background: #2563eb;

        color: white;

        text-decoration: none;

        font-size: 14px;
        font-weight: 600;

        transition: 0.2s;
    }


    .new-chat-btn:hover {
        background: #1d4ed8;
    }


    .history-title {
        padding: 18px 20px 10px;

        font-size: 12px;

        color: #94a3b8;

        text-transform: uppercase;

        letter-spacing: 0.5px;
    }


    .conversation-list {
        flex: 1;

        padding: 5px 10px;

        overflow-y: auto;
    }


    .sidebar-footer {
        padding: 15px 20px;

        font-size: 11px;

        color: #64748b;

        border-top: 1px solid rgba(255,255,255,0.08);
    }


    /* =========================
       MAIN CHAT
    ========================= */

    .chat-main {
        flex: 1;

        display: flex;
        flex-direction: column;

        min-width: 0;

        background: #f8fafc;
    }


    /* =========================
       HEADER
    ========================= */

    .chat-header {
        height: 75px;

        display: flex;
        align-items: center;

        padding: 0 25px;

        background: white;

        border-bottom: 1px solid #e2e8f0;
    }


    .bot-info {
        display: flex;
        align-items: center;

        gap: 12px;
    }


    .bot-avatar {
        width: 42px;
        height: 42px;

        display: flex;
        align-items: center;
        justify-content: center;

        background: #dbeafe;

        border-radius: 50%;

        font-size: 22px;
    }


    .bot-info h2 {
        margin: 0;

        font-size: 17px;
        font-weight: 700;

        color: #0f172a;
    }


    .bot-info span {
        display: flex;
        align-items: center;
        gap: 5px;

        margin-top: 3px;

        font-size: 12px;

        color: #64748b;
    }


    .online-dot {
        width: 7px;
        height: 7px;

        background: #22c55e;

        border-radius: 50%;
    }


    /* =========================
       CHAT BOX
    ========================= */

    #chat-box {
        flex: 1;

        padding: 25px;

        overflow-y: auto;

        scroll-behavior: smooth;
    }


    /* =========================
       MESSAGE ROW
    ========================= */

    .message-row {
        display: flex;

        align-items: flex-end;

        gap: 9px;

        margin-bottom: 18px;
    }


    .user-row {
        justify-content: flex-end;
    }


    .ai-row {
        justify-content: flex-start;
    }


    /* =========================
       AVATAR
    ========================= */

    .message-avatar {
        width: 32px;
        height: 32px;

        flex-shrink: 0;

        display: flex;
        align-items: center;
        justify-content: center;

        border-radius: 50%;

        background: #e2e8f0;

        font-size: 16px;
    }


    .user-avatar {
        background: #dbeafe;
    }


    /* =========================
       MESSAGE
    ========================= */

    .msg {
        max-width: 70%;

        padding: 12px 16px;

        border-radius: 14px;

        font-size: 14px;

        line-height: 1.6;

        word-wrap: break-word;

        white-space: pre-wrap;
    }


    .msg.user {
        background: #2563eb;

        color: white;

        border-bottom-right-radius: 4px;
    }


    .msg.ai {
        background: white;

        color: #334155;

        border: 1px solid #e2e8f0;

        border-bottom-left-radius: 4px;

        box-shadow: 0 2px 5px rgba(0,0,0,0.03);
    }


    /* =========================
       INPUT AREA
    ========================= */

    .chat-input-area {
        padding: 15px 20px;

        background: white;

        border-top: 1px solid #e2e8f0;
    }


    #chat-form {
        display: flex;

        gap: 10px;

        width: 100%;
    }


    #prompt {
        flex: 1;

        min-width: 0;

        padding: 13px 16px;

        border: 1px solid #cbd5e1;

        border-radius: 10px;

        outline: none;

        font-size: 14px;

        transition: 0.2s;
    }


    #prompt:focus {
        border-color: #2563eb;

        box-shadow:
            0 0 0 3px rgba(37,99,235,0.1);
    }


    #send-btn {
        display: flex;
        align-items: center;
        justify-content: center;

        gap: 7px;

        padding: 0 20px;

        border: none;

        border-radius: 10px;

        background: #2563eb;

        color: white;

        font-size: 14px;
        font-weight: 600;

        cursor: pointer;

        transition: 0.2s;
    }


    #send-btn:hover {
        background: #1d4ed8;
    }


    #send-btn:disabled {
        background: #93c5fd;

        cursor: not-allowed;
    }


    .input-hint {
        margin-top: 8px;

        text-align: center;

        font-size: 10px;

        color: #94a3b8;
    }


    /* =========================
       SCROLLBAR
    ========================= */

    #chat-box::-webkit-scrollbar,
    .conversation-list::-webkit-scrollbar {
        width: 5px;
    }


    #chat-box::-webkit-scrollbar-thumb,
    .conversation-list::-webkit-scrollbar-thumb {
        background: #cbd5e1;

        border-radius: 10px;
    }


    /* =========================
       MOBILE
    ========================= */

    @media (max-width: 768px) {

        .chat-app {
            height: 100vh;

            margin: 0;

            border-radius: 0;
        }


        #sidebar {
            width: 200px;
        }


        .msg {
            max-width: 82%;
        }

    }


    @media (max-width: 600px) {

        #sidebar {
            display: none;
        }


        .chat-header {
            padding: 0 15px;
        }


        #chat-box {
            padding: 15px;
        }


        .chat-input-area {
            padding: 12px;
        }


        #send-btn {
            padding: 0 15px;
        }


        #send-btn span {
            display: none;
        }

    }

</style>

    <script>
        const form = document.getElementById('chat-form');
        const box = document.getElementById('chat-box');
        const input = document.getElementById('prompt');
        const btn = document.getElementById('send-btn');
        const token = document.querySelector('meta[name="csrf-token"]').content;

        let conversationId = {{ $conversation->id ?? 'null' }};
        async function loadConversations() {
            const res = await fetch("{{ route('conversations.list') }}");
            const list = await res.json();
            const container = document.getElementById('conv-list');
            container.innerHTML = '';

            list.forEach(c => {
                const item = document.createElement('div');
                item.className = 'conv-item';

                const a = document.createElement('a');
                a.href = `/chat/${c.id}`;
                a.textContent = truncateTitle(c.title);
                a.onclick = (e) => {
                    e.preventDefault();

                    if (a.clickTimer) {
                        // এটা দ্বিতীয় ক্লিক (মানে ডাবল-ক্লিক হয়েছে) → নেভিগেট বাতিল, এডিট মোডে যাও
                        clearTimeout(a.clickTimer);
                        a.clickTimer = null;
                        startEdit();
                    } else {
                        // প্রথম ক্লিক → একটু অপেক্ষা করো, দ্বিতীয় ক্লিক না এলে তখন নেভিগেট করো
                        a.clickTimer = setTimeout(() => {
                            a.clickTimer = null;
                            window.location.href = `/chat/${c.id}`;
                        }, 250);
                    }
                };

                function startEdit() {
                    const input = document.createElement('input');
                    input.type = 'text';
                    input.value = c.title || '';
                    input.className = 'edit-title-input';

                    a.replaceWith(input);
                    input.focus();
                    input.select();

                    const save = async () => {
                        const newTitle = input.value.trim();
                        if (newTitle && newTitle !== c.title) {
                            await fetch(`/conversations/${c.id}`, {
                                method: 'PUT',
                                headers: {
                                    'Content-Type': 'application/json',
                                    'X-CSRF-TOKEN': token,
                                },
                                body: JSON.stringify({ title: newTitle }),
                            });
                            c.title = newTitle;
                        }
                        loadConversations();
                    };

                    input.addEventListener('blur', save);
                    input.addEventListener('keydown', (ev) => {
                        if (ev.key === 'Enter') input.blur();
                        if (ev.key === 'Escape') loadConversations();
                    });
                }
                item.appendChild(a);

                const delBtn = document.createElement('button');
                delBtn.textContent = '✕';
                delBtn.onclick = async (e) => {
                    e.preventDefault();
                    if (!confirm('ডিলিট করবেন?')) return;
                    await fetch(`/conversations/${c.id}`, {
                        method: 'DELETE',
                        headers: { 'X-CSRF-TOKEN': token },
                    });
                    if (c.id === conversationId) {
                        window.location.href = "{{ route('chat') }}";
                    } else {
                        loadConversations();
                    }
                };
                item.appendChild(delBtn);

                container.appendChild(item);
            });



        }

        function addMessage(text, cls) {
            const div = document.createElement('div');
            div.className = 'msg ' + cls;
            div.textContent = text;
            box.appendChild(div);
            box.scrollTop = box.scrollHeight;
            return div; 
        }

        form.addEventListener('submit', async (e) => {
            e.preventDefault();
            const prompt = input.value.trim();
            if (!prompt) return;

            addMessage(prompt, 'user');
            input.value = '';
            btn.disabled = true;

            const aiDiv = addMessage('', 'ai'); // খালি div বানিয়ে রাখলাম, পরে ধীরে ধীরে টেক্সট বসবে

            try {
                const res = await fetch("{{ route('ai.ask.stream') }}", {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': token,
                    },
                    body: JSON.stringify({ prompt, conversation_id: conversationId }),
                });

                const reader = res.body.getReader();
                const decoder = new TextDecoder();
                let buffer = '';

                while (true) {
                    const { done, value } = await reader.read();
                    if (done) break;

                    buffer += decoder.decode(value, { stream: true });

                    // SSE ইভেন্টগুলো "\n\n" দিয়ে আলাদা হয়
                    let parts = buffer.split('\n\n');
                    buffer = parts.pop(); // শেষ অংশ অসম্পূর্ণ হতে পারে, পরের বারের জন্য রেখে দিলাম

                    for (const part of parts) {
                        const eventMatch = part.match(/^event: (.+)$/m);
                        const dataMatch = part.match(/^data: (.+)$/m);
                        if (!eventMatch || !dataMatch) continue;

                        const eventType = eventMatch[1];
                        const data = JSON.parse(dataMatch[1]);

                        if (eventType === 'meta') {
                            if (!conversationId) {
                                conversationId = data.conversation_id;
                                history.pushState({}, '', `/chat/${conversationId}`);
                            }
                        } else if (eventType === 'chunk') {
                            aiDiv.textContent += data.content;
                            box.scrollTop = box.scrollHeight;
                        } else if (eventType === 'done') {
                            loadConversations();
                        }
                    }
                }
            } catch (err) {
                aiDiv.textContent = '⚠️ সার্ভারে সমস্যা হয়েছে';
            } finally {
                btn.disabled = false;
            }
        });

        // টাইটেল লম্বা হলে ... দেখাবে
        function truncateTitle(title) {
            if (!title) return 'নতুন চ্যাট';
            const words = title.trim().split(/\s+/);
            if (words.length <= 3) return title;
            return words.slice(0, 3).join(' ') + '...';
        }
        loadConversations();

    </script>
</body>
</html>
