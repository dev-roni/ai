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
        return view('chat');
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

}
