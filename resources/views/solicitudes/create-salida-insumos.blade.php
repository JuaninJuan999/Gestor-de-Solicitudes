@extends('layouts.app')

@section('content')
<!-- Contenedor con fondo de imagen -->
<div style="background-image: url('/images/create-solicitud.jpg'); 
            background-size: cover; 
            background-position: center; 
            background-attachment: fixed; 
            background-repeat: no-repeat;
            min-height: calc(100vh - 80px);
            padding-top: 3rem;
            padding-bottom: 3rem;">
    
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        
        <!-- Botón Volver -->
        <div class="mb-6">
            <a href="{{ route('solicitudes.create') }}" 
               class="inline-flex items-center px-6 py-2 bg-gray-600 bg-opacity-70 text-white font-semibold rounded-lg hover:bg-gray-700 transition shadow-lg"
               style="backdrop-filter: blur(10px);">
                ← Volver
            </a>
        </div>

        <!-- Tarjeta principal (70% transparente) -->
        <div class="bg-white bg-opacity-70 overflow-hidden shadow-2xl sm:rounded-lg"
             style="backdrop-filter: blur(10px);">
            <div class="p-6 border-b border-gray-200">
                
                <h2 class="text-2xl font-bold text-blue-600 mb-6">Solicitud de Pedido - Salida de Insumos</h2>

                <form action="{{ route('solicitudes.store') }}" method="POST" enctype="multipart/form-data">
                    @csrf

                    <!-- Campo oculto para el tipo de solicitud -->
                    <input type="hidden" name="tipo_solicitud" value="salida_insumos">

                    <!-- Información del solicitante -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                        <!-- Área Solicitante (select solo áreas) -->
                        <div>
                            <label for="area_solicitante" class="block text-sm font-medium text-gray-700 mb-2">
                                Área Solicitante:
                            </label>
                            <select name="area_solicitante" id="area_solicitante" required class="w-full px-4 py-2 border rounded-lg">
                                <option value="">Seleccione área...</option>
                                @foreach($areasBodega as $area)
                                    <option value="{{ $area }}">{{ $area }}</option>
                                @endforeach
                            </select>
                        </div>

                        <!-- Centro de Costos (select agrupado por departamento) -->
                        <div>
                            <label for="centro_costos" class="block text-sm font-medium text-gray-700 mb-2">
                                Centro de Costos:
                            </label>
                            <select name="centro_costos" id="centro_costos" required class="w-full px-4 py-2 border rounded-lg">
                                <option value="">Seleccione centro de costo y área...</option>
                                @foreach($centrosCostos->groupBy('departamento') as $depto => $areas)
                                    <optgroup label="{{ $depto }}">
                                        @foreach($areas as $area)
                                            <option value="{{ "{$area->cc}-{$area->sc}" }}">
                                                {{ $area->cc }}-{{ $area->sc }} | {{ $area->nombre_area }} ({{ $area->cuenta_contable }})
                                            </option>
                                        @endforeach
                                    </optgroup>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <!-- Título -->
                    <div class="mb-6">
                        <label for="titulo" class="block text-sm font-medium text-gray-700 mb-2">
                            Título de la solicitud:
                        </label>
                        <input type="text" name="titulo" id="titulo" required
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                    </div>

                    <!-- Tabla de items -->
                    <div class="mb-6">
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            Detalle de insumos:
                        </label>
                        <div class="overflow-x-auto">
                            <table class="min-w-full border-collapse border border-gray-300">
                                <thead class="bg-green-600 text-white">
                                    <tr>
                                        <th class="border border-gray-300 px-4 py-2">CÓDIGO</th>
                                        <th class="border border-gray-300 px-4 py-2">DESCRIPCIÓN</th>
                                        <th class="border border-gray-300 px-4 py-2">CANTIDAD</th>
                                        <th class="border border-gray-300 px-4 py-2">ÁREA CONSUMO</th>
                                        <th class="border border-gray-300 px-4 py-2">CENTRO COSTOS</th>
                                        <th class="border border-gray-300 px-4 py-2">ACCIÓN</th>
                                    </tr>
                                </thead>
                                <tbody id="itemsTable">
                                    <tr>
                                        <td class="border border-gray-300 px-2 py-2">
                                            <input type="text" name="items[0][codigo]" placeholder="CODIGO SIIMED" class="w-full px-2 py-1 border rounded">
                                        </td>
                                        <td class="border border-gray-300 px-2 py-2">
                                            <input type="text" name="items[0][descripcion]" required class="w-full px-2 py-1 border rounded">
                                        </td>
                                        <td class="border border-gray-300 px-2 py-2">
                                            <input type="number" name="items[0][cantidad]" required class="w-full px-2 py-1 border rounded">
                                        </td>
                                        <td class="border border-gray-300 px-2 py-2">
                                            <!-- Select de Área Consumo (solo áreas) -->
                                            <select name="items[0][area_consumo]" required class="w-full px-2 py-1 border rounded area-select">
                                                <option value="">Seleccione área...</option>
                                                @foreach($areasBodega as $area)
                                                    <option value="{{ $area }}">{{ $area }}</option>
                                                @endforeach
                                            </select>
                                        </td>
                                        <td class="border border-gray-300 px-2 py-2">
                                            <!-- Select de Centro Costos (agrupado) -->
                                            <select name="items[0][centro_costos_item]" required class="w-full px-2 py-1 border rounded centro-select">
                                                <option value="">Seleccione centro...</option>
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
                                        <td class="border border-gray-300 px-2 py-2 text-center">
                                            <button type="button" onclick="eliminarFila(this)" class="text-red-600 hover:text-red-800 font-bold text-xl">
                                                ✕
                                            </button>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>

                        <button type="button" onclick="agregarFila()" class="mt-3 px-4 py-2 bg-blue-500 text-white rounded hover:bg-blue-600">
                            + Agregar fila
                        </button>
                    </div>

                    <!-- Observaciones -->
                    <div class="mb-6">
                        <label for="descripcion" class="block text-sm font-medium text-gray-700 mb-2">
                            Observaciones:
                        </label>
                        <textarea name="descripcion" id="descripcion" rows="4" required
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg"></textarea>
                    </div>

                    <!-- Archivo -->
                    <div class="mb-6">
                        <label for="archivo" class="block text-sm font-medium text-gray-700 mb-2">
                            Archivo adjunto:
                        </label>
                        <input type="file" name="archivo" id="archivo" class="w-full px-4 py-2 border rounded-lg">
                    </div>

                    <!-- Botones -->
                    <div class="flex gap-4">
                        <button type="submit" class="px-6 py-3 bg-green-600 text-white rounded-lg hover:bg-green-700">
                            Registrar
                        </button>
                        <a href="{{ route('solicitudes.index') }}" class="px-6 py-3 bg-gray-300 text-gray-700 rounded-lg hover:bg-gray-400">
                            Cancelar
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
// Datos de centros de costos para sincronización
const centrosCostosData = @json($centrosCostos);
const areasBodega = @json($areasBodega);

