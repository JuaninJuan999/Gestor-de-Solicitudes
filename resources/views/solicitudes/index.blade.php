@extends('layouts.app')

@section('content')

<!-- === FONDO FIJO === -->
<div class="fixed-bg-image"></div>
<div class="fixed-bg-overlay"></div>

<style>
    /* Fondo fijo */
    .fixed-bg-image {
        position: fixed; top: 0; left: 0; width: 100%; height: 100%;
        background-image: url('{{ asset('images/create-solicitud.jpg') }}');
        background-size: cover; background-position: center; background-repeat: no-repeat;
        z-index: -2;
    }
    .fixed-bg-overlay {
        position: fixed; top: 0; left: 0; width: 100%; height: 100%;
        background-color: rgba(0, 0, 0, 0.4);
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
        
        <!-- === BOTÓN VOLVER Y CREAR === -->
        <div class="mb-6 flex flex-col sm:flex-row justify-between items-center gap-4">
            <a href="{{ route('dashboard') }}" class="btn-back-dashboard shadow-sm">
                <i class="bi bi-chevron-left"></i> Volver al Dashboard
            </a>
            
            <a href="{{ route('solicitudes.create') }}" class="px-6 py-2.5 bg-blue-600 text-white font-semibold rounded-lg hover:bg-blue-700 transition shadow-md flex items-center gap-2">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                Registrar Nueva Solicitud
            </a>
        </div>

        {{-- ENCABEZADO Y TOTALES --}}
        <div class="flex flex-col md:flex-row justify-between items-center mb-6 gap-4">
            <h2 class="text-3xl font-bold text-white drop-shadow-md">
                Mis <span class="text-purple-200">Solicitudes</span>
            </h2>
            <div class="flex items-center gap-3">
                <span class="px-4 py-2 bg-white/70 backdrop-blur-md border border-white/50 text-gray-800 rounded-lg shadow-sm font-semibold">
                   Total Registros: <span class="text-purple-700 font-bold">{{ $solicitudes->total() }}</span>
                </span>
            </div>
        </div>

        {{-- MENSAJES DE ÉXITO --}}
        @if(session('success'))
            <div class="mb-6 p-4 bg-green-100/90 backdrop-blur-sm border-l-4 border-green-500 text-green-700 rounded-r-lg shadow-sm flex items-center justify-between">
                <div class="flex items-center gap-2">
                    <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path></svg>
                    {{ session('success') }}
                </div>
                <button onclick="this.parentElement.remove()" class="text-green-600 hover:text-green-800">&times;</button>
            </div>
        @endif

        {{-- SECCIÓN DE FILTROS (SIN FECHAS) --}}
        <div class="rounded-xl shadow-lg border border-white/40 p-6 mb-8 bg-white/70 backdrop-blur-md">
            <div class="mb-4 flex items-center gap-2 border-b border-gray-300/50 pb-2">
                <svg class="w-5 h-5 text-purple-700" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z"></path></svg>
                <h3 class="text-lg font-bold text-gray-800">Filtros de Búsqueda</h3>
            </div>

            <form method="GET" action="{{ route('solicitudes.index') }}" class="space-y-4">
                {{-- Grid de 3 columnas: Estado | Tipo | Botones --}}
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4 items-end">
                    
                    {{-- Estado --}}
                    <div>
                        <label for="estado" class="block text-xs font-bold text-gray-700 uppercase tracking-wide mb-1">Estado</label>
                        <select name="estado" id="estado" class="w-full px-3 py-2 bg-white/80 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent text-sm transition">
                            <option value="">Todos</option>
                            <option value="pendiente" {{ request('estado') == 'pendiente' ? 'selected' : '' }}>⏳ Pendiente</option>
                            <option value="aprobado_supervisor" {{ request('estado') == 'aprobado_supervisor' ? 'selected' : '' }}>🔔 Aprobada Supervisor</option>
                            <option value="en_proceso" {{ request('estado') == 'en_proceso' ? 'selected' : '' }}>🔄 En Proceso</option>
                            <option value="finalizada" {{ request('estado') == 'finalizada' ? 'selected' : '' }}>✅ Finalizada</option>
                            <option value="rechazada" {{ request('estado') == 'rechazada' ? 'selected' : '' }}>❌ Rechazada</option>
                        </select>
                    </div>

                    {{-- Tipo --}}
                    <div>
                        <label for="tipo_solicitud" class="block text-xs font-bold text-gray-700 uppercase tracking-wide mb-1">Tipo Solicitud</label>
                        <select name="tipo_solicitud" id="tipo_solicitud" class="w-full px-3 py-2 bg-white/80 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent text-sm transition">
                            <option value="">Todos</option>
                            <option value="estandar" {{ request('tipo_solicitud') == 'estandar' ? 'selected' : '' }}>Estandar</option>
                            <option value="traslado_bodegas" {{ request('tipo_solicitud') == 'traslado_bodegas' ? 'selected' : '' }}>Traslado Bodegas</option>
                            <option value="solicitud_pedidos" {{ request('tipo_solicitud') == 'solicitud_pedidos' ? 'selected' : '' }}>Pedidos</option>
                            <option value="solicitud_mtto" {{ request('tipo_solicitud') == 'solicitud_mtto' ? 'selected' : '' }}>Mantenimiento</option>
                        </select>
                    </div>

                    {{-- Botones Filtro --}}
                    <div class="flex gap-2">
                        <a href="{{ route('solicitudes.index') }}" class="px-4 py-2 bg-white/80 text-gray-700 text-sm font-medium rounded-lg border border-gray-300 hover:bg-white transition flex-1 text-center">
                            Limpiar
                        </a>
                        <button type="submit" class="px-4 py-2 bg-purple-600 text-white text-sm font-medium rounded-lg hover:bg-purple-700 shadow-md transition flex items-center gap-2 flex-1 justify-center">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                            Filtrar
                        </button>
                    </div>
                </div>
            </form>
        </div>

        {{-- TABLA DE RESULTADOS --}}
        <div class="overflow-hidden shadow-xl sm:rounded-xl border border-white/40 bg-white/70 backdrop-blur-md">
            @if($solicitudes->isEmpty())
                <div class="text-center py-16">
                    <div class="inline-flex items-center justify-center w-16 h-16 rounded-full bg-white/50 mb-4">
                        <svg class="w-8 h-8 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path></svg>
                    </div>
                    <h3 class="text-lg font-medium text-gray-900">No tienes solicitudes registradas</h3>
                    <p class="mt-1 text-gray-600">¡Comienza creando una nueva solicitud!</p>
                </div>
            @else
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200/60 bg-transparent">
                        <thead class="bg-gray-50/50">
                            <tr>
                                <th scope="col" class="px-6 py-4 text-left text-xs font-bold text-gray-600 uppercase tracking-wider">Consecutivo</th>
                                <th scope="col" class="px-6 py-4 text-left text-xs font-bold text-gray-600 uppercase tracking-wider">Tipo</th>
                                <th scope="col" class="px-6 py-4 text-center text-xs font-bold text-gray-600 uppercase tracking-wider">Estado</th>
                                <th scope="col" class="px-6 py-4 text-left text-xs font-bold text-gray-600 uppercase tracking-wider">Fecha</th>
                                <th scope="col" class="px-6 py-4 text-center text-xs font-bold text-gray-600 uppercase tracking-wider">Acciones</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200/60">
                            @foreach($solicitudes as $solicitud)
                                {{-- FILA PRINCIPAL --}}
                                <tr class="hover:bg-white/40 transition-colors duration-150 group">
                                    
                                    {{-- Consecutivo (Botón Expandible) --}}
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <button onclick="toggleHistorial({{ $solicitud->id }})" class="flex items-center gap-2 text-sm font-bold text-purple-800 hover:text-purple-600 transition focus:outline-none">
                                            <span id="icon-{{ $solicitud->id }}" class="transform transition-transform duration-200">▶</span>
                                            {{ $solicitud->consecutivo }}
                                        </button>
                                    </td>

                                    {{-- Tipo --}}
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        @php
                                            $tipoClass = match($solicitud->tipo_solicitud) {
                                                'solicitud_mtto' => 'bg-purple-100 text-purple-800',
                                                'traslado_bodegas' => 'bg-blue-100 text-blue-800',
                                                'solicitud_pedidos' => 'bg-yellow-100 text-yellow-800',
                                                default => 'bg-gray-100 text-gray-800',
                                            };
                                            $tipoLabel = match($solicitud->tipo_solicitud) {
                                                'solicitud_mtto' => 'Solicitud Mtto',
                                                'traslado_bodegas' => 'Traslado Bodegas',
                                                'solicitud_pedidos' => 'Pedido',
                                                'estandar' => 'Estándar',
                                                default => ucfirst(str_replace('_', ' ', $solicitud->tipo_solicitud)),
                                            };
                                        @endphp
                                        <span class="px-3 py-1 inline-flex text-xs leading-5 font-semibold rounded-full {{ $tipoClass }}">
                                            {{ $tipoLabel }}
                                        </span>
                                    </td>

                                    {{-- Estado --}}
                                    <td class="px-6 py-4 whitespace-nowrap text-center">
                                        @if($solicitud->tipo_solicitud === 'estandar' && optional($solicitud->supervisor)->id)
                                            @switch($solicitud->estado)
                                                @case('pendiente')
                                                    <div class="flex flex-col items-center">
                                                        <span class="px-3 py-1 inline-flex items-center gap-1 text-xs leading-5 font-bold rounded-full bg-yellow-100 text-yellow-800 border border-yellow-200">
                                                            <span>⏳</span> Pendiente Aprobación
                                                        </span>
                                                        <span class="text-[10px] text-gray-500 mt-1 font-semibold">
                                                            (Supervisor: {{ optional($solicitud->supervisor)->name ?? 'N/A' }})
                                                        </span>
                                                    </div>
                                                    @break
                                                @case('aprobado_supervisor')
                                                    <div class="flex flex-col items-center">
                                                        <span class="px-3 py-1 inline-flex items-center gap-1 text-xs leading-5 font-bold rounded-full bg-blue-100 text-blue-800 border border-blue-200 shadow-sm ring-1 ring-blue-300">
                                                            <span>🔔</span> Solicitud Aprobada
                                                        </span>
                                                        <span class="text-[10px] text-blue-600 mt-1 font-bold">
                                                            (Por Supervisor)
                                                        </span>
                                                    </div>
                                                    @break
                                                @case('rechazada')
                                                    <span class="px-3 py-1 inline-flex items-center gap-1 text-xs leading-5 font-bold rounded-full bg-red-100 text-red-800 border border-red-200">
                                                        <span>✕</span> Rechazada
                                                    </span>
                                                    @break
                                                @case('en_proceso')
                                                    <span class="px-3 py-1 inline-flex items-center gap-1 text-xs leading-5 font-bold rounded-full bg-indigo-100 text-indigo-800 border border-indigo-200">
                                                        <span>↻</span> En Proceso
                                                    </span>
                                                    @break
                                                @case('finalizada')
                                                    <span class="px-3 py-1 inline-flex items-center gap-1 text-xs leading-5 font-bold rounded-full bg-green-100 text-green-800 border border-green-200">
                                                        <span>✓</span> Finalizada
                                                    </span>
                                                    @break
                                                @default
                                                    <span class="px-3 py-1 inline-flex items-center gap-1 text-xs leading-5 font-bold rounded-full bg-gray-100 text-gray-800 border border-gray-200">
                                                        {{ ucfirst(str_replace('_', ' ', $solicitud->estado)) }}
                                                    </span>
                                            @endswitch
                                        @else
                                            @switch($solicitud->estado)
                                                @case('pendiente')
                                                    <span class="px-3 py-1 inline-flex items-center gap-1 text-xs leading-5 font-bold rounded-full bg-yellow-50 text-yellow-700 border border-yellow-200">
                                                        <span>•</span> Pendiente
                                                    </span>
                                                    @break
                                                @case('en_proceso')
                                                    <span class="px-3 py-1 inline-flex items-center gap-1 text-xs leading-5 font-bold rounded-full bg-blue-100 text-blue-800 border border-blue-200">
                                                        <span>↻</span> En Proceso
                                                    </span>
                                                    @break
                                                @case('finalizada')
                                                    <span class="px-3 py-1 inline-flex items-center gap-1 text-xs leading-5 font-bold rounded-full bg-green-100 text-green-800 border border-green-200">
                                                        <span>✓</span> Finalizada
                                                    </span>
                                                    @break
                                                @case('rechazada')
                                                    <span class="px-3 py-1 inline-flex items-center gap-1 text-xs leading-5 font-bold rounded-full bg-red-100 text-red-800 border border-red-200">
                                                        <span>✕</span> Rechazada
                                                    </span>
                                                    @break
                                                @default
                                                    <span class="px-3 py-1 inline-flex items-center gap-1 text-xs leading-5 font-bold rounded-full bg-gray-100 text-gray-800 border border-gray-200">
                                                        {{ ucfirst(str_replace('_', ' ', $solicitud->estado)) }}
                                                    </span>
                                            @endswitch
                                        @endif
                                    </td>

                                    {{-- Fecha --}}
                                    <td class="px-6 py-4 whitespace-nowrap text-center">
                                        <div class="inline-flex flex-col items-center justify-center px-4 py-1.5 rounded-full bg-indigo-50 text-indigo-700 border border-indigo-100 shadow-sm">
                                            <span class="text-xs font-bold">{{ $solicitud->created_at->format('d/m/Y') }}</span>
                                            <span class="text-[10px] opacity-80 font-medium">{{ $solicitud->created_at->format('h:i a') }}</span>
                                        </div>
                                    </td>

                                    {{-- Acciones --}}
                                    <td class="px-6 py-4 whitespace-nowrap text-center text-sm font-medium">
                                        <a href="{{ route('solicitudes.show', $solicitud) }}" class="inline-flex items-center px-3 py-1.5 bg-white/80 border border-gray-300 rounded-md font-semibold text-xs text-gray-700 uppercase tracking-widest shadow-sm hover:bg-white hover:text-purple-700 focus:outline-none focus:ring-2 focus:ring-purple-500 transition ease-in-out duration-150">
                                            👁 Gestionar
                                        </a>
                                        <div class="mt-1 text-xs text-gray-500">
                                            {{ $solicitud->items ? $solicitud->items->count() : 0 }} items
                                        </div>
                                    </td>
                                </tr>

                                {{-- FILA EXPANDIBLE (HISTORIAL) --}}
                                <tr id="historial-{{ $solicitud->id }}" class="hidden bg-purple-50/30 border-t border-purple-100 transition-all duration-300">
                                    <td colspan="6" class="p-0">
                                        <div class="p-6 bg-white/50 backdrop-blur-sm shadow-inner">
                                            <h4 class="font-bold text-gray-800 mb-4 flex items-center gap-2 text-sm uppercase tracking-wide">
                                                <svg class="w-5 h-5 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                                Línea de Tiempo
                                            </h4>

                                            @if($solicitud->historial->isEmpty())
                                                <p class="text-sm text-gray-500 italic pl-2">No hay movimientos registrados.</p>
                                            @else
                                                <div class="relative border-l-2 border-purple-200 ml-3 space-y-6">
                                                    @foreach($solicitud->historial as $evento)
                                                        <div class="ml-6 relative">
                                                            {{-- Punto en la línea --}}
                                                            <span class="absolute -left-[31px] top-1 flex items-center justify-center w-4 h-4 bg-white border-2 border-purple-500 rounded-full ring-4 ring-purple-50"></span>
                                                            
                                                            <div class="flex flex-col sm:flex-row sm:items-start sm:justify-between gap-1">
                                                                <div>
                                                                    <p class="text-sm font-bold text-gray-800">
                                                                        {{ ucfirst(str_replace('_', ' ', $evento->accion)) }}
                                                                    </p>
                                                                    @if($evento->detalle)
                                                                        <p class="text-sm text-gray-600 mt-1 bg-white/60 p-2 rounded border border-gray-100 inline-block">
                                                                            {{ $evento->detalle }}
                                                                        </p>
                                                                    @endif
                                                                </div>
                                                                <div class="text-right">
                                                                    <p class="text-xs font-bold text-gray-600">{{ $evento->created_at->diffForHumans() }}</p>
                                                                    <p class="text-[10px] text-gray-400">{{ $evento->created_at->format('d M Y, h:i a') }}</p>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    @endforeach
                                                </div>
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                {{-- Paginación --}}
                <div class="px-6 py-4 border-t border-gray-200/50 bg-gray-50/30">
                    {{ $solicitudes->links() }}
                </div>
            @endif
        </div>
    </div>
</div>

<script>
    function toggleHistorial(id) {
        const row = document.getElementById('historial-' + id);
        const icon = document.getElementById('icon-' + id);
        
        if (row.classList.contains('hidden')) {
            row.classList.remove('hidden');
            icon.style.transform = 'rotate(90deg)';
        } else {
            row.classList.add('hidden');
            icon.style.transform = 'rotate(0deg)';
        }
    }
</script>

@endsection
