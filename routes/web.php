<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\InvoiceController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\TrackingController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes (Blade Views)
|--------------------------------------------------------------------------
*/

// Guest Routes
Route::middleware('guest')->group(function () {
    Route::get('login', fn () => view('auth.login'))->name('login');
    Route::post('login', [AuthController::class, 'login'])->name('login.submit');
});

// Authenticated Routes
Route::middleware(['auth', 'role:admin,owner'])->group(function () {
    Route::post('logout', [AuthController::class, 'logout'])->name('logout');

    // ─── Dashboard ──────────────────────────────────────────────────────────
    Route::get('/', [DashboardController::class, 'index'])->name('dashboard');
    Route::get('dashboard', [DashboardController::class, 'index'])->name('dashboard.index');
    Route::get('/dashboard/export', [DashboardController::class, 'exportOwnerReport'])->name('dashboard.export');

    // ─── Orders (Resource) ──────────────────────────────────────────────────
    Route::resource('orders', OrderController::class);

    // ─── Tracking ───────────────────────────────────────────────────────────
    Route::get('orders/{order}/tracking', [TrackingController::class, 'index'])->name('orders.tracking');
    Route::post('orders/{order}/tracking', [TrackingController::class, 'store'])->name('orders.tracking.store');

    // ─── Invoice ────────────────────────────────────────────────────────────
    Route::get('orders/{order}/invoice', [InvoiceController::class, 'show'])->name('orders.invoice');
    Route::patch('orders/{order}/invoice/payment', [InvoiceController::class, 'updatePayment'])->name('orders.invoice.payment');
    Route::get('orders/{order}/invoice/print', [InvoiceController::class, 'print'])->name('orders.invoice.print');

    // ─── Archives ───────────────────────────────────────────────────────────
    Route::get('archives', [OrderController::class, 'archives'])->name('archives.index');

    // ─── Reports ────────────────────────────────────────────────────────────
    Route::get('reports', [ReportController::class, 'index'])->name('reports.index');
    Route::get('reports/export', [ReportController::class, 'export'])->name('reports.export');

    // ─── Admin Only ─────────────────────────────────────────────────────────
    Route::middleware('role:admin')->group(function () {
        // User Management
        Route::get('/users', [UserController::class, 'index'])->name('users.index');
        Route::get('/users/create', [UserController::class, 'create'])->name('users.create');
        Route::post('/users', [UserController::class, 'store'])->name('users.store');
        Route::get('/users/{user}', [UserController::class, 'show'])->name('users.show');
        Route::get('/users/{user}/edit', [UserController::class, 'edit'])->name('users.edit');
        Route::put('/users/{user}', [UserController::class, 'update'])->name('users.update');
        Route::delete('/users/{user}', [UserController::class, 'destroy'])->name('users.destroy');
        
        // ─── Settings ───────────────────────────────────────────────────────────
        Route::get('settings', [\App\Http\Controllers\SettingController::class, 'index'])->name('settings.index');
        Route::put('settings', [\App\Http\Controllers\SettingController::class, 'update'])->name('settings.update');

        // ─── Bank Accounts ──────────────────────────────────────────────────────
        Route::post('settings/bank-accounts', [\App\Http\Controllers\BankAccountController::class, 'store'])->name('bank_accounts.store');
        Route::put('settings/bank-accounts/{bankAccount}', [\App\Http\Controllers\BankAccountController::class, 'update'])->name('bank_accounts.update');
        Route::patch('settings/bank-accounts/{bankAccount}/toggle', [\App\Http\Controllers\BankAccountController::class, 'toggle'])->name('bank_accounts.toggle');
        Route::delete('settings/bank-accounts/{bankAccount}', [\App\Http\Controllers\BankAccountController::class, 'destroy'])->name('bank_accounts.destroy');

        // Profile
        Route::get('profile', fn () => view(isMobile() ? 'profile.mobile.index' : 'profile.index'))->name('profile.index');
        Route::put('profile', [UserController::class, 'updateProfile'])->name('profile.update');
        Route::put('profile/password', [UserController::class, 'updatePassword'])->name('profile.password');
    });
});
