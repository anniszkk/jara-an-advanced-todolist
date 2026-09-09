<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\AdminController;

Route::get('/', function () {
    return view('welcome');
});

// ===== SRS001 & SRS002 — Auth + Admin (Programmer A) =====

Route::get('/register', [AuthController::class, 'showRegisterForm'])->name('register');
Route::post('/register', [AuthController::class, 'register']);

Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
Route::post('/login', [AuthController::class, 'login']);

Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

Route::middleware('auth')->group(function () {
    Route::get('/dashboard', function () {
        return view('dashboard');
    })->name('dashboard');
});

Route::middleware(['auth', 'admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/users', [AdminController::class, 'index'])->name('users.index');
    Route::get('/users/create', [AdminController::class, 'create'])->name('users.create');
    Route::post('/users', [AdminController::class, 'store'])->name('users.store');
    Route::delete('/users/{user}', [AdminController::class, 'destroy'])->name('users.destroy');
});

// ===== SRS003 & SRS004 — Daftar + Keanggotaan (Programmer B) =====
use App\Http\Controllers\ListController;

Route::middleware('auth')->group(function () {
    Route::resource('lists', ListController::class);
});

use App\Http\Controllers\MembershipController;

Route::middleware('auth')->group(function () {
    Route::resource('lists', ListController::class);

    Route::post('/lists/{list}/invite', [MembershipController::class, 'invite'])->name('lists.invite');
    Route::delete('/lists/{list}/members/{user}', [MembershipController::class, 'remove'])->name('lists.members.remove');
    Route::post('/lists/{list}/transfer', [MembershipController::class, 'transfer'])->name('lists.transfer');
});
// ===== SRS005 & SRS006 — Tugas + Status (Programmer C) =====