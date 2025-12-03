<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Reporte Resumen de Solicitudes</title>
    <style>
        body {
            font-family: DejaVu Sans, Arial, sans-serif;
            font-size: 12px;
            color: #333;
            margin: 20px;
        }
        h1, h2 {
            color: #1f2937;
            margin-bottom: 5px;
        }
        .header {
            text-align: center;
            margin-bottom: 20px;
            border-bottom: 2px solid #4b5563;
            padding-bottom: 10px;
        }
        .section {
            margin-bottom: 20px;
        }
        .small {
            font-size: 11px;
            color: #6b7280;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 8px;
        }
        th, td {
            border: 1px solid #d1d5db;
            padding: 6px 8px;
            text-align: left;
        }
        th {
            background-color: #e5e7eb;
            font-weight: bold;
        }
        .badge {
            display: inline-block;
            padding: 3px 8px;
            border-radius: 999px;
            font-size: 11px;
        }
        .badge-blue { background: #dbeafe; color: #1d4ed8; }
        .badge-green { background: #dcfce7; color: #15803d; }
        .badge-yellow { background: #fef9c3; color: #854d0e; }
        .badge-red { background: #fee2e2; color: #b91c1c; }
    </style>
</head>
<body>
    <div class="header">
        <h1>Reporte Resumen de Solicitudes</h1>
        <p class="small">Generado el {{ now()->format('d/m/Y H:i') }}</p>
    </div>

    <div class="section">
        <h2>Filtros aplicados</h2>
        <table>
            <tbody>
                <tr>
                    <th>Fecha inicio</th>
                    <td>{{ $filtros['fecha_inicio'] ?? 'No filtrado' }}</td>
                </tr>
                <tr>
                    <th>Fecha fin</th>
                    <td>{{ $filtros['fecha_fin'] ?? 'No filtrado' }}</td>
                </tr>
                <tr>
                    <th>Estado</th>
                    <td>{{ $filtros['estado'] ? ucfirst($filtros['estado']) : 'Todos' }}</td>
                </tr>
                <tr>
                    <th>Tipo de solicitud</th>
                    <td>
                        @php
                            $mapTipos = [
                                'estandar' => 'Solicitud Estándar',
                                'traslado_bodegas' => 'Traslados entre Bodegas',
                                'solicitud_pedidos' => 'Solicitud de Pedidos',
                            ];
                        @endphp
                        {{ $filtros['tipo_solicitud'] ? ($mapTipos[$filtros['tipo_solicitud']] ?? $filtros['tipo_solicitud']) : 'Todos' }}
                    </td>
                </tr>
            </tbody>
        </table>
    </div>

    @php $total = max($stats['total'], 1); @endphp

    <div class="section">
        <h2>Resumen por estado</h2>
        <table>
            <thead>
                <tr>
                    <th>Estado</th>
                    <th>Cantidad</th>
                    <th>Porcentaje</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td><span class="badge badge-yellow">Pendiente</span></td>
                    <td>{{ $stats['pendiente'] }}</td>
                    <td>{{ number_format(($stats['pendiente'] / $total) * 100, 1) }}%</td>
                </tr>
                <tr>
                    <td><span class="badge badge-blue">En Proceso</span></td>
                    <td>{{ $stats['en_proceso'] }}</td>
                    <td>{{ number_format(($stats['en_proceso'] / $total) * 100, 1) }}%</td>
                </tr>
                <tr>
                    <td><span class="badge badge-green">Finalizada</span></td>
                    <td>{{ $stats['finalizada'] }}</td>
                    <td>{{ number_format(($stats['finalizada'] / $total) * 100, 1) }}%</td>
                </tr>
                <tr>
                    <td><span class="badge badge-red">Rechazada</span></td>
                    <td>{{ $stats['rechazada'] }}</td>
                    <td>{{ number_format(($stats['rechazada'] / $total) * 100, 1) }}%</td>
                </tr>
                <tr>
                    <th>Total</th>
                    <th>{{ $stats['total'] }}</th>
                    <th>100%</th>
                </tr>
            </tbody>
        </table>
    </div>

    <div class="section">
        <h2>Resumen por tipo de solicitud</h2>
        <table>
            <thead>
                <tr>
                    <th>Tipo</th>
                    <th>Cantidad</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td>Solicitud Estándar</td>
                    <td>{{ $statsTipos['estandar'] }}</td>
                </tr>
                <tr>
                    <td>Traslados entre Bodegas</td>
                    <td>{{ $statsTipos['traslado_bodegas'] }}</td>
                </tr>
                <tr>
                    <td>Solicitud de Pedidos</td>
                    <td>{{ $statsTipos['solicitud_pedidos'] }}</td>
                </tr>
            </tbody>
        </table>
    </div>
</body>
</html>
