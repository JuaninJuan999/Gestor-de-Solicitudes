<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ config('app.name') }}</title>
    <link rel="icon" type="image/x-icon" href="{{ asset('images/compra.png') }}">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        body {
            background-image: url('{{ asset("images/loginv2.jpg") }}');
            background-size: cover;
            background-position: center;
            background-repeat: no-repeat;
            min-height: 100vh;
            margin: 0;
        }
    </style>
</head>

<body class="flex flex-col items-center justify-center min-h-screen relative">
    
    <!-- === BOTÓN VOLVER AL INICIO (Global) === -->
<!-- === BOTÓN VOLVER (Light Glass 70%) === -->
<a href="{{ url('/') }}" 
   class="fixed top-6 left-6 z-50 flex items-center gap-2 px-5 py-2.5 
          bg-white/70 hover:bg-white/90 text-gray-800 
          rounded-full backdrop-blur-xl border border-white/40 
          shadow-[0_8px_32px_0_rgba(31,38,135,0.37)] 
          transition-all duration-300 group hover:scale-105"
   style="text-decoration: none;">
    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 transform group-hover:-translate-x-1 transition-transform text-gray-700" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
        <path stroke-linecap="round" stroke-linejoin="round" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
    </svg>
    <span class="font-bold tracking-wide text-sm">Volver al Inicio</span>
</a>

    <div class="w-full max-w-md px-4 flex-grow flex flex-col justify-center relative z-10">
        <!-- Logo/Avatar circular -->
        <div class="flex justify-center mb-6">
            <div class="bg-white rounded-full p-3 shadow-lg">
                <img src="{{ asset('images/compras.png') }}" alt="Logo" class="h-12 w-12">
            </div>
        </div>

        <!-- Tarjeta del formulario -->
        <div class="bg-white bg-opacity-50 backdrop-blur-md rounded-2xl shadow-2xl p-8">
            {{ $slot }}
        </div>
    </div>

    <!-- FOOTER - CRÉDITOS DEL DESARROLLADOR -->
    <footer class="w-full bg-black bg-opacity-60 backdrop-blur-sm py-3 mt-auto relative z-10">
        <div class="text-center">
            <p class="text-white text-sm font-medium">
                © 2025 Sistema de Solicitudes | Desarrollado por <span class="font-bold text-blue-400">Juan Pablo Carreño Mendoza</span>
            </p>
        </div>
    </footer>
</body>
</html>
