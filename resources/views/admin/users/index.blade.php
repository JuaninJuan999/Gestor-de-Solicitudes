@extends('layouts.app')

@section('header') @endsection

@section('content')

<!-- Contenedor Principal con Fondo de Imagen -->
<div class="admin-container">
    
    <!-- Imagen de Fondo + Capa Oscura -->
    <div class="admin-bg-image"></div>
    <div class="admin-bg-overlay"></div>

    <!-- Contenedor de Ancho Limitado (Para alinear botón y tarjeta) -->
    <div class="w-100" style="max-width: 1200px;">
        
        <!-- === BOTÓN VOLVER (Alineado a la Izquierda) === -->
        <div class="mb-4 text-start">
            <a href="{{ route('dashboard') }}" class="btn-back-dashboard shadow-sm">
                <i class="bi bi-chevron-left"></i> Volver al Dashboard
            </a>
        </div>
        <!-- ============================================ -->

        <!-- Tarjeta "Cristal" Central -->
        <div class="content-box animate-up">
            
            <!-- BLOQUE DE ALERTAS -->
            @if (session('success'))
                <div class="p-4 pb-0">
                    <div class="alert alert-success alert-dismissible fade show shadow-sm text-center fs-5" role="alert" 
                         style="border: 2px solid #198754; background-color: rgba(209, 231, 221, 0.9); color: #0f5132;">
                        <i class="bi bi-check-circle-fill me-2 fs-4" style="vertical-align: middle;"></i> 
                        <span style="vertical-align: middle;">{{ session('success') }}</span>
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                </div>
            @endif

            @if (session('error'))
                <div class="p-4 pb-0">
                    <div class="alert alert-danger alert-dismissible fade show shadow-sm text-center fs-5" role="alert" 
                         style="border: 2px solid #dc3545; background-color: rgba(248, 215, 218, 0.9); color: #842029;">
                        <i class="bi bi-exclamation-triangle-fill me-2 fs-4" style="vertical-align: middle;"></i> 
                        <span style="vertical-align: middle;">{{ session('error') }}</span>
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                </div>
            @endif

            <!-- Header Transparente -->
            <div class="header-section text-center">
                <div class="title-badge-container">
                    <span class="title-badge shadow-sm">
                        <i class="bi bi-people-fill me-2"></i> Panel de Usuarios
                    </span>
                </div>
                <p class="page-subtitle mt-3 text-dark fw-bold">Gestión de roles, permisos y seguridad</p>
            </div>

            <!-- Buscador -->
            <div class="search-section">
                <div class="search-box shadow-sm">
                    <i class="bi bi-search text-primary fs-5"></i>
                    <input type="text" id="liveSearch" class="form-control bg-transparent" placeholder="Buscar por nombre o correo..." autocomplete="off">
                </div>
            </div>

            <!-- Tabla Transparente -->
            <div class="users-table-wrapper custom-scroll">
                <div class="table-header-row">
                    <div class="col-usuario">👤 Usuario</div>
                    <div class="col-rol">🔑 Rol</div>
                    <div class="col-estado">✓ Estado</div>
                    <div class="col-acciones">⚙️ Acciones</div>
                </div>

                @forelse($users as $user)
                <div class="table-data-row user-row {{ !$user->is_active ? 'row-inactive' : '' }}">
                    <div class="col-usuario">
                        <div class="user-info-cell">
                            <div class="avatar-box {{ !$user->is_active ? 'bg-secondary' : '' }}">
                                {{ strtoupper(substr($user->name, 0, 1)) }}
                            </div>
                            <div>
                                <h5 class="user-name">{{ $user->name }}</h5>
                                <small class="user-email">{{ $user->email }}</small>
                            </div>
                        </div>
                    </div>
                    <div class="col-rol">
                        <form action="{{ route('admin.users.updateRole', $user) }}" method="POST" class="w-100">
                            @csrf 
                            {{-- Este se queda PUT porque en web.php dice Route::put(...) --}}
                            @method('PUT')
                            <select name="role" class="role-select" onchange="this.form.submit()">
                                <option value="user" {{ $user->role == 'user' ? 'selected' : '' }}>Usuario</option>
                                <option value="admin" {{ $user->role == 'admin' ? 'selected' : '' }}>Admin</option>
                                <option value="supervisor" {{ $user->role == 'supervisor' ? 'selected' : '' }}>Supervisor</option>
                            </select>
                        </form>
                    </div>
                    <div class="col-estado">
                        @if($user->is_active)
                            <span class="status-badge active"><i class="bi bi-check-circle-fill"></i> Activo</span>
                        @else
                            <span class="status-badge inactive"><i class="bi bi-x-circle-fill"></i> Inactivo</span>
                        @endif
                    </div>
                    <div class="col-acciones">
                        <div class="actions-group">
                            <!-- CORREGIDO: Cambiado de PUT a PATCH para coincidir con web.php -->
                            <form action="{{ route('admin.users.toggleStatus', $user) }}" method="POST" class="d-inline">
                                @csrf 
                                @method('PATCH')
                                @if($user->is_active)
                                    <button class="btn-action btn-danger" title="Bloquear"><i class="bi bi-lock-fill"></i> Bloquear</button>
                                @else
                                    <button class="btn-action btn-success" title="Activar"><i class="bi bi-unlock-fill"></i> Activar</button>
                                @endif
                            </form>
                            
                            <!-- CORREGIDO: Cambiado de PUT a PATCH para coincidir con web.php -->
                            <form action="{{ route('admin.users.resetPassword', $user) }}" method="POST" class="d-inline" onsubmit="return confirm('¿Restablecer contraseña?');">
                                @csrf 
                                @method('PATCH')
                                <button class="btn-action btn-secondary" title="Resetear"><i class="bi bi-key-fill"></i> Reset</button>
                            </form>
                        </div>
                    </div>
                </div>
                @empty
                <div class="text-center py-5 text-white fw-bold">No hay usuarios registrados.</div>
                @endforelse

                <div class="pagination-section">
                    {{ $users->links() }}
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    document.getElementById('liveSearch').addEventListener('keyup', function() {
        let val = this.value.toLowerCase();
        document.querySelectorAll('.user-row').forEach(row => {
            let txt = (row.querySelector('.user-name')?.innerText || '') + ' ' + (row.querySelector('.user-email')?.innerText || '');
            row.style.display = txt.toLowerCase().includes(val) ? 'grid' : 'none';
        });
    });
