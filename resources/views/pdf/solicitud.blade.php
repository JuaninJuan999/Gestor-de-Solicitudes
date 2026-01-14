<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Solicitud {{ $solicitud->consecutivo }}</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 20px;
            color: #333;
            font-size: 10px;
        }
        /* Header con Tabla para alinear logo y título */
        .header-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
            border-bottom: 2px solid #1a56db;
            padding-bottom: 10px;
        }
        .header-table td {
            border: none;
            vertical-align: middle;
            padding: 5px;
        }
        .logo-cell {
            width: 150px;
        }
        .title-cell {
            text-align: right;
        }
        .title-cell h1 {
            margin: 0;
            color: #1a56db;
            font-size: 18px; /* Un poco más pequeño para títulos largos */
            text-transform: uppercase;
        }
        .title-cell p {
            margin: 5px 0 0;
            font-size: 12px;
            color: #555;
        }

        .info-block {
            margin-bottom: 20px;
            padding: 10px;
            background: #f0f0f0;
            border: 1px solid #ddd;
            border-radius: 5px;
        }
        .info-block p {
            margin: 4px 0;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 15px;
            margin-bottom: 15px;
        }
        th {
            background-color: #1a56db;
            color: white;
            padding: 8px;
            text-align: left;
            font-weight: bold;
            border: 1px solid #ddd;
            text-transform: uppercase;
            font-size: 9px;
        }
        td {
            padding: 6px;
            border: 1px solid #ddd;
        }
        tr:nth-child(even) {
            background-color: #f9f9f9;
        }
        .section-title {
            margin-top: 20px;
            margin-bottom: 10px;
            font-weight: bold;
            color: #1a56db;
            border-bottom: 1px solid #1a56db;
            padding-bottom: 5px;
        }
        .observaciones {
            margin-top: 15px;
            padding: 10px;
            background: #fffbea;
            border-left: 4px solid #ffa500;
        }
        .text-center { text-align: center; }
    </style>
