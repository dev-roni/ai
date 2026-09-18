<!DOCTYPE html>
<html lang="bn">
<head>
    <meta charset="UTF-8">
    <title>Laravel AI Chat</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <style>
        body { font-family: sans-serif; max-width: 700px; margin: 40px auto; background: #f5f5f5; }
        #chat-box { background: #fff; border-radius: 8px; padding: 20px; min-height: 300px; margin-bottom: 15px; }
        .msg { padding: 10px 14px; border-radius: 8px; margin: 8px 0; max-width: 80%; }
        .user { background: #dbeafe; margin-left: auto; text-align: right; }
        .ai { background: #e5e7eb; }
        form { display: flex; gap: 8px; }
        input { flex: 1; padding: 10px; border-radius: 6px; border: 1px solid #ccc; }
        button { padding: 10px 20px; border: none; background: #2563eb; color: white; border-radius: 6px; cursor: pointer; }
        button:disabled { background: #93c5fd; }
    </style>
</head>
<body>
    <h2>🤖 Laravel AI Chat</h2>
    <div id="chat-box"></div>

    <form id="chat-form">
        <input type="text" id="prompt" placeholder="কিছু জিজ্ঞেস করুন..." autocomplete="off" required>
        <button type="submit" id="send-btn">পাঠান</button>
    </form>

    <script>
        const form = document.getElementById('chat-form');
        const box = document.getElementById('chat-box');
        const input = document.getElementById('prompt');
        const btn = document.getElementById('send-btn');
        const token = document.querySelector('meta[name="csrf-token"]').content;

        function addMessage(text, cls) {
            const div = document.createElement('div');
            div.className = 'msg ' + cls;
            div.textContent = text;
            box.appendChild(div);
            box.scrollTop = box.scrollHeight;
        }

        form.addEventListener('submit', async (e) => {
            e.preventDefault();
            const prompt = input.value.trim();
            if (!prompt) return;

            addMessage(prompt, 'user');
            input.value = '';
            btn.disabled = true;
            addMessage('...ভাবছে', 'ai');

            try {
                const res = await fetch("{{ route('ai.ask') }}", {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': token,
                        'X-Requested-With': 'XMLHttpRequest',
                    },
                    body: JSON.stringify({ prompt }),
                });
                const data = await res.json();
                box.lastChild.remove(); // "ভাবছে" রিমুভ

                if (data.error) {
                    addMessage('⚠️ ' + data.error, 'ai');
                } else {
                    addMessage(data.reply, 'ai');
                }
            } catch (err) {
                box.lastChild.remove();
                addMessage('⚠️ সার্ভারে সমস্যা হয়েছে', 'ai');
            } finally {
                btn.disabled = false;
            }
        });
    </script>
</body>
</html>
