<?php

use App\Http\Controllers\AiController;
use App\Http\Controllers\ProjectController;
use App\Http\Controllers\DeployController;
use App\Http\Controllers\MarketplaceController;
use App\Http\Controllers\SettingsController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\AuthController;
use Illuminate\Support\Facades\Route;

// ─── PUBLIC ROUTES ─────────────────────────────────────────────────

Route::get('/', fn() => view('welcome'))->name('home');

// ─── AUTH ──────────────────────────────────────────────────────────

Route::middleware('guest')->group(function () {
    Route::get('/login',    [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login',   [AuthController::class, 'login']);
    Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
    Route::post('/register',[AuthController::class, 'register']);
    Route::get('/forgot-password', [AuthController::class, 'showForgotPassword'])->name('password.request');
    Route::post('/forgot-password',[AuthController::class, 'sendResetLink'])->name('password.email');
    Route::get('/reset-password/{token}', [AuthController::class, 'showResetForm'])->name('password.reset');
    Route::post('/reset-password',        [AuthController::class, 'resetPassword'])->name('password.update');
});

Route::post('/logout', [AuthController::class, 'logout'])
    ->middleware('auth')
    ->name('logout');

// ─── AUTHENTICATED ROUTES ──────────────────────────────────────────

Route::middleware(['auth', 'verified'])->group(function () {

    // Dashboard
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // Projects
    Route::resource('projects', ProjectController::class);
    Route::get('/projects/{project}/builder', [ProjectController::class, 'show'])->name('projects.builder');
    Route::get('/projects/{project}/files/{file}', [ProjectController::class, 'getFile'])->name('projects.files.get');
    Route::post('/projects/{project}/files', [ProjectController::class, 'saveFile'])->name('projects.files.save');

    // AI Generation
    Route::prefix('ai')->name('ai.')->group(function () {
        Route::post('/generate',       [AiController::class, 'generate'])->name('generate');
        Route::post('/fix',            [AiController::class, 'fix'])->name('fix');
        Route::post('/explain',        [AiController::class, 'explain'])->name('explain');
        Route::post('/seo',            [AiController::class, 'generateSeo'])->name('seo');
        Route::get('/chat/{project}',  [AiController::class, 'chatHistory'])->name('chat.history');
        Route::delete('/chat/{project}',[AiController::class, 'clearChat'])->name('chat.clear');
    });

    // Deploy
    Route::prefix('deploy')->name('deploy.')->group(function () {
        Route::get('/{project}',             [DeployController::class, 'show'])->name('show');
        Route::post('/{project}/credentials', [DeployController::class, 'saveFtpCredentials'])->name('credentials');
        Route::post('/{project}/test',        [DeployController::class, 'testConnection'])->name('test');
        Route::post('/{project}/deploy',      [DeployController::class, 'deploy'])->name('deploy');
    });

    // Marketplace
    Route::prefix('marketplace')->name('marketplace.')->group(function () {
        Route::get('/',              [MarketplaceController::class, 'index'])->name('index');
        Route::get('/{item}',        [MarketplaceController::class, 'show'])->name('show');
        Route::post('/{item}/install',[MarketplaceController::class, 'install'])->name('install');
        Route::post('/submit',       [MarketplaceController::class, 'submit'])->name('submit');
    });

    // Settings
    Route::prefix('settings')->name('settings.')->group(function () {
        Route::get('/',                         [SettingsController::class, 'index'])->name('index');
        Route::post('/api-keys',                [SettingsController::class, 'saveApiKeys'])->name('api-keys');
        Route::delete('/api-keys/{provider}',   [SettingsController::class, 'removeApiKey'])->name('api-keys.remove');
        Route::post('/profile',                 [SettingsController::class, 'updateProfile'])->name('profile');
    });
});

// ─── ADMIN ROUTES ──────────────────────────────────────────────────

Route::middleware(['auth', 'role:admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/', fn() => view('admin.dashboard'))->name('dashboard');
    Route::get('/marketplace/pending', fn() => view('admin.marketplace-pending'))->name('marketplace.pending');
    Route::post('/marketplace/{item}/approve', function (\App\Models\MarketplaceItem $item) {
        $item->update(['is_approved' => true]);
        return back()->with('success', 'Item approved.');
    })->name('marketplace.approve');
    Route::get('/users', fn() => view('admin.users'))->name('users');
});
