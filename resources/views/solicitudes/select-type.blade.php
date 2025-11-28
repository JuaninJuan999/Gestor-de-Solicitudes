@extends('layouts.app')

@section('content')
<div class="max-w-6xl mx-auto sm:px-6 lg:px-8 py-12">
    <div class="bg-white bg-opacity-30 backdrop-blur-sm overflow-hidden shadow-lg sm:rounded-2xl">
        <div class="p-6 border-b border-gray-200">
            
            <h2 class="text-2xl font-bold text-blue-1000 mb-2">Nueva Solicitud</h2>
            <p class="text-blue-100 mb-8">Selecciona el tipo de solicitud que deseas crear:</p>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                
                <!-- Opción 1: Solicitud de Compra Estándar -->
                <a href="{{ route('solicitudes.create', ['tipo' => 'estandar']) }}" 
                   class="block p-6 bg-white bg-opacity-80 border-2 border-gray-300 rounded-lg hover:border-blue-500 hover:shadow-lg transition duration-200">
                    <div class="flex flex-col items-center text-center">
                        <div class="text-5xl mb-4">📋</div>
                        <h3 class="text-xl font-bold text-gray-800 mb-2">Solicitud de Compra Estándar</h3>
                        <p class="text-sm text-gray-600 mb-4">
                            Formato con referencia, unidad, descripción y cantidad
                        </p>
                        <span class="px-4 py-2 bg-blue-500 text-white rounded-lg font-semibold">
                            Seleccionar
                        </span>
                    </div>
                </a>

                <!-- Opción 2: Pedido Mensual -->
                <a href="{{ route('solicitudes.create', ['tipo' => 'pedido_mensual']) }}" 
                   class="block p-6 bg-white bg-opacity-80 border-2 border-gray-300 rounded-lg hover:border-green-500 hover:shadow-lg transition duration-200">
                    <div class="flex flex-col items-center text-center">
                        <div class="text-5xl mb-4">📦</div>
                        <h3 class="text-xl font-bold text-gray-800 mb-2">Pedido Mensual</h3>
                        <p class="text-sm text-gray-600 mb-4">
                            Formato con código, descripción, cantidad y bodega
                        </p>
                        <span class="px-4 py-2 bg-green-500 text-white rounded-lg font-semibold">
                            Seleccionar
                        </span>
                    </div>
                </a>

                <!-- Opción 3: Salida de Insumos -->
                <a href="{{ route('solicitudes.create', ['tipo' => 'salida_insumos']) }}" 
                   class="block p-6 bg-white bg-opacity-80 border-2 border-gray-300 rounded-lg hover:border-yellow-500 hover:shadow-lg transition duration-200">
                    <div class="flex flex-col items-center text-center">
                        <div class="text-5xl mb-4">📤</div>
                        <h3 class="text-xl font-bold text-gray-800 mb-2">Salida de Insumos</h3>
                        <p class="text-sm text-gray-600 mb-4">
                            Formato con código, área consumo y centro de costos
                        </p>
                        <span class="px-4 py-2 bg-yellow-500 text-white rounded-lg font-semibold">
                            Seleccionar
                        </span>
                    </div>
                </a>

            </div>

            <!-- Botón Cancelar -->
            <div class="mt-8 text-center">
                <a href="{{ route('solicitudes.index') }}" 
                   class="px-6 py-3 bg-gray-300 text-gray-800 rounded-lg hover:bg-gray-400 inline-block">
                    Cancelar
                </a>
            </div>

        </div>
    </div>
</div>
@endsection

