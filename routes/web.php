<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\MarketingController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\Admin;
use App\Http\Controllers\SuperAdmin\PlatformController;
use App\Http\Controllers\Student\DashboardController as StudentDashboard;

// ---------- One-time web installer (for hosts without SSH, e.g. WhoGoHost) ----------
// Enabled only when APP_INSTALL_TOKEN is set and matches ?token=. Runs migrate --seed.
// Unset APP_INSTALL_TOKEN after first use to disable.
Route::get('/install', function (\Illuminate\Http\Request $request) {
    $token = config('app.install_token');
    abort_unless($token && hash_equals($token, (string) $request->query('token')), 404);
    \Illuminate\Support\Facades\Artisan::call('migrate', ['--force' => true]);
    \Illuminate\Support\Facades\Artisan::call('db:seed', ['--force' => true]);
    return response('<pre>SAS installed.\n\n' . e(\Illuminate\Support\Facades\Artisan::output())
        . "\n\nNow unset APP_INSTALL_TOKEN in your .env to disable this route.</pre>");
});

// ---------- Public marketing ----------
Route::get('/', [MarketingController::class, 'home'])->name('home');
Route::get('/features', [MarketingController::class, 'features'])->name('features');
Route::get('/pricing', [MarketingController::class, 'pricing'])->name('pricing');
Route::get('/faq', [MarketingController::class, 'faq'])->name('faq');
Route::get('/contact', [MarketingController::class, 'contact'])->name('contact');

// ---------- Auth ----------
Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login']);
    Route::get('/start', [AuthController::class, 'showRegister'])->name('register');
    Route::post('/start', [AuthController::class, 'register']);
});
Route::post('/logout', [AuthController::class, 'logout'])->middleware('auth')->name('logout');

// ---------- School app (admin / teacher / staff / sales) ----------
Route::middleware(['auth', 'role:school_admin,teacher,staff,sales_staff'])
    ->prefix('app')->name('app.')->group(function () {
        Route::get('/', [Admin\DashboardController::class, 'index'])->name('dashboard');

        $resource = function (string $slug, string $controller) {
            Route::get("/$slug", [$controller, 'index'])->name($slug);
            Route::post("/$slug", [$controller, 'store'])->name("$slug.store");
            Route::put("/$slug/{id}", [$controller, 'update'])->name("$slug.update");
            Route::delete("/$slug/{id}", [$controller, 'destroy'])->name("$slug.destroy");
        };
        $resource('students', Admin\StudentController::class);
        $resource('classes', Admin\SchoolClassController::class);
        $resource('subjects', Admin\SubjectController::class);
        $resource('announcements', Admin\AnnouncementController::class);
        $resource('exams', Admin\ExamController::class);

        // Results (custom)
        Route::get('/results', [Admin\ResultController::class, 'index'])->name('results');
        Route::post('/results', [Admin\ResultController::class, 'store'])->name('results.store');
        Route::put('/results/{id}/status', [Admin\ResultController::class, 'setStatus'])->name('results.status');

        // POS (school_admin + sales_staff)
        Route::get('/pos', [Admin\PosController::class, 'index'])->name('pos');
        Route::post('/pos', [Admin\PosController::class, 'checkout'])->name('pos.checkout');
        $resource('inventory', Admin\ProductController::class);
    });

// School-admin-only areas
Route::middleware(['auth', 'role:school_admin'])->prefix('app')->name('app.')->group(function () {
    Route::get('/teachers', [Admin\TeacherController::class, 'index'])->name('teachers');
    Route::post('/teachers', [Admin\TeacherController::class, 'store'])->name('teachers.store');
    Route::put('/teachers/{id}', [Admin\TeacherController::class, 'update'])->name('teachers.update');
    Route::delete('/teachers/{id}', [Admin\TeacherController::class, 'destroy'])->name('teachers.destroy');

    Route::get('/fees', [Admin\FeeStructureController::class, 'index'])->name('fees');
    Route::post('/fees', [Admin\FeeStructureController::class, 'store'])->name('fees.store');
    Route::put('/fees/{id}', [Admin\FeeStructureController::class, 'update'])->name('fees.update');
    Route::delete('/fees/{id}', [Admin\FeeStructureController::class, 'destroy'])->name('fees.destroy');

    Route::get('/accounting', [Admin\AccountingController::class, 'index'])->name('accounting');
    Route::post('/accounting', [Admin\AccountingController::class, 'store'])->name('accounting.store');
    Route::put('/accounting/{id}', [Admin\AccountingController::class, 'update'])->name('accounting.update');
    Route::delete('/accounting/{id}', [Admin\AccountingController::class, 'destroy'])->name('accounting.destroy');

    Route::get('/settings', [Admin\SettingsController::class, 'edit'])->name('settings');
    Route::put('/settings', [Admin\SettingsController::class, 'update'])->name('settings.update');
});

// ---------- Student / parent portal ----------
Route::middleware(['auth', 'role:student,parent'])->prefix('portal')->name('student.')->group(function () {
    Route::get('/', [StudentDashboard::class, 'index'])->name('dashboard');
});

// ---------- Super admin / platform ----------
Route::middleware(['auth', 'role:super_admin'])->prefix('platform')->name('platform.')->group(function () {
    Route::get('/', [PlatformController::class, 'dashboard'])->name('dashboard');
    Route::get('/schools', [PlatformController::class, 'schools'])->name('schools');
    Route::put('/schools/{id}/toggle', [PlatformController::class, 'toggleSchool'])->name('schools.toggle');
    Route::get('/plans', [PlatformController::class, 'plans'])->name('plans');
    Route::post('/plans', [PlatformController::class, 'storePlan'])->name('plans.store');
    Route::put('/plans/{id}', [PlatformController::class, 'updatePlan'])->name('plans.update');
    Route::delete('/plans/{id}', [PlatformController::class, 'destroyPlan'])->name('plans.destroy');
    Route::get('/subscriptions', [PlatformController::class, 'subscriptions'])->name('subscriptions');
    Route::put('/subscriptions/{id}', [PlatformController::class, 'updateSubscription'])->name('subscriptions.update');
});
