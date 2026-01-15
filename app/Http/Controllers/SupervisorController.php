<?php

namespace App\Http\Controllers;

use App\Models\Solicitud;
use App\Models\SolicitudHistorial; // <--- NUEVO IMPORT
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use App\Mail\CambioEstadoSolicitudMail;

class SupervisorController extends Controller
{
    public function index(Request $request)
    {
        $user = Auth::user();

        // Validar rol
        if (!$user->esSupervisor()) {
            return redirect()->route('dashboard')->with('error', 'Acceso no autorizado.');
        }

        // === LÓGICA DE PESTAÑAS (TABS) ===
        // Capturamos la pestaña actual de la URL (por defecto 'pendientes')
        $tab = $request->get('tab', 'pendientes');

        // Consulta base: Solo solicitudes ESTÁNDAR asignadas a ESTE supervisor
        // Cargamos 'historial.user' por si quieres mostrarlo en el panel también
        $query = Solicitud::where('supervisor_id', $user->id)
                          ->where('tipo_solicitud', 'estandar') // Solo solicitudes estándar
                          ->with(['user', 'historial.user']); 

        if ($tab === 'historial') {
            // PESTAÑA HISTORIAL:
            // Traer todo lo que YA NO está pendiente (Aprobado, Rechazado, Finalizado...)
            $query->where('estado', '!=', 'pendiente')
                  ->orderBy('updated_at', 'desc'); // Ordenar por fecha de última acción
        } else {
            // PESTAÑA PENDIENTES (Por defecto):
            // Solo lo que requiere acción inmediata
            $query->where('estado', 'pendiente')
                  ->orderBy('created_at', 'asc'); // Las más antiguas primero (FIFO)
        }

        $solicitudes = $query->paginate(10)->appends(['tab' => $tab]);

        return view('supervisor.index', compact('solicitudes', 'tab'));
    }

    public function aprobar(Request $request, Solicitud $solicitud)
    {
        // Validar que yo soy el supervisor asignado
        if ($solicitud->supervisor_id !== Auth::id()) {
            return back()->with('error', 'No tienes permiso para aprobar esta solicitud.');
        }

        // === VALIDACIÓN: Solo solicitudes ESTÁNDAR pueden ser aprobadas por supervisor ===
        if ($solicitud->tipo_solicitud !== 'estandar') {
            return back()->with('error', 'Solo las solicitudes estándar requieren aprobación de supervisor.');
        }

        // Guardamos estado anterior para el historial
        $estadoAnterior = $solicitud->estado;

        $solicitud->update([
            'estado' => 'aprobado_supervisor',
        ]);

        // === NUEVO: REGISTRAR EN HISTORIAL ===
        SolicitudHistorial::create([
            'solicitud_id'    => $solicitud->id,
            'user_id'         => auth()->id(),
            'accion'          => 'aprobada_supervisor',
            'estado_anterior' => $estadoAnterior,
            'estado_nuevo'    => 'aprobado_supervisor',
            'detalle'         => 'Solicitud aprobada por supervisor',
        ]);
        // =====================================

        // === Enviar notificación al usuario ===
        try {
            if ($solicitud->user && $solicitud->user->email) {
                Mail::to($solicitud->user->email)->send(
                    new CambioEstadoSolicitudMail($solicitud, "¡Buenas noticias! Tu solicitud ha sido aprobada por tu supervisor y enviada al departamento de Compras.")
                );
            }
        } catch (\Exception $e) {
            // Ignorar error de envío para no bloquear el proceso
        }

        return back()->with('success', 'Solicitud aprobada y notificada al usuario.');
    }

    public function rechazar(Request $request, Solicitud $solicitud)
    {
        if ($solicitud->supervisor_id !== Auth::id()) {
            return back()->with('error', 'No tienes permiso.');
        }

        // === VALIDACIÓN: Solo solicitudes ESTÁNDAR pueden ser rechazadas por supervisor ===
        if ($solicitud->tipo_solicitud !== 'estandar') {
            return back()->with('error', 'Solo las solicitudes estándar requieren aprobación de supervisor.');
        }

        $request->validate(['motivo' => 'required|string']);

        // Guardamos estado anterior para el historial
        $estadoAnterior = $solicitud->estado;

        $solicitud->update([
            'estado' => 'rechazada',
            'observaciones' => $solicitud->observaciones . " | Rechazo Supervisor: " . $request->motivo
        ]);

        // === NUEVO: REGISTRAR EN HISTORIAL ===
        SolicitudHistorial::create([
            'solicitud_id'    => $solicitud->id,
            'user_id'         => auth()->id(),
            'accion'          => 'rechazada_supervisor',
            'estado_anterior' => $estadoAnterior,
            'estado_nuevo'    => 'rechazada',
            'detalle'         => 'Rechazada por supervisor. Motivo: ' . $request->motivo,
        ]);
        // =====================================

        // === Enviar notificación de rechazo ===
        try {
            if ($solicitud->user && $solicitud->user->email) {
                Mail::to($solicitud->user->email)->send(
                    new CambioEstadoSolicitudMail($solicitud, "Tu solicitud fue rechazada por el supervisor. Motivo: " . $request->motivo)
                );
            }
        } catch (\Exception $e) {
            // Ignorar error de envío
        }

        return back()->with('success', 'Solicitud rechazada y notificada al usuario.');
    }
}
