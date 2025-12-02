<p>Se ha agregado un nuevo comentario a la solicitud {{ $solicitud->consecutivo ?? $solicitud->id }}.</p>

<p><strong>Autor:</strong> {{ $comentario->user->name }}</p>

<p><strong>Comentario:</strong></p>
<p>{{ $comentario->comentario }}</p>

<p>Ingresa al sistema para ver todos los detalles.</p>
