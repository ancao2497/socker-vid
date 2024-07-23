<?php

use App\Http\Controllers\YourController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Events\UserJoinedGroup;
use Illuminate\Support\Facades\Broadcast;

Route::get('/broadcast', [YourController::class, 'index']);
Route::get('/get-list-vid-present', [YourController::class, 'store']);
Route::post('/broadcasting/auth ', [YourController::class, 'loginReverb']);

// Broadcast::routes(['middleware' => ['auth:sanctum'], function () {
//     Route::get('/user', function (Request $request) {
//         return $request->user();
//     });
// }]);
