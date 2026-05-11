@extends('layouts.app')

@section('content')

<style>
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

    .selection-card {
        background: rgba(255, 255, 255, 0.85);
        backdrop-filter: blur(5px);
        transition: all 0.3s ease;
        border: 1px solid rgba(255, 255, 255, 0.6);
        width: 100%;
        cursor: pointer;
        font: inherit;
        text-align: center;
    }
    .selection-card:hover {
        transform: translateY(-5px);
        background: #fff;
        box-shadow: 0 10px 20px rgba(0,0,0,0.2);
    }

    .tipo-form { margin: 0; height: 100%; }
    .tipo-form-wrap { height: 100%; min-height: 100%; }
</style>

<div class="relative z-10 py-12">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">

        <div class="mb-6">
            <a href="{{ route('dashboard', [], false) }}" class="btn-back-dashboard shadow-sm">
                <i class="bi bi-chevron-left"></i> Volver al Dashboard
            </a>
        </div>

        <div class="bg-white bg-opacity-70 overflow-hidden shadow-2xl sm:rounded-2xl"
             style="backdrop-filter: blur(10px);">
            <div class="p-8 border-b border-gray-200 text-center">

                <h2 class="text-3xl font-bold text-gray-800 mb-3">Nueva Solicitud</h2>
                <p class="text-gray-600 mb-10 text-lg">Selecciona el tipo de solicitud que deseas crear:</p>

                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">

                    <div class="tipo-form-wrap">
                    <form class="tipo-form block h-full" method="get" action="{{ route('solicitudes.create.tipo', ['tipo' => 'estandar'], false) }}">
                        <button type="submit" class="selection-card block p-6 rounded-xl group h-full min-h-[280px]">
                            <div class="flex flex-col items-center text-center h-full">
                                <div class="text-5xl mb-4 transform group-hover:scale-110 transition-transform">📋</div>
                                <h3 class="text-xl font-bold text-gray-800 mb-2">Solicitud de Compra Estándar</h3>
                                <p class="text-sm text-gray-600 mb-6 flex-grow">
                                    Formato con referencia, unidad, descripción y cantidad.
                                </p>
                                <span class="px-4 py-2 bg-blue-600 text-white rounded-lg font-semibold w-full group-hover:bg-blue-700 transition pointer-events-none">
                                    Seleccionar
                                </span>
                            </div>
                        </button>
                    </form>
                    </div>

                    <div class="tipo-form-wrap">
                    <form class="tipo-form block h-full" method="get" action="{{ route('solicitudes.create.tipo', ['tipo' => 'traslado_bodegas'], false) }}">
                        <button type="submit" class="selection-card block p-6 rounded-xl group h-full min-h-[280px]">
                            <div class="flex flex-col items-center text-center h-full">
                                <div class="text-5xl mb-4 transform group-hover:scale-110 transition-transform">📦</div>
                                <h3 class="text-xl font-bold text-gray-800 mb-2">Traslados entre Bodegas</h3>
                                <p class="text-sm text-gray-600 mb-6 flex-grow">
                                    Formato con código, descripción, cantidad y bodega de destino.
                                </p>
                                <span class="px-4 py-2 bg-green-600 text-white rounded-lg font-semibold w-full group-hover:bg-green-700 transition pointer-events-none">
                                    Seleccionar
                                </span>
                            </div>
                        </button>
                    </form>
                    </div>

                    <div class="tipo-form-wrap">
                    <form class="tipo-form block h-full" method="get" action="{{ route('solicitudes.create.tipo', ['tipo' => 'solicitud_pedidos'], false) }}">
                        <button type="submit" class="selection-card block p-6 rounded-xl group h-full min-h-[280px]">
                            <div class="flex flex-col items-center text-center h-full">
                                <div class="text-5xl mb-4 transform group-hover:scale-110 transition-transform">📤</div>
                                <h3 class="text-xl font-bold text-gray-800 mb-2">Solicitud de Pedidos</h3>
                                <p class="text-sm text-gray-600 mb-6 flex-grow">
                                    Formato para pedidos con código, área de consumo y centros de costo.
                                </p>
                                <span class="px-4 py-2 bg-yellow-500 text-white rounded-lg font-semibold w-full group-hover:bg-yellow-600 transition pointer-events-none">
                                    Seleccionar
                                </span>
                            </div>
                        </button>
                    </form>
                    </div>

                    <div class="tipo-form-wrap">
                    <form class="tipo-form block h-full" method="get" action="{{ route('solicitudes.create.tipo', ['tipo' => 'solicitud_mtto'], false) }}">
                        <button type="submit" class="selection-card block p-6 rounded-xl group h-full min-h-[280px]">
                            <div class="flex flex-col items-center text-center h-full">
                                <div class="text-5xl mb-4 transform group-hover:scale-110 transition-transform">🛠️</div>
                                <h3 class="text-xl font-bold text-gray-800 mb-2">Solicitud Insumos / Servicios</h3>
                                <p class="text-sm text-gray-600 mb-6 flex-grow">
                                    Formato para insumos, activos y servicios presupuestados con justificación.
                                </p>
                                <span class="px-4 py-2 bg-purple-600 text-white rounded-lg font-semibold w-full group-hover:bg-purple-700 transition pointer-events-none">
                                    Seleccionar
                                </span>
                            </div>
                        </button>
                    </form>
                    </div>

                </div>

                <div class="mt-10">
                    <a href="{{ route('solicitudes.index', [], false) }}"
                       class="px-8 py-3 bg-gray-200 text-gray-700 font-semibold rounded-lg hover:bg-gray-300 transition inline-block">
                        Cancelar
                    </a>
                </div>

            </div>
        </div>
    </div>
</div>
@endsection