</script>

<style>
    /* === Layout General === */
    .admin-container { 
        width: 100%; min-height: calc(100vh - 65px); 
        display: flex; flex-direction: column; align-items: center; 
        padding: 40px 20px; 
        position: relative;
    }

    /* === FONDO DE IMAGEN === */
    .admin-bg-image {
        position: fixed; inset: 0; z-index: -2;
        background-image: url('{{ asset('images/create-solicitud.jpg') }}');
        background-size: cover;
        background-position: center;
        background-repeat: no-repeat;
    }
    .admin-bg-overlay {
        position: fixed; inset: 0; z-index: -1;
        background-color: rgba(0, 0, 0, 0.5);
    }

    /* === BOTÓN "VOLVER" (Ligeramente translúcido también) === */
    .btn-back-dashboard {
        display: inline-flex; align-items: center; gap: 8px;
        background-color: rgba(255, 255, 255, 0.8); /* Blanco semi-transparente */
        color: #2c3e50; padding: 10px 20px; border-radius: 8px;
        font-weight: 600; text-decoration: none; transition: all 0.2s;
        border: 1px solid rgba(255,255,255,0.5);
        backdrop-filter: blur(5px); /* Efecto vidrio */
    }
    .btn-back-dashboard:hover {
        background-color: #fff; transform: translateY(-1px);
    }

    /* === CAJA DE CONTENIDO CRISTAL === */
    .content-box { 
        width: 100%; 
        /* AQUÍ ESTÁ EL CAMBIO DE OPACIDAD: 0.85 (85% Opaco) */
        background: rgba(255, 255, 255, 0.75); 
        backdrop-filter: blur(10px); /* Desenfoque del fondo (Efecto Glassmorphism) */
        border-radius: 20px; 
        box-shadow: 0 15px 40px rgba(0,0,0,0.25); 
        border: 1px solid rgba(255,255,255,0.4); 
        overflow: hidden; 
    }
    
    /* Header Transparente */
    .header-section { 
        padding: 35px 20px 25px; 
        background: transparent; /* Transparente para ver el cristal */
        border-bottom: 1px solid rgba(0,0,0,0.1); 
    }
    .title-badge { display: inline-block; background: #0d6efd; color: white; padding: 10px 30px; border-radius: 50px; font-size: 1.2rem; font-weight: 700; box-shadow: 0 4px 15px rgba(13, 110, 253, 0.3); }
    
    /* Buscador Semi-Transparente */
    .search-section { 
        padding: 20px 40px; 
        background: rgba(255,255,255,0.3); /* Un poco más blanco */
        border-bottom: 1px solid rgba(0,0,0,0.05); 
    }
    .search-box { display: flex; align-items: center; background: rgba(255,255,255,0.7); border: 2px solid rgba(0,0,0,0.1); border-radius: 10px; padding: 10px 20px; }
    .search-box input { border: none; width: 100%; margin-left: 10px; outline: none; background: transparent; }

    .users-table-wrapper { padding: 0 40px 40px; }
    .table-header-row, .table-data-row { display: grid; grid-template-columns: 1.5fr 1fr 1fr 1.2fr; gap: 0; }
    
    /* Header Tabla */
    .table-header-row { 
        background: rgba(0,0,0,0.05); /* Gris muy suave transparente */
        border-bottom: 2px solid rgba(0,0,0,0.1); 
        border-top: 1px solid rgba(0,0,0,0.1); 
        font-weight: 700; color: #333; text-transform: uppercase; font-size: 0.8rem; margin-top: 20px; border-radius: 8px 8px 0 0; 
    }
    .table-header-row > div { padding: 15px; border-right: 1px solid rgba(0,0,0,0.1); }
    .table-header-row > div:last-child { border-right: none; }

    /* Filas Tabla */
    .table-data-row { 
        background: rgba(255,255,255,0.6); /* Filas semi-transparentes */
        border-bottom: 1px solid rgba(0,0,0,0.05); 
        border-left: 1px solid rgba(0,0,0,0.05); 
        border-right: 1px solid rgba(0,0,0,0.05); 
    }
    .table-data-row:hover { background: rgba(255,255,255,0.9); /* Más sólido al pasar el mouse */ }
    .table-data-row > div { padding: 12px 15px; border-right: 1px solid rgba(0,0,0,0.05); display: flex; align-items: center; }
    .table-data-row > div:last-child { border-right: none; justify-content: flex-end; }
    
    .row-inactive { background-color: rgba(200,200,200,0.4) !important; opacity: 0.8; }
    
    /* Elementos Internos */
    .avatar-box { width: 42px; height: 42px; background: #212529; color: white; border-radius: 10px; display: flex; align-items: center; justify-content: center; font-weight: 700; margin-right: 12px; }
    .user-name { margin: 0; font-weight: 700; color: #000; font-size: 0.95rem; }
    .user-email { color: #444; font-size: 0.8rem; font-weight: 500; }
    
    .role-select { width: 100%; padding: 8px; border: 1px solid #aaa; border-radius: 6px; cursor: pointer; background: rgba(255,255,255,0.8); }
    
    .status-badge { padding: 6px 12px; border-radius: 20px; font-size: 0.85rem; font-weight: 700; display: inline-flex; align-items: center; gap: 6px; }
    .status-badge.active { background: #d3f9d8; color: #0b7285; border: 1px solid #b2d8b2; }
    .status-badge.inactive { background: #ffe3e3; color: #c92a2a; border: 1px solid #e3b2b2; }

    .actions-group { display: flex; gap: 8px; width: 100%; justify-content: flex-end; }
    .btn-action { padding: 6px 12px; border: none; border-radius: 6px; font-size: 0.85rem; font-weight: 600; cursor: pointer; color: white; display: flex; align-items: center; gap: 5px; }
    .btn-action.btn-danger { background: #dc3545; }
    .btn-action.btn-success { background: #198754; }
    .btn-action.btn-secondary { background: #6c757d; }
    
    .pagination-section { padding: 20px; display: flex; justify-content: center; }
    
    @media (max-width: 768px) {
        .table-header-row { display: none; }
        .table-data-row { display: flex; flex-direction: column; margin-bottom: 15px; border-radius: 8px; background: rgba(255,255,255,0.8); }
        .table-data-row > div { border-right: none; border-bottom: 1px solid rgba(0,0,0,0.1); width: 100%; }
        .actions-group { justify-content: flex-start; }
    }
</style>
@endsection

