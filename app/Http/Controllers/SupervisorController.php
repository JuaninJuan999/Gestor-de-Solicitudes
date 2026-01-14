<?php

namespace App\Http\Controllers;

use App\Models\Solicitud;
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

        // Consulta base: Solo solicitudes asignadas a ESTE supervisor
        $query = Solicitud::where('supervisor_id', $user->id);

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

        $solicitud->update([
            'estado' => 'aprobado_supervisor', // Nuevo estado intermedio
        ]);

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

        $request->validate(['motivo' => 'required|string']);

        $solicitud->update([
            'estado' => 'rechazada',
            'observaciones' => $solicitud->observaciones . " | Rechazo Supervisor: " . $request->motivo
        ]);

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
