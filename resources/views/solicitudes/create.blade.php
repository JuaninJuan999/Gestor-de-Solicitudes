@extends('layouts.app')

@section('content')

<!-- === FONDO FIJO === -->
<div class="fixed-bg-image"></div>
<div class="fixed-bg-overlay"></div>

<style>
    /* Fondo fijo */
    .fixed-bg-image {
        position: fixed; top: 0; left: 0; width: 100%; height: 100%;
        background-image: url('/images/create-solicitud.jpg');
        background-size: cover; background-position: center; background-repeat: no-repeat;
        z-index: -2;
    }
    .fixed-bg-overlay {
        position: fixed; top: 0; left: 0; width: 100%; height: 100%;
        background-color: rgba(0, 0, 0, 0.3);
        z-index: -1;
    }

    /* Botón Volver */
    .btn-back-dashboard {
        display: inline-flex; align-items: center; gap: 8px;
        background-color: rgba(255, 255, 255, 0.8);
        color: #2c3e50; padding: 10px 20px; border-radius: 8px;
        font-weight: 600; border: 1px solid rgba(255,255,255,0.5);
        backdrop-filter: blur(5px); transition: all 0.2s;
        text-decoration: none;
    }
    .btn-back-dashboard:hover {
        background-color: #fff; transform: translateY(-1px); color: #000;
        box-shadow: 0 4px 6px rgba(0,0,0,0.1);
    }
</style>

<!-- === CONTENIDO PRINCIPAL === -->
<div class="py-12">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        
        <!-- Botón Volver -->
        <div class="mb-6">
            <a href="{{ route('dashboard') }}" class="btn-back-dashboard shadow-sm">
                <i class="bi bi-chevron-left"></i> Volver al Dashboard
            </a>
        </div>

        <!-- Tarjeta principal -->
        <div class="bg-white bg-opacity-70 overflow-hidden shadow-2xl sm:rounded-lg"
             style="backdrop-filter: blur(10px);">
            <div class="p-6 border-b border-gray-200">
                
                <h2 class="text-2xl font-bold text-blue-600 mb-6">Registrar Solicitud de Compra Estándar</h2>

                <!-- Errores -->
                @if ($errors->any())
                    <div class="mb-4 bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative">
                        <strong class="font-bold">Correcciones necesarias:</strong>
                        <ul class="mt-2 list-disc list-inside">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <form action="{{ route('solicitudes.store') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <input type="hidden" name="tipo_solicitud" value="estandar">

                    <!-- Título -->
                    <div class="mb-6">
                        <label for="titulo" class="block text-sm font-medium text-gray-700 mb-2">Título de la solicitud:</label>
                        <input type="text" name="titulo" id="titulo" value="{{ old('titulo') }}" required
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                    </div>

                    <!-- Presupuestado -->
                    <div class="mb-6">
                        <label for="presupuestado" class="block text-sm font-medium text-gray-700 mb-2">¿Es presupuestado?</label>
                        <select name="presupuestado" id="presupuestado" class="w-full px-4 py-2 border border-gray-300 rounded-lg">
                            <option value="">Seleccione...</option>
                            <option value="SI" {{ old('presupuestado') == 'SI' ? 'selected' : '' }}>SI</option>
                            <option value="NO" {{ old('presupuestado') == 'NO' ? 'selected' : '' }}>NO</option>
                        </select>
                    </div>

                    <!-- Centro de Costos -->
                    <div class="mb-6">
                        <label for="centro_costos" class="block text-sm font-bold text-gray-700 mb-2">Centro de Costo y Área (Solicitante)</label>
                        <select name="centro_costos" id="centro_costos" required class="w-full px-4 py-2 border rounded-lg centro-costo-select">
                            <option value="">Seleccione centro de costo y área...</option>
                            @foreach($centrosCostos->groupBy('departamento') as $depto => $areas)
                                <optgroup label="{{ $depto }}">
                                    @foreach($areas as $area)
                                        <option value="{{ "{$area->cc}-{$area->sc}" }}" {{ old('centro_costos') == "{$area->cc}-{$area->sc}" ? 'selected' : '' }}>
                                            {{ $area->cc }}-{{ $area->sc }} | {{ $area->nombre_area }} ({{ $area->cuenta_contable }})
                                        </option>
                                    @endforeach
                                </optgroup>
                            @endforeach
                        </select>
                    </div>

                    <!-- === NUEVO CAMPO: SELECCIONAR SUPERVISOR === -->
                    <div class="mb-6">
                        <label for="supervisor_id" class="block text-sm font-bold text-gray-700 mb-2">
                            Supervisor Aprobador (Opcional)
                        </label>
                        <select name="supervisor_id" id="supervisor_id" class="w-full px-4 py-2 border rounded-lg">
                            <option value="">-- No requiere aprobación / Ninguno --</option>
                            @foreach($supervisores as $supervisor)
                                <option value="{{ $supervisor->id }}" {{ old('supervisor_id') == $supervisor->id ? 'selected' : '' }}>
                                    {{ $supervisor->name }} ({{ $supervisor->area ?? 'Sin área' }})
                                </option>
                            @endforeach
                        </select>
                        <p class="text-xs text-gray-500 mt-1">Seleccione un supervisor solo si esta solicitud requiere visto bueno antes de Compras.</p>
                    </div>

                    <!-- Tabla Items -->
                    <div class="mb-6">
                        <label class="block text-sm font-medium text-gray-700 mb-2">Detalle de productos/servicios:</label>
                        <div class="overflow-x-auto">
                            <table class="min-w-full border-collapse border border-gray-300">
                                <thead class="bg-green-600 text-white">
                                    <tr>
                                        <th class="border px-4 py-2">CÓDIGO SIIMED</th>
                                        <th class="border px-4 py-2">UNIDAD</th>
                                        <th class="border px-4 py-2">DESCRIPCION</th>
                                        <th class="border px-4 py-2">CANTIDAD</th>
                                        <th class="border px-4 py-2">CENTRO DE COSTO</th>
                                        <th class="border px-4 py-2">ACCIÓN</th>
                                    </tr>
                                </thead>
                                <tbody id="itemsTable">
                                    <tr>
                                        <td class="border px-4 py-2"><input type="text" name="items[0][codigo]" class="w-full px-2 py-1 border rounded"></td>
                                        <td class="border px-4 py-2"><input type="text" name="items[0][unidad]" class="w-full px-2 py-1 border rounded"></td>
                                        <td class="border px-4 py-2"><input type="text" name="items[0][descripcion]" class="w-full px-2 py-1 border rounded"></td>
                                        <td class="border px-4 py-2"><input type="number" name="items[0][cantidad]" class="w-full px-2 py-1 border rounded"></td>
                                        <td class="border px-4 py-2">
                                            <select name="items[0][centro_costos_item]" class="w-full px-2 py-1 border rounded centro-costo-select">
                                                <option value="">Seleccione...</option>
                                                @foreach($centrosCostos->groupBy('departamento') as $depto => $areas)
                                                    <optgroup label="{{ $depto }}">
                                                        @foreach($areas as $area)
                                                            <option value="{{ "{$area->cc}-{$area->sc}" }}">
                                                                {{ $area->cc }}-{{ $area->sc }} | {{ $area->nombre_area }}
                                                            </option>
                                                        @endforeach
                                                    </optgroup>
                                                @endforeach
                                            </select>
                                        </td>
                                        <td class="border px-4 py-2 text-center">
                                            <button type="button" onclick="eliminarFila(this)" class="text-red-600 font-bold text-xl">✕</button>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                        <button type="button" onclick="agregarFila()" class="mt-3 px-4 py-2 bg-blue-500 text-white rounded hover:bg-blue-600">+ Agregar fila</button>
                    </div>

                    <!-- Observaciones -->
                    <div class="mb-6">
                        <label for="descripcion" class="block text-sm font-medium text-gray-700 mb-2">Observaciones:</label>
                        <textarea name="descripcion" id="descripcion" rows="4" required class="w-full px-4 py-2 border border-gray-300 rounded-lg">{{ old('descripcion') }}</textarea>
                    </div>

                    <!-- Archivo -->
                    <div class="mb-6">
                        <label for="archivo" class="block text-sm font-medium text-gray-700 mb-2">Archivo adjunto:</label>
                        <input type="file" name="archivo" id="archivo" class="w-full px-4 py-2 border rounded-lg">
                    </div>

                    <!-- Botones -->
                    <div class="flex gap-4">
                        <button type="submit" class="px-6 py-3 bg-green-600 text-white rounded-lg hover:bg-green-700 shadow-md">Registrar</button>
                        <a href="{{ route('solicitudes.index') }}" class="px-6 py-3 bg-gray-300 text-gray-700 rounded-lg hover:bg-gray-400">Cancelar</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Scripts -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

