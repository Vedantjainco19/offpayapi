<?php

use Illuminate\Http\Request;
use App\Http\Controllers\TokenDetailController;
use App\Http\Controllers\UserLoginController;
use Illuminate\Support\Facades\Route;

Route::post('login', [UserLoginController::class, 'login']);
Route::post('verifyLogin', [UserLoginController::class, 'verifyLogin']);
Route::post('resendOtp', [UserLoginController::class, 'resendOtp']);
Route::post('createToken', [TokenDetailController::class, 'addToken']);
Route::get('getAllTokens', [TokenDetailController::class, 'getAllTokens']);
Route::post('deleteToken', [TokenDetailController::class, 'deleteToken']);
Route::post('updateTokenExpiry', [TokenDetailController::class, 'updateTokenExpiry']);

Route::fallback(function () {
    return response()->json([
        'message' => 'Api Route Not Found. Contact vedantjainco19@acropolis.in',
        'errors' => [],
    ], 404);
});

