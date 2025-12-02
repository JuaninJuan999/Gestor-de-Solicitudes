@component('mail::message')
# Nueva solicitud registrada

Se ha registrado una nueva solicitud en el sistema.

- Consecutivo: **{{ $solicitud->consecutivo ?? 'Sin consecutivo' }}**
- Tipo: **{{ ucwords(str_replace('_', ' ', $solicitud->tipo_solicitud)) }}**
- Estado: **{{ ucfirst($solicitud->estado) }}**
- Usuario: **{{ $solicitud->user->name ?? 'N/A' }}**
- Área solicitante: **{{ $solicitud->area_solicitante ?? 'N/A' }}**

@component('mail::button', ['url' => route('solicitudes.show', $solicitud->id)])
Ver solicitud en el sistema
@endcomponent

Gracias,<br>
{{ config('app.name') }}
@endcomponent
