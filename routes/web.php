<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AiController;

//available model name
Route::get('/test-groq-models', function () {
    return \Illuminate\Support\Facades\Http::withToken(config('services.groq.key'))
        ->get('https://api.groq.com/openai/v1/models')
        ->json();
});


Route::get('/', [AiController::class, 'index'])->name('chat');
Route::post('/ask', [AiController::class, 'ask'])->name('ai.ask');