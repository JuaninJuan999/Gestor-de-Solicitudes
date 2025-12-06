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
        }
        .header {
            text-align: center;
            margin-bottom: 30px;
            border-bottom: 2px solid #333;
            padding-bottom: 10px;
        }
        .header h1 {
            margin: 0;
            color: #1a56db;
        }
        .info-block {
            margin-bottom: 20px;
            padding: 10px;
            background: #f0f0f0;
            border-radius: 5px;
        }
        .info-block p {
            margin: 5px 0;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
            margin-bottom: 20px;
        }
        th {
            background-color: #1a56db;
            color: white;
            padding: 10px;
            text-align: left;
            font-weight: bold;
            border: 1px solid #ddd;
        }
        td {
            padding: 8px;
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
    </style>
</head>
<body>
    <div class="header">
        <h1>SOLICITUD DE COMPRA</h1>
        <p><strong>Consecutivo:</strong> {{ $solicitud->consecutivo }}</p>
    </div>

    <div class="info-block">
        <h3 style="margin-top: 0;">Información General</h3>
        <p><strong>Título:</strong> {{ $solicitud->titulo }}</p>
        <p><strong>Usuario:</strong> {{ $solicitud->user->name ?? '-' }}</p>
        <p><strong>Área:</strong> {{ $solicitud->user->area ?? '-' }}</p>
        <p><strong>Estado:</strong> {{ ucfirst($solicitud->estado) }}</p>
        <p><strong>Tipo:</strong> 
            @if($solicitud->tipo_solicitud == 'estandar')
                Solicitud Estándar
            @elseif($solicitud->tipo_solicitud == 'traslado_bodegas')
                Traslados entre Bodegas
            @elseif($solicitud->tipo_solicitud == 'solicitud_pedidos')
                Solicitud de Pedidos
            @elseif($solicitud->tipo_solicitud == 'solicitud_mtto')
                Solicitud Insumos / Servicio
            @endif
        </p>

        {{-- Nueva línea para mostrar la justificación en este tipo específico --}}
        @if($solicitud->tipo_solicitud == 'solicitud_mtto')
            <p><strong>Justificación:</strong> {{ $solicitud->justificacion ?? 'Sin justificación' }}</p>
        @endif

        <p><strong>Fecha:</strong> {{ $solicitud->created_at->format('d/m/Y H:i') }}</p>
    </div>

    <div class="section-title">Items Solicitados (Solo Revisados)</div>

    @if($itemsRevisados->isEmpty())
        <p style="color: #999; font-style: italic;">No hay ítems revisados en esta solicitud.</p>
    @else
        @if($solicitud->tipo_solicitud == 'estandar')
            <table>
                <thead>
                    <tr>
                        <th>REFERENCIA</th>
                        <th>UNIDAD</th>
                        <th>DESCRIPCIÓN</th>
                        <th>CANTIDAD</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($itemsRevisados as $item)
                        <tr>
                            <td>{{ $item->referencia ?? '-' }}</td>
                            <td>{{ $item->unidad ?? '-' }}</td>
                            <td>{{ $item->descripcion ?? '-' }}</td>
                            <td>{{ $item->cantidad ?? '-' }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        @elseif($solicitud->tipo_solicitud == 'traslado_bodegas')
            <table>
                <thead>
                    <tr>
                        <th>CÓDIGO</th>
                        <th>DESCRIPCIÓN</th>
                        <th>CANTIDAD</th>
                        <th>BODEGA</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($itemsRevisados as $item)
                        <tr>
                            <td>{{ $item->codigo ?? '-' }}</td>
                            <td>{{ $item->descripcion ?? '-' }}</td>
                            <td>{{ $item->cantidad ?? '-' }}</td>
                            <td>{{ $item->bodega ?? '-' }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        @elseif($solicitud->tipo_solicitud == 'solicitud_pedidos')
            <table>
                <thead>
                    <tr>
                        <th>CÓDIGO</th>
                        <th>DESCRIPCIÓN</th>
                        <th>CANTIDAD</th>
                        <th>ÁREA CONSUMO</th>
                        <th>CENTRO DE COSTOS</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($itemsRevisados as $item)
                        <tr>
                            <td>{{ $item->codigo ?? '-' }}</td>
                            <td>{{ $item->descripcion ?? '-' }}</td>
                            <td>{{ $item->cantidad ?? '-' }}</td>
                            <td>{{ $item->area_consumo ?? '-' }}</td>
                            <td>{{ $item->centro_costos_item ?? '-' }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        @elseif($solicitud->tipo_solicitud == 'solicitud_mtto')
            {{-- Nueva tabla para Insumos / Servicio --}}
            <table>
                <thead>
                    <tr>
                        <th>DESCRIPCIÓN</th>
                        <th>ESPECIFICACIONES</th>
                        <th>CANTIDAD</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($itemsRevisados as $item)
                        <tr>
                            <td>{{ $item->descripcion ?? '-' }}</td>
                            <td>{{ $item->especificaciones ?? '-' }}</td>
                            <td>{{ $item->cantidad ?? '-' }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        @endif
    @endif

    @if(!empty($solicitud->descripcion))
        <div class="observaciones">
            <strong>Observaciones:</strong>
            <p>{{ $solicitud->descripcion }}</p>
        </div>
    @endif

    <div style="margin-top: 40px; text-align: center; font-size: 10px; color: #999;">
        <p>Documento generado el {{ now()->format('d/m/Y H:i') }}</p>
    </div>
</body>
</html>
