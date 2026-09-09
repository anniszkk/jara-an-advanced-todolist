<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\TaskController;

Route::get('/', function () {
    return view('welcome');
});

// ===== SRS001 & SRS002 — Auth + Admin (Programmer A) =====


// ===== SRS003 & SRS004 — Daftar + Keanggotaan (Programmer B) =====


// ===== SRS005 & SRS006 — Tugas + Status (Programmer C) =====
Route::resource('tasks', TaskController::class);
Route::patch('tasks/{id}/status', [TaskController::class, 'updateStatus'])
    ->name('tasks.updateStatus');