<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Models\Conversation;
use App\Services\AiService;

class AiController extends Controller
{
    protected AiService $ai;

    public function __construct(AiService $ai)
    {
        $this->ai = $ai;
    }

    // চ্যাট পেজ দেখানো
    public function index()
    {
        return view('chat', [
            'conversation' => null,
            'messages' => collect(),
        ]);
    }

     // AJAX দিয়ে প্রশ্ন পাঠানো, উত্তর ফেরত পাওয়া
    public function ask(Request $request)
    {
        $request->validate([
            'prompt' => 'required|string|max:2000',
            'conversation_id' => 'nullable|exists:conversations,id',
        ]);

        //conversation থাকলে সেটা কন্টিনিউ না থাকলে নতুন তৈরি 
        $conversation = $request->input('conversation_id')
                        ? Conversation::findOrFail($request->input('conversation_id'))
                        : Conversation::create([
                            'title' => str($request->input('prompt'))->limit(40),
                        ]);
        // ইউজার এর মেসেজ সেভ
        $conversation->messages()->create([
                    'role' => 'user',
                    'content' => $request->input('prompt'),
                ]);

        try {
            
            $history = $conversation->messages()
                ->orderBy('id')
                ->get(['role', 'content'])
                ->map(fn ($m) => ['role' => $m->role, 'content' => $m->content])
                ->toArray();

            $reply = $this->ai->chat($history);

            $conversation->messages()->create([
                'role' => 'assistant',
                'content' => $reply,
            ]);

            return response()->json([
                'reply' => $reply,
                'conversation_id' => $conversation->id,
            ]);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    public function askStream(Request $request)
    {
        $request->validate([
            'prompt' => 'required|string|max:2000',
            'conversation_id' => 'nullable|exists:conversations,id',
        ]);

        $conversation = $request->input('conversation_id')
            ? Conversation::findOrFail($request->input('conversation_id'))
            : Conversation::create([
                'title' => str($request->input('prompt'))->limit(40),
            ]);

        $conversation->messages()->create([
            'role' => 'user',
            'content' => $request->input('prompt'),
        ]);

        $history = $conversation->messages()
            ->orderBy('id')
            ->get(['role', 'content'])
            ->map(fn ($m) => ['role' => $m->role, 'content' => $m->content])
            ->toArray();

        return response()->stream(function () use ($history, $conversation) {

            // প্রথম ইভেন্টে conversation_id পাঠিয়ে দিচ্ছি (নতুন চ্যাট হলে ফ্রন্টএন্ডের জানা দরকার)
            echo "event: meta\n";
            echo 'data: ' . json_encode(['conversation_id' => $conversation->id]) . "\n\n";
            ob_flush();
            flush();

            $fullReply = $this->ai->stream($history, function ($chunk) {
                echo "event: chunk\n";
                echo 'data: ' . json_encode(['content' => $chunk]) . "\n\n";
                ob_flush();
                flush();
            });

            $conversation->messages()->create([
                'role' => 'assistant',
                'content' => $fullReply,
            ]);

            echo "event: done\n";
            echo "data: {}\n\n";
            ob_flush();
            flush();

        }, 200, [
            'Content-Type' => 'text/event-stream',
            'Cache-Control' => 'no-cache',
            'X-Accel-Buffering' => 'no',
        ]);
    }

    public function conversations()
    {
        return response()->json(Conversation::latest()->get());
    }

    public function show(Conversation $conversation)
    {
        return view('chat', [
            'conversation' => $conversation,
            'messages' => $conversation->messages,
        ]);
    }

    public function update(Request $request, Conversation $conversation)
    {
        $request->validate([
            'title' => 'required|string|max:100',
        ]);

        $conversation->update([
            'title' => $request->input('title'),
        ]);

        return response()->json(['title' => $conversation->title]);
    }

    public function destroy(Conversation $conversation)
    {
        $conversation->delete();
        return response()->json(['deleted' => true]);
    }

}
