@component('mail::message')
# Actualización de estado de tu solicitud

Tu solicitud **{{ $solicitud->consecutivo ?? 'sin consecutivo' }}** ha cambiado de estado.

- Tipo: **{{ ucwords(str_replace('_', ' ', $solicitud->tipo_solicitud)) }}**
- Nuevo estado: **{{ ucfirst($solicitud->estado) }}**
- Área solicitante: **{{ $solicitud->user->area ?? ($solicitud->area_solicitante ?? 'N/A') }}**

@if($comentario)
**Comentario del área de compras:**

> {{ $comentario }}
@endif

@component('mail::button', ['url' => route('solicitudes.show', $solicitud->id)])
Ver detalle de la solicitud
@endcomponent

Gracias por usar el Gestor de Solicitudes.<br>
{{ config('app.name') }}
@endcomponent
