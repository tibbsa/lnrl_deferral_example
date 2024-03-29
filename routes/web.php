<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/send-notification-to-user', function () {
    $user = \App\Models\User::firstOrFail();
    $notification = new \App\Notifications\TestNotification();
    $user->notify($notification);
    return response()->json(['success' => true]);
});

Route::get('/send-notification-to-anonymous', function () {
    $notification = new \App\Notifications\TestNotification();
    (new \Illuminate\Notifications\AnonymousNotifiable)->route(
        'mail',
        'test2@example.com')
        ->notify($notification);
    return response()->json(['success' => true]);
});
