<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\MemberController;
use App\Http\Controllers\PublicController;
use App\Http\Controllers\DevApiController;
use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/about', function () {
    return view('pages.about');
})->name('about');

Route::get('/faq', function () {
    return view('pages.faq');
})->name('faq');

Route::get('/privacy-policy', function () {
    return view('pages.privacy_policy');
})->name('privacy_policy');

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
        
        Route::get('/community', [MemberController::class, 'feed'])->name('member.feed');
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
        // Route::get('/call/{id}', [MemberController::class, 'videoCall'])->name('member.video_call');
        Route::get('/call/{id}', [MemberController::class, 'videoCall'])->name('member.video_call');
        
        Route::post('/member/feed/{id}/like', [\App\Http\Controllers\MemberController::class, 'toggleLike'])->name('member.feed.like');
Route::post('/member/feed/{id}/comment', [\App\Http\Controllers\MemberController::class, 'storeComment'])->name('member.feed.comment');
        Route::post('/member/feed/store', [\App\Http\Controllers\MemberController::class, 'storePost'])->name('member.feed.store');
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
        Route::post('/posts/{id}', [AdminController::class, 'updatePost'])->name('admin.posts.update');
        Route::delete('/posts/{id}', [AdminController::class, 'destroyPost'])->name('admin.posts.destroy');

        // Media CMS
        Route::get('/media', [AdminController::class, 'media'])->name('admin.media');
        Route::post('/media', [AdminController::class, 'storeMedia'])->name('admin.media.store');
        Route::delete('/media/{filename}', [AdminController::class, 'destroyMedia'])->name('admin.media.destroy');

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
        Route::put('/messages/{id}/mark-read', [AdminController::class, 'markMessageAsRead'])->name('admin.messages.mark_read');
        Route::get('/logs', [AdminController::class, 'logs'])->name('admin.logs');
        
        // AI Analytics
        Route::get('/ai-analytics', [AdminController::class, 'aiAnalytics'])->name('admin.ai_analytics');

        // Hasil Produksi (V1.2)
        Route::get('/productions', [AdminController::class, 'productions'])->name('admin.productions');
        Route::post('/productions', [AdminController::class, 'storeProduction'])->name('admin.productions.store');
        Route::delete('/productions/{id}', [AdminController::class, 'destroyProduction'])->name('admin.productions.destroy');

        // API & Integrasi OAuth Clients
        Route::get('/integrations', [AdminController::class, 'oauthClients'])->name('admin.integrations');
        Route::post('/integrations', [AdminController::class, 'storeOauthClient'])->name('admin.integrations.store');
        Route::delete('/integrations/{id}/revoke', [AdminController::class, 'revokeOauthClient'])->name('admin.integrations.revoke');
    });
});

// --- Public API Endpoint (OAuth Resource) ---
Route::middleware('auth')->get('/api/user/me', function () {
    $user = auth()->user();
    return response()->json([
        'id'         => $user->id,
        'name'       => $user->name,
        'email'      => $user->email,
        'jabatan'    => $user->jabatan,
        'member_id'  => $user->member_id,
        'status'     => $user->status,
        'role'       => $user->role,
        'avatar_url' => $user->photo ? asset('storage/' . $user->photo) : null,
        'can_chat'   => (bool) $user->can_chat,
        'can_post'   => (bool) $user->can_post,
        'can_comment'=> (bool) $user->can_comment,
        'created_at' => $user->created_at,
    ]);
})->name('api.user.me');

Route::get('/v/{id}', [PublicController::class, 'verify_card'])->name('member.verify');
Route::post('/api/parse-gmaps', [PublicController::class, 'parseGmapsLink'])->name('api.parse_gmaps');

// API Khusus untuk Google Colab / Python Script
Route::get('/api/export-waste-data', [AdminController::class, 'exportWasteData'])->name('api.export_waste');
Route::post('/api/save-ai-results', [AdminController::class, 'saveAiResults'])->name('api.save_ai_results');

Route::get('/public-feed', [MemberController::class, 'feed']);

// ─── Developer Diagnostic API ────────────────────────────────────────────────
// Diproteksi oleh Bearer token (DEV_API_SECRET di .env).
// Digunakan AI assistant untuk inspect kode, log, DB tanpa FTP.
// SEMUA ENDPOINT BERSIFAT READ-ONLY.
Route::prefix('api/dev')->middleware('dev.api')->group(function () {
    Route::get('/ping',        [DevApiController::class, 'ping']);        // Cek status server
    Route::get('/logs',        [DevApiController::class, 'logs']);        // Laravel log terbaru
    Route::get('/errors',      [DevApiController::class, 'errors']);      // Error ter-parse dari log
    Route::get('/files',       [DevApiController::class, 'listFiles']);   // List file/direktori
    Route::get('/file',        [DevApiController::class, 'readFile']);    // Baca isi file
    Route::get('/routes',      [DevApiController::class, 'routes']);      // Semua route terdaftar
    Route::get('/env',         [DevApiController::class, 'envInfo']);     // Info env (aman, tanpa secret)
    Route::get('/db/tables',   [DevApiController::class, 'dbTables']);   // Daftar tabel & kolom DB
    Route::post('/db/query',   [DevApiController::class, 'dbQuery']);    // Jalankan SELECT query
});
