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
        
        <!-- === BOTÓN VOLVER AL DASHBOARD === -->
        <div class="mb-6">
            <a href="{{ route('dashboard') }}" class="btn-back-dashboard shadow-sm">
                <i class="bi bi-chevron-left"></i> Volver al Dashboard
            </a>
        </div>
        
        <!-- Tarjeta principal (Glassmorphism) -->
        <div class="bg-white bg-opacity-70 overflow-hidden shadow-2xl sm:rounded-lg"
             style="backdrop-filter: blur(10px);">
            <div class="p-6 border-b border-gray-200">
               
                <div class="flex justify-between items-center mb-6">
                    <h2 class="text-3xl font-bold text-blue-600">Mis Solicitudes</h2>
                    <a href="{{ route('solicitudes.create') }}" 
                       class="px-6 py-3 bg-blue-600 text-white font-semibold rounded-lg hover:bg-blue-700 transition">
                        Registrar nueva solicitud
                    </a>
                </div>

                @if(session('success'))
                    <div class="mb-4 p-4 bg-green-100 border border-green-400 text-green-700 rounded-lg">
                        {{ session('success') }}
                    </div>
                @endif

                <!-- Filtros -->
                <div class="mb-6 bg-white bg-opacity-80 border-2 border-green-500 rounded-lg p-5 shadow-md">
                    <form method="GET" action="{{ route('solicitudes.index') }}" class="flex flex-wrap gap-4 items-end">
                        <div class="flex-1 min-w-[250px]">
                            <label for="estado" class="block text-sm font-bold text-gray-700 mb-2">
                                🔍 Filtrar por Estado
                            </label>
                            <select name="estado" id="estado" 
                                class="w-full px-4 py-2.5 border-2 border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-green-500 transition">
                                <option value="">📋 Todas mis solicitudes</option>
                                <option value="pendiente" {{ request('estado') == 'pendiente' ? 'selected' : '' }}>⏳ Pendiente</option>
                                <option value="en_proceso" {{ request('estado') == 'en_proceso' ? 'selected' : '' }}>🔄 En Proceso</option>
                                <option value="finalizada" {{ request('estado') == 'finalizada' ? 'selected' : '' }}>✅ Finalizada</option>
                                <option value="rechazada" {{ request('estado') == 'rechazada' ? 'selected' : '' }}>❌ Rechazada</option>
                            </select>
                        </div>
                        <div class="flex gap-3">
                            <button type="submit" 
                                class="px-6 py-2.5 bg-green-600 text-white font-semibold rounded-lg hover:bg-green-700 transition shadow-md">
                                🔍 Filtrar
                            </button>
                            <a href="{{ route('solicitudes.index') }}" 
                               class="px-6 py-2.5 bg-gray-500 text-white font-semibold rounded-lg hover:bg-gray-600 transition shadow-md">
                                🔄 Limpiar
                            </a>
                        </div>
                    </form>
                </div>

                @if(!$solicitudes->isEmpty())
                    <div class="mb-4 flex items-center gap-3">
                        <span class="px-4 py-2 bg-blue-100 text-blue-800 font-semibold rounded-lg text-sm">
                            📊 Total: {{ $solicitudes->total() }} solicitud(es)
                        </span>
                        @if(request('estado'))
                            <span class="px-4 py-2 bg-green-100 text-green-800 font-semibold rounded-lg text-sm">
                                Filtro: 
                                @if(request('estado') == 'pendiente') ⏳ Pendiente
                                @elseif(request('estado') == 'en_proceso') 🔄 En Proceso
                                @elseif(request('estado') == 'finalizada') ✅ Finalizada
                                @elseif(request('estado') == 'rechazada') ❌ Rechazada
                                @endif
                            </span>
                        @endif
                    </div>
                @endif

                @if($solicitudes->isEmpty())
                    <div class="text-center py-12 bg-white bg-opacity-60 rounded-lg border-2 border-dashed border-gray-300">
                        <p class="text-gray-700 text-lg font-semibold">
                            @if(request('estado'))
                                ⚠️ No se encontraron solicitudes con el estado seleccionado.
                            @else
                                No tienes solicitudes registradas aún.
                            @endif
                        </p>
                        @if(request('estado'))
                            <a href="{{ route('solicitudes.index') }}" 
                               class="mt-4 inline-block px-6 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition">
                                Ver todas las solicitudes
                            </a>
                        @endif
                    </div>
                @else
                    <div class="space-y-6">
                        @foreach($solicitudes as $solicitud)
                            <div class="bg-white bg-opacity-80 border-2 border-gray-200 rounded-lg p-6 hover:border-blue-400 transition shadow-md"
                                 style="backdrop-filter: blur(5px);">
                                
                                <!-- Encabezado de la solicitud -->
                                <div class="flex justify-between items-start mb-4">
                                    <div>
                                        <div class="mb-2 flex flex-wrap gap-2">
                                            <!-- Consecutivo -->
                                            <span class="px-3 py-1 bg-blue-600 text-white rounded-lg text-sm font-mono font-bold">
                                                {{ $solicitud->consecutivo ?? 'TICKET-' . str_pad($solicitud->id, 4, '0', STR_PAD_LEFT) }}
                                            </span>
                                            <!-- Área -->
                                            @if($solicitud->user && $solicitud->user->area)
                                                <span class="px-3 py-1 bg-purple-600 text-white rounded-lg text-sm font-semibold">
                                                    📍 {{ $solicitud->user->area }}
                                                </span>
                                            @endif
                                            <!-- Usuario -->
                                            <span class="px-3 py-1 bg-green-600 text-white rounded-lg text-sm font-semibold">
                                                👤 {{ $solicitud->user->name ?? 'Usuario' }}
                                            </span>
                                            <!-- Tipo -->
                                            @php
                                                $etiquetaTipo = '';
                                                $colorTipo = 'bg-gray-500';
                                                if ($solicitud->tipo_solicitud == 'estandar') {
                                                    $etiquetaTipo = 'Solicitud Estándar';
                                                    $colorTipo = 'bg-green-700';
                                                } elseif ($solicitud->tipo_solicitud == 'traslado_bodegas') {
                                                    $etiquetaTipo = 'Traslados entre Bodegas';
                                                    $colorTipo = 'bg-blue-700';
                                                } elseif ($solicitud->tipo_solicitud == 'solicitud_pedidos') {
                                                    $etiquetaTipo = 'Solicitud de Pedidos';
                                                    $colorTipo = 'bg-yellow-600';
                                                } elseif ($solicitud->tipo_solicitud == 'solicitud_mtto') {
                                                    $etiquetaTipo = 'Solicitud Insumos / Servicio';
                                                    $colorTipo = 'bg-purple-700';
                                                }
                                            @endphp
                                            <span class="px-3 py-1 {{ $colorTipo }} text-white rounded-lg text-sm font-semibold">
                                                {{ $etiquetaTipo }}
                                            </span>

                                            <!-- Presupuestado -->
                                            @if($solicitud->presupuestado)
                                                <span class="px-3 py-1 {{ $solicitud->presupuestado == 'SI' ? 'bg-indigo-600' : 'bg-red-500' }} text-white rounded-lg text-sm font-semibold">
                                                    Presupuestado: {{ $solicitud->presupuestado }}
                                                </span>
                                            @endif
                                        </div>
                                        <h3 class="text-xl font-bold text-gray-800">{{ $solicitud->titulo }}</h3>
                                        <p class="text-sm text-gray-600 mt-1">
                                            Fecha: {{ $solicitud->created_at->format('d/m/Y H:i') }}
                                        </p>
                                    </div>
                                    <div>
                                        @if($solicitud->estado == 'pendiente')
                                            <span class="px-4 py-2 bg-yellow-100 text-yellow-800 font-semibold rounded-full">⏳ Pendiente</span>
                                        @elseif($solicitud->estado == 'en_proceso')
                                            <span class="px-4 py-2 bg-blue-100 text-blue-800 font-semibold rounded-full">🔄 En Proceso</span>
                                        @elseif($solicitud->estado == 'finalizada')
                                            <span class="px-4 py-2 bg-green-100 text-green-800 font-semibold rounded-full">✅ Finalizada</span>
                                        @elseif($solicitud->estado == 'rechazada')
                                            <span class="px-4 py-2 bg-red-100 text-red-800 font-semibold rounded-full">❌ Rechazada</span>
                                        @else
                                            <span class="px-4 py-2 bg-gray-100 text-gray-800 font-semibold rounded-full">{{ ucfirst($solicitud->estado) }}</span>
                                        @endif
                                    </div>
                                </div>

                                <!-- Lógica de items -->
                                @php
                                    $itemsTabla = $solicitud->items ?? collect();
                                    $itemsJson = [];
                                    $observaciones = '';
                                    if ($itemsTabla->isEmpty() && strpos($solicitud->descripcion, 'Items solicitados:') !== false) {
                                        $partes = explode('Items solicitados:', $solicitud->descripcion);
                                        $observaciones = trim($partes[0]);
                                        $itemsJsonString = trim($partes[1] ?? '');
                                        $itemsJson = json_decode($itemsJsonString, true) ?? [];
                                    } else {
                                        $observaciones = $solicitud->descripcion;
                                    }
                                @endphp

                                @if($itemsTabla->isNotEmpty() || !empty($itemsJson))
                                    <div class="mb-4">
                                        <h4 class="text-sm font-semibold text-gray-700 mb-2">Items solicitados:</h4>
                                        <div class="overflow-x-auto">
                                            
                                            <!-- ESTÁNDAR -->
                                            @if($solicitud->tipo_solicitud == 'estandar')
                                                <table class="min-w-full border-collapse border border-gray-300">
                                                    <thead class="bg-green-600 text-white">
                                                        <tr>
                                                            <th class="border border-gray-300 px-4 py-2 text-left">CÓDIGO SIIMED</th>
                                                            <th class="border border-gray-300 px-4 py-2 text-center">UNIDAD</th>
                                                            <th class="border border-gray-300 px-4 py-2 text-left">DESCRIPCIÓN</th>
                                                            <th class="border border-gray-300 px-4 py-2 text-center">CANTIDAD</th>
                                                            <th class="border border-gray-300 px-4 py-2 text-center">CENTRO DE COSTO</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody class="bg-white">
                                                        @if($itemsTabla->isNotEmpty())
                                                            @foreach($itemsTabla as $item)
                                                                <tr class="hover:bg-gray-50">
                                                                    <td class="border border-gray-300 px-4 py-2">{{ $item->codigo ?? '-' }}</td>
                                                                    <td class="border border-gray-300 px-4 py-2 text-center">{{ $item->unidad ?? '-' }}</td>
                                                                    <td class="border border-gray-300 px-4 py-2">{{ $item->descripcion ?? '-' }}</td>
                                                                    <td class="border border-gray-300 px-4 py-2 text-center">{{ $item->cantidad ?? '-' }}</td>
                                                                    <td class="border border-gray-300 px-4 py-2 text-center">{{ $item->centro_costos_item ?? '-' }}</td>
                                                                </tr>
                                                            @endforeach
                                                        @else
                                                            @foreach($itemsJson as $item)
                                                                <tr class="hover:bg-gray-50">
                                                                    <td class="border border-gray-300 px-4 py-2">{{ $item['codigo'] ?? '-' }}</td>
                                                                    <td class="border border-gray-300 px-4 py-2 text-center">{{ $item['unidad'] ?? '-' }}</td>
                                                                    <td class="border border-gray-300 px-4 py-2">{{ $item['descripcion'] ?? '-' }}</td>
                                                                    <td class="border border-gray-300 px-4 py-2 text-center">{{ $item['cantidad'] ?? '-' }}</td>
                                                                    <td class="border border-gray-300 px-4 py-2 text-center">{{ $item['centro_costos_item'] ?? '-' }}</td>
                                                                </tr>
                                                            @endforeach
                                                        @endif
                                                    </tbody>
                                                </table>

                                            <!-- TRASLADO BODEGAS -->
                                            @elseif($solicitud->tipo_solicitud == 'traslado_bodegas')
                                                <table class="min-w-full border-collapse border border-gray-300">
                                                    <thead class="bg-blue-600 text-white">
                                                        <tr>
                                                            <th class="border border-gray-300 px-4 py-2 text-left">CÓDIGO</th>
                                                            <th class="border border-gray-300 px-4 py-2 text-left">DESCRIPCIÓN</th>
                                                            <th class="border border-gray-300 px-4 py-2 text-center">CANTIDAD</th>
                                                            <th class="border border-gray-300 px-4 py-2 text-left">BODEGA</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody class="bg-white">
                                                        @if($itemsTabla->isNotEmpty())
                                                            @foreach($itemsTabla as $item)
                                                                <tr class="hover:bg-gray-50">
                                                                    <td class="border border-gray-300 px-4 py-2">{{ $item->codigo ?? '-' }}</td>
                                                                    <td class="border border-gray-300 px-4 py-2">{{ $item->descripcion ?? '-' }}</td>
                                                                    <td class="border border-gray-300 px-4 py-2 text-center">{{ $item->cantidad ?? '-' }}</td>
                                                                    <td class="border border-gray-300 px-4 py-2">{{ $item->bodega ?? '-' }}</td>
                                                                </tr>
                                                            @endforeach
                                                        @else
                                                            @foreach($itemsJson as $item)
                                                                <tr class="hover:bg-gray-50">
                                                                    <td class="border border-gray-300 px-4 py-2">{{ $item['codigo'] ?? '-' }}</td>
                                                                    <td class="border border-gray-300 px-4 py-2">{{ $item['descripcion'] ?? '-' }}</td>
                                                                    <td class="border border-gray-300 px-4 py-2 text-center">{{ $item['cantidad'] ?? '-' }}</td>
                                                                    <td class="border border-gray-300 px-4 py-2">{{ $item['bodega'] ?? '-' }}</td>
                                                                </tr>
                                                            @endforeach
                                                        @endif
                                                    </tbody>
                                                </table>

                                            <!-- PEDIDOS -->
                                            @elseif($solicitud->tipo_solicitud == 'solicitud_pedidos')
                                                <table class="min-w-full border-collapse border border-gray-300">
                                                    <thead class="bg-yellow-600 text-white">
                                                        <tr>
                                                            <th class="border px-4 py-2">CÓDIGO</th>
                                                            <th class="border px-4 py-2">DESCRIPCIÓN</th>
                                                            <th class="border px-4 py-2">CANTIDAD</th>
                                                            <th class="border px-4 py-2">ÁREA</th>
                                                            <th class="border px-4 py-2">C. COSTO</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody class="bg-white">
                                                        @if($itemsTabla->isNotEmpty())
                                                            @foreach($itemsTabla as $item)
                                                                <tr>
                                                                    <td class="border px-4 py-2">{{ $item->codigo ?? '-' }}</td>
                                                                    <td class="border px-4 py-2">{{ $item->descripcion ?? '-' }}</td>
                                                                    <td class="border px-4 py-2">{{ $item->cantidad ?? '-' }}</td>
                                                                    <td class="border px-4 py-2">{{ $item->area_consumo ?? '-' }}</td>
                                                                    <td class="border px-4 py-2">{{ $item->centro_costos_item ?? '-' }}</td>
                                                                </tr>
                                                            @endforeach
                                                        @else
                                                            @foreach($itemsJson as $item)
                                                                <tr>
                                                                    <td class="border px-4 py-2">{{ $item['codigo'] ?? '-' }}</td>
                                                                    <td class="border px-4 py-2">{{ $item['descripcion'] ?? '-' }}</td>
                                                                    <td class="border px-4 py-2">{{ $item['cantidad'] ?? '-' }}</td>
                                                                    <td class="border px-4 py-2">{{ $item['area_consumo'] ?? '-' }}</td>
                                                                    <td class="border px-4 py-2">{{ $item['centro_costos_item'] ?? '-' }}</td>
                                                                </tr>
                                                            @endforeach
                                                        @endif
                                                    </tbody>
                                                </table>

                                            <!-- MANTENIMIENTO -->
                                            @elseif($solicitud->tipo_solicitud == 'solicitud_mtto')
                                                <table class="min-w-full border-collapse border border-gray-300">
                                                    <thead class="bg-purple-600 text-white">
                                                        <tr>
                                                            <th class="border px-4 py-2">DESCRIPCIÓN</th>
                                                            <th class="border px-4 py-2">ESPECIFICACIONES</th>
                                                            <th class="border px-4 py-2">CANTIDAD</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody class="bg-white">
                                                        @if($itemsTabla->isNotEmpty())
                                                            @foreach($itemsTabla as $item)
                                                                <tr class="hover:bg-gray-50">
                                                                    <td class="border px-4 py-2">{{ $item->descripcion ?? '-' }}</td>
                                                                    <td class="border px-4 py-2">{{ $item->especificaciones ?? '-' }}</td>
                                                                    <td class="border px-4 py-2">{{ $item->cantidad ?? '-' }}</td>
                                                                </tr>
                                                            @endforeach
                                                        @endif
                                                    </tbody>
                                                </table>
                                            @endif
                                        </div>
                                    </div>
                                @endif

                                <!-- Observaciones -->
                                @if(!empty($observaciones) && $observaciones != '')
                                    <div class="mb-4">
                                        <h4 class="text-sm font-semibold text-gray-700 mb-1">Observaciones:</h4>
                                        <p class="text-gray-600">{{ $observaciones }}</p>
                                    </div>
                                @endif

                                <!-- Archivo -->
                                @if($solicitud->archivo)
                                    <div class="mt-3">
                                        <a href="{{ url('storage/' . $solicitud->archivo) }}" target="_blank"
                                           class="inline-flex items-center px-4 py-2 bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200 transition">
                                            📎 Ver archivo adjunto
                                        </a>
                                    </div>
                                @endif

                                <!-- Botón Detalle -->
                                <div class="mt-4 pt-4 border-t border-gray-200">
                                    <a href="{{ route('solicitudes.show', $solicitud) }}" 
                                       class="inline-flex items-center px-6 py-3 bg-blue-600 text-white font-semibold rounded-lg hover:bg-blue-700 transition">
                                        💬 Ver Detalle y Comentarios
                                    </a>
                                </div>
                            </div>
                        @endforeach
                    </div>
                    <div class="mt-6">
                        {{ $solicitudes->links() }}
                    </div>
                @endif

            </div>
        </div>
    </div>
</div>
@endsection
