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
                
                <h2 class="text-2xl font-bold text-blue-600 mb-6">Registrar Solicitud de Compra Estándar</h2>

                <form action="{{ route('solicitudes.store') }}" method="POST" enctype="multipart/form-data">
                    @csrf

                    <!-- Campo oculto para el tipo de solicitud -->
                    <input type="hidden" name="tipo_solicitud" value="estandar">

                    <!-- Título -->
                    <div class="mb-6">
                        <label for="titulo" class="block text-sm font-medium text-gray-700 mb-2">
                            Título de la solicitud:
                        </label>
                        <input type="text" name="titulo" id="titulo" required
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                    </div>

                    <!-- Centro de costo y área -->
                    <div class="mb-6">
                        <label for="centro_costos" class="block text-sm font-bold text-gray-700 mb-2">
                            Centro de Costo y Área
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

                    <!-- Tabla de items -->
                    <div class="mb-6">
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            Detalle de productos/servicios:
                        </label>
                        <div class="overflow-x-auto">
                            <table class="min-w-full border-collapse border border-gray-300">
                                <thead class="bg-green-600 text-white">
                                    <tr>
                                        <th class="border border-gray-300 px-4 py-2">REFERENCIA</th>
                                        <th class="border border-gray-300 px-4 py-2">UNIDAD</th>
                                        <th class="border border-gray-300 px-4 py-2">DESCRIPCION</th>
                                        <th class="border border-gray-300 px-4 py-2">CANTIDAD</th>
                                        <th class="border border-gray-300 px-4 py-2">ACCIÓN</th>
                                    </tr>
                                </thead>
                                <tbody id="itemsTable">
                                    <tr>
                                        <td class="border border-gray-300 px-4 py-2">
                                            <input type="text" name="items[0][referencia]" class="w-full px-2 py-1 border rounded">
                                        </td>
                                        <td class="border border-gray-300 px-4 py-2">
                                            <input type="text" name="items[0][unidad]" class="w-full px-2 py-1 border rounded">
                                        </td>
                                        <td class="border border-gray-300 px-4 py-2">
                                            <input type="text" name="items[0][descripcion]" class="w-full px-2 py-1 border rounded">
                                        </td>
                                        <td class="border border-gray-300 px-4 py-2">
                                            <input type="number" name="items[0][cantidad]" class="w-full px-2 py-1 border rounded">
                                        </td>
                                        <td class="border border-gray-300 px-4 py-2 text-center">
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
    let filaIndex = 1;
    
    function agregarFila() {
        const tbody = document.getElementById('itemsTable');
        const fila = `
            <tr>
                <td class="border border-gray-300 px-4 py-2">
                    <input type="text" name="items[${filaIndex}][referencia]" class="w-full px-2 py-1 border rounded">
                </td>
                <td class="border border-gray-300 px-4 py-2">
                    <input type="text" name="items[${filaIndex}][unidad]" class="w-full px-2 py-1 border rounded">
                </td>
                <td class="border border-gray-300 px-4 py-2">
                    <input type="text" name="items[${filaIndex}][descripcion]" class="w-full px-2 py-1 border rounded">
                </td>
                <td class="border border-gray-300 px-4 py-2">
                    <input type="number" name="items[${filaIndex}][cantidad]" class="w-full px-2 py-1 border rounded">
                </td>
                <td class="border border-gray-300 px-4 py-2 text-center">
                    <button type="button" onclick="eliminarFila(this)" class="text-red-600 hover:text-red-800 font-bold text-xl">
                        ✕
                    </button>
                </td>
            </tr>
        `;
        tbody.insertAdjacentHTML('beforeend', fila);
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


