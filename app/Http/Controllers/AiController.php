<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

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
        ]);

        try {
            $reply = $this->ai->chat([
                ['role' => 'user', 'content' => $request->input('prompt')],
            ]);

            return response()->json(['reply' => $reply]);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

}
