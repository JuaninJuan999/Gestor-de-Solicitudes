<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\PerfilController;
use App\Http\Controllers\SolicitudController;
use App\Http\Controllers\AdminComprasController;
use App\Http\Controllers\ComentarioController;
use App\Http\Controllers\AdminUserController;
use App\Http\Controllers\SupervisorController; // <--- NUEVO CONTROLADOR IMPORTADO

use Illuminate\Support\Facades\Route;

// Ruta de bienvenida
Route::get('/', function () {
    return view('welcome');
})->name('welcome'); // Le puse nombre para usar route('welcome') en los botones de volver

// Dashboard principal (redirecciona según rol o muestra panel general)
Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');


// === RUTAS PARA USUARIOS AUTENTICADOS (CUALQUIER ROL) ===
Route::middleware(['auth'])->group(function () {

    // --- PERFIL ---
    Route::get('/perfil', [PerfilController::class, 'edit'])->name('perfil.edit');
    Route::put('/perfil/update', [PerfilController::class, 'update'])->name('perfil.update');
    Route::put('/perfil/password', [PerfilController::class, 'updatePassword'])->name('perfil.password');

    // Perfil default de Laravel (Breeze/Jetstream)
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // --- SOLICITUDES (CRUD Básico) ---
    Route::get('/solicitudes', [SolicitudController::class, 'index'])->name('solicitudes.index');
    Route::get('/solicitudes/create', [SolicitudController::class, 'create'])->name('solicitudes.create');
    Route::post('/solicitudes', [SolicitudController::class, 'store'])->name('solicitudes.store');
    Route::get('/solicitudes/{solicitud}', [SolicitudController::class, 'show'])->name('solicitudes.show');
    Route::get('/solicitudes/{id}/edit', [SolicitudController::class, 'edit'])->name('solicitudes.edit');
    Route::put('/solicitudes/{id}', [SolicitudController::class, 'update'])->name('solicitudes.update');
    Route::delete('/solicitudes/{id}', [SolicitudController::class, 'destroy'])->name('solicitudes.destroy');

    // Actualizar checklist
    Route::post('/solicitudes/{solicitud}/items/checklist', [SolicitudController::class, 'updateChecklist'])
        ->name('solicitudes.updateChecklist');

    // Comentarios
    Route::post('/solicitudes/{solicitud}/comentarios', [ComentarioController::class, 'store'])->name('comentarios.store');

    // Actualizar estado (usuario cancela, etc.)
    Route::patch('/solicitudes/{solicitud}/status', [SolicitudController::class, 'updateStatus'])->name('solicitudes.updateStatus');
});


// === RUTAS PARA SUPERVISORES (NUEVO) ===
Route::middleware(['auth'])->prefix('supervisor')->name('supervisor.')->group(function () {
    
    // Panel de aprobación
    Route::get('/panel', [SupervisorController::class, 'index'])->name('index');
    
    // Acciones
    Route::post('/solicitudes/{solicitud}/aprobar', [SupervisorController::class, 'aprobar'])->name('aprobar');
    Route::post('/solicitudes/{solicitud}/rechazar', [SupervisorController::class, 'rechazar'])->name('rechazar');
});


// === RUTAS PARA ADMINISTRADORES (Compras y Gestión de Usuarios) ===
// Middleware 'is_admin' debe estar registrado en Kernel.php o alias
Route::middleware(['auth', 'is_admin'])->prefix('admin')->name('admin.')->group(function () {
    
    // --- GESTIÓN DE USUARIOS ---
    Route::get('/users', [AdminUserController::class, 'index'])->name('users.index');
    Route::get('/users/{user}/edit', [AdminUserController::class, 'edit'])->name('users.edit'); // Agregué edit por si acaso
    Route::put('/users/{user}/role', [AdminUserController::class, 'updateRole'])->name('users.updateRole');
    Route::patch('/users/{user}/status', [AdminUserController::class, 'toggleStatus'])->name('users.toggleStatus'); // Cambié a PATCH que es más semántico
    Route::patch('/users/{user}/reset-password', [AdminUserController::class, 'resetPassword'])->name('users.resetPassword'); 

    // --- GESTIÓN DE COMPRAS ---
    Route::get('/solicitudes', [AdminComprasController::class, 'index'])->name('solicitudes.index');
    Route::post('/solicitudes/{id}/estado', [AdminComprasController::class, 'actualizarEstado'])->name('solicitudes.estado');

    // --- REPORTES ---
    Route::get('/reportes', [SolicitudController::class, 'reportes'])->name('reportes');
    Route::get('/reportes/export', [AdminComprasController::class, 'export'])->name('reportes.export');
    Route::get('/reportes/export-pdf', [SolicitudController::class, 'exportReportPdf'])->name('reportes.exportPdf');
    Route::get('/solicitudes/{solicitud}/pdf-revisados', [SolicitudController::class, 'exportPdfRevisados'])->name('solicitudes.pdf.revisados');
});

require __DIR__ . '/auth.php';
