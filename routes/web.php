<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\MemberController;
use App\Http\Controllers\DividendController;
use App\Http\Controllers\PublicController;
use App\Http\Controllers\DevApiController;
use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request;

Route::get('/', function () {
    $galleries = \App\Models\Gallery::whereNull('division_id')->latest()->get();
    $banners = $galleries->where('category', 'banner');
    $galleryMedia = $galleries->whereIn('category', ['gallery', 'post']);
    $divisions = \App\Models\Division::all();
    
    return view('welcome', compact('banners', 'galleryMedia', 'divisions'));
});

Route::get('/run-migration', function() {
    try {
        \Illuminate\Support\Facades\Schema::table('chats', function (\Illuminate\Database\Schema\Blueprint $table) {
            if (!\Illuminate\Support\Facades\Schema::hasColumn('chats', 'attachment_path')) {
                $table->string('attachment_path')->nullable()->after('message');
            }
            if (!\Illuminate\Support\Facades\Schema::hasColumn('chats', 'attachment_type')) {
                $table->string('attachment_type')->nullable()->after('attachment_path');
            }
        });
        return 'Migration schema for chats attachments updated successfully!';
    } catch (\Exception $e) {
        return 'Error running migration: ' . $e->getMessage();
    }
});

Route::get('/read-logs', function() {
    $logFile = storage_path('logs/laravel.log');
    if (!file_exists($logFile)) return 'No log file found.';
    // Get last 50 lines
    $lines = file($logFile);
    $lastLines = array_slice($lines, -50);
    return implode("<br>", $lastLines);
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

// Public Certificate Verification
Route::get('/verify-cert/{certificate_id}', function ($certificate_id) {
    $shareholder = \App\Models\Shareholder::where('certificate_id', $certificate_id)->firstOrFail();
    return view('verify_certificate', compact('shareholder'));
})->name('verify.cert');

// Division Landing Pages
Route::get('/divisi/{slug}', [PublicController::class, 'divisionLanding'])->name('public.division');

Route::post('/verify-cert/{certificate_id}', function (\Illuminate\Http\Request $request, $certificate_id) {
    $shareholder = \App\Models\Shareholder::where('certificate_id', $certificate_id)->firstOrFail();
    if ($request->pin === $shareholder->secret_pin) {
        session()->put('verified_cert_' . $certificate_id, true);
        return back()->with('success', 'PIN Benar! Sertifikat Terverifikasi.');
    }
    return back()->with('error', 'PIN Salah!');
})->name('verify.cert.post');

Route::middleware('auth')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

    // Notifications
    Route::post('/notifications/{id}/read', [\App\Http\Controllers\NotificationController::class, 'markAsRead'])->name('notifications.read');
    Route::post('/notifications/read-all', [\App\Http\Controllers\NotificationController::class, 'markAllAsRead'])->name('notifications.read_all');

    Route::middleware(['role:member,admin,superadmin'])->group(function () {
        
        // Onboarding Routes
        Route::get('/onboarding', [\App\Http\Controllers\MemberController::class, 'onboarding'])->name('member.onboarding');
        Route::post('/onboarding/submit', [\App\Http\Controllers\MemberController::class, 'submitInterview'])->name('member.onboarding.submit');

        Route::middleware(['member.verified'])->group(function () {
            Route::get('/dashboard', [\App\Http\Controllers\MemberController::class, 'dashboard'])->name('dashboard');
            Route::get('/profile', [AuthController::class, 'profile'])->name('member.profile');
            Route::post('/profile', [AuthController::class, 'updateProfile'])->name('member.profile.update');
        Route::post('/profile/password', [AuthController::class, 'updatePassword'])->name('member.password.update');
        
        Route::get('/community', [MemberController::class, 'feed'])->name('member.feed');
        // Member Finances (NEW)
        Route::get('/finances', [MemberController::class, 'finances'])->name('member.finances');
        Route::post('/finances', [MemberController::class, 'storeFinance'])->name('member.finances.store');
        Route::put('/finances/{id}', [MemberController::class, 'updateFinance'])->name('member.finances.update');
        Route::delete('/finances/{id}', [MemberController::class, 'destroyFinance'])->name('member.finances.destroy');
        Route::post('/budgets', [MemberController::class, 'storeBudget'])->name('member.budgets.store');
        
        // Member Debts
        Route::post('/finances/debts', [MemberController::class, 'storeDebt'])->name('member.debts.store');
        Route::put('/finances/debts/{id}', [MemberController::class, 'updateDebt'])->name('member.debts.update');
        Route::delete('/finances/debts/{id}', [MemberController::class, 'destroyDebt'])->name('member.debts.destroy');
        Route::post('/finances/debts/{id}/pay', [MemberController::class, 'payDebt'])->name('member.debts.pay');

        // Member Delegations
        Route::get('/delegations', [MemberController::class, 'delegations'])->name('member.delegations');
        Route::post('/delegations', [MemberController::class, 'storeDelegation'])->name('member.delegations.store');
        Route::delete('/delegations/{id}', [MemberController::class, 'revokeDelegation'])->name('member.delegations.revoke');

        Route::get('/events', [MemberController::class, 'events'])->name('member.events');
        Route::get('/waste-report', [MemberController::class, 'wasteReport'])->name('member.waste_report');
        Route::post('/waste-report', [MemberController::class, 'storeWasteReport'])->name('member.waste_report.store');

        // Kartu Digital Member
        Route::get('/my-card', [MemberController::class, 'myCardEditor'])->name('member.card.editor');
        Route::get('/my-card/auth', [MemberController::class, 'cardAuth'])->name('member.card.auth');
        Route::post('/my-card/auth', [MemberController::class, 'verifyCardAuth'])->name('member.card.auth.verify');
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
        }); // END member.verified group
    });

    Route::middleware(['role:admin,superadmin'])->prefix('admin')->group(function () {
        Route::get('/dashboard', [AdminController::class, 'dashboard'])->name('admin.dashboard');
        Route::get('/members', [AdminController::class, 'members'])->name('admin.members');
        Route::post('/members', [AdminController::class, 'storeMember'])->name('admin.members.store');
        Route::put('/members/{id}', [AdminController::class, 'updateMember'])->name('admin.members.update');
        Route::delete('/members/{id}', [AdminController::class, 'destroyMember'])->name('admin.members.destroy');
Route::post('/members/{id}/toggle-chat', [AdminController::class, 'toggleChatAccess'])->name('admin.members.toggle_chat');
        Route::get('/members/export', [AdminController::class, 'exportMembers'])->name('admin.members.export');

        Route::get('/posts', [AdminController::class, 'posts'])->name('admin.posts');
        Route::post('/posts', [AdminController::class, 'storePost'])->name('admin.posts.store');
        Route::post('/posts/{id}', [AdminController::class, 'updatePost'])->name('admin.posts.update');
        Route::delete('/posts/{id}', [AdminController::class, 'destroyPost'])->name('admin.posts.destroy');

        // --- RAB ---
        Route::get('/rabs', [AdminController::class, 'rabs'])->name('admin.rabs');
        Route::get('/rabs/export', [AdminController::class, 'exportRabs'])->name('admin.rabs.export');
        Route::post('/rabs', [AdminController::class, 'storeRab'])->name('admin.rabs.store');
        Route::put('/rabs/{id}/status', [AdminController::class, 'updateRabStatus'])->name('admin.rabs.status');

        // --- Productions ---
        Route::get('/productions', [AdminController::class, 'productions'])->name('admin.productions');
        Route::get('/productions/export', [AdminController::class, 'exportProductions'])->name('admin.productions.export');
        Route::post('/productions', [AdminController::class, 'storeProduction'])->name('admin.productions.store');
        Route::delete('/productions/{id}', [AdminController::class, 'destroyProduction'])->name('admin.productions.destroy');

        // --- Enterprise Resource Planning (ERP) ---
        Route::prefix('erp')->name('admin.erp.')->group(function() {
            Route::get('/', [\App\Http\Controllers\ERPController::class, 'index'])->name('index');
            Route::get('/roles', [\App\Http\Controllers\ERPController::class, 'roles'])->name('roles');
            Route::post('/roles/update', [\App\Http\Controllers\ERPController::class, 'updateRoles'])->name('roles.update');
            Route::get('/chat-relations', [\App\Http\Controllers\ERPController::class, 'chatRelations'])->name('chat_relations');
            Route::post('/chat-relations/save', [\App\Http\Controllers\ERPController::class, 'saveChatRelations'])->name('chat_relations.save');
            Route::get('/tools', [\App\Http\Controllers\ERPController::class, 'tools'])->name('tools');
        });

        // --- Divisions ---
        Route::get('/divisions', [\App\Http\Controllers\DivisionController::class, 'index'])->name('admin.divisions');
        Route::post('/divisions', [\App\Http\Controllers\DivisionController::class, 'store'])->name('admin.divisions.store');
        Route::get('/divisions/{id}', [\App\Http\Controllers\DivisionController::class, 'show'])->name('admin.divisions.show');
        Route::put('/divisions/{id}', [\App\Http\Controllers\DivisionController::class, 'update'])->name('admin.divisions.update');
        Route::delete('/divisions/{id}', [\App\Http\Controllers\DivisionController::class, 'destroy'])->name('admin.divisions.destroy');
        Route::post('/divisions/{id}/assign', [\App\Http\Controllers\DivisionController::class, 'assignMember'])->name('admin.divisions.assign');
        Route::delete('/divisions/{id}/remove/{userId}', [\App\Http\Controllers\DivisionController::class, 'removeMember'])->name('admin.divisions.remove');
        Route::get('/divisions/{id}/budgets', [\App\Http\Controllers\DivisionController::class, 'budgets'])->name('admin.divisions.budgets');
        Route::post('/divisions/{id}/budgets', [\App\Http\Controllers\DivisionController::class, 'storeBudget'])->name('admin.divisions.budgets.store');
        Route::get('/divisions/{id}/finances', [\App\Http\Controllers\DivisionController::class, 'finances'])->name('admin.divisions.finances');
        Route::post('/divisions/{id}/finances', [\App\Http\Controllers\DivisionController::class, 'storeFinance'])->name('admin.divisions.finances.store');
        Route::put('/divisions/{id}/finances/{finance_id}', [\App\Http\Controllers\DivisionController::class, 'updateFinance'])->name('admin.divisions.finances.update');
        Route::delete('/divisions/{id}/finances/{finance_id}', [\App\Http\Controllers\DivisionController::class, 'destroyFinance'])->name('admin.divisions.finances.destroy');
        Route::get('/divisions/export/data', [AdminController::class, 'exportDivisions'])->name('admin.divisions.export');

        // --- Logs & Messages ---
        Route::get('/logs', [AdminController::class, 'logs'])->name('admin.logs');
        Route::get('/logs/export', [AdminController::class, 'exportLogs'])->name('admin.logs.export');
        Route::get('/messages', [AdminController::class, 'messages'])->name('admin.messages');
        Route::get('/messages/export', [AdminController::class, 'exportMessages'])->name('admin.messages.export');
        Route::put('/messages/{id}', [AdminController::class, 'markMessageAsRead'])->name('admin.messages.read');

        // --- Dividends ---
        Route::get('/dividends', [DividendController::class, 'index'])->name('admin.dividends.index');
        Route::post('/dividends', [DividendController::class, 'store'])->name('admin.dividends.store');
        Route::delete('/dividends/{id}', [DividendController::class, 'destroy'])->name('admin.dividends.destroy');
        Route::get('/dividends/scanner', function() {
            return view('admin.dividends.scanner');
        })->name('admin.dividends.scanner');
        Route::get('/dividends/{id}/generate', [DividendController::class, 'generateCertificate'])->name('admin.dividends.generate');

        // --- Master Data (Categories) ---
        Route::get('/waste-categories', [AdminController::class, 'wasteCategories'])->name('admin.waste_categories');
        Route::get('/waste-categories/export', [AdminController::class, 'exportWasteCategories'])->name('admin.waste_categories.export');
        Route::post('/waste-categories', [AdminController::class, 'storeWasteCategory'])->name('admin.waste_categories.store');
        Route::put('/waste-categories/{id}', [AdminController::class, 'updateWasteCategory'])->name('admin.waste_categories.update');
        Route::delete('/waste-categories/{id}', [AdminController::class, 'destroyWasteCategory'])->name('admin.waste_categories.destroy');

        // --- Waste Deposits ---
        Route::get('/waste-deposits', [AdminController::class, 'wasteDeposits'])->name('admin.waste_deposits');
        Route::get('/waste-deposits/export', [AdminController::class, 'exportWasteDepositsExcel'])->name('admin.waste_deposits.export');
        Route::post('/waste-deposits', [AdminController::class, 'storeWasteDepositAdmin'])->name('admin.waste_deposits.store');
        Route::put('/waste-deposits/{id}', [AdminController::class, 'updateWasteDepositAdmin'])->name('admin.waste_deposits.update');
        Route::put('/waste-deposits/{id}/status', [AdminController::class, 'updateWasteStatus'])->name('admin.waste_deposits.status');
        Route::delete('/waste-deposits/{id}', [AdminController::class, 'destroyWasteDeposit'])->name('admin.waste_deposits.destroy');

        // Media CMS
Route::get('/media', [AdminController::class, 'media'])->name('admin.media');
Route::post('/media', [AdminController::class, 'storeMedia'])->name('admin.media.store');
Route::put('/media/{id}', [AdminController::class, 'updateMedia'])->name('admin.media.update');
Route::delete('/media/{id}', [AdminController::class, 'destroyMedia'])->name('admin.media.destroy');

        Route::get('/events', [AdminController::class, 'adminEvents'])->name('admin.events');
        Route::post('/events', [AdminController::class, 'storeEvent'])->name('admin.events.store');
        Route::delete('/events/{id}', [AdminController::class, 'destroyEvent'])->name('admin.events.destroy');

        Route::put('/divisions/{id}', [\App\Http\Controllers\DivisionController::class, 'update'])->name('admin.divisions.update');
        Route::delete('/divisions/{id}', [\App\Http\Controllers\DivisionController::class, 'destroy'])->name('admin.divisions.destroy');
        Route::get('/divisions/{id}', [\App\Http\Controllers\DivisionController::class, 'show'])->name('admin.divisions.show');

        Route::get('/settings', [AdminController::class, 'settings'])->name('admin.settings');
        Route::post('/settings', [AdminController::class, 'updateSettings'])->name('admin.settings.update');

        Route::get('/waste-categories', [AdminController::class, 'wasteCategories'])->name('admin.waste_categories');
        Route::post('/waste-categories', [AdminController::class, 'storeWasteCategory'])->name('admin.waste_categories.store');
        Route::put('/waste-categories/{id}', [AdminController::class, 'updateWasteCategory'])->name('admin.waste_categories.update');
        Route::delete('/waste-categories/{id}', [AdminController::class, 'destroyWasteCategory'])->name('admin.waste_categories.destroy');

        Route::get('/finances', [AdminController::class, 'finances'])->name('admin.finances');
        Route::post('/finances', [AdminController::class, 'storeFinance'])->name('admin.finances.store');
        Route::put('/finances/{id}', [AdminController::class, 'updateFinance'])->name('admin.finances.update');
        Route::delete('/finances/{id}', [AdminController::class, 'destroyFinance'])->name('admin.finances.destroy');
        Route::get('/finances/export', [AdminController::class, 'exportFinances'])->name('admin.finances.export');
        
        Route::post('/budgets', [AdminController::class, 'storeBudget'])->name('admin.budgets.store');
        
        // Admin Debts
        Route::post('/finances/debts', [AdminController::class, 'storeDebt']);
        Route::put('/finances/debts/{id}', [AdminController::class, 'updateDebt']);
        Route::delete('/finances/debts/{id}', [AdminController::class, 'destroyDebt']);
        Route::post('/finances/debts/{id}/pay', [AdminController::class, 'payDebt']);

        // Admin Delegations
        Route::get('/delegations', [AdminController::class, 'delegations'])->name('admin.delegations');
        // Data Lake Routes
        Route::get('/data-lake', [\App\Http\Controllers\DataLakeController::class, 'index'])->name('admin.data_lake.index');
        Route::get('/data-lake/ingest', [\App\Http\Controllers\DataLakeController::class, 'ingest'])->name('admin.data_lake.ingest');
        Route::post('/data-lake/store', [\App\Http\Controllers\DataLakeController::class, 'store'])->name('admin.data_lake.store');
        Route::post('/data-lake/{id}/process', [\App\Http\Controllers\DataLakeController::class, 'process'])->name('admin.data_lake.process');
        Route::delete('/data-lake/{id}/delete', [\App\Http\Controllers\DataLakeController::class, 'destroy'])->name('admin.data_lake.destroy');

        Route::post('/delegations/{id}/approve', [\App\Http\Controllers\AdminController::class, 'approveDelegation'])->name('admin.delegations.approve');
        Route::post('/delegations/{id}/reject', [\App\Http\Controllers\AdminController::class, 'rejectDelegation'])->name('admin.delegations.reject');
        
        // RAB
        Route::get('/rabs', [AdminController::class, 'rabs'])->name('admin.rabs');
        Route::post('/rabs', [AdminController::class, 'storeRab'])->name('admin.rabs.store');
        Route::put('/rabs/{id}/status', [AdminController::class, 'updateRabStatus'])->name('admin.rabs.status');
        Route::put('/rabs/{id}', [AdminController::class, 'updateRab'])->name('admin.rabs.update');
        Route::delete('/rabs/{id}', [AdminController::class, 'destroyRab'])->name('admin.rabs.destroy');

        Route::get('/cards', [AdminController::class, 'cards'])->name('admin.cards');
        Route::get('/generate-card/{id}', [AdminController::class, 'generateCard'])->name('admin.card_generate');
        
        Route::get('/messages', [AdminController::class, 'messages'])->name('admin.messages');
        Route::put('/messages/{id}/mark-read', [AdminController::class, 'markMessageAsRead'])->name('admin.messages.mark_read');
        Route::get('/logs', [AdminController::class, 'logs'])->name('admin.logs');
        
        // Settings Phase 1
        Route::get('/settings', [App\Http\Controllers\SettingsController::class, 'index'])->name('admin.settings');
        Route::post('/settings', [App\Http\Controllers\SettingsController::class, 'update'])->name('admin.settings.update');

        // Share Link Generator
        Route::post('/share-link/generate', [App\Http\Controllers\SharedReportController::class, 'generateLink'])->name('admin.share_link.generate');

        // Chatbot API
        Route::post('/chatbot/message', [App\Http\Controllers\ChatbotController::class, 'sendMessage'])->name('admin.chatbot.message');

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

// --- Public Shared Links (Signed URLs) ---
Route::get('/shared/report/finance', [App\Http\Controllers\SharedReportController::class, 'finance'])->name('shared.report.finance')->middleware('signed');
Route::get('/shared/report/datalake/{id}', [App\Http\Controllers\SharedReportController::class, 'datalake'])->name('shared.report.datalake')->middleware('signed');
