@extends('layouts.app')

@section('content')
<!-- Contenedor con fondo de imagen -->
<div style="background-image: url('/images/perfil.jpg'); 
            background-size: cover; 
            background-position: center; 
            background-attachment: fixed; 
            background-repeat: no-repeat;
            min-height: calc(100vh - 80px);
            padding-top: 3rem;
            padding-bottom: 3rem;">

    <div class="max-w-4xl mx-auto sm:px-6 lg:px-8 space-y-6">
        
        {{-- Mensaje de éxito --}}
        @if(session('success'))
            <div class="bg-green-100/90 border-l-4 border-green-500 text-green-700 p-4 mb-4 shadow-lg backdrop-blur-sm" role="alert">
                <p class="font-bold">¡Éxito!</p>
                <p>{{ session('success') }}</p>
            </div>
        @endif

        <!-- Encabezado del Perfil (Nuevo, para dar contexto) -->
        <div class="p-6 bg-white/60 shadow-xl sm:rounded-lg border-l-4 border-blue-600 backdrop-blur-md">
            <h2 class="font-bold text-2xl text-gray-800 leading-tight">
                👤 Mi Perfil
            </h2>
            <p class="text-gray-700 mt-1 font-medium">Gestiona tu información personal y seguridad.</p>
        </div>

        <!-- Sección 1: Información del Perfil -->
        <div class="p-4 sm:p-8 bg-white/60 shadow-xl sm:rounded-lg border border-white/50 backdrop-blur-md">
            <div class="max-w-xl">
                <header>
                    <h2 class="text-lg font-bold text-blue-900">Información Básica</h2>
                    <p class="mt-1 text-sm text-gray-700">Actualiza tu nombre de usuario.</p>
                </header>

                <form method="post" action="{{ route('perfil.update') }}" class="mt-6 space-y-6">
                    @csrf
                    @method('put')

                    <div>
                        <label for="name" class="block font-bold text-sm text-gray-800">Nombre</label>
                        <input id="name" name="name" type="text" 
                               class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 bg-white/80" 
                               value="{{ old('name', $user->name) }}" required autofocus autocomplete="name">
                        @error('name') <span class="text-red-600 text-sm font-bold">{{ $message }}</span> @enderror
                    </div>

                    {{-- Campo Email (Solo lectura para evitar conflictos) --}}
                    <div>
                        <label for="email" class="block font-bold text-sm text-gray-800">Correo Electrónico</label>
                        <input id="email" type="email" 
                               class="mt-1 block w-full bg-gray-200/80 border-gray-300 rounded-md shadow-sm text-gray-600 cursor-not-allowed" 
                               value="{{ $user->email }}" disabled>
                        <p class="text-xs text-gray-600 mt-1 font-semibold">El correo no se puede cambiar.</p>
                    </div>

                    <div class="flex items-center gap-4">
                        <button type="submit" class="px-6 py-2 bg-blue-600 text-white rounded-md font-bold text-xs uppercase tracking-widest hover:bg-blue-700 transition shadow-md">
                            Guardar Cambios
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Sección 2: Cambiar Contraseña -->
        <div class="p-4 sm:p-8 bg-white/60 shadow-xl sm:rounded-lg border border-white/50 backdrop-blur-md">
            <div class="max-w-xl">
                <header>
                    <h2 class="text-lg font-bold text-green-900">Cambiar Contraseña</h2>
                    <p class="mt-1 text-sm text-gray-700">Asegúrate de usar una contraseña larga y segura.</p>
                </header>

                <form method="post" action="{{ route('perfil.password') }}" class="mt-6 space-y-6">
                    @csrf
                    @method('put')

                    <div>
                        <label for="current_password" class="block font-bold text-sm text-gray-800">Contraseña Actual</label>
                        <input id="current_password" name="current_password" type="password" 
                               class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-green-500 focus:border-green-500 bg-white/80" 
                               autocomplete="current-password">
                        @error('current_password') <span class="text-red-600 text-sm font-bold">{{ $message }}</span> @enderror
                    </div>

                    <div>
                        <label for="password" class="block font-bold text-sm text-gray-800">Nueva Contraseña</label>
                        <input id="password" name="password" type="password" 
                               class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-green-500 focus:border-green-500 bg-white/80" 
                               autocomplete="new-password">
                        @error('password') <span class="text-red-600 text-sm font-bold">{{ $message }}</span> @enderror
                    </div>

                    <div>
                        <label for="password_confirmation" class="block font-bold text-sm text-gray-800">Confirmar Contraseña</label>
                        <input id="password_confirmation" name="password_confirmation" type="password" 
                               class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-green-500 focus:border-green-500 bg-white/80" 
                               autocomplete="new-password">
                    </div>

                    <div class="flex items-center gap-4">
                        <button type="submit" class="px-6 py-2 bg-green-600 text-white rounded-md font-bold text-xs uppercase tracking-widest hover:bg-green-700 transition shadow-md">
                            Actualizar Contraseña
                        </button>
                    </div>
                </form>
            </div>
        </div>

    </div>
</div>
@endsection
