<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\MemberController;
use App\Http\Controllers\PublicController;
use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request;

Route::get('/', function () {
    return view('welcome');
});

Route::post('/contact', function (Request $request) {
    App\Models\Contact::create($request->validate([
        'name' => 'required',
        'email' => 'required|email',
        'message' => 'required'
    ]));
    return back()->with('success', 'Pesan berhasil dikirim!');
});

Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login']);
    Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
    Route::post('/register', [AuthController::class, 'register']);
});

Route::get('/auth/google', [AuthController::class, 'redirectToGoogle'])->name('auth.google');
Route::get('/auth/google/callback', [AuthController::class, 'handleGoogleCallback']);

Route::middleware('auth')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

    Route::middleware(['role:member,admin,superadmin'])->group(function () {
        Route::get('/dashboard', [MemberController::class, 'dashboard'])->name('dashboard');
        Route::get('/profile', [AuthController::class, 'profile'])->name('member.profile');
        Route::post('/profile', [AuthController::class, 'updateProfile'])->name('member.profile.update');
        Route::post('/profile/password', [AuthController::class, 'updatePassword'])->name('member.password.update');
        
        Route::get('/feed', [MemberController::class, 'feed'])->name('member.feed');
        Route::get('/events', [MemberController::class, 'events'])->name('member.events');
        Route::get('/waste-report', [MemberController::class, 'wasteReport'])->name('member.waste_report');
        Route::post('/waste-report', [MemberController::class, 'storeWasteReport'])->name('member.waste_report.store');

        // Kartu Digital Member
        Route::get('/my-card', [MemberController::class, 'myCardEditor'])->name('member.card.editor');
        Route::post('/my-card/download', [MemberController::class, 'downloadMyCard'])->name('member.card.download');

        // Chat & Call Routes
        Route::get('/chat', [MemberController::class, 'chatList'])->name('member.chat.list');
        Route::get('/chat/{id}', [MemberController::class, 'chatRoom'])->name('member.chat.room');
        Route::post('/chat/{id}/send', [MemberController::class, 'sendMessage'])->name('member.chat.send');
        Route::get('/chat/{id}/poll', [MemberController::class, 'pollMessages'])->name('member.chat.poll');
        Route::get('/call/{id}', [MemberController::class, 'videoCall'])->name('member.video_call');
        
        Route::post('/api/update-player-id', function(\Illuminate\Http\Request $request) {
            auth()->user()->update(['onesignal_player_id' => $request->player_id]);
            return response()->json(['success' => true]);
        });
    });

    Route::middleware(['role:admin,superadmin'])->prefix('admin')->group(function () {
        Route::get('/dashboard', [AdminController::class, 'dashboard'])->name('admin.dashboard');
        Route::get('/members', [AdminController::class, 'members'])->name('admin.members');
        Route::post('/members', [AdminController::class, 'storeMember'])->name('admin.members.store');
        Route::put('/members/{id}', [AdminController::class, 'updateMember'])->name('admin.members.update');
        Route::delete('/members/{id}', [AdminController::class, 'destroyMember'])->name('admin.members.destroy');
        Route::get('/members/export', [AdminController::class, 'exportMembers'])->name('admin.members.export');

        Route::get('/posts', [AdminController::class, 'posts'])->name('admin.posts');
        Route::post('/posts', [AdminController::class, 'storePost'])->name('admin.posts.store');
        Route::delete('/posts/{id}', [AdminController::class, 'destroyPost'])->name('admin.posts.destroy');

        Route::get('/events', [AdminController::class, 'adminEvents'])->name('admin.events');
        Route::post('/events', [AdminController::class, 'storeEvent'])->name('admin.events.store');
        Route::delete('/events/{id}', [AdminController::class, 'destroyEvent'])->name('admin.events.destroy');

        Route::get('/waste-deposits', [AdminController::class, 'wasteDeposits'])->name('admin.waste_deposits');
        Route::put('/waste-deposits/{id}/status', [AdminController::class, 'updateWasteStatus'])->name('admin.waste_deposits.status');
        Route::delete('/waste-deposits/{id}', [AdminController::class, 'destroyWasteDeposit'])->name('admin.waste_deposits.destroy');

        Route::get('/finances', [AdminController::class, 'finances'])->name('admin.finances');
        Route::post('/finances', [AdminController::class, 'storeFinance'])->name('admin.finances.store');
        Route::put('/finances/{id}', [AdminController::class, 'updateFinance'])->name('admin.finances.update');
        Route::delete('/finances/{id}', [AdminController::class, 'destroyFinance'])->name('admin.finances.destroy');
        Route::get('/finances/export', [AdminController::class, 'exportFinances'])->name('admin.finances.export');

        Route::get('/cards', [AdminController::class, 'cards'])->name('admin.cards');
        Route::get('/card-preview/{id}', [AdminController::class, 'previewCard'])->name('admin.card_preview');
        Route::post('/generate-card-custom/{id}', [AdminController::class, 'generateCardCustom'])->name('admin.generate_card_custom');
        
        Route::get('/messages', [AdminController::class, 'messages'])->name('admin.messages');
        Route::get('/logs', [AdminController::class, 'logs'])->name('admin.logs');
        
        // AI Analytics
        Route::get('/ai-analytics', [AdminController::class, 'aiAnalytics'])->name('admin.ai_analytics');

        // Hasil Produksi (V1.2)
        Route::get('/productions', [AdminController::class, 'productions'])->name('admin.productions');
        Route::post('/productions', [AdminController::class, 'storeProduction'])->name('admin.productions.store');
        Route::delete('/productions/{id}', [AdminController::class, 'destroyProduction'])->name('admin.productions.destroy');
    });
});

Route::get('/v/{id}', [PublicController::class, 'verify_card'])->name('member.verify');
Route::post('/api/parse-gmaps', [PublicController::class, 'parseGmapsLink'])->name('api.parse_gmaps');

// API Khusus untuk Google Colab / Python Script
Route::get('/api/export-waste-data', [AdminController::class, 'exportWasteData'])->name('api.export_waste');
Route::post('/api/save-ai-results', [AdminController::class, 'saveAiResults'])->name('api.save_ai_results');
