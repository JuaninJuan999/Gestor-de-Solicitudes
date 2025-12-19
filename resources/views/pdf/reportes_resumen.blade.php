<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Reporte de Solicitudes</title>
    <style>
        @page {
            margin: 20px 30px;
        }
        body {
            font-family: DejaVu Sans, Arial, sans-serif;
            font-size: 11px;
            color: #333;
        }
        h1, h2, h3 {
            color: #1f2937;
            margin-bottom: 8px;
            margin-top: 15px;
        }
        .header {
            text-align: center;
            border-bottom: 2px solid #4b5563;
            padding-bottom: 10px;
            margin-bottom: 20px;
        }
        .section {
            margin-bottom: 25px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 5px;
        }
        th, td {
            border: 1px solid #d1d5db;
            padding: 5px 8px;
            text-align: left;
            vertical-align: top;
        }
        th {
            background-color: #f3f4f6;
            font-weight: bold;
            color: #374151;
        }
        .badge {
            display: inline-block;
            padding: 2px 6px;
            border-radius: 4px;
            font-size: 10px;
            font-weight: bold;
            text-transform: uppercase;
        }
        .badge-blue { background: #dbeafe; color: #1e40af; border: 1px solid #93c5fd; }
        .badge-green { background: #dcfce7; color: #166534; border: 1px solid #86efac; }
        .badge-yellow { background: #fef9c3; color: #854d0e; border: 1px solid #fde047; }
        .badge-red { background: #fee2e2; color: #991b1b; border: 1px solid #fca5a5; }
        
        /* Estilos para el detalle de solicitudes */
        .solicitud-container {
            border: 1px solid #9ca3af;
            margin-bottom: 20px;
            page-break-inside: avoid; /* Intenta no cortar una solicitud a mitad de página */
        }
        .solicitud-header {
            background-color: #e5e7eb;
            padding: 8px;
            border-bottom: 1px solid #9ca3af;
            display: table; /* Hack para layout tipo flex en PDF antiguos */
            width: 100%;
        }
        .solicitud-body {
            padding: 10px;
        }
        .info-row td {
            border: none;
            padding: 2px 5px;
        }
    </style>
</head>
<body>

    {{-- ENCABEZADO GENERAL --}}
    <div class="header">
        <h1 style="margin:0;">Reporte de Gestión de Solicitudes</h1>
        <p style="margin:2px 0; font-size:10px; color:#6b7280;">Generado el {{ now()->format('d/m/Y H:i') }}</p>
    </div>

    {{-- SECCIÓN 1: RESUMEN ESTADÍSTICO --}}
    <div class="section">
        <h2 style="border-bottom: 1px solid #ccc; padding-bottom: 5px;">1. Resumen Ejecutivo</h2>
        
        <table style="width: 100%; border: none;">
            <tr style="border: none;">
                <!-- Tabla de Totales -->
                <td style="width: 50%; border: none; padding-right: 20px;">
                    <h3>Por Estado</h3>
                    <table>
                        <thead>
                            <tr>
                                <th>Estado</th>
                                <th style="text-align: center;">Cant.</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td><span class="badge badge-yellow">Pendiente</span></td>
                                <td style="text-align: center;">{{ $stats['pendiente'] }}</td>
                            </tr>
                            <tr>
                                <td><span class="badge badge-blue">En Proceso</span></td>
                                <td style="text-align: center;">{{ $stats['en_proceso'] }}</td>
                            </tr>
                            <tr>
                                <td><span class="badge badge-green">Finalizada</span></td>
                                <td style="text-align: center;">{{ $stats['finalizada'] }}</td>
                            </tr>
                            <tr>
                                <td><span class="badge badge-red">Rechazada</span></td>
                                <td style="text-align: center;">{{ $stats['rechazada'] }}</td>
                            </tr>
                            <tr style="background-color: #f9fafb; font-weight: bold;">
                                <td>TOTAL</td>
                                <td style="text-align: center;">{{ $stats['total'] }}</td>
                            </tr>
                        </tbody>
                    </table>
                </td>

                <!-- Tabla de Tipos -->
                <td style="width: 50%; border: none;">
                    <h3>Por Tipo de Solicitud</h3>
                    <table>
                        <thead>
                            <tr>
                                <th>Tipo</th>
                                <th style="text-align: center;">Cant.</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td>Estándar</td>
                                <td style="text-align: center;">{{ $statsTipos['estandar'] }}</td>
                            </tr>
                            <tr>
                                <td>Traslado Bodegas</td>
                                <td style="text-align: center;">{{ $statsTipos['traslado_bodegas'] }}</td>
                            </tr>
                            <tr>
                                <td>Pedidos</td>
                                <td style="text-align: center;">{{ $statsTipos['solicitud_pedidos'] }}</td>
                            </tr>
                            <tr>
                                <td>Insumos / Mtto</td>
                                <td style="text-align: center;">{{ $statsTipos['solicitud_mtto'] }}</td>
                            </tr>
                        </tbody>
                    </table>
                </td>
            </tr>
        </table>
    </div>

    {{-- SECCIÓN 2: DETALLE DE SOLICITUDES --}}
    <div class="section">
        <h2 style="border-bottom: 1px solid #ccc; padding-bottom: 5px; page-break-before: always;">2. Detalle de Solicitudes</h2>

        @forelse($solicitudes as $solicitud)
            <div class="solicitud-container">
                {{-- Encabezado de la solicitud individual --}}
                <div class="solicitud-header">
                    <div style="float: left; width: 60%;">
                        <strong style="font-size: 13px;">{{ $solicitud->consecutivo }}</strong>
                        <span style="font-size: 10px; color: #555; margin-left: 10px;">
                            {{ $solicitud->created_at->format('d/m/Y h:i A') }}
                        </span>
                    </div>
                    <div style="float: right; width: 35%; text-align: right;">
                         @php
                            $estadoLabel = match($solicitud->estado) {
                                'finalizada' => 'Finalizada',
                                'en_proceso' => 'En Proceso',
                                'rechazada' => 'Rechazada',
                                default => 'Pendiente',
                            };
                            $estadoClass = match($solicitud->estado) {
                                'finalizada' => 'badge-green',
                                'en_proceso' => 'badge-blue',
                                'rechazada' => 'badge-red',
                                default => 'badge-yellow',
                            };
                        @endphp
                        <span class="badge {{ $estadoClass }}">{{ $estadoLabel }}</span>
                    </div>
                    <div style="clear: both;"></div>
                </div>

                <div class="solicitud-body">
                    {{-- Datos Generales --}}
                    <table style="width: 100%; border: none; margin-bottom: 10px;">
                        <tr class="info-row">
                            <td style="width: 15%; font-weight: bold;">Solicitante:</td>
                            <td style="width: 35%;">{{ $solicitud->user->name ?? 'N/A' }}</td>
                            <td style="width: 15%; font-weight: bold;">Área/Depto:</td>
                            <td style="width: 35%;">
                                {{ $solicitud->user->area ?? 'Sin área asignada' }}
                            </td>
                        </tr>
                        <tr class="info-row">
                            <td style="font-weight: bold;">Tipo:</td>
                            <td>
                                @php
                                    $tipoTexto = match($solicitud->tipo_solicitud) {
                                        'estandar' => 'Estándar',
                                        'traslado_bodegas' => 'Traslado de Bodegas',
                                        'solicitud_pedidos' => 'Pedido',
                                        'solicitud_mtto' => 'Insumos / Mantenimiento',
                                        default => $solicitud->tipo_solicitud
                                    };
                                @endphp
                                {{ $tipoTexto }}
                            </td>
                            <td style="font-weight: bold;">Justificación:</td>
                            <td>
                                <i>{{ $solicitud->descripcion ? Str::limit($solicitud->descripcion, 60) : 'Sin descripción' }}</i>
                            </td>
                        </tr>
                        {{-- Campos específicos de Mtto --}}
                        @if($solicitud->tipo_solicitud == 'solicitud_mtto')
                            <tr class="info-row">
                                <td style="font-weight: bold; color: #4b5563;">Función:</td>
                                <td colspan="3">
                                    {{ $solicitud->funcion_formato == 'insumos_activos' ? 'Insumos / Activos' : 'Servicios Presupuestados' }}
                                </td>
                            </tr>
                        @endif
                    </table>

                    {{-- Tabla de Ítems --}}
                    <table style="font-size: 10px;">
                        <thead>
                            <tr style="background-color: #f3f4f6;">
                                @if($solicitud->tipo_solicitud == 'estandar')
                                    <th width="15%">Ref.</th>
                                    <th width="50%">Descripción</th>
                                    <th width="15%">Unidad</th>
                                    <th width="10%" style="text-align: center;">Cant.</th>

                                @elseif($solicitud->tipo_solicitud == 'traslado_bodegas')
                                    <th width="15%">Código</th>
                                    <th width="40%">Descripción</th>
                                    <th width="30%">Bodega Destino</th>
                                    <th width="15%" style="text-align: center;">Cant.</th>

                                @elseif($solicitud->tipo_solicitud == 'solicitud_pedidos')
                                    <th width="15%">Código</th>
                                    <th width="40%">Descripción</th>
                                    <th width="30%">Área Consumo</th>
                                    <th width="15%" style="text-align: center;">Cant.</th>

                                @elseif($solicitud->tipo_solicitud == 'solicitud_mtto')
                                    <th width="40%">Descripción / Equipo</th>
                                    <th width="45%">Especificaciones / Daño</th>
                                    <th width="15%" style="text-align: center;">Cant.</th>
                                @endif
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($solicitud->items as $item)
                                <tr>
                                    @if($solicitud->tipo_solicitud == 'estandar')
                                        <td>{{ $item->referencia ?? '-' }}</td>
                                        <td>{{ $item->descripcion }}</td>
                                        <td>{{ $item->unidad ?? '-' }}</td>
                                        <td style="text-align: center;">{{ $item->cantidad }}</td>

                                    @elseif($solicitud->tipo_solicitud == 'traslado_bodegas')
                                        <td>{{ $item->codigo ?? '-' }}</td>
                                        <td>{{ $item->descripcion }}</td>
                                        <td>{{ $item->bodega ?? '-' }}</td>
                                        <td style="text-align: center;">{{ $item->cantidad }}</td>

                                    @elseif($solicitud->tipo_solicitud == 'solicitud_pedidos')
                                        <td>{{ $item->codigo ?? '-' }}</td>
                                        <td>{{ $item->descripcion }}</td>
                                        <td>{{ $item->area_consumo ?? '-' }}</td>
                                        <td style="text-align: center;">{{ $item->cantidad }}</td>

                                    @elseif($solicitud->tipo_solicitud == 'solicitud_mtto')
                                        <td>{{ $item->descripcion }}</td>
                                        <td>{{ $item->especificaciones ?? '-' }}</td>
                                        <td style="text-align: center;">{{ $item->cantidad }}</td>
                                    @endif
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        @empty
            <div style="text-align: center; padding: 20px; color: #666; border: 1px dashed #ccc;">
                <p>No se encontraron solicitudes con los filtros seleccionados.</p>
            </div>
        @endforelse
    </div>

</body>
</html>
