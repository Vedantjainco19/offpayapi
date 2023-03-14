<?php

use Illuminate\Http\Request;
use App\Http\Controllers\GeoIpLocationController;
use App\Http\Controllers\UserLoginController;
use Illuminate\Support\Facades\Route;

Route::post('login', [UserLoginController::class, 'login']);
Route::post('verifyLogin', [UserLoginController::class, 'verifyLogin']);
Route::post('resendOtp', [UserLoginController::class, 'resendOtp']);
Route::fallback(function () {
    return response()->json([
        'message' => 'Api Route Not Found. Contact support@msg91.com',
        'errors' => [],
    ], 404);
});

