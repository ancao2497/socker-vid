<?php

use App\Http\Controllers\ChannelController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Events\UserJoinedGroup;
use Illuminate\Support\Facades\Broadcast;
use App\Http\Controllers\AuthController;
use App\Http\Middleware\VerifyJwtToken;
Route::post('login', [AuthController::class, 'login'])->name('login');
Route::post('/broadcasting/auth', [ChannelController::class, 'loginReverb'])->middleware(VerifyJwtToken::class);
Route::get('/broadcast', [ChannelController::class, 'index'])->middleware(VerifyJwtToken::class);
Route::get('/get-list-vid-present', [ChannelController::class, 'store']);
Route::post('/create-channel-vid', [ChannelController::class, 'create']);
Route::get('/send-signal-sub-channel', [ChannelController::class, 'sendSignal']);
