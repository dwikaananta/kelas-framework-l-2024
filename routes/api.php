<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\UserController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::get('/testing-api', function () {
    return response()->json([
        'author' => 'Dwika Ananta',
        'type' => 'public',
        'desc' => 'This api only for testing',
        'data' => [
            'site_name' => 'Belajar API',
            'domain_name' => 'www.belajarapi.com',
        ]
    ]);
});

Route::post('/get-user-name', function (Request $request) {
    $username = $request->username;

    if ($username) {
        return response()->json([
            'username' => 'Username is ' . $username,
        ]);
    } else {
        return response()->json([
            'username' => "Username doesn't exist",
        ]);
    }
});

Route::post('/login', [AuthController::class, 'login']);

Route::middleware('auth:sanctum')->group(function () {
    Route::apiResource('/users', UserController::class);
});

// ini hanya sementara
Route::apiResource('/users', UserController::class);
