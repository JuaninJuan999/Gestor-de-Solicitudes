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
    <!-- HEADER -->
    <header class="bg-white border-b shadow-sm">
        <div class="max-w-7xl mx-auto px-6 py-3">
            <div class="flex items-center justify-between gap-6">
                <!-- Izquierda: Logo y título -->
                <div class="flex items-center space-x-3">
                    <img src="{{ asset('images/compras.png') }}" alt="Logo" class="h-8 w-auto">
                    <h2 class="text-lg font-bold text-gray-800">Gestor de Solicitudes</h2>
                </div>

                <!-- Centro: Carrusel de logos (una sola línea) -->
                <div class="flex-1 flex justify-center">
                    <div id="logosCarousel" class="h-10 flex items-center justify-center">
                        <img id="logoSlide" 
                             src="{{ asset('images/logos/logo1.png') }}" 
                             alt="Logo institucional" 
                             class="h-10 w-auto object-contain transition-opacity duration-500 opacity-100">
                    </div>
                </div>

                <!-- Derecha: DASHBOARD + Cerrar sesión en línea -->
                <div class="flex items-center space-x-4">
                    @auth
                        <a href="{{ route('dashboard') }}" 
                           class="px-6 py-2 bg-gray-100 border-2 border-gray-300 rounded-lg hover:bg-gray-200 hover:border-gray-400 transition">
                            <span class="text-xl font-bold text-gray-700">📊 DASHBOARD</span>
                        </a>
                    @endauth

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
    
    <!-- CONTENIDO -->
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

    <!-- FOOTER -->
    <footer class="bg-gray-800 text-white py-4 mt-auto">
        <div class="max-w-7xl mx-auto px-6 text-center">
            <p class="text-sm font-medium">
                © 2025 Sistema de Solicitudes | Desarrollado por <span class="font-bold text-blue-400">Juan Pablo Carreño Mendoza</span>
            </p>
        </div>
    </footer>

    <!-- Script carrusel de logos -->
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const logos = [
                "{{ asset('images/logos/logo1.png') }}",
                "{{ asset('images/logos/logo2.png') }}",
                "{{ asset('images/logos/logo3.png') }}",
                "{{ asset('images/logos/logo4.png') }}",
                "{{ asset('images/logos/logo5.png') }}",
            ];

            const img = document.getElementById('logoSlide');
            let index = 0;

            if (!img || logos.length === 0) return;

            setInterval(() => {
                img.classList.add('opacity-0');
                setTimeout(() => {
                    index = (index + 1) % logos.length;
                    img.src = logos[index];
                    img.classList.remove('opacity-0');
                }, 500); // tiempo de fade-out
            }, 3000); // cambia cada 3 segundos
        });
    </script>
</body>
</html>
