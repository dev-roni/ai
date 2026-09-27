<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AiController;

//available model name test
Route::get('/test-groq-models', function () {
    return \Illuminate\Support\Facades\Http::withToken(config('services.groq.key'))
        ->get('https://api.groq.com/openai/v1/models')
        ->json();
});


Route::get('/', [AiController::class, 'index'])->name('chat');
Route::post('/ask', [AiController::class, 'ask'])->name('ai.ask');
Route::get('/chat/{conversation}', [AiController::class, 'show'])->name('chat.show');
Route::get('/conversations', [AiController::class, 'conversations'])->name('conversations.list');
Route::put('/conversations/{conversation}', [AiController::class, 'update'])->name('conversations.update');
Route::delete('/conversations/{conversation}', [AiController::class, 'destroy'])->name('conversations.destroy');