</head>
<body>
    @php
        // 1. Lógica para cargar la imagen en Base64
        $path = public_path('images/logos/logo2.png');
        $type = pathinfo($path, PATHINFO_EXTENSION);
        $data = '';
        
        if (file_exists($path)) {
            $data = file_get_contents($path);
            $base64 = 'data:image/' . $type . ';base64,' . base64_encode($data);
        }

        // 2. Lógica para el TÍTULO DINÁMICO
        $tituloDocumento = match($solicitud->tipo_solicitud) {
            'estandar'          => 'SOLICITUD DE COMPRA',
            'traslado_bodegas'  => 'TRASLADO ENTRE BODEGAS',
            'solicitud_pedidos' => 'SOLICITUD DE PEDIDO',
            'solicitud_mtto'    => 'SOLICITUD DE INSUMOS / SERVICIOS',
            default             => 'DETALLE DE SOLICITUD'
        };
    @endphp

    <!-- ENCABEZADO CON LOGO Y TÍTULO DINÁMICO -->
    <table class="header-table">
        <tr>
            <td class="logo-cell">
                @if(!empty($data))
                    <img src="{{ $base64 }}" alt="Logo Institucional" style="max-height: 70px; max-width: 150px;">
                @else
                    <strong>LOGO</strong>
                @endif
            </td>
            <td class="title-cell">
                <h1>{{ $tituloDocumento }}</h1>
                <p><strong>Consecutivo:</strong> {{ $solicitud->consecutivo }}</p>
            </td>
        </tr>
    </table>

    <div class="info-block">
        <h3 style="margin-top: 0;">Información General</h3>
        <p><strong>Título:</strong> {{ $solicitud->titulo }}</p>
        <p><strong>Usuario:</strong> {{ $solicitud->user->name ?? '-' }}</p>
        <p><strong>Área:</strong> {{ $solicitud->user->area ?? '-' }}</p>
        
        <!-- Centro de Costos General (con nombre si existe) -->
        <p>
            <strong>Centro de Costos General:</strong> 
            {{ $solicitud->centro_costos ?? '-' }}
            @if(isset($solicitud->centro_costos) && isset($centrosMap[$solicitud->centro_costos]))
                 - {{ $centrosMap[$solicitud->centro_costos] }}
            @endif
        </p>

        <p><strong>Estado:</strong> {{ ucfirst($solicitud->estado) }}</p>
        <p><strong>Tipo:</strong> 
            @if($solicitud->tipo_solicitud == 'estandar') Solicitud Estándar
            @elseif($solicitud->tipo_solicitud == 'traslado_bodegas') Traslados entre Bodegas
            @elseif($solicitud->tipo_solicitud == 'solicitud_pedidos') Solicitud de Pedidos
            @elseif($solicitud->tipo_solicitud == 'solicitud_mtto') Solicitud Insumos / Servicio
            @endif
        </p>

        @if($solicitud->presupuestado)
             <p><strong>¿Presupuestado?:</strong> {{ $solicitud->presupuestado }}</p>
        @endif

        @if($solicitud->tipo_solicitud == 'solicitud_mtto')
            <p><strong>Justificación:</strong> {{ $solicitud->justificacion ?? 'Sin justificación' }}</p>
        @endif

        <p><strong>Fecha:</strong> {{ $solicitud->created_at->format('d/m/Y H:i') }}</p>
    </div>

    <div class="section-title">Items Solicitados (Solo Revisados)</div>

    @if($itemsRevisados->isEmpty())
        <p style="color: #999; font-style: italic;">No hay ítems revisados en esta solicitud.</p>
    @else
        <table>
            <thead>
                <tr>
                    @if($solicitud->tipo_solicitud == 'estandar')
                        <th>CÓDIGO SIIMED</th>
                        <th class="text-center">UNIDAD</th>
                        <th>DESCRIPCIÓN</th>
                        <th class="text-center">CANTIDAD</th>
                        <th class="text-center">CENTRO DE COSTO</th>
                    @elseif($solicitud->tipo_solicitud == 'traslado_bodegas')
                        <th>CÓDIGO</th>
                        <th>DESCRIPCIÓN</th>
                        <th class="text-center">CANTIDAD</th>
                        <th>BODEGA</th>
                    @elseif($solicitud->tipo_solicitud == 'solicitud_pedidos')
                        <th>CÓDIGO</th>
                        <th>DESCRIPCIÓN</th>
                        <th class="text-center">CANTIDAD</th>
                        <th>ÁREA CONSUMO</th>
                        <th>CENTRO DE COSTOS</th>
                    @elseif($solicitud->tipo_solicitud == 'solicitud_mtto')
                        <th>DESCRIPCIÓN</th>
                        <th>ESPECIFICACIONES</th>
                        <th class="text-center">CANTIDAD</th>
                    @endif
                </tr>
            </thead>
            <tbody>
                @foreach($itemsRevisados as $item)
                    <tr>
                        @if($solicitud->tipo_solicitud == 'estandar')
                            <td>{{ $item->codigo ?? '-' }}</td>
                            <td class="text-center">{{ $item->unidad ?? '-' }}</td>
                            <td>{{ $item->descripcion ?? '-' }}</td>
                            <td class="text-center">{{ $item->cantidad ?? '-' }}</td>
                            <td class="text-center">
                                {{ $item->centro_costos_item ?? '-' }}
                                @if(isset($item->centro_costos_item) && isset($centrosMap[$item->centro_costos_item]))
                                    <br><small>{{ $centrosMap[$item->centro_costos_item] }}</small>
                                @endif
                            </td>
                        @elseif($solicitud->tipo_solicitud == 'traslado_bodegas')
                            <td>{{ $item->codigo ?? '-' }}</td>
                            <td>{{ $item->descripcion ?? '-' }}</td>
                            <td class="text-center">{{ $item->cantidad ?? '-' }}</td>
                            <td>
                                {{ $item->bodega ?? '-' }}
                                @if(isset($item->bodega) && isset($centrosMap[$item->bodega]))
                                    <br><small>{{ $centrosMap[$item->bodega] }}</small>
                                @endif
                            </td>
                        @elseif($solicitud->tipo_solicitud == 'solicitud_pedidos')
                            <td>{{ $item->codigo ?? '-' }}</td>
                            <td>{{ $item->descripcion ?? '-' }}</td>
                            <td class="text-center">{{ $item->cantidad ?? '-' }}</td>
                            <td>{{ $item->area_consumo ?? '-' }}</td>
                            <td>
                                {{ $item->centro_costos_item ?? '-' }}
                                @if(isset($item->centro_costos_item) && isset($centrosMap[$item->centro_costos_item]))
                                    <br><small>{{ $centrosMap[$item->centro_costos_item] }}</small>
                                @endif
                            </td>
                        @elseif($solicitud->tipo_solicitud == 'solicitud_mtto')
                            <td>{{ $item->descripcion ?? '-' }}</td>
                            <td>{{ $item->especificaciones ?? '-' }}</td>
                            <td class="text-center">{{ $item->cantidad ?? '-' }}</td>
                        @endif
                    </tr>
                @endforeach
            </tbody>
        </table>
    @endif

    @if(!empty($solicitud->descripcion))
        <div class="observaciones">
            <strong>Observaciones Generales:</strong>
            <p>{{ $solicitud->descripcion }}</p>
        </div>
    @endif

    <div style="margin-top: 40px; text-align: center; font-size: 10px; color: #999;">
        <p>Documento generado el {{ now()->format('d/m/Y H:i') }}</p>
    </div>
</body>
</html>
