<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <title>{{ config('app.name') }}</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="icon" type="image/x-icon" href="{{ asset('images/compra.png') }}">
    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">
    <!-- Scripts (Asegúrate de que Alpine.js esté cargado via vite o CDN si no usas Breeze) -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="flex flex-col min-h-screen bg-gray-50">
    
    <!-- HEADER -->
    <header class="bg-white bg-opacity-80 backdrop-blur-md border-b border-white/20 shadow-md sticky top-0 z-50">
        <div class="max-w-7xl mx-auto px-6 py-3">
            <div class="flex items-center justify-between gap-6">
                
                <!-- Izquierda: Logo y Título -->
                <div class="flex items-center space-x-3">
                    <img src="{{ asset('images/compras.png') }}" alt="Logo" class="h-9 w-auto">
                    <h2 class="text-xl font-bold text-gray-800 tracking-tight hidden sm:block">Gestor de Solicitudes</h2>
                </div>

                <!-- Centro: Carrusel -->
                <div class="flex-1 flex justify-center">
                    <div id="logosCarousel" class="h-10 flex items-center justify-center">
                        <img id="logoSlide" 
                             src="{{ asset('images/logos/logo1.png') }}" 
                             alt="Logo" 
                             class="h-9 w-auto object-contain transition-opacity duration-500 opacity-100">
                    </div>
                </div>

                <!-- Derecha: Menú Unificado + Logout -->
                <div class="flex items-center space-x-4">
                    @auth
                        <!-- MENÚ DESPLEGABLE UNIFICADO -->
                        <div x-data="{ open: false }" class="relative">
                            <!-- Botón Trigger -->
                            <button @click="open = !open" @click.away="open = false" 
                                    class="px-5 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-all shadow-md flex items-center gap-2 font-bold">
                                <span class="text-lg">☰</span> 
                                <span>MENÚ</span>
                                <svg class="w-4 h-4 ml-1 transition-transform" :class="{'rotate-180': open}" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                            </button>

                            <!-- Contenido del Dropdown -->
                            <div x-show="open" 
                                 x-transition:enter="transition ease-out duration-200"
                                 x-transition:enter-start="opacity-0 scale-95"
                                 x-transition:enter-end="opacity-100 scale-100"
                                 x-transition:leave="transition ease-in duration-75"
                                 x-transition:leave-start="opacity-100 scale-100"
                                 x-transition:leave-end="opacity-0 scale-95"
                                 class="absolute right-0 mt-2 w-56 bg-white rounded-xl shadow-2xl py-2 border border-gray-100 z-50" 
                                 style="display: none;">
                                
                                <!-- Opciones del Menú -->
                                <div class="px-4 py-2 border-b border-gray-100">
                                    <p class="text-sm text-gray-500">Hola,</p>
                                    <p class="font-bold text-gray-800 truncate">{{ Auth::user()->name }}</p>
                                </div>

                                <a href="{{ route('dashboard') }}" class="block px-4 py-3 text-gray-700 hover:bg-blue-50 hover:text-blue-700 transition-colors flex items-center gap-3">
                                    <span class="text-xl">📊</span> Dashboard
                                </a>

                                @if(auth()->user()->role === 'admin' || auth()->user()->is_admin)
                                    <a href="{{ route('admin.users.index') }}" class="block px-4 py-3 text-gray-700 hover:bg-indigo-50 hover:text-indigo-700 transition-colors flex items-center gap-3">
                                        <span class="text-xl">👥</span> Usuarios
                                    </a>
                                @endif

                                <a href="{{ route('perfil.edit') }}" class="block px-4 py-3 text-gray-700 hover:bg-green-50 hover:text-green-700 transition-colors flex items-center gap-3">
                                    <span class="text-xl">👤</span> Mi Perfil
                                </a>
                            </div>
                        </div>
                    @endauth

                    @auth
                        <!-- Botón Cerrar Sesión (Mantenemos separado para acceso rápido) -->
                        <form action="{{ route('logout') }}" method="POST">
                            @csrf
                            <button type="submit" class="p-2 text-red-500 hover:bg-red-50 rounded-lg transition-colors" title="Cerrar sesión">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-7 w-7" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />
                                </svg>
                            </button>
                        </form>
                    @else
                        <a href="{{ route('login') }}" class="text-blue-600 font-bold hover:underline">Iniciar sesión</a>
                    @endauth
                </div>
            </div>
        </div>
    </header>
    
    <!-- CONTENIDO -->
    <main class="flex-grow">
        @if(request()->routeIs('solicitudes.*'))
            <div class="min-h-[calc(100vh-112px)] bg-cover bg-center bg-no-repeat" style="background-image: url('{{ asset('images/create-solicitud.jpg') }}');">
                <div class="py-12 bg-black bg-opacity-40 min-h-full">
                    @yield('content')
                </div>
            </div>
        @else
            @yield('content')
        @endif
    </main>

    <!-- FOOTER -->
    <footer class="bg-gray-800 text-white py-4 mt-auto">
        <div class="max-w-7xl mx-auto px-6 text-center">
            <p class="text-sm font-medium opacity-80">
                © 2025 Sistema de Solicitudes | <span class="text-blue-400">Juan Pablo Carreño Mendoza</span>
            </p>
        </div>
    </footer>

    <!-- Script Carrusel -->
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const logos = [
                "{{ asset('images/logos/logo1.png') }}",
                "{{ asset('images/logos/logo2.png') }}",
                "{{ asset('images/logos/logo3.png') }}",
                "{{ asset('images/logos/logo4.png') }}",
            ];
            const img = document.getElementById('logoSlide');
            let index = 0;
            if (img && logos.length) {
                setInterval(() => {
                    img.classList.add('opacity-0');
                    setTimeout(() => {
                        index = (index + 1) % logos.length;
                        img.src = logos[index];
                        img.classList.remove('opacity-0');
                    }, 500);
                }, 3000);
            }
        });
    </script>
</body>
</html>
