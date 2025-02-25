<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\PatientController;
use App\Http\Controllers\ComplaintController;
use App\Http\Controllers\BillController;
use App\Http\Controllers\ReceptController;

// Route utama
Route::get('/', [PatientController::class, 'index'])->name('index');

// ✅ Route untuk Manajemen Pasien (Bisa diakses semua role)
Route::prefix('patients')->name('patients.')->group(function () {
    Route::get('/', [PatientController::class, 'index'])->name('index');

    // Hanya bisa diakses oleh guest (pengguna yang belum login)
    Route::middleware('isGuest')->group(function () {
        Route::get('/create', [PatientController::class, 'create'])->name('create');
        Route::post('/store', [PatientController::class, 'store'])->name('store');
    });

    // Hanya bisa diakses oleh admin
    Route::middleware('isAdmin', 'isOffice')->group(function () {
        Route::get('/edit/{patient}', [PatientController::class, 'edit'])->name('edit');
        Route::put('/update/{patient}', [PatientController::class, 'update'])->name('update');
        Route::delete('/destroy/{patient}', [PatientController::class, 'destroy'])->name('destroy');
    });

    // Route untuk datatable (bisa diakses oleh siapa saja yang terautentikasi)
    Route::get('/patients/datatable', [PatientController::class, 'datatable'])->name('patients.datatable')->middleware('auth');
    Route::post('/{id}/restore', [PatientController::class, 'restore'])->name('patients.restore');
    Route::delete('/{id}/forceDelete', [PatientController::class, 'forceDelete'])->name('patients.forceDelete');
    Route::get('/history', [PatientController::class, 'history'])->name('history');
});

// ✅ Route untuk Rekam Medis
Route::prefix('complaints')->name('complaints.')->group(function () {
    Route::get('/', [ComplaintController::class, 'index'])->name('index');
    Route::get('/datatable', [ComplaintController::class, 'datatable'])->name('datatable');
    Route::get('/complaints/datatable', [ComplaintController::class, 'datatable'])->name('complaints.datatable');

    Route::middleware('isOffice', 'isAdmin')->group(function () {
        Route::get('/create', [ComplaintController::class, 'create'])->name('create');
        Route::post('/store', [ComplaintController::class, 'store'])->name('store');
        Route::get('/edit/{complaint}', [ComplaintController::class, 'edit'])->name('edit');
        Route::put('/update/{complaint}', [ComplaintController::class, 'update'])->name('update');
        Route::delete('/destroy/{complaint}', [ComplaintController::class, 'destroy'])->name('destroy');
        Route::get('/download-pdf/{id}', [ComplaintController::class, 'downloadPDF'])->name('download-pdf');
    });
    Route::get('/history', [ComplaintController::class, 'history'])->name('history');
    Route::post('/restore/{id}', [ComplaintController::class, 'restore'])->name('complaints.restore');
    Route::delete('/force-delete/{id}', [ComplaintController::class, 'forceDelete'])->name('complaints.force-delete');
});

// ✅ Route untuk Tagihan
Route::prefix('bills')->name('bills.')->group(function () {
    Route::get('/', [BillController::class, 'index'])->name('index');
    Route::get('/bills/datatable', [BillController::class, 'datatable'])->name('bills.datatable');

    Route::middleware('isOffice', 'isAdmin')->group(function () {
        Route::get('/create', [BillController::class, 'create'])->name('create');
        Route::post('/store', [BillController::class, 'store'])->name('store');
        Route::delete('/destroy/{bill}', [BillController::class, 'destroy'])->name('destroy');
        Route::get('/download-pdf/{id}', [BillController::class, 'downloadPDF'])->name('download-pdf');
    });
    // Route untuk halaman history
    Route::get('/history', [BillController::class, 'history'])->name('history');
    Route::post('/restore/{id}', [BillController::class, 'restore'])->name('bills.restore');
    Route::delete('/force-delete/{id}', [BillController::class, 'forceDelete'])->name('bills.force-delete');
});

// ✅ Route untuk Resep Dokter & History
Route::prefix('recepts')->name('recepts.')->group(function () {
    Route::get('/', [ReceptController::class, 'index'])->name('index');

    Route::middleware('isAdmin')->group(function () {
        Route::get('/create', [ReceptController::class, 'create'])->name('create');
        Route::post('/store', [ReceptController::class, 'store'])->name('store');
        Route::get('/edit', [ReceptController::class, 'edit'])->name('edit'); // Pastikan ini ada
        Route::put('/update', [ReceptController::class, 'update'])->name('update');
        Route::delete('/destroy/{recept}', [ReceptController::class, 'destroy'])->name('destroy');
        Route::get('/recepts/datatable', [ReceptController::class, 'datatable'])->name('recepts.datatable');
        Route::get('/download-pdf/{id}', [ReceptController::class, 'downloadPDF'])->name('download-pdf');
    });
    Route::get('/history', [ReceptController::class, 'history'])->name('history');
    Route::post('/restore/{id}', [ReceptController::class, 'restore'])->name('recepts.restore');
    Route::delete('/force-delete/{id}', [ReceptController::class, 'forceDelete'])->name('recepts.force-delete');
});

// ✅ Office Bisa Akses History Pasien

// ✅ Route untuk Autentikasi
Auth::routes();
Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');
