<?php

use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\Auth\VerificationController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\InstallController;
use App\Http\Controllers\SuperAdmin\SuperAdminController;
use App\Http\Controllers\SuperAdmin\UserManagementController;
use App\Http\Controllers\WebinarController;
use App\Http\Controllers\ChatController;
use App\Http\Controllers\PurchaseAlertController;
use App\Http\Controllers\EmailTemplateController;
use App\Http\Controllers\TrackingPixelController;
use App\Http\Controllers\AnalyticsController;
use App\Http\Controllers\BillingController;
use App\Http\Controllers\EmailSettingsController;
use App\Http\Controllers\GlobalAnalyticsController;
use App\Http\Controllers\RegistrantController;
use App\Http\Controllers\SettingsController;
use App\Http\Controllers\SmsSettingsController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Public routes
|--------------------------------------------------------------------------
*/

Route::get('/', function () {
    return redirect()->route('login');
});

Route::get('/install', [InstallController::class, 'index'])->name('install');
Route::post('/install', [InstallController::class, 'run'])->name('install.run');

/*
|--------------------------------------------------------------------------
| Auth routes
|--------------------------------------------------------------------------
*/

Route::middleware('guest')->group(function () {
    Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [LoginController::class, 'login'])->middleware('throttle:5,1');
    Route::get('/register', [RegisterController::class, 'showRegistrationForm'])->name('register');
    Route::post('/register', [RegisterController::class, 'register']);
});

Route::post('/logout', [LoginController::class, 'logout'])->middleware('auth')->name('logout');

// Email verification
Route::middleware('auth')->group(function () {
    Route::get('/email/verify', [VerificationController::class, 'notice'])->name('verification.notice');
    Route::get('/email/verify/{id}/{hash}', [VerificationController::class, 'verify'])
        ->middleware('signed')->name('verification.verify');
    Route::post('/email/verification-notification', [VerificationController::class, 'resend'])
        ->middleware('throttle:6,1')->name('verification.send');
});

/*
|--------------------------------------------------------------------------
| Dashboard routes (authenticated user)
|--------------------------------------------------------------------------
*/

Route::middleware(['auth', 'tenant', 'subscription'])->prefix('dashboard')->name('dashboard.')->group(function () {
    Route::get('/', [DashboardController::class, 'index'])->name('index');

    // Webinars CRUD
    Route::resource('webinars', WebinarController::class);
    Route::post('webinars/{webinar}/duplicate', [WebinarController::class, 'duplicate'])->name('webinars.duplicate');
    Route::post('webinars/{webinar}/toggle-status', [WebinarController::class, 'toggleStatus'])->name('webinars.toggle-status');

    // Chat management
    Route::get('webinars/{webinar}/chat', [ChatController::class, 'fakeMessages'])->name('webinars.chat.index');
    Route::post('webinars/{webinar}/chat', [ChatController::class, 'storeFakeMessage'])->name('webinars.chat.store');
    Route::delete('webinars/{webinar}/chat/{message}', [ChatController::class, 'destroyFakeMessage'])->name('webinars.chat.destroy');
    Route::post('webinars/{webinar}/chat/import', [ChatController::class, 'importFakeMessages'])->name('webinars.chat.import');
    Route::post('webinars/{webinar}/chat/config', [ChatController::class, 'updateConfig'])->name('webinars.chat.config');
    Route::get('webinars/{webinar}/control-room', [ChatController::class, 'controlRoom'])->name('webinars.control-room');
    Route::post('webinars/{webinar}/control-room/reply', [ChatController::class, 'replyToViewer'])->name('webinars.control-room.reply');

    // Purchase alerts
    Route::get('webinars/{webinar}/alerts', [PurchaseAlertController::class, 'index'])->name('webinars.alerts.index');
    Route::post('webinars/{webinar}/alerts', [PurchaseAlertController::class, 'store'])->name('webinars.alerts.store');
    Route::delete('webinars/{webinar}/alerts/{alert}', [PurchaseAlertController::class, 'destroy'])->name('webinars.alerts.destroy');
    Route::post('webinars/{webinar}/alerts/import', [PurchaseAlertController::class, 'import'])->name('webinars.alerts.import');

    // Email templates
    Route::get('webinars/{webinar}/emails', [EmailTemplateController::class, 'index'])->name('webinars.emails.index');
    Route::post('webinars/{webinar}/emails', [EmailTemplateController::class, 'store'])->name('webinars.emails.store');
    Route::put('webinars/{webinar}/emails/{template}', [EmailTemplateController::class, 'update'])->name('webinars.emails.update');
    Route::delete('webinars/{webinar}/emails/{template}', [EmailTemplateController::class, 'destroy'])->name('webinars.emails.destroy');

    // Tracking pixels
    Route::get('webinars/{webinar}/tracking', [TrackingPixelController::class, 'index'])->name('webinars.tracking.index');
    Route::post('webinars/{webinar}/tracking', [TrackingPixelController::class, 'store'])->name('webinars.tracking.store');
    Route::delete('webinars/{webinar}/tracking/{pixel}', [TrackingPixelController::class, 'destroy'])->name('webinars.tracking.destroy');

    // Analytics
    Route::get('webinars/{webinar}/analytics', [AnalyticsController::class, 'webinar'])->name('webinars.analytics');
    Route::get('webinars/{webinar}/analytics/export', [AnalyticsController::class, 'exportCsv'])->name('webinars.analytics.export');

    // Registrants
    Route::get('registrants', [RegistrantController::class, 'index'])->name('registrants.index');
    Route::get('registrants/{registrant}', [RegistrantController::class, 'show'])->name('registrants.show');
    Route::get('registrants-export', [RegistrantController::class, 'exportCsv'])->name('registrants.export');

    // Smart videá (filtered webinars)
    Route::get('smart-videos', [WebinarController::class, 'index'])->name('smart-videos.index')->defaults('type', 'smart_video');

    // Global analytics
    Route::get('analytics', [GlobalAnalyticsController::class, 'index'])->name('analytics.index');

    // Email templates overview
    Route::get('email-templates', [EmailSettingsController::class, 'index'])->name('email-templates.index');

    // SMS templates overview
    Route::get('sms-templates', [SmsSettingsController::class, 'index'])->name('sms-templates.index');

    // Settings
    Route::get('settings', [SettingsController::class, 'index'])->name('settings.index');
    Route::put('settings/profile', [SettingsController::class, 'updateProfile'])->name('settings.profile');
    Route::put('settings/password', [SettingsController::class, 'updatePassword'])->name('settings.password');
    Route::put('settings/smtp', [SettingsController::class, 'updateSmtp'])->name('settings.smtp');
    Route::post('settings/smtp/test', [SettingsController::class, 'testSmtp'])->name('settings.smtp.test');
    Route::put('settings/twilio', [SettingsController::class, 'updateTwilio'])->name('settings.twilio');
    Route::post('settings/api-key/regenerate', [SettingsController::class, 'regenerateApiKey'])->name('settings.api-key.regenerate');
    Route::post('settings/webhooks', [SettingsController::class, 'storeWebhook'])->name('settings.webhooks.store');
    Route::delete('settings/webhooks/{webhook}', [SettingsController::class, 'destroyWebhook'])->name('settings.webhooks.destroy');

    // Billing
    Route::get('billing', [BillingController::class, 'index'])->name('billing.index');
});

