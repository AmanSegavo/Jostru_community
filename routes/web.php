<?php

use App\Http\Controllers\AuthController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::post('/contact', function (Illuminate\Http\Request $request) {
    App\Models\Contact::create($request->validate([
        'name' => 'required',
        'email' => 'required|email',
        'message' => 'required'
    ]));
    return back()->with('success', 'Pesan berhasil dikirim! Admin akan segera membaca pesan Anda.');
});

Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login']);
    Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
    Route::post('/register', [AuthController::class, 'register']);
});

Route::middleware('auth')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

    Route::middleware('role:member')->prefix('dashboard')->group(function () {
        Route::get('/', function () {
            return view('member.dashboard');
        })->name('dashboard');
    });

    Route::middleware('role:admin')->prefix('admin')->group(function () {
        Route::get('/dashboard', function () {
            return view('admin.dashboard');
        })->name('admin.dashboard');
    });
});
