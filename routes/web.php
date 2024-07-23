<?php

use Illuminate\Support\Facades\Route;
use App\Events\NewMessage;

Route::get('/', function () {
    return view('welcome');
});
Route::get('/user', function () {
    $message = [
        'propertyOne' => 'foo', 
        'propertyTwo' => 42,
    ];;
    // NewMessage::dispatch($message);
    broadcast(new NewMessage($message))->toOthers();
    return $message;
});
