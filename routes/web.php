<?php

use App\Http\Controllers\ProfileController; // (Este es el que trae Laravel por defecto, si usas Breeze/Jetstream)
use App\Http\Controllers\PerfilController;  // <--- Agregamos el NUEVO controlador personalizado
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

    // --- NUEVAS RUTAS DE PERFIL PERSONALIZADO ---
    Route::get('/perfil', [PerfilController::class, 'edit'])->name('perfil.edit');
    Route::put('/perfil/update', [PerfilController::class, 'update'])->name('perfil.update');
    Route::put('/perfil/password', [PerfilController::class, 'updatePassword'])->name('perfil.password');
    // ---------------------------------------------

    // Rutas de perfil predeterminadas de Laravel (Breeze/Jetstream)
    // Puedes mantenerlas o quitarlas si solo usarás tu PerfilController
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // Rutas de solicitudes para usuarios normales
    Route::get('/solicitudes', [SolicitudController::class, 'index'])->name('solicitudes.index');
    Route::get('/solicitudes/create', [SolicitudController::class, 'create'])->name('solicitudes.create');
    // (ya NO hay ruta createMtto, todo pasa por create?tipo=...)

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

    // === RUTAS DE REPORTES ===
    Route::get('/reportes', [SolicitudController::class, 'reportes'])->name('reportes');

    // Excel usando AdminComprasController + SolicitudesExport
    Route::get('/reportes/export', [AdminComprasController::class, 'export'])
        ->name('reportes.export');

    // PDF (sigue igual, si ya te funciona)
    Route::get('/reportes/export-pdf', [SolicitudController::class, 'exportReportPdf'])
        ->name('reportes.exportPdf');

    // === RUTA: PDF solo ítems revisados de UNA solicitud ===
    Route::get('/solicitudes/{solicitud}/pdf-revisados', [SolicitudController::class, 'exportPdfRevisados'])
        ->name('solicitudes.pdf.revisados');
});

// ruta temporal
Route::get('/php-info', function () {
    phpinfo();
});

// Incluir las rutas de autenticación (login, register, password, etc.)
require __DIR__ . '/auth.php';
