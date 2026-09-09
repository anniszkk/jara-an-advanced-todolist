<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AdminController;


Route::get('/', function () {
    return view('welcome');
});

// ===== SRS001 & SRS002 — Auth + Admin (Programmer A) =====

Route::middleware(['auth', 'admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/users', [AdminController::class, 'index'])->name('users.index');
    Route::get('/users/create', [AdminController::class, 'create'])->name('users.create');
    Route::post('/users', [AdminController::class, 'store'])->name('users.store');
    Route::delete('/users/{user}', [AdminController::class, 'destroy'])->name('users.destroy');
});
// ===== SRS003 & SRS004 — Daftar + Keanggotaan (Programmer B) =====


// ===== SRS005 & SRS006 — Tugas + Status (Programmer C) =====