<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>Gestor de Solicitudes</title>
<link rel="icon" type="image/x-icon" href="{{ asset('images/compra.png') }}">
@vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body style="background-image: url('{{ asset('images/inicio6.png') }}'); 
             background-size: cover; 
             background-position: center; 
             background-attachment: fixed; 
             background-repeat: no-repeat;
             min-height: 100vh;">
    
    <div class="min-h-screen flex flex-col items-center justify-center px-4 pb-16">
        <!-- Header con logo -->
        <div class="text-center mb-8 bg-white bg-opacity-70 p-8 rounded-2xl shadow-2xl backdrop-blur-sm">
            <img src="{{ asset('images/compras.png') }}" alt="Logo" class="h-20 w-auto mx-auto mb-4">
            <h1 class="text-5xl font-bold text-gray-800 mb-2">Gestor de Solicitudes Departamento Compras</h1>
            <img src="{{ asset('images/colbeef.png') }}" alt="COLBEEF" class="h-12 w-auto mx-auto mt-2">
        </div>

        <!-- Botones de acceso -->
        <div class="flex gap-4 mb-12">
            @if (Route::has('login'))
                @auth
                    <a href="{{ route('solicitudes.index') }}" 
                       class="px-8 py-3 bg-green-600 text-white rounded-lg hover:bg-green-700 transition text-lg font-semibold shadow-lg">
                        Ir a mis solicitudes
                    </a>
                @else
                    <a href="{{ route('login') }}" 
                       class="px-8 py-3 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition text-lg font-semibold shadow-lg">
                        Iniciar sesión
                    </a>
                    <a href="{{ route('register') }}" 
                       class="px-8 py-3 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 transition text-lg font-semibold shadow-lg">
                        Registrarse
                    </a>
                @endauth
            @endif
        </div>

        <!-- Descripción o características -->
        <div class="max-w-4xl grid md:grid-cols-3 gap-6">
            <div class="bg-white bg-opacity-70 p-6 rounded-lg shadow-xl text-center backdrop-blur-sm hover:shadow-2xl transition transform hover:scale-105">
                <div class="text-3xl mb-3">📋</div>
                <h3 class="font-bold text-lg mb-2">Gestión Simple</h3>
                <p class="text-gray-600">Administra tus solicitudes de forma fácil y rápida</p>
            </div>
            <div class="bg-white bg-opacity-70 p-6 rounded-lg shadow-xl text-center backdrop-blur-sm hover:shadow-2xl transition transform hover:scale-105">
                <div class="text-3xl mb-3">🔒</div>
                <h3 class="font-bold text-lg mb-2">Seguro</h3>
                <p class="text-gray-600">Tus datos protegidos con autenticación segura</p>
            </div>
            <div class="bg-white bg-opacity-70 p-6 rounded-lg shadow-xl text-center backdrop-blur-sm hover:shadow-2xl transition transform hover:scale-105">
                <div class="text-3xl mb-3">📊</div>
                <h3 class="font-bold text-lg mb-2">Control Total</h3>
                <p class="text-gray-600">Seguimiento completo de todas tus solicitudes</p>
            </div>
        </div>
    </div>

    <!-- FOOTER - CRÉDITOS DEL DESARROLLADOR -->
    <footer class="fixed bottom-0 left-0 right-0 w-full bg-black bg-opacity-60 backdrop-blur-sm py-3">
        <div class="text-center">
            <p class="text-white text-sm font-medium">
                © 2025 Sistema de Solicitudes | Desarrollado por <span class="font-bold text-blue-400">Juan Pablo Carreño Mendoza</span>
            </p>
        </div>
    </footer>
    
</body>
</html>
