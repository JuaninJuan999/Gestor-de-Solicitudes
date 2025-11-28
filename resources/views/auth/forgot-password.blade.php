<x-guest-layout>
    <!-- Texto descriptivo - AHORA MÁS OSCURO Y EN ESPAÑOL -->
    <div class="mb-4 text-sm text-gray-900 dark:text-gray-900 font-medium">
        ¿Olvidaste tu contraseña? No hay problema. Solo indícanos tu dirección de correo electrónico y te enviaremos un enlace para restablecer tu contraseña que te permitirá elegir una nueva.
    </div>

    <!-- Session Status -->
    <x-auth-session-status class="mb-4" :status="session('status')" />

    <form method="POST" action="{{ route('password.email') }}" class="space-y-4">
        @csrf

        <!-- Email Address - ESPAÑOL Y TEXTO MÁS OSCURO -->
        <div>
            <x-input-label for="email" value="Correo Electrónico" class="text-gray-900 font-semibold" />
            <x-text-input id="email" 
                          class="block mt-1 w-full px-4 py-3 rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500 text-gray-900" 
                          type="email" 
                          name="email" 
                          :value="old('email')" 
                          required 
                          autofocus 
                          placeholder="correo@ejemplo.com" />
            <x-input-error :messages="$errors->get('email')" class="mt-2" />
        </div>

        <!-- Botón - ESPAÑOL Y MÁS VISIBLE -->
        <div class="flex items-center justify-end mt-4">
            <x-primary-button class="bg-blue-500 hover:bg-blue-600 text-white font-semibold py-3 px-6 rounded-lg">
                Enviar Enlace de Recuperación
            </x-primary-button>
        </div>
    </form>
</x-guest-layout>
