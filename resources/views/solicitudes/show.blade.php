{{-- Vista: Detalle de solicitud con comentarios --}}
@extends('layouts.app')

@section('content')
<div class="py-12">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        
        <!-- Botón volver -->
        <div class="mb-6 flex items-center justify-between gap-4">
            <a href="{{ Auth::user()->esAdminCompras() ? route('admin.solicitudes.index') : route('solicitudes.index') }}" 
               class="px-6 py-2 bg-gray-600 text-white font-semibold rounded-lg hover:bg-gray-700 transition">
                ← Volver
            </a>

            @if(Auth::user()->esAdminCompras())
                <a href="{{ route('admin.solicitudes.pdf.revisados', $solicitud) }}"
                   class="px-6 py-2 bg-red-600 text-white font-semibold rounded-lg hover:bg-red-700 transition">
                    📄 Descargar PDF solo ítems revisados
                </a>
            @endif
        </div>

        @if(session('success'))
            <div class="mb-4 p-4 bg-green-100 border border-green-400 text-green-700 rounded-lg">
                {{ session('success') }}
            </div>
        @endif

        <!-- Encabezado de solicitud -->
        <div class="overflow-hidden mb-6">
            <div class="p-6 bg-white/70 rounded-2xl shadow-lg">
                <h1 class="text-3xl font-bold text-blue-600 mb-4">
                    {{ $solicitud->consecutivo ?? $solicitud->ticket_id }}
                </h1>
                
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <div class="bg-white/70 p-4 rounded-xl shadow">
                        <label class="block text-sm font-bold text-gray-700 mb-2">Departamento:</label>
                        <span class="px-3 py-1 bg-purple-600 text-white rounded-lg text-sm font-semibold">
                            {{ $solicitud->user->area ?? 'Sin departamento' }}
                        </span>
                    </div>

                    <div class="bg-white/70 p-4 rounded-xl shadow">
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
                        } elseif ($solicitud->tipo_solicitud == 'traslado_bodegas') {
                            $etiquetaTipo = 'Traslados entre Bodegas';
                            $colorTipo = 'bg-blue-700';
                        } elseif ($solicitud->tipo_solicitud == 'solicitud_pedidos') {
                            $etiquetaTipo = 'Solicitud de Pedidos';
                            $colorTipo = 'bg-yellow-600';
                        }
                    @endphp

                    <div class="bg-white/70 p-4 rounded-xl shadow">
                        <label class="block text-sm font-bold text-gray-700 mb-2">Tipo de Solicitud:</label>
                        <span class="px-3 py-1 {{ $colorTipo }} text-white rounded-lg text-sm font-semibold">
                            {{ $etiquetaTipo }}
                        </span>
                    </div>

                    <div class="bg-white/70 p-4 rounded-xl shadow">
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

                    <!-- Fecha como etiqueta -->
                    <div class="bg-white/70 p-4 rounded-xl shadow">
                        <label class="block text-sm font-bold text-gray-700 mb-2">Fecha:</label>
                        <span class="px-3 py-1 bg-gray-200 text-gray-800 rounded-lg text-sm font-semibold">
                            {{ $solicitud->created_at->format('d/m/Y h:i A') }}
                        </span>
                    </div>

                    <!-- Centro de costos como etiqueta -->
                    <div class="bg-white/70 p-4 rounded-xl shadow">
                        <label class="block text-sm font-bold text-gray-700 mb-2">Centro de Costos:</label>
                        <span class="px-3 py-1 bg-indigo-100 text-indigo-800 rounded-lg text-sm font-semibold">
                            {{ $solicitud->centro_costos ?? 'Sin centro de costos' }}
                        </span>
                    </div>

                    <!-- Título como etiqueta, ocupando toda la fila -->
                    <div class="bg-white/70 p-4 rounded-xl shadow md:col-span-3">
                        <label class="block text-sm font-bold text-gray-700 mb-2">Título de la Solicitud:</label>
                        <span class="px-3 py-1 bg-gray-100 text-gray-800 rounded-lg text-sm font-semibold inline-block">
                            {{ $solicitud->titulo ?? 'Sin título' }}
                        </span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Items solicitados -->
        <div class="overflow-hidden mb-6">
            <div class="p-6 bg-white/70 rounded-2xl shadow-lg">
                <h2 class="text-2xl font-bold text-gray-800 mb-4">Ítems Solicitados</h2>
                
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

                @if(Auth::user()->esAdminCompras())
                    <form action="{{ route('solicitudes.updateChecklist', $solicitud) }}" method="POST">
                        @csrf
                @endif

                @if($itemsTabla->isNotEmpty() || !empty($itemsJson))
                    <div class="overflow-x-auto bg-white/70 rounded-xl shadow mb-4">
                        @if($solicitud->tipo_solicitud == 'estandar')
                            <table class="min-w-full border-collapse border border-gray-300">
                                <thead class="bg-green-600 text-white">
                                    <tr>
                                        @if(Auth::user()->esAdminCompras())
                                            <th class="border border-gray-300 px-4 py-2 text-center">✔</th>
                                        @endif
                                        <th class="border border-gray-300 px-4 py-2 text-left">REFERENCIA</th>
                                        <th class="border border-gray-300 px-4 py-2 text-center">UNIDAD</th>
                                        <th class="border border-gray-300 px-4 py-2 text-left">DESCRIPCIÓN</th>
                                        <th class="border border-gray-300 px-4 py-2 text-center">CANTIDAD</th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white">
                                    @if($itemsTabla->isNotEmpty())
                                        @foreach($itemsTabla as $item)
                                            <tr class="hover:bg-gray-50 {{ $item->revisado ? 'bg-green-50' : '' }}">
                                                @if(Auth::user()->esAdminCompras())
                                                    <td class="border border-gray-300 px-4 py-2 text-center">
                                                        <input type="checkbox"
                                                               name="items_revisados[]"
                                                               value="{{ $item->id }}"
                                                               class="w-4 h-4"
                                                               {{ $item->revisado ? 'checked' : '' }}>
                                                    </td>
                                                @endif
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
                        @elseif($solicitud->tipo_solicitud == 'traslado_bodegas')
                            <table class="min-w-full border-collapse border border-gray-300">
                                <thead class="bg-blue-600 text-white">
                                    <tr>
                                        @if(Auth::user()->esAdminCompras())
                                            <th class="border border-gray-300 px-4 py-2 text-center">✔</th>
                                        @endif
                                        <th class="border border-gray-300 px-4 py-2 text-left">CÓDIGO</th>
                                        <th class="border border-gray-300 px-4 py-2 text-left">DESCRIPCIÓN</th>
                                        <th class="border border-gray-300 px-4 py-2 text-center">CANTIDAD</th>
                                        <th class="border border-gray-300 px-4 py-2 text-left">BODEGA</th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white">
                                    @if($itemsTabla->isNotEmpty())
                                        @foreach($itemsTabla as $item)
                                            <tr class="hover:bg-gray-50 {{ $item->revisado ? 'bg-green-50' : '' }}">
                                                @if(Auth::user()->esAdminCompras())
                                                    <td class="border border-gray-300 px-4 py-2 text-center">
                                                        <input type="checkbox"
                                                               name="items_revisados[]"
                                                               value="{{ $item->id }}"
                                                               class="w-4 h-4"
                                                               {{ $item->revisado ? 'checked' : '' }}>
                                                    </td>
                                                @endif
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
                        @elseif($solicitud->tipo_solicitud == 'solicitud_pedidos')
                            <table class="min-w-full border-collapse border border-gray-300">
                                <thead class="bg-yellow-600 text-white">
                                    <tr>
                                        @if(Auth::user()->esAdminCompras())
                                            <th class="border px-4 py-2 text-center">✔</th>
                                        @endif
                                        <th class="border px-4 py-2">CÓDIGO</th>
                                        <th class="border px-4 py-2">DESCRIPCIÓN</th>
                                        <th class="border px-4 py-2">CANTIDAD</th>
                                        <th class="border px-4 py-2">ÁREA CONSUMO</th>
                                        <th class="border px-4 py-2">CENTRO DE COSTOS</th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white">
                                    @if($itemsTabla->isNotEmpty())
                                        @foreach($itemsTabla as $item)
                                            <tr class="{{ $item->revisado ? 'bg-green-50' : '' }}">
                                                @if(Auth::user()->esAdminCompras())
                                                    <td class="border px-4 py-2 text-center">
                                                        <input type="checkbox"
                                                               name="items_revisados[]"
                                                               value="{{ $item->id }}"
                                                               class="w-4 h-4"
                                                               {{ $item->revisado ? 'checked' : '' }}>
                                                    </td>
                                                @endif
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

                    @if(Auth::user()->esAdminCompras() && $itemsTabla->isNotEmpty())
                        <div class="mt-2">
                            <button type="submit"
                                class="px-6 py-2 bg-green-600 text-white font-semibold rounded-lg hover:bg-green-700 transition">
                                Guardar checklist
                            </button>
                        </div>
                    @endif
                @else
                    <p class="text-gray-500 text-center py-6">No hay items registrados.</p>
                @endif

                @if(Auth::user()->esAdminCompras())
                    </form>
                @endif

                @if($observaciones && $observaciones != '')
                    <div class="mt-4 p-4 bg-white/70 rounded-xl shadow">
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
        <div class="overflow-hidden">
            <div class="p-6 bg-white/70 rounded-2xl shadow-lg">
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
                            <span class="text-sm text-gray-500">{{ $comentario->created_at->format('d/m/Y h:i A') }}</span>
                        </div>
                        <p class="text-gray-700">{{ $comentario->comentario }}</p>

                        @if($comentario->archivo)
                            <div class="mt-2">
                                <a href="{{ url('storage/' . $comentario->archivo) }}"
                                   target="_blank"
                                   class="inline-flex items-center px-3 py-1 bg-gray-600 text-white text-sm rounded hover:bg-gray-700 transition">
                                    📎 Ver archivo adjunto
                                </a>
                            </div>
                        @endif
                    </div>
                @empty
                    <p class="text-gray-500 text-center py-6">
                        No hay comentarios aún. ¡Sé el primero en comentar!
                    </p>
                @endforelse

                <!-- Formulario de comentario -->
                <div class="mt-6 p-6 bg-white/70 rounded-xl shadow">
                    <h3 class="text-lg font-bold text-gray-800 mb-4">Agregar Comentario</h3>
                    <form action="{{ route('comentarios.store', $solicitud) }}" method="POST" enctype="multipart/form-data">
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

                        <div class="mb-4">
                            <label class="block text-sm font-bold text-gray-700 mb-2">Adjuntar archivo (opcional):</label>
                            <input type="file" name="archivo"
                                   class="block w-full text-sm text-gray-700 border-2 border-gray-300 rounded-lg cursor-pointer">
                            @error('archivo')
                                <span class="text-red-600 text-sm">{{ $message }}</span>
                            @enderror
                        </div>

                        <button type="submit" class="px-6 py-2 bg-blue-600 text-white font-semibold rounded-lg hover:bg-blue-700 transition">
                            Enviar Comentario
                        </button>
                    </form>
                </div>

                <!-- Cambiar estado (solo admin) -->
                @if(Auth::user()->esAdminCompras())
                    <div class="mt-6 bg-yellow-50 border-2 border-yellow-400 rounded-lg p-6">
                        <h3 class="text-lg font-bold text-yellow-800 mb-3">Cambiar Estado de la Solicitud</h3>
                        <form action="{{ route('solicitudes.updateStatus', $solicitud) }}" method="POST">
                            @csrf
                            @method('PATCH')
                            
                            <div class="mb-4">
                                <label for="estado" class="block text-sm font-bold text-gray-700 mb-2">Nuevo Estado:</label>
                                <select name="estado" id="estado" class="w-full px-4 py-2 border-2 border-gray-300 rounded-lg focus:ring-2 focus:ring-yellow-500">
                                    <option value="pendiente" {{ $solicitud->estado == 'pendiente' ? 'selected' : '' }}>⏳ Pendiente</option>
                                    <option value="en_proceso" {{ $solicitud->estado == 'en_proceso' ? 'selected' : '' }}>🔄 En Proceso</option>
                                    <option value="finalizada" {{ $solicitud->estado == 'finalizada' ? 'selected' : '' }}>✅ Finalizada</option>
                                    <option value="rechazada" {{ $solicitud->estado == 'rechazada' ? 'selected' : '' }}>❌ Rechazada</option>
                                </select>
                            </div>

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
