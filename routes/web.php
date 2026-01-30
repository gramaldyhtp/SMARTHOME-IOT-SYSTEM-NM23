<?php

use App\Http\Controllers\DashboardController;
use App\Http\Controllers\LightingController;
use App\Http\Controllers\DoorLockController;
use App\Http\Controllers\EnergyController;
use App\Http\Controllers\TemperatureController;
use App\Http\Controllers\CurtainController;
use App\Http\Controllers\FireGasController;
use App\Http\Controllers\GardenController;
use App\Http\Controllers\CameraController;
use App\Http\Controllers\ApplianceController;

// DASHBOARD
Route::get('dashboard', [DashboardController::class, 'index'])->name('dashboard');

// 9 HALAMAN DEVICE
Route::get('/lighting', [LightingController::class, 'index'])->name('lighting');
Route::get('/doorlock', [DoorLockController::class, 'index'])->name('doorlock');
Route::get('/energy-monitoring', [EnergyController::class, 'index'])->name('energy');
Route::get('/temperature', [TemperatureController::class, 'index'])->name('temperature');
Route::get('/curtain', [CurtainController::class, 'index'])->name('curtain');
Route::get('/firegas', [FireGasController::class, 'index'])->name('firegas');
Route::get('/garden', [GardenController::class, 'index'])->name('garden');
Route::get('/camera', [CameraController::class, 'index'])->name('camera');
Route::get('/appliance', [ApplianceController::class, 'index'])->name('appliance');
Route::match(['get', 'post'], '/camera/security', [CameraController::class, 'index']);
Route::get('/camera/image/{filename}', [CameraController::class, 'serveImage']);
