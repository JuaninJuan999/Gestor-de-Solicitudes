@extends('layouts.app')

@section('content')
<!-- Contenedor con fondo de imagen -->
<div style="background-image: url('/images/create-solicitud.jpg'); 
            background-size: cover; 
            background-position: center; 
            background-attachment: fixed; 
            background-repeat: no-repeat;
            min-height: calc(100vh - 80px);
            padding-top: 3rem;
            padding-bottom: 3rem;">
    
    <div class="max-w-7xl mx-auto px-6">
        
        <!-- Tarjeta de bienvenida (70% transparente) -->
        <div class="bg-white bg-opacity-70 overflow-hidden shadow-2xl rounded-lg mb-8" 
             style="backdrop-filter: blur(10px);">
            <div class="p-8 text-gray-900">
                <h3 class="text-3xl font-bold mb-4 text-blue-600">
                    ¡Bienvenido al Sistema de Gestión de Solicitudes!
                </h3>
                <p class="text-lg text-gray-700">
                    Estás conectado como <strong>{{ auth()->user()->name }}</strong>
                </p>
            </div>
        </div>

        <!-- Tarjetas de acceso -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            
            <!-- Tarjeta 1: Nueva Solicitud -->
            <div class="bg-white bg-opacity-70 p-6 rounded-lg shadow-xl hover:shadow-2xl transition transform hover:scale-105" 
                 style="backdrop-filter: blur(10px);">
                <div class="text-5xl mb-4 text-center">📝</div>
                <h4 class="text-xl font-bold text-gray-900 mb-3 text-center">
                    Nueva Solicitud
                </h4>
                <p class="text-sm text-gray-700 mb-4 text-center">
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
            <div class="bg-white bg-opacity-70 p-6 rounded-lg shadow-xl hover:shadow-2xl transition transform hover:scale-105" 
                 style="backdrop-filter: blur(10px);">
                <div class="text-5xl mb-4 text-center">📊</div>
                <h4 class="text-xl font-bold text-gray-900 mb-3 text-center">
                    Mis Solicitudes
                </h4>
                <p class="text-sm text-gray-700 mb-4 text-center">
                    Consulta el estado de todas tus solicitudes
                </p>
                <div class="text-center">
                    <a href="{{ route('solicitudes.index') }}" 
                       class="inline-block px-5 py-2 bg-blue-600 hover:bg-blue-700 text-white font-semibold rounded-lg transition">
                        Ver listado →
                    </a>
                </div>
            </div>

            <!-- Tarjeta 3: Lógica de Roles (Admin / Supervisor / Soporte) -->
            
            {{-- CASO 1: ADMINISTRADOR --}}
            @if(auth()->user()->role === 'admin' || auth()->user()->is_admin)
                <div class="bg-white bg-opacity-70 p-6 rounded-lg shadow-xl hover:shadow-2xl transition transform hover:scale-105" 
                     style="backdrop-filter: blur(10px);">
                    <div class="text-5xl mb-4 text-center">👨‍💼</div>
                    <h4 class="text-xl font-bold text-gray-900 mb-3 text-center">
                        Centro de Control
                    </h4>
                    <p class="text-sm text-gray-700 mb-4 text-center">
                        Gestiona todas las solicitudes del sistema
                    </p>
                    <div class="text-center">
                        <a href="{{ route('admin.solicitudes.index') }}" 
                           class="inline-block px-5 py-2 bg-purple-600 hover:bg-purple-700 text-white font-semibold rounded-lg transition">
                            Ir al panel →
                        </a>
                    </div>
                </div>

            {{-- CASO 2: SUPERVISOR (NUEVO) --}}
            @elseif(auth()->user()->role === 'supervisor')
                <div class="bg-white bg-opacity-70 p-6 rounded-lg shadow-xl hover:shadow-2xl transition transform hover:scale-105" 
                     style="backdrop-filter: blur(10px);">
                    <div class="text-5xl mb-4 text-center">✅</div>
                    <h4 class="text-xl font-bold text-gray-900 mb-3 text-center">
                        Aprobar Solicitudes
                    </h4>
                    <p class="text-sm text-gray-700 mb-4 text-center">
                        Revisa y autoriza las solicitudes de tu equipo
                    </p>
                    <div class="text-center">
                        <a href="{{ route('supervisor.index') }}" 
                           class="inline-block px-5 py-2 bg-indigo-600 hover:bg-indigo-700 text-white font-semibold rounded-lg transition">
                            Ir a aprobar →
                        </a>
                    </div>
                </div>

            {{-- CASO 3: USUARIO NORMAL (SOPORTE) --}}
            @else
                <div class="bg-white bg-opacity-70 p-6 rounded-lg shadow-xl hover:shadow-2xl transition transform hover:scale-105" 
                     style="backdrop-filter: blur(10px);">
                    <div class="text-5xl mb-4 text-center">💬</div>
                    <h4 class="text-xl font-bold text-gray-900 mb-3 text-center">
                        Soporte
                    </h4>
                    <p class="text-sm text-gray-700 mb-4 text-center">
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

        @if(auth()->check() && (auth()->user()->role === 'admin' || auth()->user()->is_admin))
            <!-- Tarjeta 4: Reportes (solo admin) -->
            <div class="mt-8">
                <div class="bg-white bg-opacity-70 p-6 rounded-lg shadow-xl hover:shadow-2xl transition transform hover:scale-105"
                     style="backdrop-filter: blur(10px);">
                    <div class="flex items-center">
                        <div class="text-5xl mr-4">📈</div>
                        <div class="flex-1">
                            <h4 class="text-xl font-bold text-gray-900 mb-1">
                                Reportes e Indicadores
                            </h4>
                            <p class="text-sm text-gray-700 mb-3">
                                Analiza todas las solicitudes por estado, fechas y tipo. Exporta la información a Pdf/Excel.
                            </p>
                            <a href="{{ route('admin.reportes') }}"
                               class="inline-block px-5 py-2 bg-indigo-600 hover:bg-indigo-700 text-white font-semibold rounded-lg transition">
                                Ir a reportes →
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        @endif

    </div>
</div>
@endsection
