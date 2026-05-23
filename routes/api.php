<?php
use App\Http\Controllers\ChatbotController;
use Illuminate\Support\Facades\Route;

// API Chatbot (protégée par Sanctum ou session)
Route::middleware('auth:sanctum')->group(function () {
    Route::post('/chatbot', [ChatbotController::class, 'ask']);
});

// Alternative: protéger par session web pour le chat embarqué
Route::middleware('web', 'auth')->group(function () {
    Route::post('/chatbot', [ChatbotController::class, 'ask'])->name('api.chatbot');
});