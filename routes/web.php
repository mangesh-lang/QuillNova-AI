<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Artisan;
use App\Modules\Auth\Controllers\AuthController;
use App\Modules\Auth\Controllers\ProfileController;
use App\Modules\Dashboard\Controllers\DashboardController;
use App\Modules\Templates\Controllers\TemplateController;
use App\Modules\Templates\Controllers\AdminTemplateController;
use App\Modules\BlogGenerator\Controllers\BlogGeneratorController;
use App\Modules\EmailWriter\Controllers\EmailWriterController;
use App\Modules\Chat\Controllers\ChatController;
use App\Modules\History\Controllers\HistoryController;
use App\Modules\Notification\Controllers\NotificationController;
use App\Modules\Admin\Controllers\AdminDashboardController;
use App\Modules\Admin\Controllers\AdminUserController;
use App\Modules\Settings\Controllers\SettingsController;

/*
|--------------------------------------------------------------------------
| Application Setup Route (For sandbox environment migration execution)
|--------------------------------------------------------------------------
*/
Route::get('/run-setup', function () {
    try {
        echo "Starting migration and seeding processes...<br>";
        Artisan::call('migrate:fresh', ['--force' => true]);
        echo "Migrations executed successfully.<br>";
        Artisan::call('db:seed', ['--force' => true]);
        echo "Database seeding executed successfully.<br>";
        echo "<strong>QuillNova setup completed! Default credentials:</strong><br>";
        echo "- Super Admin: admin@quillnova.com / admin123<br>";
        echo "- Regular User: user@quillnova.com / user123<br>";
        return "Setup successfully finalized.";
    } catch (\Exception $e) {
        return "Error occurred during setup: " . $e->getMessage();
    }
});


/*
|--------------------------------------------------------------------------
| Guest Authentication Routes
|--------------------------------------------------------------------------
*/
Route::middleware('guest')->group(function () {
    Route::get('/', function () {
        return redirect()->route('login');
    });

    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login']);

    Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
    Route::post('/register', [AuthController::class, 'register']);

    Route::get('/forgot-password', [AuthController::class, 'showForgotPassword'])->name('password.request');
    Route::post('/forgot-password', [AuthController::class, 'sendResetLink'])->name('password.email');

    Route::get('/reset-password/{token}', [AuthController::class, 'showResetPassword'])->name('password.reset');
    Route::post('/reset-password', [AuthController::class, 'resetPassword'])->name('password.update');
});

