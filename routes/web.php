<?php

use App\Http\Controllers\AuthController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AdminController;

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
    
    // Google OAuth
    Route::get('/auth/google', [AuthController::class, 'redirectToGoogle'])->name('auth.google');
    Route::get('/auth/google/callback', [AuthController::class, 'handleGoogleCallback']);
});

Route::middleware('auth')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

    Route::middleware('role:member')->prefix('dashboard')->group(function () {
        Route::get('/', function () {
            return view('member.dashboard');
        })->name('dashboard');
    });

    Route::middleware('role:admin')->prefix('admin')->group(function () {
        Route::get('/dashboard', [AdminController::class, 'dashboard'])->name('admin.dashboard');
        Route::get('/members', [AdminController::class, 'members'])->name('admin.members');
        Route::get('/members/export', [AdminController::class, 'exportMembers'])->name('admin.members.export');
        Route::post('/members', [AdminController::class, 'storeMember'])->name('admin.members.store');
        Route::put('/members/{id}', [AdminController::class, 'updateMember'])->name('admin.members.update');
        Route::delete('/members/{id}', [AdminController::class, 'destroyMember'])->name('admin.members.destroy');
        
        Route::get('/cards', [AdminController::class, 'cards'])->name('admin.cards');
        Route::get('/messages', [AdminController::class, 'messages'])->name('admin.messages');
        Route::get('/logs', [AdminController::class, 'logs'])->name('admin.logs');
        
        Route::get('/finances', [AdminController::class, 'finances'])->name('admin.finances');
        Route::get('/finances/export', [AdminController::class, 'exportFinances'])->name('admin.finances.export');
        Route::post('/finances', [AdminController::class, 'storeFinance'])->name('admin.finances.store');
        Route::delete('/finances/{id}', [AdminController::class, 'destroyFinance'])->name('admin.finances.destroy');
        Route::get('/members/{id}/preview', [AdminController::class, 'previewCard'])->name('admin.preview_card');
        Route::get('/members/{id}/card', [AdminController::class, 'generateCard'])->name('admin.generate_card');
        Route::post('/members/{id}/card-custom', [AdminController::class, 'generateCardCustom'])->name('admin.generate_card_custom');
    });
    
    Route::middleware('role:member')->prefix('member')->group(function () {
        // Assume MemberController will be created
        Route::get('/profile', [App\Http\Controllers\MemberController::class, 'profile'])->name('member.profile');
        Route::post('/profile', [App\Http\Controllers\MemberController::class, 'updateProfile'])->name('member.profile.update');
    });
});

Route::get('/v/{id}', [App\Http\Controllers\PublicController::class, 'verify_card'])->name('member.verify');
