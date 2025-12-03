@extends('layouts.app')

@section('content')
<div style="background-image: url('/images/create-solicitud.jpg'); 
            background-size: cover; 
            background-position: center; 
            background-attachment: fixed; 
            min-height: calc(100vh - 80px); 
            padding-top: 3rem;">
    
    <div class="max-w-7xl mx-auto px-6 py-12">
        
        <!-- Botón Volver -->
        <div class="mb-8">
            <a href="{{ route('dashboard') }}" 
               class="inline-flex items-center px-6 py-3 bg-white bg-opacity-70 backdrop-blur-md border border-white/20 rounded-xl shadow-xl hover:shadow-2xl transition-all duration-300 hover:bg-opacity-80 text-gray-800 font-semibold">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
                </svg>
                Volver al Dashboard
            </a>
        </div>

        <!-- Tarjeta Principal -->
        <div class="bg-white bg-opacity-70 backdrop-blur-md border border-white/20 rounded-2xl shadow-2xl p-8">
            
            <h1 class="text-4xl font-bold text-gray-800 mb-8 text-center">
                📊 Reportes e Indicadores
            </h1>

            <!-- Formulario de Filtros -->
            <form method="GET" action="{{ route('admin.reportes') }}" class="bg-white/50 backdrop-blur-sm p-6 rounded-xl border border-white/30 mb-8">
                <div class="grid md:grid-cols-4 gap-4 mb-6">
                    
                    <!-- Fecha Inicio -->
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2">Fecha Inicio</label>
                        <input type="date" 
                               name="fecha_inicio" 
                               value="{{ request('fecha_inicio') }}" 
                               class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                    </div>

                    <!-- Fecha Fin -->
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2">Fecha Fin</label>
                        <input type="date" 
                               name="fecha_fin" 
                               value="{{ request('fecha_fin') }}" 
                               class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                    </div>

                    <!-- Estado -->
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2">Estado</label>
                        <select name="estado" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                            <option value="">Todos</option>
                            <option value="pendiente" {{ request('estado') == 'pendiente' ? 'selected' : '' }}>Pendiente</option>
                            <option value="en_proceso" {{ request('estado') == 'en_proceso' ? 'selected' : '' }}>En Proceso</option>
                            <option value="finalizada" {{ request('estado') == 'finalizada' ? 'selected' : '' }}>Finalizada</option>
                            <option value="rechazada" {{ request('estado') == 'rechazada' ? 'selected' : '' }}>Rechazada</option>
                        </select>
                    </div>

                    <!-- Tipo de Solicitud -->
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2">Tipo</label>
                        <select name="tipo_solicitud" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                            <option value="">Todos</option>
                            <option value="estandar" {{ request('tipo_solicitud') == 'estandar' ? 'selected' : '' }}>Solicitud Estándar</option>
                            <option value="traslado_bodegas" {{ request('tipo_solicitud') == 'traslado_bodegas' ? 'selected' : '' }}>Traslados entre Bodegas</option>
                            <option value="solicitud_pedidos" {{ request('tipo_solicitud') == 'solicitud_pedidos' ? 'selected' : '' }}>Solicitud de Pedidos</option>
                        </select>
                    </div>
                    
                </div>

                <!-- Botones de Acción -->
                <div class="flex flex-wrap gap-4">
                    <button type="submit" class="flex-1 min-w-[180px] bg-blue-600 hover:bg-blue-700 text-white font-semibold py-3 px-6 rounded-xl transition-all duration-300 shadow-lg hover:shadow-xl">
                        🔍 Filtrar Resultados
                    </button>

                    <!-- Exportar a Excel con filtros actuales -->
                    <a href="{{ route('admin.reportes.export', request()->query()) }}" 
                       class="flex-1 min-w-[180px] bg-green-600 hover:bg-green-700 text-white font-semibold py-3 px-6 rounded-xl text-center transition-all duration-300 shadow-lg hover:shadow-xl">
                        📥 Exportar a Excel
                    </a>

                    <!-- Exportar a PDF con filtros actuales (debes tener la ruta admin.reportes.exportPdf) -->
                    <a href="{{ route('admin.reportes.exportPdf', request()->query()) }}" 
                       class="flex-1 min-w-[180px] bg-red-600 hover:bg-red-700 text-white font-semibold py-3 px-6 rounded-xl text-center transition-all duration-300 shadow-lg hover:shadow-xl">
                        📄 Exportar a PDF
                    </a>

                    <a href="{{ route('admin.reportes') }}" 
                       class="px-6 py-3 bg-gray-500 hover:bg-gray-600 text-white font-semibold rounded-xl transition-all duration-300">
                        🔄 Limpiar Filtros
                    </a>
                </div>
            </form>

            <!-- Estadísticas -->
            <div class="grid md:grid-cols-5 gap-4 mb-8">
                <div class="bg-gradient-to-r from-blue-500 to-blue-600 text-white p-6 rounded-xl shadow-xl">
                    <h3 class="text-sm font-semibold opacity-90 mb-1">Total</h3>
                    <p class="text-3xl font-bold">{{ $stats['total'] }}</p>
                </div>
                <div class="bg-gradient-to-r from-yellow-500 to-yellow-600 text-white p-6 rounded-xl shadow-xl">
                    <h3 class="text-sm font-semibold opacity-90 mb-1">Pendientes</h3>
                    <p class="text-3xl font-bold">{{ $stats['pendiente'] }}</p>
                </div>
                <div class="bg-gradient-to-r from-purple-500 to-purple-600 text-white p-6 rounded-xl shadow-xl">
                    <h3 class="text-sm font-semibold opacity-90 mb-1">En Proceso</h3>
                    <p class="text-3xl font-bold">{{ $stats['en_proceso'] }}</p>
                </div>
                <div class="bg-gradient-to-r from-green-500 to-green-600 text-white p-6 rounded-xl shadow-xl">
                    <h3 class="text-sm font-semibold opacity-90 mb-1">Finalizadas</h3>
                    <p class="text-3xl font-bold">{{ $stats['finalizada'] }}</p>
                </div>
                <div class="bg-gradient-to-r from-red-500 to-red-600 text-white p-6 rounded-xl shadow-xl">
                    <h3 class="text-sm font-semibold opacity-90 mb-1">Rechazadas</h3>
                    <p class="text-3xl font-bold">{{ $stats['rechazada'] }}</p>
                </div>
            </div>

            {{-- Gráfico por estado --}}
            <div class="bg-white/70 backdrop-blur-md border border-white/30 rounded-xl shadow-2xl p-6 mb-10">
                <h3 class="text-xl font-bold text-gray-800 mb-4">
                    Distribución de solicitudes por estado
                </h3>
                <div class="h-72">
                    anvas id="chartEstados"></canvas>
                </div>
            </div>

            {{-- Gráfico por tipo --}}
            <div class="bg-white/70 backdrop-blur-md border border-white/30 rounded-xl shadow-2xl p-6 mb-10">
                <h3 class="text-xl font-bold text-gray-800 mb-4">
                    Distribución de solicitudes por tipo
                </h3>
                <div class="h-72">
                    anvas id="chartTipos"></canvas>
                </div>
            </div>

            {{-- Gráfico por mes --}}
            <div class="bg-white/70 backdrop-blur-md border border-white/30 rounded-xl shadow-2xl p-6 mb-10">
                <h3 class="text-xl font-bold text-gray-800 mb-4">
                    Solicitudes por mes ({{ now()->year }})
                </h3>
                <div class="h-72">
                    anvas id="chartMeses"></canvas>
                </div>
            </div>

            <!-- Tabla de Resultados -->
            @if($solicitudes->count() > 0)
                <div class="overflow-x-auto bg-white/60 backdrop-blur-sm rounded-xl border border-white/30 mb-4">
                    <table class="w-full">
                        <thead class="bg-white/80">
                            <tr>
                                <th class="px-6 py-4 text-left text-sm font-bold text-gray-800">Consecutivo</th>
                                <th class="px-6 py-4 text-left text-sm font-bold text-gray-800">Tipo</th>
                                <th class="px-6 py-4 text-left text-sm font-bold text-gray-800">Estado</th>
                                <th class="px-6 py-4 text-left text-sm font-bold text-gray-800">Usuario</th>
                                <th class="px-6 py-4 text-left text-sm font-bold text-gray-800">Área</th>
                                <th class="px-6 py-4 text-left text-sm font-bold text-gray-800">Fecha</th>
                                <th class="px-6 py-4 text-center text-sm font-bold text-gray-800">Items</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($solicitudes as $solicitud)
                                <tr class="border-t border-gray-200 hover:bg-white/50 transition-colors">
                                    <td class="px-6 py-4">
                                        <a href="{{ route('solicitudes.show', $solicitud->id) }}" 
                                           class="font-mono font-semibold text-blue-600 hover:underline">
                                            {{ $solicitud->consecutivo }}
                                        </a>
                                    </td>
                                    <td class="px-6 py-4">
                                        @php
                                            $tipoColor = [
                                                'estandar' => 'bg-green-100 text-green-800',
                                                'traslado_bodegas' => 'bg-blue-100 text-blue-800',
                                                'solicitud_pedidos' => 'bg-yellow-100 text-yellow-800',
                                            ][$solicitud->tipo_solicitud] ?? 'bg-gray-100 text-gray-800';
                                        @endphp
                                        <span class="px-3 py-1 {{ $tipoColor }} text-xs font-semibold rounded-full">
                                            {{ ucwords(str_replace('_', ' ', $solicitud->tipo_solicitud)) }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4">
                                        @php
                                            $estadoColor = [
                                                'pendiente' => 'bg-yellow-100 text-yellow-800',
                                                'en_proceso' => 'bg-blue-100 text-blue-800',
                                                'finalizada' => 'bg-green-100 text-green-800',
                                                'rechazada' => 'bg-red-100 text-red-800',
                                            ][$solicitud->estado] ?? 'bg-gray-100 text-gray-800';
                                        @endphp
                                        <span class="px-3 py-1 {{ $estadoColor }} text-xs font-semibold rounded-full">
                                            {{ ucfirst($solicitud->estado) }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 text-sm">{{ $solicitud->user->name ?? 'N/A' }}</td>
                                    <td class="px-6 py-4 text-sm">{{ $solicitud->area_solicitante ?? 'N/A' }}</td>
                                    <td class="px-6 py-4 text-sm text-gray-600">{{ $solicitud->created_at->format('d/m/Y') }}</td>
                                    <td class="px-6 py-4 text-center text-sm font-semibold">{{ $solicitud->items->count() }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <!-- Paginación -->
                <div class="mt-4">
                    {{ $solicitudes->links() }}
                </div>

                <div class="mt-2 text-sm text-gray-600 text-center">
                    Mostrando {{ $solicitudes->firstItem() }} - {{ $solicitudes->lastItem() }} de {{ $solicitudes->total() }} solicitudes
                </div>
            @else
                <div class="text-center py-16 bg-white/50 rounded-xl">
                    <div class="text-6xl mb-4">📋</div>
                    <div class="text-gray-600 text-lg mb-2">No hay solicitudes que coincidan con los filtros</div>
                    <p class="text-gray-400">Intenta ajustar las fechas o eliminar algunos filtros</p>
                </div>
            @endif

        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.1/dist/chart.umd.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function () {
        // Gráfico por estado
        const canvasEstados = document.getElementById('chartEstados');
        if (canvasEstados) {
            const ctx = canvasEstados.getContext('2d');
            const dataEstados = {
                labels: ['Pendiente', 'En Proceso', 'Finalizada', 'Rechazada'],
                datasets: [{
                    label: 'Cantidad de solicitudes',
                    data: [
                        {{ $stats['pendiente'] }},
                        {{ $stats['en_proceso'] }},
                        {{ $stats['finalizada'] }},
                        {{ $stats['rechazada'] }},
                    ],
                    backgroundColor: [
                        'rgba(234, 179, 8, 0.6)',
                        'rgba(59, 130, 246, 0.6)',
                        'rgba(34, 197, 94, 0.6)',
                        'rgba(248, 113, 113, 0.6)',
                    ],
                    borderColor: [
                        'rgba(202, 138, 4, 1)',
                        'rgba(37, 99, 235, 1)',
                        'rgba(22, 163, 74, 1)',
                        'rgba(220, 38, 38, 1)',
                    ],
                    borderWidth: 1,
                    borderRadius: 6,
                }]
            };

            new Chart(ctx, {
                type: 'bar',
                data: dataEstados,
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            display: true,
                            position: 'bottom',
                        },
                        tooltip: {
                            callbacks: {
                                label: function (context) {
                                    return context.raw + ' solicitudes';
                                }
                            }
                        }
                    },
                    scales: {
                        y: {
                            beginAtZero: true,
                            ticks: {
                                precision: 0
                            }
                        }
                    }
                }
            });
        }

        // Gráfico por tipo
        const canvasTipos = document.getElementById('chartTipos');
        if (canvasTipos) {
            const ctxTipos = canvasTipos.getContext('2d');
            const dataTipos = {
                labels: ['Solicitud Estándar', 'Traslados entre Bodegas', 'Solicitud de Pedidos'],
                datasets: [{
                    label: 'Cantidad de solicitudes',
                    data: [
                        {{ $statsTipos['estandar'] }},
                        {{ $statsTipos['traslado_bodegas'] }},
                        {{ $statsTipos['solicitud_pedidos'] }},
                    ],
                    backgroundColor: [
                        'rgba(34, 197, 94, 0.6)',
                        'rgba(59, 130, 246, 0.6)',
                        'rgba(234, 179, 8, 0.6)',
                    ],
                    borderColor: [
                        'rgba(22, 163, 74, 1)',
                        'rgba(37, 99, 235, 1)',
                        'rgba(202, 138, 4, 1)',
                    ],
                    borderWidth: 1,
                    borderRadius: 6,
                }]
            };

            new Chart(ctxTipos, {
                type: 'bar',
                data: dataTipos,
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            display: true,
                            position: 'bottom',
                        },
                        tooltip: {
                            callbacks: {
                                label: function (context) {
                                    return context.raw + ' solicitudes';
                                }
                            }
                        }
                    },
                    scales: {
                        y: {
                            beginAtZero: true,
                            ticks: {
                                precision: 0
                            }
                        }
                    }
                }
            });
        }

        // Gráfico por mes
        const canvasMeses = document.getElementById('chartMeses');
        if (canvasMeses) {
            const ctxMeses = canvasMeses.getContext('2d');
            const dataMeses = {
                labels: [
                    'Ene', 'Feb', 'Mar', 'Abr', 'May', 'Jun',
                    'Jul', 'Ago', 'Sep', 'Oct', 'Nov', 'Dic'
                ],
                datasets: [{
                    label: 'Solicitudes',
                    data: [
                        {{ $statsMeses[1] ?? 0 }},
                        {{ $statsMeses[2] ?? 0 }},
                        {{ $statsMeses[3] ?? 0 }},
                        {{ $statsMeses[4] ?? 0 }},
                        {{ $statsMeses[5] ?? 0 }},
                        {{ $statsMeses[6] ?? 0 }},
                        {{ $statsMeses[7] ?? 0 }},
                        {{ $statsMeses[8] ?? 0 }},
                        {{ $statsMeses[9] ?? 0 }},
                        {{ $statsMeses[10] ?? 0 }},
                        {{ $statsMeses[11] ?? 0 }},
                        {{ $statsMeses[12] ?? 0 }},
                    ],
                    fill: true,
                    backgroundColor: 'rgba(59, 130, 246, 0.2)',
                    borderColor: 'rgba(37, 99, 235, 1)',
                    borderWidth: 2,
                    tension: 0.3,
                    pointRadius: 4,
                    pointBackgroundColor: 'rgba(37, 99, 235, 1)',
                }]
            };

            new Chart(ctxMeses, {
                type: 'line',
                data: dataMeses,
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            display: true,
                            position: 'bottom',
                        },
                        tooltip: {
                            callbacks: {
                                label: function (context) {
                                    return context.raw + ' solicitudes';
                                }
                            }
                        }
                    },
                    scales: {
                        y: {
                            beginAtZero: true,
                            ticks: {
                                precision: 0
                            }
                        }
                    }
                }
            });
        }
    });
</script>
@endsection