/*
|--------------------------------------------------------------------------
| Authenticated User Routes (Requires active status check)
|--------------------------------------------------------------------------
*/
Route::middleware(['auth', 'active.user'])->group(function () {
    // Logout
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

    // User Dashboard
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // Profile Management
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::post('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::post('/profile/password', [ProfileController::class, 'updatePassword'])->name('profile.password');
    Route::post('/profile/delete', [ProfileController::class, 'destroy'])->name('profile.delete');

    // AI Templates & Generation
    Route::get('/templates', [TemplateController::class, 'index'])->name('templates.index');
    Route::get('/templates/{slug}', [TemplateController::class, 'show'])->name('templates.show');
    Route::post('/generate/{slug}', [TemplateController::class, 'generate'])->name('templates.generate');

    // Dedicated Generators (Multi-step / Advanced form modules)
    Route::get('/blog-generator', [BlogGeneratorController::class, 'index'])->name('blog.index');
    Route::post('/blog-generator/outline', [BlogGeneratorController::class, 'generateOutline'])->name('blog.outline');
    Route::post('/blog-generator/post', [BlogGeneratorController::class, 'generatePost'])->name('blog.post');

    Route::get('/email-writer', [EmailWriterController::class, 'index'])->name('email.index');
    Route::post('/email-writer/generate', [EmailWriterController::class, 'generate'])->name('email.generate');

    // AI Interactive Chat System (Conversations)
    Route::get('/chat', [ChatController::class, 'index'])->name('chat.index');
    Route::get('/chat/{id}', [ChatController::class, 'index'])->name('chat.show');
    Route::post('/chat-api/session', [ChatController::class, 'createSession'])->name('chat.session.create');
    Route::put('/chat-api/session/{id}', [ChatController::class, 'renameSession'])->name('chat.session.rename');
    Route::delete('/chat-api/session/{id}', [ChatController::class, 'deleteSession'])->name('chat.session.delete');
    Route::post('/chat-api/session/{id}/message', [ChatController::class, 'sendMessage'])->name('chat.message.send');

    // Content Generation History
    Route::get('/history', [HistoryController::class, 'index'])->name('history.index');
    Route::post('/history/{id}/favorite', [HistoryController::class, 'toggleFavorite'])->name('history.favorite');
    Route::delete('/history/{id}/delete', [HistoryController::class, 'destroy'])->name('history.destroy');
    Route::get('/history/{id}/download/txt', [HistoryController::class, 'downloadTxt'])->name('history.download.txt');
    Route::get('/history/{id}/download/pdf', [HistoryController::class, 'downloadPdf'])->name('history.download.pdf');

    // Notifications Log
    Route::get('/notifications', [NotificationController::class, 'index'])->name('notifications.index');
    Route::post('/notifications/{id}/read', [NotificationController::class, 'markAsRead'])->name('notifications.read');
    Route::post('/notifications/read-all', [NotificationController::class, 'markAllAsRead'])->name('notifications.read_all');
});

/*
|--------------------------------------------------------------------------
| Super Admin Panel Routes (Restricted)
|--------------------------------------------------------------------------
*/
Route::middleware(['auth', 'active.user', 'admin'])->prefix('admin')->group(function () {
    // Admin Dashboard & Logs
    Route::get('/', [AdminDashboardController::class, 'index'])->name('admin.dashboard');
    Route::get('/logs', [AdminDashboardController::class, 'logs'])->name('admin.logs');

    // Users Management CRUD & Limits Control
    Route::get('/users', [AdminUserController::class, 'index'])->name('admin.users');
    Route::post('/users', [AdminUserController::class, 'store'])->name('admin.users.store');
    Route::put('/users/{id}', [AdminUserController::class, 'update'])->name('admin.users.update');
    Route::delete('/users/{id}', [AdminUserController::class, 'destroy'])->name('admin.users.destroy');
    Route::post('/users/{id}/suspend', [AdminUserController::class, 'suspend'])->name('admin.users.suspend');
    Route::post('/users/{id}/unsuspend', [AdminUserController::class, 'unsuspend'])->name('admin.users.unsuspend');
    Route::post('/users/{id}/reset-limits', [AdminUserController::class, 'resetLimits'])->name('admin.users.reset_limits');

    // Templates CRUD & Category CRUD
    Route::get('/templates', [AdminTemplateController::class, 'index'])->name('admin.templates.index');
    Route::post('/categories', [AdminTemplateController::class, 'storeCategory'])->name('admin.categories.store');
    Route::put('/categories/{id}', [AdminTemplateController::class, 'updateCategory'])->name('admin.categories.update');
    Route::delete('/categories/{id}', [AdminTemplateController::class, 'destroyCategory'])->name('admin.categories.destroy');
    Route::post('/templates', [AdminTemplateController::class, 'storeTemplate'])->name('admin.templates.store');
    Route::put('/templates/{id}', [AdminTemplateController::class, 'updateTemplate'])->name('admin.templates.update');
    Route::delete('/templates/{id}', [AdminTemplateController::class, 'destroyTemplate'])->name('admin.templates.destroy');

    // System Configurations Settings
    Route::get('/settings', [SettingsController::class, 'index'])->name('admin.settings');
    Route::post('/settings', [SettingsController::class, 'update'])->name('admin.settings.update');
});
