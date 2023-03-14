<?php

use Illuminate\Http\Request;
use App\Http\Controllers\GeoIpLocationController;
use App\Http\Controllers\UserLoginController;
use Illuminate\Support\Facades\Route;

Route::post('login', [UserLoginController::class, 'index']);
Route::get('getUrlLogs', [LinkController::class, 'getUrlLogs']);
Route::get('getClickDetails', [LinkController::class, 'getClickDetails']);
Route::post('updateExpiry', [LinkController::class, 'updateExpiry']);
Route::get('getTotalClicks', [LinkController::class, 'getTotalClicks']);
Route::get('exportReport', [ExportReportController::class, 'getUrlLogsReport']);
Route::post('getIpDetails', [GeoIpLocationController::class, 'index']);
Route::fallback(function () {
    return response()->json([
        'message' => 'Api Route Not Found. Contact support@msg91.com',
        'errors' => [],
    ], 404);
});

