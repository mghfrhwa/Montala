<?php

use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\DokumentasiFotoController;
use App\Http\Controllers\Kph\DashboardController;
use App\Http\Controllers\Kph\ExportController;
use App\Http\Controllers\Kph\TargetController;
use App\Http\Controllers\Kph\UserManagementController;
use App\Http\Controllers\Krph\ProgresController;
use App\Http\Controllers\Krph\RealisasiController;
use App\Http\Controllers\Krph\RiwayatController;
use Illuminate\Support\Facades\Route;

Route::get('/', fn () => redirect()->route('login'));

Route::middleware('guest')->group(function () {
    Route::get('/login', [LoginController::class, 'create'])->name('login');
    Route::post('/login', [LoginController::class, 'store'])->name('login.store');
});

Route::middleware('auth')->group(function () {
    Route::post('/logout', [LoginController::class, 'destroy'])->name('logout');

    // ============ SISI KPH ============
    Route::middleware('role:KPH')->prefix('dashboard')->name('kph.')->group(function () {
        Route::get('/ringkasan', [DashboardController::class, 'ringkasan'])->name('ringkasan');
        Route::get('/peta', [DashboardController::class, 'peta'])->name('peta');
        Route::get('/target', [TargetController::class, 'index'])->name('target');
        Route::post('/target', [TargetController::class, 'store'])->name('target.store');
        Route::get('/export', [ExportController::class, 'index'])->name('export');
        Route::get('/export/unduh', [ExportController::class, 'unduh'])->name('export.unduh');
        Route::get('/user', [UserManagementController::class, 'index'])->name('user');
        Route::post('/user', [UserManagementController::class, 'store'])->name('user.store');
        Route::patch('/user/{user}/toggle', [UserManagementController::class, 'toggleStatus'])->name('user.toggle');
    });

    // ============ SISI KRPH / ASPER ============
    Route::middleware('role:KRPH')->prefix('input')->name('krph.')->group(function () {
        Route::get('/progres', [ProgresController::class, 'index'])->name('progres');
        Route::get('/input', [RealisasiController::class, 'create'])->name('input');
        Route::post('/input', [RealisasiController::class, 'store'])->name('input.store');
        Route::get('/riwayat', [RiwayatController::class, 'index'])->name('riwayat');
    });

    // ============ BERSAMA (KPH & KRPH) ============
    Route::get('/foto/{dokumentasiFoto}', [DokumentasiFotoController::class, 'show'])->name('foto.show');
});
