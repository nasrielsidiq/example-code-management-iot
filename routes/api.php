<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\DeviceController;

Route::prefix('device')->group(function () {
    Route::post('/check', [DeviceController::class, 'checkConnection']);
    Route::post('/sensor-data', [DeviceController::class, 'storeSensorData']);
    Route::get('/lamps', [DeviceController::class, 'getLampStatus']);
    Route::post('/lamps', [DeviceController::class, 'updateLampStatus']);
});