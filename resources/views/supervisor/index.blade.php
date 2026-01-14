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

    /* Estilo Glass Mejorado para la Tarjeta Principal */
    .glass-card-container {
        background: rgba(255, 255, 255, 0.85); /* Más sólido para mejor lectura */
        backdrop-filter: blur(12px);
        border: 1px solid rgba(255, 255, 255, 0.6);
        border-radius: 16px;
        box-shadow: 0 8px 32px 0 rgba(31, 38, 135, 0.37);
        overflow: hidden;
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

<div class="py-12">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        
        <!-- Botón Volver -->
        <div class="mb-6">
            <a href="{{ route('dashboard') }}" class="btn-back-dashboard shadow-sm">
                <i class="bi bi-chevron-left"></i> Volver al Dashboard
            </a>
        </div>

        <!-- Encabezado fuera de la tarjeta para dar contexto -->
        <div class="flex flex-col md:flex-row justify-between items-center mb-6 gap-4">
            <h2 class="text-3xl font-bold text-white drop-shadow-md">
                Panel de Aprobación <span class="text-indigo-200">Supervisores</span>
            </h2>
            <span class="px-4 py-2 bg-white/20 backdrop-blur-md text-white rounded-lg border border-white/30 font-semibold shadow-sm">
    @if($tab == 'historial')
        📋 Solicitudes Procesadas: {{ $solicitudes->total() }}
    @else
        ⏳ Pendientes por aprobar: {{ $solicitudes->total() }}
    @endif
</span>

        </div>

        <!-- Mensajes de Alerta -->
        @if(session('success'))
            <div class="mb-6 p-4 bg-green-100 border-l-4 border-green-500 text-green-700 rounded shadow-md flex items-center gap-2">
                ✅ {{ session('success') }}
            </div>
        @endif
        @if(session('error'))
            <div class="mb-6 p-4 bg-red-100 border-l-4 border-red-500 text-red-700 rounded shadow-md flex items-center gap-2">
                ⚠️ {{ session('error') }}
            </div>
        @endif

        <!-- TARJETA PRINCIPAL (GLASS CARD MEJORADA) -->
        <div class="glass-card-container">
            
            <!-- PESTAÑAS DE NAVEGACIÓN (NUEVO) -->
            <div class="flex border-b border-gray-200/50 bg-white/40 backdrop-blur-sm">
                <a href="{{ route('supervisor.index', ['tab' => 'pendientes']) }}" 
                   class="flex-1 py-4 text-center text-sm font-bold uppercase tracking-wider border-b-2 transition-colors duration-200 
                   {{ $tab == 'pendientes' ? 'border-indigo-600 text-indigo-700 bg-indigo-50/50' : 'border-transparent text-gray-500 hover:text-indigo-600 hover:bg-gray-50/30' }}">
                   ⏳ Pendientes
                </a>
                <a href="{{ route('supervisor.index', ['tab' => 'historial']) }}" 
                   class="flex-1 py-4 text-center text-sm font-bold uppercase tracking-wider border-b-2 transition-colors duration-200 
                   {{ $tab == 'historial' ? 'border-indigo-600 text-indigo-700 bg-indigo-50/50' : 'border-transparent text-gray-500 hover:text-indigo-600 hover:bg-gray-50/30' }}">
                   📂 Historial
                </a>
            </div>

            @if($solicitudes->isEmpty())
                <div class="text-center py-16">
                    <div class="inline-flex items-center justify-center w-20 h-20 bg-indigo-100 rounded-full mb-4">
                        <span class="text-4xl">{{ $tab == 'historial' ? '📂' : '🎉' }}</span>
                    </div>
                    <h3 class="text-2xl font-bold text-gray-800 mb-2">
                        {{ $tab == 'historial' ? 'Historial Vacío' : '¡Todo al día!' }}
                    </h3>
                    <p class="text-gray-600">
                        {{ $tab == 'historial' ? 'Aún no has procesado ninguna solicitud.' : 'No tienes solicitudes pendientes de aprobación en este momento.' }}
                    </p>
                </div>
            @else
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-indigo-600 text-white">
                            <tr>
                                <th class="px-6 py-4 text-left text-xs font-bold uppercase tracking-wider">Ticket</th>
                                <th class="px-6 py-4 text-left text-xs font-bold uppercase tracking-wider">Fecha</th>
                                <th class="px-6 py-4 text-left text-xs font-bold uppercase tracking-wider">Solicitante</th>
                                <th class="px-6 py-4 text-left text-xs font-bold uppercase tracking-wider">Detalle</th>
                                <th class="px-6 py-4 text-center text-xs font-bold uppercase tracking-wider">
                                    {{ $tab == 'historial' ? 'Estado' : 'Acciones' }}
                                </th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @foreach($solicitudes as $solicitud)
                                <tr class="hover:bg-indigo-50 transition duration-150">
                                    <td class="px-6 py-4 whitespace-nowrap font-bold text-indigo-700">
                                        {{ $solicitud->consecutivo }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600">
                                        {{ $solicitud->created_at->format('d/m/Y H:i') }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="text-sm font-medium text-gray-900">{{ $solicitud->user->name }}</div>
                                        <div class="text-xs text-gray-500 font-semibold">{{ $solicitud->area_solicitante ?? $solicitud->user->area }}</div>
                                    </td>
                                    <td class="px-6 py-4">
                                        <div class="text-sm text-gray-900 font-bold">{{ $solicitud->titulo }}</div>
                                        <div class="text-xs text-gray-500 truncate max-w-xs mb-1">{{ $solicitud->descripcion }}</div>
                                        <a href="{{ route('solicitudes.show', $solicitud) }}" target="_blank" class="text-indigo-600 hover:text-indigo-800 text-xs font-bold underline">
                                            Ver detalle completo
                                        </a>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-center text-sm font-medium">
                                        
                                        @if($tab == 'pendientes')
                                            <!-- ACCIONES (Solo visibles en Pendientes) -->
                                            <div class="flex justify-center gap-2">
                                                <!-- Botón APROBAR -->
                                                <form action="{{ route('supervisor.aprobar', $solicitud) }}" method="POST" onsubmit="return confirm('¿Aprobar solicitud? Pasará a Compras.')">
                                                    @csrf
                                                    <button type="submit" class="inline-flex items-center px-3 py-1.5 bg-green-100 text-green-700 border border-green-200 rounded-lg hover:bg-green-200 transition font-bold shadow-sm" title="Aprobar">
                                                        ✅ Aprobar
                                                    </button>
                                                </form>

                                                <!-- Botón RECHAZAR -->
                                                <button type="button" onclick="abrirModalRechazo('{{ $solicitud->id }}')" class="inline-flex items-center px-3 py-1.5 bg-red-100 text-red-700 border border-red-200 rounded-lg hover:bg-red-200 transition font-bold shadow-sm" title="Rechazar">
                                                    ❌ Rechazar
                                                </button>
                                            </div>
                                        @else
                                            <!-- ESTADO (Visible en Historial) -->
                                            <span class="px-3 py-1 inline-flex text-xs leading-5 font-semibold rounded-full 
                                                {{ $solicitud->estado == 'rechazada' ? 'bg-red-100 text-red-800' : 'bg-green-100 text-green-800' }}">
                                                {{ ucfirst(str_replace('_', ' ', $solicitud->estado)) }}
                                            </span>
                                            <div class="text-xs text-gray-400 mt-1">
                                                {{ $solicitud->updated_at->diffForHumans() }}
                                            </div>
                                        @endif

                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                <div class="px-6 py-4 border-t border-gray-200 bg-gray-50">
                    {{ $solicitudes->links() }}
                </div>
            @endif
        </div>
    </div>
</div>

<!-- Modal de Rechazo -->
<div id="modalRechazo" class="fixed inset-0 z-50 hidden overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
    <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
        <div class="fixed inset-0 bg-gray-900 bg-opacity-75 transition-opacity" aria-hidden="true"></div>
        <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>
        <div class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">
            
            <form id="formRechazo" method="POST" action="">
                @csrf
                <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                    <div class="sm:flex sm:items-start">
                        <div class="mx-auto flex-shrink-0 flex items-center justify-center h-12 w-12 rounded-full bg-red-100 sm:mx-0 sm:h-10 sm:w-10">
                            <svg class="h-6 w-6 text-red-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                            </svg>
                        </div>
                        <div class="mt-3 text-center sm:mt-0 sm:ml-4 sm:text-left w-full">
                            <h3 class="text-lg leading-6 font-medium text-gray-900" id="modal-title">Rechazar Solicitud</h3>
                            <div class="mt-2">
                                <p class="text-sm text-gray-500 mb-2">Motivo del rechazo (obligatorio):</p>
                                <textarea name="motivo" rows="3" class="w-full border-gray-300 rounded-md shadow-sm focus:border-red-500 focus:ring focus:ring-red-200" required placeholder="Explica por qué se rechaza..."></textarea>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                    <button type="submit" class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-red-600 text-base font-medium text-white hover:bg-red-700 focus:outline-none sm:ml-3 sm:w-auto sm:text-sm">
                        Confirmar
                    </button>
                    <button type="button" onclick="cerrarModalRechazo()" class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm">
                        Cancelar
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    function abrirModalRechazo(solicitudId) {
        const form = document.getElementById('formRechazo');
        form.action = `/supervisor/solicitudes/${solicitudId}/rechazar`;
        document.getElementById('modalRechazo').classList.remove('hidden');
    }
    function cerrarModalRechazo() {
        document.getElementById('modalRechazo').classList.add('hidden');
    }
</script>

@endsection