// Crear mapeo bidireccional: nombre_area <-> centro_costo (cc-sc)
const areaToCentro = {};
const centroToArea = {};

centrosCostosData.forEach(item => {
    const centroKey = `${item.cc}-${item.sc}`;
    areaToCentro[item.nombre_area] = centroKey;
    centroToArea[centroKey] = item.nombre_area;
});

// Función para sincronizar área -> centro
function syncAreaToCentro(areaSelect, centroSelect) {
    const selectedArea = areaSelect.value;
    if (selectedArea && areaToCentro[selectedArea]) {
        centroSelect.value = areaToCentro[selectedArea];
    }
}

// Función para sincronizar centro -> área
function syncCentroToArea(centroSelect, areaSelect) {
    const selectedCentro = centroSelect.value;
    if (selectedCentro && centroToArea[selectedCentro]) {
        areaSelect.value = centroToArea[selectedCentro];
    }
}

// Aplicar eventos a la primera fila
document.addEventListener('DOMContentLoaded', function() {
    const firstAreaSelect = document.querySelector('select[name="items[0][area_consumo]"]');
    const firstCentroSelect = document.querySelector('select[name="items[0][centro_costos_item]"]');
    
    if (firstAreaSelect && firstCentroSelect) {
        firstAreaSelect.addEventListener('change', function() {
            syncAreaToCentro(this, firstCentroSelect);
        });
        
        firstCentroSelect.addEventListener('change', function() {
            syncCentroToArea(this, firstAreaSelect);
        });
    }
});

