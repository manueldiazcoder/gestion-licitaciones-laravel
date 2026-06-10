<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\OAuthController;
use App\Http\Controllers\ProcesoController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\ForgotPasswordController;
use App\Http\Controllers\ResetPasswordController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

// ─────────────────────────────────────────
//  Rutas públicas
// ─────────────────────────────────────────

Route::get('/', function () {
    $data = [
        'totalProcesos' => \App\Models\Proceso::count(),
        'activos'       => \App\Models\Proceso::where('estado', 'activo')->count(),
        'enEvaluacion'  => \App\Models\Proceso::where('estado', 'evaluacion')->count(),
        'adjudicados'   => \App\Models\Proceso::where('estado', 'adjudicado')->count(),
    ];
    return view('home', $data);
})->name('home');

Route::middleware('guest')->group(function () {
    Route::get('/login',  [AuthController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [AuthController::class, 'login']);

    Route::get('/register',  [AuthController::class, 'showRegisterForm'])->name('register');
    Route::post('/register', [AuthController::class, 'register']);

    // ── OAuth ──────────────────────────────────────
    Route::get('/auth/{provider}/redirect', [OAuthController::class, 'redirect'])->name('oauth.redirect');
    Route::get('/auth/{provider}/callback', [OAuthController::class, 'callback'])->name('oauth.callback');

    // ── Password Reset ─────────────────────────────
    Route::get('/forgot-password',     [ForgotPasswordController::class, 'showLinkRequestForm'])->name('password.request');
    Route::post('/forgot-password',    [ForgotPasswordController::class, 'sendResetLinkEmail'])->name('password.email');
    Route::get('/reset-password/{token}', [ResetPasswordController::class, 'showResetForm'])->name('password.reset');
    Route::post('/reset-password',     [ResetPasswordController::class, 'reset'])->name('password.update');
});

// ─────────────────────────────────────────
//  Rutas protegidas (autenticado)
// ─────────────────────────────────────────

Route::middleware('auth')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

    // CRUD de Procesos — el middleware de roles está en el constructor del controller
    Route::resource('procesos', ProcesoController::class);

    // Reportes
    Route::prefix('reportes')->name('reportes.')->group(function () {
        Route::get('/',             [ReportController::class, 'index'])->name('index');
        Route::get('/exportar-csv', [ReportController::class, 'exportCsv'])->name('export');
    });

    // Responsables (solo admin)
    Route::resource('responsables', \App\Http\Controllers\ResponsableController::class)
        ->middleware('role:admin');

    // Admin: gestión de usuarios
    Route::prefix('usuarios')->name('usuarios.')->middleware('role:admin')->group(function () {
        Route::get('/',                         [\App\Http\Controllers\AdminUserController::class, 'index'])->name('index');
        Route::get('/{user}/edit',              [\App\Http\Controllers\AdminUserController::class, 'edit'])->name('edit');
        Route::put('/{user}',                   [\App\Http\Controllers\AdminUserController::class, 'update'])->name('update');
        Route::delete('/{user}',                [\App\Http\Controllers\AdminUserController::class, 'destroy'])->name('destroy');
    });
});
