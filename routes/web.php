<?php

use App\Http\Controllers\SitemapController;
use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\TestController;
use App\Http\Controllers\ChatController;
use App\Http\Controllers\LeaderboardController;
use App\Http\Controllers\SuggestionController;
use App\Http\Controllers\PageController;
use App\Http\Controllers\WebhookController;

Route::get('/', function () {
    return view('welcome');
});

// Route::get('/dashboard', function () {
//     return view('dashboard');
// })->middleware(['auth', 'verified'])->name('dashboard');

Route::get('/sitemap.xml', [SitemapController::class, 'index']);

Route::get('/dashboard', [TestController::class, 'index'])
    ->middleware(['auth', 'verified'])->name('dashboard');

// RUTE BARU UNTUK MENERIMA WEBHOOK DARI SAWERIA
Route::post('/hooks/saweria', [WebhookController::class, 'handleSaweria']);

Route::get('/test/{test}', [TestController::class, 'show'])
    ->middleware(['auth', 'verified'])->name('test.show');

Route::post('/test/{test}/submit', [TestController::class, 'submit'])
    ->middleware(['auth', 'verified'])->name('test.submit');

Route::get('/result/{testResult}', [TestController::class, 'result'])
    ->middleware(['auth', 'verified'])->name('test.result');

Route::get('/test/{test}/begin', [TestController::class, 'start'])
    ->middleware(['auth', 'verified'])->name('test.start');

Route::get('/hasil/{testResult:share_uuid}', [TestController::class, 'shareableResult'])
    ->name('test.share');

Route::middleware('auth')->group(function () {
    // ... (rute profile, chat, dll. biarkan saja)

    // --- RUTE BARU UNTUK PAPAN PERINGKAT ---
    Route::get('/peringkat', [LeaderboardController::class, 'index'])->name('leaderboard.index');
});

Route::middleware(['auth', 'verified'])->group(function () {
    // Rute untuk menampilkan halaman chat utama
    Route::get('/diskusi', [ChatController::class, 'index'])->name('chat.index');
    
    // Rute untuk mengambil pesan baru (untuk polling)
    Route::get('/diskusi/fetch', [ChatController::class, 'fetch'])->name('chat.fetch');

    // Rute untuk mengirim (menyimpan) pesan baru
    Route::post('/diskusi', [ChatController::class, 'store'])
        ->middleware('throttle:10,1') // Middleware auth sudah ada dari grup
        ->name('chat.store');

    // Rute untuk menghapus pesan
    Route::post('/diskusi/{message}/delete', [ChatController::class, 'destroy'])->name('chat.destroy');
});

Route::get('/kebijakan-privasi', [PageController::class, 'privacyPolicy'])->name('privacy.policy');

// Rute baru untuk menjadi jembatan/proxy gambar
Route::get('/image-proxy', [App\Http\Controllers\ChatController::class, 'imageProxy'])->name('image.proxy');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
    // Route::get('/progres-saya', [ProgressController::class, 'index'])->name('progress.index');
    Route::get('/saran', [SuggestionController::class, 'create'])->name('suggestions.create');
    Route::post('/saran', [SuggestionController::class, 'store'])->name('suggestions.store');
});

require __DIR__.'/auth.php';
