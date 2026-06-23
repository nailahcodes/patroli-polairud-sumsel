<?php

use App\Http\Controllers\AbkAnevController;
use App\Http\Controllers\AbkLaporanController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\KapalController;
use App\Http\Controllers\PatroliController;
use App\Http\Controllers\PimpinanValidasiController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\SopController;
use App\Http\Controllers\SopProgressController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return redirect()->route('login');
});

/*
|--------------------------------------------------------------------------
| Auth Manual NRP + Password
|--------------------------------------------------------------------------
| Tidak memakai Breeze dan tidak membutuhkan routes/auth.php.
*/
Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLogin'])
        ->name('login');

    Route::post('/login', [AuthController::class, 'login'])
        ->name('login.process');
});

Route::post('/logout', [AuthController::class, 'logout'])
    ->middleware('auth')
    ->name('logout');

Route::middleware('auth')->group(function () {
    /*
    |--------------------------------------------------------------------------
    | Dashboard
    |--------------------------------------------------------------------------
    */
    Route::get('/dashboard', DashboardController::class)
        ->name('dashboard');

    /*
    |--------------------------------------------------------------------------
    | Profile / Foto Profil
    |--------------------------------------------------------------------------
    */
    Route::get('/profile', [ProfileController::class, 'show'])
    ->name('profile.show');

    Route::post('/profile/photo', [ProfileController::class, 'updatePhoto'])
    ->name('profile.photo.update');

    Route::delete('/profile/photo', [ProfileController::class, 'deletePhoto'])
    ->name('profile.photo.delete');
    /*
    |--------------------------------------------------------------------------
    | Manajemen User
    |--------------------------------------------------------------------------
    | Export PDF harus di atas resource users agar tidak terbaca sebagai {user}.
    */
    Route::get('/users/export-pdf', [UserController::class, 'exportPdf'])
        ->name('users.export-pdf');

    Route::resource('/users', UserController::class)
        ->except(['show']);

    Route::patch('/users/{user}/toggle-status', [UserController::class, 'toggleStatus'])
        ->name('users.toggle-status');

    Route::patch('/users/{user}/reset-password', [UserController::class, 'resetPassword'])
        ->name('users.reset-password');

    /*
    |--------------------------------------------------------------------------
    | Master Kapal
    |--------------------------------------------------------------------------
    */
    Route::resource('/kapal', KapalController::class);

    /*
    |--------------------------------------------------------------------------
    | Master SOP
    |--------------------------------------------------------------------------
    */
    Route::resource('/sop', SopController::class);

    /*
    |--------------------------------------------------------------------------
    | Patroli
    |--------------------------------------------------------------------------
    */
    Route::resource('/patroli', PatroliController::class);

    /*
    |--------------------------------------------------------------------------
    | Progress SOP
    |--------------------------------------------------------------------------
    */
    Route::post('/sop-progress/{id}', [SopProgressController::class, 'update'])
        ->name('sop-progress.update');

    /*
    |--------------------------------------------------------------------------
    | Laporan ABK - SOP 14
    |--------------------------------------------------------------------------
    */
    Route::get('/arsip-laporan-abk', [AbkLaporanController::class, 'index'])
        ->name('abk-laporan.index');

    Route::get('/patroli/{patroli}/laporan-abk', [AbkLaporanController::class, 'edit'])
        ->name('abk-laporan.edit');

    Route::put('/patroli/{patroli}/laporan-abk', [AbkLaporanController::class, 'update'])
        ->name('abk-laporan.update');

    Route::get('/patroli/{patroli}/laporan-abk/export', [AbkLaporanController::class, 'export'])
        ->name('abk-laporan.export');

    /*
    |--------------------------------------------------------------------------
    | ANEV ABK - SOP 15
    |--------------------------------------------------------------------------
    */
    Route::get('/arsip-anev-abk', [AbkAnevController::class, 'index'])
        ->name('abk-anev.index');

    Route::get('/patroli/{patroli}/anev-abk', [AbkAnevController::class, 'edit'])
        ->name('abk-anev.edit');

    Route::put('/patroli/{patroli}/anev-abk', [AbkAnevController::class, 'update'])
        ->name('abk-anev.update');

    Route::get('/patroli/{patroli}/anev-abk/export', [AbkAnevController::class, 'export'])
        ->name('abk-anev.export');

    /*
    |--------------------------------------------------------------------------
    | Validasi Pimpinan
    |--------------------------------------------------------------------------
    */
    Route::patch('/patroli/{patroli}/validasi-pimpinan/valid', [PimpinanValidasiController::class, 'valid'])
        ->name('pimpinan.validasi.valid');

    Route::patch('/patroli/{patroli}/validasi-pimpinan/perbaiki', [PimpinanValidasiController::class, 'perbaiki'])
        ->name('pimpinan.validasi.perbaiki');

    Route::delete(
        '/abk-laporan/lampiran/{lampiran}',
        [AbkLaporanController::class, 'hapusLampiran']
    )->name('abk-laporan.lampiran.destroy');
});