/*
|--------------------------------------------------------------------------
| Super Admin routes
|--------------------------------------------------------------------------
*/

Route::middleware(['auth', 'super_admin'])->prefix('super-admin')->name('super-admin.')->group(function () {
    Route::get('/', [SuperAdminController::class, 'dashboard'])->name('dashboard');

    // User management
    Route::get('/users', [UserManagementController::class, 'index'])->name('users.index');
    Route::get('/users/{user}', [UserManagementController::class, 'show'])->name('users.show');
    Route::post('/users/{user}/toggle-active', [UserManagementController::class, 'toggleActive'])->name('users.toggle-active');
    Route::post('/users/{user}/change-plan', [UserManagementController::class, 'changePlan'])->name('users.change-plan');

    // Impersonation
    Route::post('/users/{user}/impersonate', [UserManagementController::class, 'impersonate'])->name('users.impersonate');
    Route::get('/stop-impersonation', [UserManagementController::class, 'stopImpersonation'])->name('stop-impersonation');

    // Billing overview
    Route::get('/billing', [SuperAdminController::class, 'billing'])->name('billing');

    // Global settings
    Route::get('/settings', [SuperAdminController::class, 'settings'])->name('settings');
    Route::put('/settings', [SuperAdminController::class, 'updateSettings'])->name('settings.update');

    // Logs
    Route::get('/logs', [SuperAdminController::class, 'logs'])->name('logs');
});

/*
|--------------------------------------------------------------------------
| Billing (placeholder)
|--------------------------------------------------------------------------
*/

Route::middleware('auth')->group(function () {
    Route::get('/billing/plans', function () {
        return redirect()->route('dashboard.billing.index');
    })->name('billing.plans');
});

/*
|--------------------------------------------------------------------------
| Public webinar routes (for viewers)
|--------------------------------------------------------------------------
*/

// Embed routes (iframe-friendly, CORS headers)
Route::get('/embed/register/{tenantSlug}/{webinarSlug}', [App\Http\Controllers\EmbedController::class, 'registerForm'])->name('embed.register');
Route::post('/embed/register/{tenantSlug}/{webinarSlug}', [App\Http\Controllers\EmbedController::class, 'registerSubmit']);
Route::get('/embed/player/{webinarId}', [App\Http\Controllers\EmbedController::class, 'player'])->name('embed.player');

Route::get('/{tenantSlug}/w/{webinarSlug}', [App\Http\Controllers\PublicWebinarController::class, 'registrationPage'])->name('public.register');
Route::post('/{tenantSlug}/w/{webinarSlug}', [App\Http\Controllers\PublicWebinarController::class, 'register']);
Route::get('/thankyou/{accessToken}', [App\Http\Controllers\PublicWebinarController::class, 'thankYouPage'])->name('public.thankyou');
Route::get('/watch/{accessToken}', [App\Http\Controllers\PublicWebinarController::class, 'watchPage'])->name('public.watch');
