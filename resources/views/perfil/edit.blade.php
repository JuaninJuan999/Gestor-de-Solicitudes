@extends('layouts.app')

@section('content')
<div class="py-12">
    <div class="max-w-4xl mx-auto sm:px-6 lg:px-8 space-y-6">
        
        {{-- Mensaje de éxito --}}
        @if(session('success'))
            <div class="bg-green-100 border-l-4 border-green-500 text-green-700 p-4 mb-4" role="alert">
                <p class="font-bold">¡Éxito!</p>
                <p>{{ session('success') }}</p>
            </div>
        @endif

        <!-- Sección 1: Información del Perfil -->
        <div class="p-4 sm:p-8 bg-white shadow sm:rounded-lg">
            <div class="max-w-xl">
                <header>
                    <h2 class="text-lg font-medium text-gray-900">Información del Perfil</h2>
                    <p class="mt-1 text-sm text-gray-600">Actualiza tu nombre de usuario.</p>
                </header>

                <form method="post" action="{{ route('perfil.update') }}" class="mt-6 space-y-6">
                    @csrf
                    @method('put')

                    <div>
                        <label for="name" class="block font-medium text-sm text-gray-700">Nombre</label>
                        <input id="name" name="name" type="text" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500" value="{{ old('name', $user->name) }}" required autofocus autocomplete="name">
                        @error('name') <span class="text-red-600 text-sm">{{ $message }}</span> @enderror
                    </div>

                    {{-- Campo Email (Solo lectura para evitar conflictos) --}}
                    <div>
                        <label for="email" class="block font-medium text-sm text-gray-700">Correo Electrónico</label>
                        <input id="email" type="email" class="mt-1 block w-full bg-gray-100 border-gray-300 rounded-md shadow-sm text-gray-500 cursor-not-allowed" value="{{ $user->email }}" disabled>
                        <p class="text-xs text-gray-500 mt-1">El correo no se puede cambiar.</p>
                    </div>

                    <div class="flex items-center gap-4">
                        <button type="submit" class="px-4 py-2 bg-gray-800 text-white rounded-md font-semibold text-xs uppercase tracking-widest hover:bg-gray-700 transition">
                            Guardar Cambios
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Sección 2: Cambiar Contraseña -->
        <div class="p-4 sm:p-8 bg-white shadow sm:rounded-lg">
            <div class="max-w-xl">
                <header>
                    <h2 class="text-lg font-medium text-gray-900">Cambiar Contraseña</h2>
                    <p class="mt-1 text-sm text-gray-600">Asegúrate de usar una contraseña larga y segura.</p>
                </header>

                <form method="post" action="{{ route('perfil.password') }}" class="mt-6 space-y-6">
                    @csrf
                    @method('put')

                    <div>
                        <label for="current_password" class="block font-medium text-sm text-gray-700">Contraseña Actual</label>
                        <input id="current_password" name="current_password" type="password" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500" autocomplete="current-password">
                        @error('current_password') <span class="text-red-600 text-sm">{{ $message }}</span> @enderror
                    </div>

                    <div>
                        <label for="password" class="block font-medium text-sm text-gray-700">Nueva Contraseña</label>
                        <input id="password" name="password" type="password" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500" autocomplete="new-password">
                        @error('password') <span class="text-red-600 text-sm">{{ $message }}</span> @enderror
                    </div>

                    <div>
                        <label for="password_confirmation" class="block font-medium text-sm text-gray-700">Confirmar Contraseña</label>
                        <input id="password_confirmation" name="password_confirmation" type="password" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500" autocomplete="new-password">
                    </div>

                    <div class="flex items-center gap-4">
                        <button type="submit" class="px-4 py-2 bg-red-600 text-white rounded-md font-semibold text-xs uppercase tracking-widest hover:bg-red-500 transition">
                            Actualizar Contraseña
                        </button>
                    </div>
                </form>
            </div>
        </div>

    </div>
</div>
@endsection
