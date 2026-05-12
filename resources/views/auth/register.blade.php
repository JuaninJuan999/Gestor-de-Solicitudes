<x-guest-layout>
    <form method="POST" action="{{ route('register') }}" class="register-form-es space-y-4">
        @csrf

        <!-- Primer nombre -->
        <div>
            <x-input-label for="primer_nombre" value="Primer nombre" class="register-label-es font-semibold" />
            <x-text-input id="primer_nombre"
                class="register-field-dark block mt-1 w-full shadow-sm js-register-name-field"
                type="text" name="primer_nombre" :value="old('primer_nombre')" required autofocus autocomplete="given-name"
                placeholder="Coloque su primer nombre" />
            <x-input-error :messages="$errors->get('primer_nombre')" class="mt-2 text-red-700 font-medium" />
        </div>

        <!-- Primer apellido -->
        <div>
            <x-input-label for="primer_apellido" value="Primer apellido" class="register-label-es font-semibold" />
            <x-text-input id="primer_apellido"
                class="register-field-dark block mt-1 w-full shadow-sm js-register-name-field"
                type="text" name="primer_apellido" :value="old('primer_apellido')" required autocomplete="family-name"
                placeholder="Coloque su primer apellido" />
            <x-input-error :messages="$errors->get('primer_apellido')" class="mt-2 text-red-700 font-medium" />
        </div>

        <!-- Usuario generado (vista previa) -->
        <div>
            <span class="block text-sm register-label-es font-semibold">Usuario (se asigna automáticamente)</span>
            <div id="register-username-preview"
                class="register-field-dark register-username-preview mt-1 flex min-h-[2.75rem] w-full items-center rounded-md border px-4 text-sm shadow-sm"
                aria-live="polite">—</div>
        </div>

        <!-- Correo -->
        <div>
            <x-input-label for="email" value="Correo electrónico" class="register-label-es font-semibold" />
            <x-text-input id="email"
                class="register-field-dark block mt-1 w-full shadow-sm"
                type="email" name="email" :value="old('email')" required autocomplete="email"
                placeholder="correo@ejemplo.com" />
            <x-input-error :messages="$errors->get('email')" class="mt-2 text-red-700 font-medium" />
        </div>

        <!-- Área -->
        <div>
            <x-input-label for="area" value="Área" class="register-label-es font-semibold" />
            <select id="area" name="area" required autocomplete="off"
                class="register-field-dark register-select-native mt-1 block w-full rounded-md shadow-sm px-4 py-3 border">
                <option value="" @selected(old('area') === null || old('area') === '')>Seleccione su área…</option>
                @foreach ($areas as $areaOption)
                    <option value="{{ $areaOption }}" @selected(old('area') === $areaOption)>{{ $areaOption }}</option>
                @endforeach
            </select>
            <x-input-error :messages="$errors->get('area')" class="mt-2 text-red-700 font-medium" />
        </div>

        <!-- Contraseña -->
        <div>
            <x-input-label for="password" value="Contraseña" class="register-label-es font-semibold" />
            <div class="register-password-wrap relative mt-1">
                <x-text-input id="password"
                    class="register-field-dark register-password-input block w-full pr-11 shadow-sm"
                    type="password" name="password" required autocomplete="new-password" />
                <button type="button" class="register-password-toggle" data-toggle-password="password" aria-pressed="false" aria-label="Mostrar contraseña">
                    <svg class="register-password-toggle-icon show" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M2.036 12.322a1.012 1.012 0 010-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178z" />
                        <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                    </svg>
                    <svg class="register-password-toggle-icon hide hidden" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M3.98 8.223A10.477 10.477 0 001.934 12C3.226 16.338 7.244 19.5 12 19.5c.993 0 1.953-.138 2.863-.395M6.228 6.228A10.45 10.45 0 0112 4.5c4.756 0 8.773 3.162 10.065 7.498a10.523 10.523 0 01-4.293 5.774M6.228 6.228L3 3m3.228 3.228l3.65 3.65m7.894 7.894L21 21m-3.228-3.228l-3.65-3.65m0 0a3 3 0 10-4.243-4.243m4.242 4.242L9.88 9.88" />
                    </svg>
                </button>
            </div>
            <x-input-error :messages="$errors->get('password')" class="mt-2 text-red-700 font-medium" />
        </div>

        <!-- Confirmar contraseña -->
        <div>
            <x-input-label for="password_confirmation" value="Confirmar contraseña" class="register-label-es font-semibold" />
            <div class="register-password-wrap relative mt-1">
                <x-text-input id="password_confirmation"
                    class="register-field-dark register-password-input block w-full pr-11 shadow-sm"
                    type="password" name="password_confirmation" required autocomplete="new-password" />
                <button type="button" class="register-password-toggle" data-toggle-password="password_confirmation" aria-pressed="false" aria-label="Mostrar contraseña">
                    <svg class="register-password-toggle-icon show" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M2.036 12.322a1.012 1.012 0 010-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178z" />
                        <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                    </svg>
                    <svg class="register-password-toggle-icon hide hidden" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M3.98 8.223A10.477 10.477 0 001.934 12C3.226 16.338 7.244 19.5 12 19.5c.993 0 1.953-.138 2.863-.395M6.228 6.228A10.45 10.45 0 0112 4.5c4.756 0 8.773 3.162 10.065 7.498a10.523 10.523 0 01-4.293 5.774M6.228 6.228L3 3m3.228 3.228l3.65 3.65m7.894 7.894L21 21m-3.228-3.228l-3.65-3.65m0 0a3 3 0 10-4.243-4.243m4.242 4.242L9.88 9.88" />
                    </svg>
                </button>
            </div>
            <x-input-error :messages="$errors->get('password_confirmation')" class="mt-2 text-red-700 font-medium" />
        </div>

        <div class="flex flex-col-reverse sm:flex-row sm:items-center sm:justify-end gap-4 pt-2">
            <a class="text-sm text-black font-semibold underline decoration-2 underline-offset-2 hover:text-gray-800 text-center sm:text-left"
                href="{{ route('login') }}">
                ¿Ya estás registrado?
            </a>

            <button type="submit"
                class="inline-flex justify-center items-center px-5 py-3 bg-white border-2 border-black rounded-lg font-bold text-sm text-black uppercase tracking-wide shadow-md hover:bg-gray-100 focus:outline-none focus:ring-2 focus:ring-black focus:ring-offset-2 transition w-full sm:w-auto">
                Registrarse
            </button>
        </div>
    </form>

    @push('styles')
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/tom-select@2.3.1/dist/css/tom-select.default.min.css">
    <style>
        /* Etiquetas siempre negro (vence modo oscuro del layout) */
        .register-form-es label.register-label-es,
        .register-form-es .register-label-es {
            color: #000000 !important;
        }

        /* Mismo estilo oscuro que los demás inputs (gray-900 / texto claro) */
        .register-form-es .register-field-dark {
            background-color: #111827 !important;
            color: #e5e7eb !important;
            border-color: #4b5563 !important;
        }
        .register-form-es .register-field-dark::placeholder {
            color: #9ca3af !important;
        }
        .register-form-es select.register-select-native {
            background-color: #111827 !important;
            color: #e5e7eb !important;
            border-color: #4b5563 !important;
        }
        .register-form-es select.register-select-native option {
            background-color: #111827;
            color: #e5e7eb;
        }

        .register-form-es .register-password-wrap .register-password-input {
            padding-right: 2.75rem !important;
        }
        .register-form-es .register-password-toggle {
            position: absolute;
            right: 0.35rem;
            top: 50%;
            transform: translateY(-50%);
            padding: 0.35rem;
            border-radius: 0.375rem;
            color: #9ca3af;
            background: transparent;
            border: none;
            cursor: pointer;
            line-height: 0;
        }
        .register-form-es .register-password-toggle:hover {
            color: #e5e7eb;
            background: rgba(255, 255, 255, 0.08);
        }
        .register-form-es .register-password-toggle:focus-visible {
            outline: 2px solid #6366f1;
            outline-offset: 2px;
        }
        .register-form-es .register-password-toggle-icon {
            width: 1.35rem;
            height: 1.35rem;
        }

        .register-form-es .register-area-ts.ts-wrapper { width: 100%; }
        .register-form-es .register-area-ts .ts-control {
            min-height: 2.75rem;
            padding: 0.5rem 0.75rem;
            border-radius: 0.375rem;
            background: #111827 !important;
            border: 1px solid #4b5563 !important;
            color: #e5e7eb !important;
        }
        .register-form-es .register-area-ts .ts-control input {
            color: #e5e7eb !important;
        }
        .register-form-es .register-area-ts .ts-control input::placeholder {
            color: #9ca3af !important;
            opacity: 1;
        }
        .register-form-es .register-area-ts .ts-control .item {
            color: #e5e7eb !important;
        }
        .register-form-es .register-area-ts.focus .ts-control {
            border-color: #6366f1 !important;
            box-shadow: 0 0 0 2px rgba(99, 102, 241, 0.35);
        }
        .register-form-es .register-area-ts .ts-dropdown {
            z-index: 100;
            border-radius: 0.375rem;
            max-height: 14rem;
            border: 1px solid #4b5563;
            background: #111827;
        }
        .register-form-es .register-area-ts .ts-dropdown .option {
            color: #e5e7eb;
        }
        .register-form-es .register-area-ts .ts-dropdown .option.active,
        .register-form-es .register-area-ts .ts-dropdown .option:hover {
            background: #374151;
            color: #fff;
        }
    </style>
    @endpush

    @push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/tom-select@2.3.1/dist/js/tom-select.complete.min.js"></script>
    <script>
        function registerNormalizeUsernameSegment(value) {
            if (!value || !String(value).trim()) {
                return null;
            }
            var s = String(value).trim().toLowerCase();
            s = s.replace(/[^a-z0-9ñ]+/gu, '');
            return s || 'x';
        }
        function registerUpdateUsernamePreview() {
            var fn = document.getElementById('primer_nombre');
            var ln = document.getElementById('primer_apellido');
            var el = document.getElementById('register-username-preview');
            if (!fn || !ln || !el) return;
            var n = registerNormalizeUsernameSegment(fn.value);
            var a = registerNormalizeUsernameSegment(ln.value);
            if (n === null || a === null) {
                el.textContent = '—';
                return;
            }
            el.textContent = n + '.' + a;
        }
        function registerBindPasswordToggles() {
            document.querySelectorAll('[data-toggle-password]').forEach(function (btn) {
                btn.addEventListener('click', function () {
                    var id = btn.getAttribute('data-toggle-password');
                    var input = document.getElementById(id);
                    if (!input) return;
                    var show = input.getAttribute('type') === 'password';
                    input.setAttribute('type', show ? 'text' : 'password');
                    btn.setAttribute('aria-pressed', show ? 'true' : 'false');
                    btn.setAttribute('aria-label', show ? 'Ocultar contraseña' : 'Mostrar contraseña');
                    var showIcon = btn.querySelector('.register-password-toggle-icon.show');
                    var hideIcon = btn.querySelector('.register-password-toggle-icon.hide');
                    if (showIcon && hideIcon) {
                        showIcon.classList.toggle('hidden', show);
                        hideIcon.classList.toggle('hidden', !show);
                    }
                });
            });
        }
        document.addEventListener('DOMContentLoaded', function () {
            registerBindPasswordToggles();
            document.querySelectorAll('.js-register-name-field').forEach(function (el) {
                el.addEventListener('input', registerUpdateUsernamePreview);
            });
            registerUpdateUsernamePreview();

            var areaEl = document.getElementById('area');
            if (areaEl && typeof TomSelect !== 'undefined') {
                new TomSelect(areaEl, {
                    allowEmptyOption: true,
                    create: false,
                    maxOptions: null,
                    sortField: { field: 'text', direction: 'asc' },
                    placeholder: 'Escriba para filtrar o elija su área…',
                    openOnFocus: true,
                    onInitialize: function () {
                        this.wrapper.classList.add('register-area-ts');
                    },
                });
            }
        });
    </script>
    @endpush
</x-guest-layout>
