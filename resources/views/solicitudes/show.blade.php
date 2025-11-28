<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $solicitud->ticket_id }} - Detalle</title>
    <style>
        /* Tu CSS aquí, igual que lo tienes actualmente */
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: Arial, sans-serif; background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); min-height: 100vh; padding: 20px; }
        .container { max-width: 1000px; margin: 0 auto; }
        .header { background: white; padding: 20px; border-radius: 10px; margin-bottom: 20px; box-shadow: 0 2px 10px rgba(0,0,0,0.1); }
        .header h1 { color: #667eea; margin-bottom: 10px; }
        .ticket-info { display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 15px; margin-top: 15px; }
        .info-item { padding: 10px; background: #f8f9fa; border-radius: 5px; }
        .info-item label { font-weight: bold; color: #666; display: block; margin-bottom: 5px; }
        .badge { display: inline-block; padding: 5px 12px; border-radius: 20px; font-size: 12px; font-weight: bold; }
        .badge-pending { background: #ffc107; color: white; }
        .badge-process { background: #17a2b8; color: white; }
        .badge-finished { background: #28a745; color: white; }
        .items-section { background: white; padding: 20px; border-radius: 10px; margin-bottom: 20px; box-shadow: 0 2px 10px rgba(0,0,0,0.1); }
        .items-table { width: 100%; border-collapse: collapse; margin-top: 15px; }
        .items-table th { background: #667eea; color: white; padding: 12px; text-align: left; }
        .items-table td { padding: 12px; border-bottom: 1px solid #ddd; }
        .comments-section { background: white; padding: 20px; border-radius: 10px; margin-bottom: 20px; box-shadow: 0 2px 10px rgba(0,0,0,0.1); }
        .comment { padding: 15px; margin-bottom: 15px; border-radius: 8px; border-left: 4px solid #667eea; }
        .comment.admin { background: #e8f5e9; border-left-color: #28a745; }
        .comment.user { background: #f3e5f5; border-left-color: #667eea; }
        .comment-header { display: flex; justify-content: space-between; margin-bottom: 10px; font-size: 14px; color: #666; }
        .comment-author { font-weight: bold; color: #333; }
        .comment-body { color: #333; line-height: 1.6; }
        .comment-form { background: #f8f9fa; padding: 20px; border-radius: 8px; margin-top: 20px; }
        .form-group { margin-bottom: 15px; }
        .form-group label { display: block; margin-bottom: 5px; font-weight: bold; color: #333; }
        .form-control { width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 5px; font-family: Arial, sans-serif; }
        textarea.form-control { min-height: 100px; resize: vertical; }
        select.form-control { cursor: pointer; }
        .btn { padding: 10px 20px; border: none; border-radius: 5px; cursor: pointer; font-weight: bold; text-decoration: none; display: inline-block; }
        .btn-primary { background: #667eea; color: white; }
        .btn-primary:hover { background: #5568d3; }
        .btn-secondary { background: #6c757d; color: white; margin-left: 10px; }
        .btn-secondary:hover { background: #5a6268; }
        .alert { padding: 15px; border-radius: 5px; margin-bottom: 20px; }
        .alert-success { background: #d4edda; color: #155724; border: 1px solid #c3e6cb; }
        .status-form { background: #fff3cd; padding: 15px; border-radius: 8px; margin-bottom: 20px; }
        .back-button { margin-bottom: 20px; }
    </style>
</head>
<body>
    <div class="container">
        <div class="back-button">
            <a href="{{ Auth::user()->esAdminCompras() ? route('admin.solicitudes.index') : route('solicitudes.index') }}" class="btn btn-secondary">← Volver</a>
        </div>

        @if(session('success'))
        <div class="alert alert-success">
            {{ session('success') }}
        </div>
        @endif

        <div class="header">
            <h1>{{ $solicitud->ticket_id }}</h1>
            <div class="ticket-info">
                <div class="info-item">
                    <label>Departamento:</label>
                    <span class="badge" style="background: #9b59b6; color: white;">
                        {{ $solicitud->user->area ?? 'Sin departamento' }}
                    </span>
                </div>
                <div class="info-item">
                    <label>Solicitante:</label>
                    <span class="badge" style="background: #17a2b8; color: white;">
                        👤 {{ $solicitud->user->name }}
                    </span>
                </div>
                <!-- Etiqueta tipo de solicitud añadida aquí -->
                @php
                    $etiquetaTipo = '';
                    $colorTipo = '#6c757d'; // gris por defecto
                    if ($solicitud->tipo_solicitud == 'estandar') {
                        $etiquetaTipo = 'Solicitud Estándar';
                        $colorTipo = '#27ae60'; // verde
                    } elseif ($solicitud->tipo_solicitud == 'pedido_mensual') {
                        $etiquetaTipo = 'Pedido Mensual';
                        $colorTipo = '#2980b9'; // azul
                    } elseif ($solicitud->tipo_solicitud == 'salida_insumos') {
                        $etiquetaTipo = 'Salida Insumos';
                        $colorTipo = '#f1c40f'; // amarillo
                    }
                @endphp
                <div class="info-item">
                    <label>Tipo de Solicitud:</label>
                    <span class="badge" style="background: {{ $colorTipo }}; color: white;">
                        {{ $etiquetaTipo }}
                    </span>
                </div>
                <div class="info-item">
                    <label>Estado:</label>
                    <span class="badge 
                        @if($solicitud->estado == 'pendiente') badge-pending
                        @elseif($solicitud->estado == 'en_proceso') badge-process
                        @else badge-finished
                        @endif">
                        @if($solicitud->estado == 'pendiente') Pendiente
                        @elseif($solicitud->estado == 'en_proceso') En Proceso
                        @elseif($solicitud->estado == 'finalizada') Finalizada
                        @else {{ ucfirst($solicitud->estado) }}
                        @endif
                    </span>
                </div>
                <div class="info-item">
                    <label>Fecha:</label>
                    {{ $solicitud->created_at->format('d/m/Y H:i') }}
                </div>
            </div>
        </div>

        <!-- Cambiar Estado (Solo Admin) -->
        @if(Auth::user()->esAdminCompras())
        <div class="status-form">
            <form action="{{ route('solicitudes.updateStatus', $solicitud) }}" method="POST">
                @csrf
                @method('PATCH')
                <div class="form-group">
                    <label>Cambiar Estado:</label>
                    <select name="estado" class="form-control" onchange="this.form.submit()">
                        <option value="pendiente" {{ $solicitud->estado == 'pendiente' ? 'selected' : '' }}>⏳ Pendiente</option>
                        <option value="en_proceso" {{ $solicitud->estado == 'en_proceso' ? 'selected' : '' }}>🔄 En Proceso</option>
                        <option value="finalizada" {{ $solicitud->estado == 'finalizada' ? 'selected' : '' }}>✅ Finalizada</option>
                    </select>
                </div>
            </form>
        </div>
        @endif

        <!-- Items Solicitados -->
        <div class="items-section">
            <h2>Ítems Solicitados</h2>
            @php
                $itemsTabla = $solicitud->items;
                $itemsJson = [];
                if ($itemsTabla->isEmpty() && strpos($solicitud->descripcion, 'Items solicitados:') !== false) {
                    $partes = explode('Items solicitados:', $solicitud->descripcion);
                    $itemsJsonString = trim($partes[1] ?? '');
                    $itemsJson = json_decode($itemsJsonString, true) ?? [];
                }
            @endphp

            @if($itemsTabla->isNotEmpty() || !empty($itemsJson))
            <table class="items-table">
                <thead>
                    <tr>
                        @if($solicitud->tipo_solicitud === 'estandar')
                            <th>Referencia</th>
                            <th>Unidad</th>
                            <th>Descripción</th>
                            <th>Cantidad</th>
                        @elseif($solicitud->tipo_solicitud === 'pedido_mensual')
                            <th>Código</th>
                            <th>Descripción</th>
                            <th>Cantidad</th>
                            <th>Bodega</th>
                        @elseif($solicitud->tipo_solicitud === 'salida_insumos')
                            <th>Código</th>
                            <th>Descripción</th>
                            <th>Cantidad</th>
                            <th>Área de Consumo</th>
                            <th>Centro de Costos</th>
                        @endif
                    </tr>
                </thead>
                <tbody>
                    @if($itemsTabla->isNotEmpty())
                        @foreach($itemsTabla as $item)
                            @if($solicitud->tipo_solicitud === 'estandar')
                                <tr>
                                    <td>{{ $item->referencia ?? '-' }}</td>
                                    <td>{{ $item->unidad ?? '-' }}</td>
                                    <td>{{ $item->descripcion ?? '-' }}</td>
                                    <td>{{ $item->cantidad ?? '-' }}</td>
                                </tr>
                            @elseif($solicitud->tipo_solicitud === 'pedido_mensual')
                                <tr>
                                    <td>{{ $item->codigo ?? '-' }}</td>
                                    <td>{{ $item->descripcion ?? '-' }}</td>
                                    <td>{{ $item->cantidad ?? '-' }}</td>
                                    <td>{{ $item->bodega ?? '-' }}</td>
                                </tr>
                            @elseif($solicitud->tipo_solicitud === 'salida_insumos')
                                <tr>
                                    <td>{{ $item->codigo ?? '-' }}</td>
                                    <td>{{ $item->descripcion ?? '-' }}</td>
                                    <td>{{ $item->cantidad ?? '-' }}</td>
                                    <td>{{ $item->area_consumo ?? '-' }}</td>
                                    <td>{{ $item->centro_costos_item ?? '-' }}</td>
                                </tr>
                            @endif
                        @endforeach
                    @else
                        @foreach($itemsJson as $item)
                            @if($solicitud->tipo_solicitud === 'estandar')
                                <tr>
                                    <td>{{ $item['referencia'] ?? '-' }}</td>
                                    <td>{{ $item['unidad'] ?? '-' }}</td>
                                    <td>{{ $item['descripcion'] ?? '-' }}</td>
                                    <td>{{ $item['cantidad'] ?? '-' }}</td>
                                </tr>
                            @elseif($solicitud->tipo_solicitud === 'pedido_mensual')
                                <tr>
                                    <td>{{ $item['codigo'] ?? '-' }}</td>
                                    <td>{{ $item['descripcion'] ?? '-' }}</td>
                                    <td>{{ $item['cantidad'] ?? '-' }}</td>
                                    <td>{{ $item['bodega'] ?? '-' }}</td>
                                </tr>
                            @elseif($solicitud->tipo_solicitud === 'salida_insumos')
                                <tr>
                                    <td>{{ $item['codigo'] ?? '-' }}</td>
                                    <td>{{ $item['descripcion'] ?? '-' }}</td>
                                    <td>{{ $item['cantidad'] ?? '-' }}</td>
                                    <td>{{ $item['area_consumo'] ?? '-' }}</td>
                                    <td>{{ $item['centro_costos_item'] ?? '-' }}</td>
                                </tr>
                            @endif
                        @endforeach
                    @endif
                </tbody>
            </table>
            @else
            <p style="color: #666; text-align: center; padding: 20px;">No hay items registrados.</p>
            @endif

            @php
                $observaciones = $solicitud->descripcion;
                if (strpos($observaciones, 'Items solicitados:') !== false) {
                    $observaciones = trim(explode('Items solicitados:', $observaciones)[0]);
                }
            @endphp

            @if($observaciones && $observaciones != '')
            <div style="margin-top: 15px; padding: 10px; background: #f8f9fa; border-radius: 5px;">
                <strong>Observaciones:</strong>
                <p>{{ $observaciones }}</p>
            </div>
            @endif

            @if($solicitud->archivo)
            <div style="margin-top: 15px;">
                <a href="{{ asset('storage/' . $solicitud->archivo) }}" target="_blank" class="btn btn-secondary">
                    📎 Ver archivo adjunto
                </a>
            </div>
            @endif
        </div>

        <!-- Comentarios -->
        <div class="comments-section">
            <h2>Comentarios</h2>
            @forelse($solicitud->comentarios as $comentario)
            <div class="comment {{ $comentario->user->esAdminCompras() ? 'admin' : 'user' }}">
                <div class="comment-header">
                    <span class="comment-author">
                        {{ $comentario->user->name }}
                        @if($comentario->user->esAdminCompras())
                        <span class="badge" style="background: #28a745;">Admin</span>
                        @endif
                    </span>
                    <span>{{ $comentario->created_at->format('d/m/Y H:i') }}</span>
                </div>
                <div class="comment-body">
                    {{ $comentario->comentario }}
                </div>
            </div>
            @empty
            <p style="color: #666; text-align: center; padding: 20px;">
                No hay comentarios aún. ¡Sé el primero en comentar!
            </p>
            @endforelse

            <!-- Formulario para agregar comentario -->
            <div class="comment-form">
                <h3>Agregar Comentario</h3>
                <form action="{{ route('comentarios.store', $solicitud) }}" method="POST">
                    @csrf
                    <div class="form-group">
                        <textarea name="comentario" class="form-control" placeholder="Escribe tu comentario aquí..." required></textarea>
                        @error('comentario')
                        <span style="color: red; font-size: 14px;">{{ $message }}</span>
                        @enderror
                    </div>
                    <button type="submit" class="btn btn-primary">Enviar Comentario</button>
                </form>
            </div>
        </div>
    </div>
</body>
</html>
