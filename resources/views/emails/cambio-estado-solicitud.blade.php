@component('mail::message')
# Hola, {{ $solicitud->user->name ?? 'Usuario' }}

Se ha registrado una actualización en tu solicitud **{{ $solicitud->consecutivo ?? '#' . $solicitud->id }}**.

---

### 📋 Detalles del Cambio:

- **Nuevo Estado:** {{ ucfirst(str_replace('_', ' ', $solicitud->estado)) }}
- **Tipo:** {{ ucwords(str_replace('_', ' ', $solicitud->tipo_solicitud)) }}
- **Área:** {{ $solicitud->user->area ?? ($solicitud->area_solicitante ?? 'N/A') }}

@if($comentario)
### 💬 Observaciones / Motivo:

> {{ $comentario }}
@endif

---

@component('mail::button', ['url' => route('solicitudes.show', $solicitud->id)])
Ver Solicitud
@endcomponent

Gracias,<br>
{{ config('app.name') }}
@endcomponent

