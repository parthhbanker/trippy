<?php

use App\Http\Controllers\Webhook;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::post('/pusher-webhook',[Webhook::class,'pusher']);

