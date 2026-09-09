<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

// ===== SRS001 & SRS002 — Auth + Admin (Programmer A) =====


// ===== SRS003 & SRS004 — Daftar + Keanggotaan (Programmer B) =====
use App\Http\Controllers\ListController;

Route::middleware('auth')->group(function () {
    Route::resource('lists', ListController::class);
});

// ===== SRS005 & SRS006 — Tugas + Status (Programmer C) =====