<script>
    function initCentroCostoSelect(element) {
        $(element).select2({ width: '100%', placeholder: 'Buscar centro de costo...', allowClear: true });
    }
    document.addEventListener('DOMContentLoaded', function () {
        document.querySelectorAll('.centro-costo-select').forEach(function (el) { initCentroCostoSelect(el); });
    });
    let filaIndex = 1;
    function agregarFila() {
        const tbody = document.getElementById('itemsTable');
        const fila = document.createElement('tr');
        const optionsHtml = `
            <option value="">Seleccione...</option>
            @foreach($centrosCostos->groupBy('departamento') as $depto => $areas)
            <optgroup label="{{ $depto }}">
                @foreach($areas as $area)
                <option value="{{ "{$area->cc}-{$area->sc}" }}">{{ $area->cc }}-{{ $area->sc }} | {{ $area->nombre_area }}</option>
                @endforeach
            </optgroup>
            @endforeach
        `;
        
        fila.innerHTML = `
            <td class="border px-4 py-2"><input type="text" name="items[${filaIndex}][codigo]" class="w-full px-2 py-1 border rounded"></td>
            <td class="border px-4 py-2"><input type="text" name="items[${filaIndex}][unidad]" class="w-full px-2 py-1 border rounded"></td>
            <td class="border px-4 py-2"><input type="text" name="items[${filaIndex}][descripcion]" class="w-full px-2 py-1 border rounded"></td>
            <td class="border px-4 py-2"><input type="number" name="items[${filaIndex}][cantidad]" class="w-full px-2 py-1 border rounded"></td>
            <td class="border px-4 py-2"><select name="items[${filaIndex}][centro_costos_item]" class="w-full px-2 py-1 border rounded centro-costo-select">${optionsHtml}</select></td>
            <td class="border px-4 py-2 text-center"><button type="button" onclick="eliminarFila(this)" class="text-red-600 font-bold text-xl">✕</button></td>
        `;
        tbody.appendChild(fila);
        initCentroCostoSelect(fila.querySelector('.centro-costo-select'));
        filaIndex++;
    }
    function eliminarFila(boton) {
        const tbody = document.getElementById('itemsTable');
        if (tbody.children.length > 1) boton.closest('tr').remove();
        else alert('Debe haber al menos un item');
    }
</script>
@endsection