// Agregar filas dinámicas
let filaIndex = 1;

function agregarFila() {
    const tbody = document.getElementById('itemsTable');
    
    // Generar opciones para área consumo
    let areaOptions = '<option value="">Seleccione área...</option>';
    areasBodega.forEach(area => {
        areaOptions += `<option value="${area}">${area}</option>`;
    });
    
    // Generar opciones para centro costos (agrupado)
    let ccOptions = '<option value="">Seleccione centro...</option>';
    const ccGrouped = {};
    centrosCostosData.forEach(item => {
        if (!ccGrouped[item.departamento]) ccGrouped[item.departamento] = [];
        ccGrouped[item.departamento].push(item);
    });
    for (const depto in ccGrouped) {
        ccOptions += `<optgroup label="${depto}">`;
        ccGrouped[depto].forEach(area => {
            ccOptions += `<option value="${area.cc}-${area.sc}">${area.cc}-${area.sc} | ${area.nombre_area}</option>`;
        });
        ccOptions += `</optgroup>`;
    }
    
    const fila = `
        <tr>
            <td class="border border-gray-300 px-2 py-2">
                <input type="text" name="items[${filaIndex}][codigo]" placeholder="CODIGO SIIMED" class="w-full px-2 py-1 border rounded">
            </td>
            <td class="border border-gray-300 px-2 py-2">
                <input type="text" name="items[${filaIndex}][descripcion]" required class="w-full px-2 py-1 border rounded">
            </td>
            <td class="border border-gray-300 px-2 py-2">
                <input type="number" name="items[${filaIndex}][cantidad]" required class="w-full px-2 py-1 border rounded">
            </td>
            <td class="border border-gray-300 px-2 py-2">
                <select name="items[${filaIndex}][area_consumo]" required class="w-full px-2 py-1 border rounded area-select">
                    ${areaOptions}
                </select>
            </td>
            <td class="border border-gray-300 px-2 py-2">
                <select name="items[${filaIndex}][centro_costos_item]" required class="w-full px-2 py-1 border rounded centro-select">
                    ${ccOptions}
                </select>
            </td>
            <td class="border border-gray-300 px-2 py-2 text-center">
                <button type="button" onclick="eliminarFila(this)" class="text-red-600 hover:text-red-800 font-bold text-xl">
                    ✕
                </button>
            </td>
        </tr>
    `;
    tbody.insertAdjacentHTML('beforeend', fila);
    
    // Agregar eventos de sincronización a la nueva fila
    const newRow = tbody.lastElementChild;
    const newAreaSelect = newRow.querySelector('.area-select');
    const newCentroSelect = newRow.querySelector('.centro-select');
    
    newAreaSelect.addEventListener('change', function() {
        syncAreaToCentro(this, newCentroSelect);
    });
    
    newCentroSelect.addEventListener('change', function() {
        syncCentroToArea(this, newAreaSelect);
    });
    
    filaIndex++;
}

function eliminarFila(boton) {
    const fila = boton.closest('tr');
    const tbody = document.getElementById('itemsTable');
    if (tbody.children.length > 1) {
        fila.remove();
    } else {
        alert('Debe haber al menos un item');
    }
}
</script>
@endsection
