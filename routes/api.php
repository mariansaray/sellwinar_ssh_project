<?php

use App\Http\Controllers\Api\ChatController;
use App\Http\Controllers\Api\TrackingController;
use Illuminate\Support\Facades\Route;

// Event tracking (public — rate limited to 100/min)
Route::post('/track', [TrackingController::class, 'store'])->middleware('throttle:100,1');

// Chat (public — for webinar viewers)
Route::middleware('throttle:60,1')->group(function () {
    Route::get('/chat', [ChatController::class, 'index']);
    Route::post('/chat/send', [ChatController::class, 'send'])->middleware('throttle:10,1');
});
