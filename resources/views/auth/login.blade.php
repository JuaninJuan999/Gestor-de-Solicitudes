<x-guest-layout>
    <!-- Session Status -->
    <x-auth-session-status class="mb-4" :status="session('status')" />

    <!-- Etiqueta animada sobre el formulario -->
    <div class="relative mb-6">
        <div class="flex justify-center">
            <span class="animated-badge">
                Sistema de Solicitudes
            </span>
        </div>
    </div>

    <form method="POST" action="{{ route('login') }}" class="space-y-4">
        @csrf

        <!-- Email Address -->
        <div>
            <x-text-input id="email" 
                          class="block w-full px-4 py-3 rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500" 
                          type="email" 
                          name="email" 
                          :value="old('email')" 
                          required 
                          autofocus 
                          autocomplete="username" 
                          placeholder="Usuario" />
            <x-input-error :messages="$errors->get('email')" class="mt-2" />
        </div>

        <!-- Password -->
        <div>
            <x-text-input id="password" 
                          class="block w-full px-4 py-3 rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500"
                          type="password"
                          name="password"
                          required 
                          autocomplete="current-password" 
                          placeholder="Contraseña" />
            <x-input-error :messages="$errors->get('password')" class="mt-2" />
        </div>

        <!-- Login Button -->
        <div>
            <button type="submit" 
                    class="w-full bg-blue-500 hover:bg-blue-600 text-white font-semibold py-3 rounded-lg transition duration-200 shadow-md">
                Iniciar Sesión
            </button>
        </div>

        <!-- Forgot Password Link -->
        @if (Route::has('password.request'))
            <div class="text-center">
                <a class="text-sm text-gray-600 hover:text-gray-900 underline" href="{{ route('password.request') }}">
                    ¿Olvidaste tu contraseña?
                </a>
            </div>
        @endif
    </form>

    <!-- Estilos CSS para la etiqueta animada -->
    <style>
        .animated-badge {
            display: inline-block;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 8px 20px;
            border-radius: 25px;
            font-size: 14px;
            font-weight: 600;
            opacity: 0.7; /* 70% transparencia */
            box-shadow: 0 4px 15px rgba(102, 126, 234, 0.5);
            animation: badgePulse 2s ease-in-out infinite;
            text-align: center;
        }

        /* Animación de pulsación */
        @keyframes badgePulse {
            0% {
                transform: scale(1);
                box-shadow: 0 4px 15px rgba(102, 126, 234, 0.5);
            }
            50% {
                transform: scale(1.08);
                box-shadow: 0 6px 20px rgba(102, 126, 234, 0.7);
            }
            100% {
                transform: scale(1);
                box-shadow: 0 4px 15px rgba(102, 126, 234, 0.5);
            }
        }

        /* Efecto hover adicional */
        .animated-badge:hover {
            opacity: 0.9;
            cursor: default;
        }
    </style>
</x-guest-layout>

