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
        
        <!-- === BOTÓN VOLVER AL DASHBOARD === -->
        <div class="mb-6">
            <a href="{{ route('dashboard') }}" class="btn-back-dashboard shadow-sm">
                <i class="bi bi-chevron-left"></i> Volver al Dashboard
            </a>
        </div>

        {{-- ENCABEZADO Y TOTALES --}}
        <div class="flex flex-col md:flex-row justify-between items-center mb-6 gap-4">
            <h2 class="text-3xl font-bold text-white drop-shadow-md">
                Centro de Control de <span class="text-purple-200">Solicitudes</span>
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

        {{-- SECCIÓN DE FILTROS --}}
        <div class="rounded-xl shadow-lg border border-white/40 p-6 mb-8 bg-white/70 backdrop-blur-md">
            <div class="mb-4 flex items-center gap-2 border-b border-gray-300/50 pb-2">
                <svg class="w-5 h-5 text-purple-700" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z"></path></svg>
                <h3 class="text-lg font-bold text-gray-800">Filtros de Búsqueda</h3>
            </div>

            <form method="GET" action="{{ route('admin.solicitudes.index') }}" class="space-y-4">
                <div class="grid grid-cols-1 md:grid-cols-5 gap-4 items-end">
                    
                    {{-- Estado --}}
                    <div>
                        <label for="estado" class="block text-xs font-bold text-gray-700 uppercase tracking-wide mb-1">Estado</label>
                        <select name="estado" id="estado" class="w-full px-3 py-2 bg-white/80 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent text-sm transition">
                            <option value="">Todos</option>
                            <option value="pendiente" {{ request('estado') == 'pendiente' ? 'selected' : '' }}>⏳ Pendiente</option>
                            <option value="en_proceso" {{ request('estado') == 'en_proceso' ? 'selected' : '' }}>🔄 En Proceso</option>
                            <option value="finalizada" {{ request('estado') == 'finalizada' ? 'selected' : '' }}>✅ Finalizada</option>
                            <option value="rechazada" {{ request('estado') == 'rechazada' ? 'selected' : '' }}>❌ Rechazada</option>
                        </select>
                    </div>

                    {{-- Área --}}
                    <div>
                        <label for="area" class="block text-xs font-bold text-gray-700 uppercase tracking-wide mb-1">Área</label>
                        <select name="area" id="area" class="w-full px-3 py-2 bg-white/80 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent text-sm transition">
                            <option value="">Todas</option>
                            @foreach($areas as $area)
                                <option value="{{ $area }}" {{ request('area') == $area ? 'selected' : '' }}>{{ $area }}</option>
                            @endforeach
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

                    {{-- Fechas --}}
                    <div>
                        <label for="fecha_desde" class="block text-xs font-bold text-gray-700 uppercase tracking-wide mb-1">Desde</label>
                        <input type="date" name="fecha_desde" value="{{ request('fecha_desde') }}" class="w-full px-3 py-2 bg-white/80 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 text-sm">
                    </div>
                    <div>
                        <label for="fecha_hasta" class="block text-xs font-bold text-gray-700 uppercase tracking-wide mb-1">Hasta</label>
                        <input type="date" name="fecha_hasta" value="{{ request('fecha_hasta') }}" class="w-full px-3 py-2 bg-white/80 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 text-sm">
                    </div>
                </div>

                {{-- Botones Filtro --}}
                <div class="flex justify-end gap-2 pt-2">
                    <a href="{{ route('admin.solicitudes.index') }}" class="px-4 py-2 bg-white/80 text-gray-700 text-sm font-medium rounded-lg border border-gray-300 hover:bg-white transition">
                        Limpiar
                    </a>
                    <button type="submit" class="px-4 py-2 bg-purple-600 text-white text-sm font-medium rounded-lg hover:bg-purple-700 shadow-md transition flex items-center gap-2">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                        Filtrar Resultados
                    </button>
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
                    <h3 class="text-lg font-medium text-gray-900">No se encontraron solicitudes</h3>
                    <p class="mt-1 text-gray-600">Intenta ajustar los filtros de búsqueda.</p>
                </div>
            @else
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200/60 bg-transparent">
                        <thead class="bg-gray-50/50">
                            <tr>
                                <th scope="col" class="px-6 py-4 text-left text-xs font-bold text-gray-600 uppercase tracking-wider">Consecutivo</th>
                                <th scope="col" class="px-6 py-4 text-left text-xs font-bold text-gray-600 uppercase tracking-wider">Tipo</th>
                                <th scope="col" class="px-6 py-4 text-center text-xs font-bold text-gray-600 uppercase tracking-wider">Estado</th>
                                <th scope="col" class="px-6 py-4 text-left text-xs font-bold text-gray-600 uppercase tracking-wider">Usuario / Área</th>
                                <th scope="col" class="px-6 py-4 text-left text-xs font-bold text-gray-600 uppercase tracking-wider">Fecha</th>
                                <th scope="col" class="px-6 py-4 text-center text-xs font-bold text-gray-600 uppercase tracking-wider">Acciones</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200/60">
                            @foreach($solicitudes as $solicitud)
                                <tr class="hover:bg-white/40 transition-colors duration-150">
                                    {{-- Consecutivo --}}
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="text-sm font-bold text-purple-800">
                                            {{ $solicitud->consecutivo }}
                                        </div>
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
                                                default => $solicitud->tipo_solicitud,
                                            };
                                        @endphp
                                        <span class="px-3 py-1 inline-flex text-xs leading-5 font-semibold rounded-full {{ $tipoClass }}">
                                            {{ $tipoLabel }}
                                        </span>
                                    </td>

                                    {{-- Estado --}}
                                    <td class="px-6 py-4 whitespace-nowrap text-center">
                                        @php
                                            $estadoClass = match($solicitud->estado) {
                                                'finalizada' => 'bg-green-100 text-green-800 border border-green-200',
                                                'en_proceso' => 'bg-blue-100 text-blue-800 border border-blue-200',
                                                'rechazada' => 'bg-red-100 text-red-800 border border-red-200',
                                                default => 'bg-yellow-100 text-yellow-800 border border-yellow-200',
                                            };
                                            $estadoIcon = match($solicitud->estado) {
                                                'finalizada' => '✓',
                                                'rechazada' => '✕',
                                                'en_proceso' => '↻',
                                                default => '⏳',
                                            };
                                        @endphp
                                        <span class="px-3 py-1 inline-flex items-center gap-1 text-xs leading-5 font-bold rounded-full {{ $estadoClass }}">
                                            <span>{{ $estadoIcon }}</span> {{ ucfirst(str_replace('_', ' ', $solicitud->estado)) }}
                                        </span>
                                    </td>

                                    {{-- Usuario y Área --}}
                                    <td class="px-6 py-4 whitespace-nowrap text-center">
                                        <div class="inline-flex flex-col items-center justify-center px-4 py-1.5 rounded-full bg-slate-100 text-slate-700 border border-slate-200 shadow-sm">
                                            <span class="text-xs font-bold">{{ strtoupper($solicitud->user->name ?? 'USUARIO') }}</span>
                                            <span class="text-[10px] text-slate-500 font-semibold tracking-wider">{{ $solicitud->user->area ?? 'SIN ÁREA' }}</span>
                                        </div>
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
@endsection
