<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\SolicitudController;
use App\Http\Controllers\AdminComprasController;
use App\Http\Controllers\ComentarioController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

// Rutas para usuarios autenticados
Route::middleware(['auth'])->group(function () {
    
    // Rutas de perfil
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
    
    // Rutas de solicitudes para usuarios normales
    Route::get('/solicitudes', [SolicitudController::class, 'index'])->name('solicitudes.index');
    Route::get('/solicitudes/create', [SolicitudController::class, 'create'])->name('solicitudes.create');
    Route::post('/solicitudes', [SolicitudController::class, 'store'])->name('solicitudes.store');
    Route::get('/solicitudes/{solicitud}', [SolicitudController::class, 'show'])->name('solicitudes.show');
    Route::get('/solicitudes/{id}/edit', [SolicitudController::class, 'edit'])->name('solicitudes.edit');
    Route::put('/solicitudes/{id}', [SolicitudController::class, 'update'])->name('solicitudes.update');
    Route::delete('/solicitudes/{id}', [SolicitudController::class, 'destroy'])->name('solicitudes.destroy');

    // Ruta para actualizar checklist de ítems revisados
    Route::post('/solicitudes/{solicitud}/items/checklist', [SolicitudController::class, 'updateChecklist'])
        ->name('solicitudes.updateChecklist');
    
    // Rutas de comentarios
    Route::post('/solicitudes/{solicitud}/comentarios', [ComentarioController::class, 'store'])->name('comentarios.store');
    
    // Actualizar estado (solo admin, pero protegido también por middleware is_admin si lo usas aquí)
    Route::patch('/solicitudes/{solicitud}/status', [SolicitudController::class, 'updateStatus'])->name('solicitudes.updateStatus');
});

// Rutas para administradores de compras
Route::middleware(['auth', 'is_admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/solicitudes', [AdminComprasController::class, 'index'])->name('solicitudes.index');
    Route::post('/solicitudes/{id}/estado', [AdminComprasController::class, 'actualizarEstado'])->name('solicitudes.estado');
    
    // === NUEVAS RUTAS DE REPORTES ===
    Route::get('/reportes', [SolicitudController::class, 'reportes'])->name('reportes');
    Route::get('/reportes/export', [SolicitudController::class, 'exportReport'])->name('reportes.export');

    // === NUEVA RUTA: PDF solo ítems revisados de UNA solicitud ===
    Route::get('/solicitudes/{solicitud}/pdf-revisados', [SolicitudController::class, 'exportPdfRevisados'])
        ->name('solicitudes.pdf.revisados');
});

// Incluir las rutas de autenticación (login, register, password, etc.)
require __DIR__.'/auth.php';
