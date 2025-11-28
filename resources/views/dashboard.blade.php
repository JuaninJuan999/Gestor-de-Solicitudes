@extends('layouts.app')

@section('content')
<!-- Contenedor con fondo de imagen -->
<div style="background-image: url('/images/create-solicitud.jpg'); 
            background-size: cover; 
            background-position: center; 
            background-attachment: fixed; 
            background-repeat: no-repeat;
            min-height: calc(100vh - 80px);">
    
    <!-- Overlay para mejorar legibilidad -->
    <div style="background: linear-gradient(to bottom, rgba(30, 58, 138, 0.5), rgba(88, 28, 135, 0.5)); 
                min-height: calc(100vh - 80px);
                padding-top: 3rem;
                padding-bottom: 3rem;">
        
        <div class="max-w-7xl mx-auto px-6">
            
            <!-- Tarjeta de bienvenida (SIN botón) -->
            <div class="bg-white bg-opacity-30 overflow-hidden shadow-2xl rounded-lg mb-8" 
                 style="backdrop-filter: blur(8px); border: 2px solid rgba(255, 255, 255, 0.3);">
                <div class="p-8 text-gray-900">
                    <h3 class="text-3xl font-bold mb-4 text-blue-1000">
                        ¡Bienvenido al Sistema de Gestión de Solicitudes!
                    </h3>
                    <p class="text-lg text-gray-100">
                        Estás conectado como <strong>{{ auth()->user()->name }}</strong>
                    </p>
                </div>
            </div>

            <!-- Tarjetas de acceso -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                
                <!-- Tarjeta 1: Nueva Solicitud -->
                <div class="bg-white bg-opacity-30 p-6 rounded-lg shadow-xl hover:shadow-2xl transition transform hover:scale-105"
                     style="backdrop-filter: blur(8px); border: 1px solid rgba(255, 255, 255, 0.4);">
                    <div class="text-5xl mb-4 text-center">📝</div>
                    <h4 class="text-xl font-bold text-gray-900 mb-3 text-center">
                        Nueva Solicitud
                    </h4>
                    <p class="text-sm text-gray-100 mb-4 text-center">
                        Registra una nueva solicitud de compra o suministro
                    </p>
                    <div class="text-center">
                        <a href="{{ route('solicitudes.create') }}" 
                           class="inline-block px-5 py-2 bg-green-600 hover:bg-green-700 text-white font-semibold rounded-lg transition">
                            Crear ahora →
                        </a>
                    </div>
                </div>

                <!-- Tarjeta 2: Mis Solicitudes -->
                <div class="bg-white bg-opacity-30 p-6 rounded-lg shadow-xl hover:shadow-2xl transition transform hover:scale-105"
                     style="backdrop-filter: blur(8px); border: 1px solid rgba(255, 255, 255, 0.4);">
                    <div class="text-5xl mb-4 text-center">📊</div>
                    <h4 class="text-xl font-bold text-gray-900 mb-3 text-center">
                        Mis Solicitudes
                    </h4>
                    <p class="text-sm text-gray-100 mb-4 text-center">
                        Consulta el estado de todas tus solicitudes
                    </p>
                    <div class="text-center">
                        <a href="{{ route('solicitudes.index') }}" 
                           class="inline-block px-5 py-2 bg-blue-600 hover:bg-blue-700 text-white font-semibold rounded-lg transition">
                            Ver listado →
                        </a>
                    </div>
                </div>

                <!-- Tarjeta 3: Admin o Soporte -->
                @if(auth()->check() && auth()->user()->is_admin)
                    <div class="bg-white bg-opacity-30 p-6 rounded-lg shadow-xl hover:shadow-2xl transition transform hover:scale-105"
                         style="backdrop-filter: blur(8px); border: 1px solid rgba(255, 255, 255, 0.4);">
                        <div class="text-5xl mb-4 text-center">👨‍💼</div>
                        <h4 class="text-xl font-bold text-gray-900 mb-3 text-center">
                            Panel Admin
                        </h4>
                        <p class="text-sm text-gray-100 mb-4 text-center">
                            Gestiona todas las solicitudes del sistema
                        </p>
                        <div class="text-center">
                            <a href="{{ route('admin.solicitudes.index') }}" 
                               class="inline-block px-5 py-2 bg-purple-600 hover:bg-purple-700 text-white font-semibold rounded-lg transition">
                                Ir al panel →
                            </a>
                        </div>
                    </div>
                @else
                    <div class="bg-white bg-opacity-30 p-6 rounded-lg shadow-xl hover:shadow-2xl transition transform hover:scale-105"
                         style="backdrop-filter: blur(8px); border: 1px solid rgba(255, 255, 255, 0.4);">
                        <div class="text-5xl mb-4 text-center">💬</div>
                        <h4 class="text-xl font-bold text-gray-900 mb-3 text-center">
                            Soporte
                        </h4>
                        <p class="text-sm text-gray-100 mb-4 text-center">
                            ¿Necesitas ayuda? Contáctanos
                        </p>
                        <div class="text-center">
                            <a href="#" class="inline-block px-5 py-2 bg-gray-600 hover:bg-gray-700 text-white font-semibold rounded-lg transition">
                                Contactar →
                            </a>
                        </div>
                    </div>
                @endif

            </div>

        </div>

    </div>
</div>
@endsection
