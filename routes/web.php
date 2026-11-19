<?php

use App\Http\Controllers\ArsipController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\LaporanController;
use Illuminate\Support\Facades\Route;

// Authentication Routes
Route::get('/', function () {
    return redirect()->route('dashboard');
});

Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthController::class, 'login'])->name('login.post');
Route::get('/logout/konfirmasi', [AuthController::class, 'showLogoutConfirm'])->name('logout.confirm');
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
Route::get('/lupa-kata-sandi', [AuthController::class, 'showForgotPassword'])->name('password.request');
Route::post('/lupa-kata-sandi', [AuthController::class, 'submitForgotPassword'])->name('password.post');

// Role Switcher for Simulator / Interactive demo
Route::post('/switch-role', [AuthController::class, 'switchRole'])->name('switch.role');

// Protected / Main System Routes
Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

Route::prefix('arsip')->name('arsip.')->group(function () {
    Route::get('/', [ArsipController::class, 'index'])->name('index');
    Route::get('/tambah', [ArsipController::class, 'create'])->name('create');
    Route::post('/simpan', [ArsipController::class, 'store'])->name('store');
    Route::get('/{id}/detail', [ArsipController::class, 'show'])->name('show');
    Route::get('/{id}/edit', [ArsipController::class, 'edit'])->name('edit');
    Route::post('/{id}/update', [ArsipController::class, 'update'])->name('update');
    Route::post('/{id}/delete', [ArsipController::class, 'destroy'])->name('destroy');
    Route::get('/{id}/barcode', [ArsipController::class, 'printBarcode'])->name('barcode');
    Route::get('/ekspor/excel', [ArsipController::class, 'exportExcel'])->name('export.excel');
    Route::get('/impor/template', [\App\Http\Controllers\ArsipImportController::class, 'template'])->name('import.template');
    Route::post('/impor', [\App\Http\Controllers\ArsipImportController::class, 'store'])->name('import.store');
    Route::get('/ekspor/pdf', [ArsipController::class, 'exportPdf'])->name('export.pdf');
});

Route::prefix('laporan')->name('laporan.')->group(function () {
    Route::get('/', [LaporanController::class, 'index'])->name('index');
    Route::get('/ekspor/excel', [LaporanController::class, 'exportExcel'])->name('export.excel');
});
