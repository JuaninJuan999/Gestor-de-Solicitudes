{{-- 
    Vista: Panel de Administración de Compras
    Ruta protegida por middleware 'is_admin' 
    Solo usuarios con is_admin = 1 pueden acceder
--}}
@extends('layouts.app')

@section('content')
<div class="py-12">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
            <div class="p-6 bg-white border-b border-gray-200">
                <div class="flex justify-between items-center mb-6">
                    <h2 class="text-3xl font-bold text-purple-600">Panel de Administración - Todas las Solicitudes</h2>
                    <span class="px-4 py-2 bg-purple-100 text-purple-800 rounded-lg font-semibold">
                        Total: {{ $solicitudes->total() }}
                    </span>
                </div>

                @if(session('success'))
                    <div class="mb-4 p-4 bg-green-100 border border-green-400 text-green-700 rounded-lg">
                        {{ session('success') }}
                    </div>
                @endif

                <div class="mb-6 bg-gradient-to-r from-purple-50 to-pink-50 border-3 border-purple-500 rounded-lg p-6 shadow-lg">
                    <div class="mb-3">
                        <h3 class="text-lg font-bold text-purple-700 flex items-center gap-2">🔍 Filtros de Búsqueda Avanzada</h3>
                    </div>

                    {{-- FORMULARIO DE FILTROS --}}
                    <form method="GET" action="{{ route('admin.solicitudes.index') }}" class="space-y-4">
                        {{-- Fila 1: filtros --}}
                        <div class="grid grid-cols-1 md:grid-cols-5 gap-4 items-end">
                            <div>
                                <label for="estado" class="block text-sm font-bold text-gray-700 mb-2">📊 Estado de Solicitud</label>
                                <select name="estado" id="estado"
                                    class="w-full px-4 py-2.5 border-2 border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-purple-500 transition">
                                    <option value="">Todos los estados</option>
                                    <option value="pendiente" {{ request('estado') == 'pendiente' ? 'selected' : '' }}>⏳ Pendiente</option>
                                    <option value="en_proceso" {{ request('estado') == 'en_proceso' ? 'selected' : '' }}>🔄 En Proceso</option>
                                    <option value="finalizada" {{ request('estado') == 'finalizada' ? 'selected' : '' }}>✅ Finalizada</option>
                                    <option value="rechazada" {{ request('estado') == 'rechazada' ? 'selected' : '' }}>❌ Rechazada</option>
                                </select>
                            </div>
                            <div>
                                <label for="area" class="block text-sm font-bold text-gray-700 mb-2">🏢 Área/Departamento</label>
                                <select name="area" id="area"
                                    class="w-full px-4 py-2.5 border-2 border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-purple-500 transition">
                                    <option value="">Todas las áreas</option>
                                    @foreach($areas as $area)
                                        <option value="{{ $area }}" {{ request('area') == $area ? 'selected' : '' }}>{{ $area }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div>
                                <label for="tipo_solicitud" class="block text-sm font-bold text-gray-700 mb-2">📋 Tipo de Solicitud</label>
                                <select name="tipo_solicitud" id="tipo_solicitud"
                                    class="w-full px-4 py-2.5 border-2 border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-purple-500 transition">
                                    <option value="">Todos los tipos</option>
                                    <option value="estandar" {{ request('tipo_solicitud') == 'estandar' ? 'selected' : '' }}>Solicitud Estándar</option>
                                    <option value="pedido_mensual" {{ request('tipo_solicitud') == 'pedido_mensual' ? 'selected' : '' }}>Pedido Mensual</option>
                                    <option value="salida_insumos" {{ request('tipo_solicitud') == 'salida_insumos' ? 'selected' : '' }}>Salida Insumos</option>
                                </select>
                            </div>
                            <div>
                                <label for="fecha_desde" class="block text-sm font-bold text-gray-700 mb-2">
                                    📅 Desde
                                </label>
                                <input
                                    type="date"
                                    name="fecha_desde"
                                    id="fecha_desde"
                                    value="{{ request('fecha_desde') }}"
                                    class="w-full px-4 py-2.5 border-2 border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-purple-500 transition">
                            </div>
                            <div>
                                <label for="fecha_hasta" class="block text-sm font-bold text-gray-700 mb-2">
                                    📅 Hasta
                                </label>
                                <input
                                    type="date"
                                    name="fecha_hasta"
                                    id="fecha_hasta"
                                    value="{{ request('fecha_hasta') }}"
                                    class="w-full px-4 py-2.5 border-2 border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-purple-500 transition">
                            </div>
                        </div>

                        {{-- Fila 2: botones --}}
                        <div class="flex flex-wrap justify-end gap-3">
                            <button type="submit" class="px-6 py-2.5 bg-purple-600 text-white font-semibold rounded-lg hover:bg-purple-700 transition shadow-md">
                                🔍 Filtrar
                            </button>
                            <a href="{{ route('admin.solicitudes.index') }}" class="px-6 py-2.5 bg-gray-500 text-white font-semibold rounded-lg hover:bg-gray-600 transition shadow-md text-center">
                                🔄 Limpiar
                            </a>
                        </div>
                    </form>
                </div>

                @if(request('estado') || request('area') || request('tipo_solicitud') || request('fecha_desde') || request('fecha_hasta'))
                    <div class="mb-4 flex flex-wrap items-center gap-3">
                        <span class="text-sm font-semibold text-gray-700">Filtros activos:</span>
                        @if(request('estado'))
                            <span class="px-4 py-2 bg-purple-100 text-purple-800 font-semibold rounded-lg text-sm flex items-center gap-2">
                                Estado:
                                @if(request('estado') == 'pendiente') ⏳ Pendiente
                                @elseif(request('estado') == 'en_proceso') 🔄 En Proceso
                                @elseif(request('estado') == 'finalizada') ✅ Finalizada
                                @elseif(request('estado') == 'rechazada') ❌ Rechazada
                                @endif
                            </span>
                        @endif
                        @if(request('area'))
                            <span class="px-4 py-2 bg-pink-100 text-pink-800 font-semibold rounded-lg text-sm">📍 Área: {{ request('area') }}</span>
                        @endif
                        @if(request('tipo_solicitud'))
                            @php
                                $labelTipo = match(request('tipo_solicitud')) {
                                    'estandar' => 'Solicitud Estándar',
                                    'pedido_mensual' => 'Pedido Mensual',
                                    'salida_insumos' => 'Salida Insumos',
                                    default => request('tipo_solicitud'),
                                };
                            @endphp
                            <span class="px-4 py-2 bg-blue-100 text-blue-800 font-semibold rounded-lg text-sm">
                                📋 Tipo: {{ $labelTipo }}
                            </span>
                        @endif
                        @if(request('fecha_desde') || request('fecha_hasta'))
                            <span class="px-4 py-2 bg-yellow-100 text-yellow-800 font-semibold rounded-lg text-sm">
                                🗓 Fecha:
                                @if(request('fecha_desde')) desde {{ request('fecha_desde') }} @endif
                                @if(request('fecha_hasta')) hasta {{ request('fecha_hasta') }} @endif
                            </span>
                        @endif
                    </div>
                @endif

                @if($solicitudes->isEmpty())
                    <div class="text-center py-12 bg-gray-50 rounded-lg border-2 border-dashed border-gray-300">
                        <p class="text-gray-500 text-lg font-semibold">
                            @if(request('estado') || request('area') || request('tipo_solicitud') || request('fecha_desde') || request('fecha_hasta'))
                                ⚠️ No se encontraron solicitudes con los filtros seleccionados.
                            @else
                                No hay solicitudes registradas aún.
                            @endif
                        </p>
                        @if(request('estado') || request('area') || request('tipo_solicitud') || request('fecha_desde') || request('fecha_hasta'))
                            <a href="{{ route('admin.solicitudes.index') }}" class="mt-4 inline-block px-6 py-2 bg-purple-600 text-white rounded-lg hover:bg-purple-700 transition">Ver todas las solicitudes</a>
                        @endif
                    </div>
                @else
                    <div class="space-y-6">
                        @foreach($solicitudes as $solicitud)
                            <div class="border-2 border-gray-200 rounded-lg p-6 hover:border-purple-400 transition">
                                <div class="flex justify-between items-start mb-4">
                                    <div>
                                        <div class="mb-2 flex flex-wrap gap-2">
                                            <span class="px-3 py-1 bg-blue-600 text-white rounded-lg text-sm font-mono font-bold">{{ $solicitud->consecutivo }}</span>
                                            @if($solicitud->user && $solicitud->user->area)
                                                <span class="px-3 py-1 bg-purple-600 text-white rounded-lg text-sm font-semibold">📍 {{ $solicitud->user->area }}</span>
                                            @endif
                                            <span class="px-3 py-1 bg-green-600 text-white rounded-lg text-sm font-semibold">👤 {{ $solicitud->user->name ?? 'Usuario' }}</span>

                                            @php
                                                $etiquetaTipo = '';
                                                $colorTipo = 'bg-gray-500';
                                                if ($solicitud->tipo_solicitud == 'estandar') {
                                                    $etiquetaTipo = 'Solicitud Estándar';
                                                    $colorTipo = 'bg-green-700';
                                                } elseif ($solicitud->tipo_solicitud == 'pedido_mensual') {
                                                    $etiquetaTipo = 'Pedido Mensual';
                                                    $colorTipo = 'bg-blue-700';
                                                } elseif ($solicitud->tipo_solicitud == 'salida_insumos') {
                                                    $etiquetaTipo = 'Salida Insumos';
                                                    $colorTipo = 'bg-yellow-600 text-black';
                                                }
                                            @endphp
                                            <span class="px-3 py-1 {{ $colorTipo }} rounded-lg text-sm font-semibold" style="color: white;">
                                                {{ $etiquetaTipo }}
                                            </span>
                                        </div>
                                        <h3 class="text-xl font-bold text-gray-800">{{ $solicitud->titulo }}</h3>
                                        <div class="mt-1">
                                            <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold bg-gray-100 text-gray-700 border border-gray-300">
                                                🗓 Fecha: {{ $solicitud->created_at->format('d/m/Y h:i a') }}
                                            </span>
                                        </div>
                                    </div>
                                    <form action="{{ route('admin.solicitudes.estado', $solicitud->id) }}" method="POST" class="flex gap-2">
                                        @csrf
                                        <select name="estado" class="px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500">
                                            <option value="pendiente" {{ $solicitud->estado == 'pendiente' ? 'selected' : '' }}>⏳ Pendiente</option>
                                            <option value="en_proceso" {{ $solicitud->estado == 'en_proceso' ? 'selected' : '' }}>🔄 En Proceso</option>
                                            <option value="finalizada" {{ $solicitud->estado == 'finalizada' ? 'selected' : '' }}>✅ Finalizada</option>
                                            <option value="rechazada" {{ $solicitud->estado == 'rechazada' ? 'selected' : '' }}>❌ Rechazada</option>
                                        </select>
                                        <button type="submit" class="px-4 py-2 bg-purple-600 text-white rounded-lg hover:bg-purple-700 font-semibold">Actualizar</button>
                                    </form>
                                </div>

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
                                            @if($solicitud->tipo_solicitud == 'estandar')
                                                <table class="min-w-full border-collapse border border-gray-300">
                                                    <thead class="bg-green-600 text-white">
                                                        <tr>
                                                            <th class="border border-gray-300 px-4 py-2 text-left">REFERENCIA</th>
                                                            <th class="border border-gray-300 px-4 py-2 text-center">UNIDAD</th>
                                                            <th class="border border-gray-300 px-4 py-2 text-left">DESCRIPCIÓN</th>
                                                            <th class="border border-gray-300 px-4 py-2 text-center">CANTIDAD</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody class="bg-white">
                                                        @if($itemsTabla->isNotEmpty())
                                                            @foreach($itemsTabla as $item)
                                                                <tr class="hover:bg-gray-50">
                                                                    <td class="border border-gray-300 px-4 py-2">{{ $item->referencia ?? '-' }}</td>
                                                                    <td class="border border-gray-300 px-4 py-2 text-center">{{ $item->unidad ?? '-' }}</td>
                                                                    <td class="border border-gray-300 px-4 py-2">{{ $item->descripcion ?? '-' }}</td>
                                                                    <td class="border border-gray-300 px-4 py-2 text-center">{{ $item->cantidad ?? '-' }}</td>
                                                                </tr>
                                                            @endforeach
                                                        @else
                                                            @foreach($itemsJson as $item)
                                                                <tr class="hover:bg-gray-50">
                                                                    <td class="border border-gray-300 px-4 py-2">{{ $item['referencia'] ?? '-' }}</td>
                                                                    <td class="border border-gray-300 px-4 py-2 text-center">{{ $item['unidad'] ?? '-' }}</td>
                                                                    <td class="border border-gray-300 px-4 py-2">{{ $item['descripcion'] ?? '-' }}</td>
                                                                    <td class="border border-gray-300 px-4 py-2 text-center">{{ $item['cantidad'] ?? '-' }}</td>
                                                                </tr>
                                                            @endforeach
                                                        @endif
                                                    </tbody>
                                                </table>
                                            @elseif($solicitud->tipo_solicitud == 'pedido_mensual')
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
                                            @elseif($solicitud->tipo_solicitud == 'salida_insumos')
                                                <table class="min-w-full border-collapse border border-gray-300">
                                                    <thead class="bg-yellow-600 text-white">
                                                        <tr>
                                                            <th class="border px-4 py-2">CÓDIGO</th>
                                                            <th class="border px-4 py-2">DESCRIPCIÓN</th>
                                                            <th class="border px-4 py-2">CANTIDAD</th>
                                                            <th class="border px-4 py-2">ÁREA CONSUMO</th>
                                                            <th class="border px-4 py-2">CENTRO DE COSTOS</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
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
                                            @endif
                                        </div>
                                    </div>
                                @endif

                                @if(!empty($observaciones))
                                    <div class="mb-4">
                                        <h4 class="text-sm font-semibold text-gray-700 mb-1">Observaciones:</h4>
                                        <p class="text-gray-600">{{ $observaciones }}</p>
                                    </div>
                                @endif

                                @if($solicitud->archivo)
                                    <div class="mt-3">
                                        <a href="{{ url('storage/' . $solicitud->archivo) }}"
                                            target="_blank"
                                            class="inline-flex items-center px-4 py-2 bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200 transition">
                                            📎 Ver archivo adjunto
                                        </a>
                                    </div>
                                @endif

                                <div class="mt-4 pt-4 border-t border-gray-200">
                                    <a href="{{ route('solicitudes.show', $solicitud) }}"
                                        class="inline-flex items-center px-6 py-3 bg-purple-600 text-white font-semibold rounded-lg hover:bg-purple-700 transition">
                                        💬 Ver Detalle, Comentarios y Gestionar Estado
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
