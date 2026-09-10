<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AiController;

Route::get('/chat', [AiController::class, 'index'])->name('chat');
Route::post('/ask', [AiController::class, 'ask'])->name('ai.ask');