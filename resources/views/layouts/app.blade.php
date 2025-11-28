<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <title>{{ config('app.name') }}</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="icon" type="image/x-icon" href="{{ asset('images/compra.png') }}">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="flex flex-col min-h-screen">
    <header class="bg-white border-b shadow-sm">
        <div class="max-w-7xl mx-auto px-6 py-3">
            <div class="grid grid-cols-3 items-center">
                <!-- Columna izquierda: Logo y título -->
                <div class="flex items-center space-x-3">
                    <img src="{{ asset('images/compras.png') }}" alt="Logo" class="h-8 w-auto">
                    <h2 class="text-lg font-bold text-gray-800">Gestor de Solicitudes</h2>
                </div>

                <!-- Columna centro: Botón DASHBOARD -->
                <div class="flex justify-center">
                    @auth
                        <a href="{{ route('dashboard') }}" 
                           class="px-6 py-2 bg-gray-100 border-2 border-gray-300 rounded-lg hover:bg-gray-200 hover:border-gray-400 transition">
                            <span class="text-xl font-bold text-gray-700">📊 DASHBOARD</span>
                        </a>
                    @endauth
                </div>

                <!-- Columna derecha: Botón cerrar sesión -->
                <div class="flex justify-end">
                    @if (Route::has('login'))
                        @auth
                            <form action="{{ route('logout') }}" method="POST">
                                @csrf
                                <button type="submit" 
                                        class="px-5 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 transition font-semibold">
                                    🚪 Cerrar sesión
                                </button>
                            </form>
                        @else
                            <div class="flex items-center space-x-4">
                                <a href="{{ route('login') }}" class="text-blue-600 hover:underline font-medium">Iniciar sesión</a>
                                <a href="{{ route('register') }}" class="text-blue-600 hover:underline font-medium">Registrarse</a>
                            </div>
                        @endauth
                    @endif
                </div>
            </div>
        </div>
    </header>
    
    <main class="flex-grow">
        @if(request()->routeIs('solicitudes.*'))
            {{-- Fondo y overlay solo para vistas de solicitudes --}}
            <div class="min-h-[calc(100vh-112px)] bg-cover bg-center bg-no-repeat" style="background-image: url('{{ asset('images/create-solicitud.jpg') }}');">
                <div class="py-12 bg-black bg-opacity-40">
                    @yield('content')
                </div>
            </div>
        @else
            @yield('content')
        @endif
    </main>

    <!-- FOOTER - CRÉDITOS DEL DESARROLLADOR -->
    <footer class="bg-gray-800 text-white py-4 mt-auto">
        <div class="max-w-7xl mx-auto px-6 text-center">
            <p class="text-sm font-medium">
                © 2025 Sistema de Solicitudes | Desarrollado por <span class="font-bold text-blue-400">Juan Pablo Carreño Mendoza</span>
            </p>
        </div>
    </footer>
</body>
</html>
