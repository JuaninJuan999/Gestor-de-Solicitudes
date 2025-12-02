{{-- Vista: Detalle de solicitud con comentarios --}}
@extends('layouts.app')

@section('content')
<div class="py-12">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        
        <!-- Botón volver -->
        <div class="mb-6">
            <a href="{{ Auth::user()->esAdminCompras() ? route('admin.solicitudes.index') : route('solicitudes.index') }}" 
               class="px-6 py-2 bg-gray-600 text-white font-semibold rounded-lg hover:bg-gray-700 transition">
                ← Volver
            </a>
        </div>

        @if(session('success'))
            <div class="mb-4 p-4 bg-green-100 border border-green-400 text-green-700 rounded-lg">
                {{ session('success') }}
            </div>
        @endif

        <!-- Encabezado de solicitud -->
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
            <div class="p-6 bg-white border-b border-gray-200">
                <h1 class="text-3xl font-bold text-blue-600 mb-4">{{ $solicitud->ticket_id }}</h1>
                
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <div class="bg-gray-50 p-4 rounded-lg">
                        <label class="block text-sm font-bold text-gray-700 mb-2">Departamento:</label>
                        <span class="px-3 py-1 bg-purple-600 text-white rounded-lg text-sm font-semibold">
                            {{ $solicitud->user->area ?? 'Sin departamento' }}
                        </span>
                    </div>
                    <div class="bg-gray-50 p-4 rounded-lg">
                        <label class="block text-sm font-bold text-gray-700 mb-2">Solicitante:</label>
                        <span class="px-3 py-1 bg-blue-600 text-white rounded-lg text-sm font-semibold">
                            👤 {{ $solicitud->user->name }}
                        </span>
                    </div>
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
                            $colorTipo = 'bg-yellow-600';
                        }
                    @endphp
                    <div class="bg-gray-50 p-4 rounded-lg">
                        <label class="block text-sm font-bold text-gray-700 mb-2">Tipo de Solicitud:</label>
                        <span class="px-3 py-1 {{ $colorTipo }} text-white rounded-lg text-sm font-semibold">
                            {{ $etiquetaTipo }}
                        </span>
                    </div>
                    <div class="bg-gray-50 p-4 rounded-lg">
                        <label class="block text-sm font-bold text-gray-700 mb-2">Estado:</label>
                        @if($solicitud->estado == 'pendiente')
                            <span class="px-3 py-1 bg-yellow-100 text-yellow-800 font-semibold rounded-lg">
                                ⏳ Pendiente
                            </span>
                        @elseif($solicitud->estado == 'en_proceso')
                            <span class="px-3 py-1 bg-blue-100 text-blue-800 font-semibold rounded-lg">
                                🔄 En Proceso
                            </span>
                        @elseif($solicitud->estado == 'finalizada')
                            <span class="px-3 py-1 bg-green-100 text-green-800 font-semibold rounded-lg">
                                ✅ Finalizada
                            </span>
                        @elseif($solicitud->estado == 'rechazada')
                            <span class="px-3 py-1 bg-red-100 text-red-800 font-semibold rounded-lg">
                                ❌ Rechazada
                            </span>
                        @else
                            <span class="px-3 py-1 bg-gray-100 text-gray-800 font-semibold rounded-lg">
                                {{ ucfirst($solicitud->estado) }}
                            </span>
                        @endif
                    </div>
                    <div class="bg-gray-50 p-4 rounded-lg">
                        <label class="block text-sm font-bold text-gray-700 mb-2">Fecha:</label>
                        {{ $solicitud->created_at->format('d/m/Y H:i') }}
                    </div>
                </div>
            </div>
        </div>

        <!-- Items solicitados -->
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
            <div class="p-6 bg-white border-b border-gray-200">
                <h2 class="text-2xl font-bold text-gray-800 mb-4">Ítems Solicitados</h2>
                
                @php
                    $itemsTabla = $solicitud->items;
                    $itemsJson = [];
                    if ($itemsTabla->isEmpty() && strpos($solicitud->descripcion, 'Items solicitados:') !== false) {
                        $partes = explode('Items solicitados:', $solicitud->descripcion);
                        $itemsJsonString = trim($partes[1] ?? '');
                        $itemsJson = json_decode($itemsJsonString, true) ?? [];
                    }
                @endphp

                @if($itemsTabla->isNotEmpty() || !empty($itemsJson))
                    <div class="overflow-x-auto">
                        <table class="min-w-full border-collapse border border-gray-300">
                            <thead class="
                                @if($solicitud->tipo_solicitud === 'estandar') bg-green-600
                                @elseif($solicitud->tipo_solicitud === 'pedido_mensual') bg-blue-600
                                @elseif($solicitud->tipo_solicitud === 'salida_insumos') bg-yellow-600
                                @endif text-white">
                                <tr>
                                    @if($solicitud->tipo_solicitud === 'estandar')
                                        <th class="border border-gray-300 px-4 py-2 text-left">REFERENCIA</th>
                                        <th class="border border-gray-300 px-4 py-2 text-center">UNIDAD</th>
                                        <th class="border border-gray-300 px-4 py-2 text-left">DESCRIPCIÓN</th>
                                        <th class="border border-gray-300 px-4 py-2 text-center">CANTIDAD</th>
                                    @elseif($solicitud->tipo_solicitud === 'pedido_mensual')
                                        <th class="border border-gray-300 px-4 py-2 text-left">CÓDIGO</th>
                                        <th class="border border-gray-300 px-4 py-2 text-left">DESCRIPCIÓN</th>
                                        <th class="border border-gray-300 px-4 py-2 text-center">CANTIDAD</th>
                                        <th class="border border-gray-300 px-4 py-2 text-left">BODEGA</th>
                                    @elseif($solicitud->tipo_solicitud === 'salida_insumos')
                                        <th class="border px-4 py-2">CÓDIGO</th>
                                        <th class="border px-4 py-2">DESCRIPCIÓN</th>
                                        <th class="border px-4 py-2">CANTIDAD</th>
                                        <th class="border px-4 py-2">ÁREA CONSUMO</th>
                                        <th class="border px-4 py-2">CENTRO DE COSTOS</th>
                                    @endif
                                </tr>
                            </thead>
                            <tbody class="bg-white">
                                @if($itemsTabla->isNotEmpty())
                                    @foreach($itemsTabla as $item)
                                        @if($solicitud->tipo_solicitud === 'estandar')
                                            <tr class="hover:bg-gray-50">
                                                <td class="border border-gray-300 px-4 py-2">{{ $item->referencia ?? '-' }}</td>
                                                <td class="border border-gray-300 px-4 py-2 text-center">{{ $item->unidad ?? '-' }}</td>
                                                <td class="border border-gray-300 px-4 py-2">{{ $item->descripcion ?? '-' }}</td>
                                                <td class="border border-gray-300 px-4 py-2 text-center">{{ $item->cantidad ?? '-' }}</td>
                                            </tr>
                                        @elseif($solicitud->tipo_solicitud === 'pedido_mensual')
                                            <tr class="hover:bg-gray-50">
                                                <td class="border border-gray-300 px-4 py-2">{{ $item->codigo ?? '-' }}</td>
                                                <td class="border border-gray-300 px-4 py-2">{{ $item->descripcion ?? '-' }}</td>
                                                <td class="border border-gray-300 px-4 py-2 text-center">{{ $item->cantidad ?? '-' }}</td>
                                                <td class="border border-gray-300 px-4 py-2">{{ $item->bodega ?? '-' }}</td>
                                            </tr>
                                        @elseif($solicitud->tipo_solicitud === 'salida_insumos')
                                            <tr>
                                                <td class="border px-4 py-2">{{ $item->codigo ?? '-' }}</td>
                                                <td class="border px-4 py-2">{{ $item->descripcion ?? '-' }}</td>
                                                <td class="border px-4 py-2">{{ $item->cantidad ?? '-' }}</td>
                                                <td class="border px-4 py-2">{{ $item->area_consumo ?? '-' }}</td>
                                                <td class="border px-4 py-2">{{ $item->centro_costos_item ?? '-' }}</td>
                                            </tr>
                                        @endif
                                    @endforeach
                                @else
                                    @foreach($itemsJson as $item)
                                        @if($solicitud->tipo_solicitud === 'estandar')
                                            <tr class="hover:bg-gray-50">
                                                <td class="border border-gray-300 px-4 py-2">{{ $item['referencia'] ?? '-' }}</td>
                                                <td class="border border-gray-300 px-4 py-2 text-center">{{ $item['unidad'] ?? '-' }}</td>
                                                <td class="border border-gray-300 px-4 py-2">{{ $item['descripcion'] ?? '-' }}</td>
                                                <td class="border border-gray-300 px-4 py-2 text-center">{{ $item['cantidad'] ?? '-' }}</td>
                                            </tr>
                                        @elseif($solicitud->tipo_solicitud === 'pedido_mensual')
                                            <tr class="hover:bg-gray-50">
                                                <td class="border border-gray-300 px-4 py-2">{{ $item['codigo'] ?? '-' }}</td>
                                                <td class="border border-gray-300 px-4 py-2">{{ $item['descripcion'] ?? '-' }}</td>
                                                <td class="border border-gray-300 px-4 py-2 text-center">{{ $item['cantidad'] ?? '-' }}</td>
                                                <td class="border border-gray-300 px-4 py-2">{{ $item['bodega'] ?? '-' }}</td>
                                            </tr>
                                        @elseif($solicitud->tipo_solicitud === 'salida_insumos')
                                            <tr>
                                                <td class="border px-4 py-2">{{ $item['codigo'] ?? '-' }}</td>
                                                <td class="border px-4 py-2">{{ $item['descripcion'] ?? '-' }}</td>
                                                <td class="border px-4 py-2">{{ $item['cantidad'] ?? '-' }}</td>
                                                <td class="border px-4 py-2">{{ $item['area_consumo'] ?? '-' }}</td>
                                                <td class="border px-4 py-2">{{ $item['centro_costos_item'] ?? '-' }}</td>
                                            </tr>
                                        @endif
                                    @endforeach
                                @endif
                            </tbody>
                        </table>
                    </div>
                @else
                    <p class="text-gray-500 text-center py-6">No hay items registrados.</p>
                @endif

                @php
                    $observaciones = $solicitud->descripcion;
                    if (strpos($observaciones, 'Items solicitados:') !== false) {
                        $observaciones = trim(explode('Items solicitados:', $observaciones)[0]);
                    }
                @endphp

                @if($observaciones && $observaciones != '')
                    <div class="mt-4 p-4 bg-gray-50 rounded-lg">
                        <h4 class="font-semibold text-gray-700 mb-2">Observaciones:</h4>
                        <p class="text-gray-600">{{ $observaciones }}</p>
                    </div>
                @endif

                @if($solicitud->archivo)
                    <div class="mt-4">
                        <a href="{{ url('storage/' . $solicitud->archivo) }}" 
                           target="_blank"
                           class="inline-flex items-center px-4 py-2 bg-gray-600 text-white rounded-lg hover:bg-gray-700 transition">
                            📎 Ver archivo adjunto
                        </a>
                    </div>
                @endif
            </div>
        </div>

        <!-- Comentarios -->
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
            <div class="p-6 bg-white">
                <h2 class="text-2xl font-bold text-gray-800 mb-6">Comentarios</h2>
                
                @forelse($solicitud->comentarios as $comentario)
                    <div class="mb-4 p-4 rounded-lg border-l-4 
                        {{ $comentario->user->esAdminCompras() ? 'bg-green-50 border-green-500' : 'bg-purple-50 border-purple-500' }}">
                        <div class="flex justify-between items-start mb-2">
                            <div>
                                <span class="font-bold text-gray-800">{{ $comentario->user->name }}</span>
                                @if($comentario->user->esAdminCompras())
                                    <span class="ml-2 px-2 py-1 bg-green-600 text-white text-xs font-semibold rounded">Admin</span>
                                @endif
                            </div>
                            <span class="text-sm text-gray-500">{{ $comentario->created_at->format('d/m/Y H:i') }}</span>
                        </div>
                        <p class="text-gray-700">{{ $comentario->comentario }}</p>
                    </div>
                @empty
                    <p class="text-gray-500 text-center py-6">
                        No hay comentarios aún. ¡Sé el primero en comentar!
                    </p>
                @endforelse

                <!-- Formulario de comentario -->
                <div class="mt-6 p-6 bg-gray-50 rounded-lg">
                    <h3 class="text-lg font-bold text-gray-800 mb-4">Agregar Comentario</h3>
                    <form action="{{ route('comentarios.store', $solicitud) }}" method="POST">
                        @csrf
                        <div class="mb-4">
                            <textarea name="comentario" 
                                      rows="4"
                                      class="w-full px-4 py-2 border-2 border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                                      placeholder="Escribe tu comentario aquí..." 
                                      required></textarea>
                            @error('comentario')
                                <span class="text-red-600 text-sm">{{ $message }}</span>
                            @enderror
                        </div>
                        <button type="submit" class="px-6 py-2 bg-blue-600 text-white font-semibold rounded-lg hover:bg-blue-700 transition">
                            Enviar Comentario
                        </button>
                    </form>
                </div>

                <!-- Cambiar estado (solo admin) - MOVIDO AQUÍ ABAJO -->
                @if(Auth::user()->esAdminCompras())
                    <div class="mt-6 bg-yellow-50 border-2 border-yellow-400 rounded-lg p-6">
                        <h3 class="text-lg font-bold text-yellow-800 mb-3">Cambiar Estado de la Solicitud</h3>
                        <form action="{{ route('solicitudes.updateStatus', $solicitud) }}" method="POST">
                            @csrf
                            @method('PATCH')
                            
                            <!-- Selector de estado -->
                            <div class="mb-4">
                                <label for="estado" class="block text-sm font-bold text-gray-700 mb-2">Nuevo Estado:</label>
                                <select name="estado" id="estado" class="w-full px-4 py-2 border-2 border-gray-300 rounded-lg focus:ring-2 focus:ring-yellow-500">
                                    <option value="pendiente" {{ $solicitud->estado == 'pendiente' ? 'selected' : '' }}>⏳ Pendiente</option>
                                    <option value="en_proceso" {{ $solicitud->estado == 'en_proceso' ? 'selected' : '' }}>🔄 En Proceso</option>
                                    <option value="finalizada" {{ $solicitud->estado == 'finalizada' ? 'selected' : '' }}>✅ Finalizada</option>
                                    <option value="rechazada" {{ $solicitud->estado == 'rechazada' ? 'selected' : '' }}>❌ Rechazada</option>
                                </select>
                            </div>

                            <!-- boton actualizar estado -->
                    
                            <button type="submit" class="w-full px-6 py-3 bg-yellow-600 text-white font-semibold rounded-lg hover:bg-yellow-700 transition shadow-lg">
                                📧 Actualizar Estado y Notificar al Usuario
                            </button>
                        </form>
                    </div>
                @endif

            </div>
        </div>

    </div>
</div>
@endsection
