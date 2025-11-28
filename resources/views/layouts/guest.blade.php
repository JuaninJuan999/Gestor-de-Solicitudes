<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
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

</head>
<body class="flex flex-col items-center justify-center min-h-screen">
    
    <div class="w-full max-w-md px-4 flex-grow flex flex-col justify-center">
        <!-- Logo/Avatar circular -->
        <div class="flex justify-center mb-6">
            <div class="bg-white rounded-full p-3 shadow-lg">
                <img src="{{ asset('images/compras.png') }}" alt="Logo" class="h-12 w-12">
            </div>
        </div>

        <!-- Título Sign in - ELIMINADO -->
        {{-- <h2 class="text-center text-white text-2xl font-semibold mb-6">Sign in</h2> --}}

        <!-- Tarjeta del formulario - MÁS TRANSPARENTE -->
        <div class="bg-white bg-opacity-50 backdrop-blur-md rounded-2xl shadow-2xl p-8">
            {{ $slot }}
        </div>
    </div>

    <!-- FOOTER - CRÉDITOS DEL DESARROLLADOR -->
    <footer class="w-full bg-black bg-opacity-60 backdrop-blur-sm py-3 mt-auto">
        <div class="text-center">
            <p class="text-white text-sm font-medium">
                © 2025 Sistema de Solicitudes | Desarrollado por <span class="font-bold text-blue-400">Juan Pablo Carreño Mendoza</span>
            </p>
        </div>
    </footer>
</body>
</html>


