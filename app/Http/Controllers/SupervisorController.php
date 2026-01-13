<?php

namespace App\Http\Controllers;

use App\Models\Solicitud;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class SupervisorController extends Controller
{
    public function index()
    {
        $user = Auth::user();

        // Validar rol
        if (!$user->esSupervisor()) {
            return redirect()->route('dashboard')->with('error', 'Acceso no autorizado.');
        }

        // LÓGICA CORREGIDA:
        // Traer solicitudes donde el campo 'supervisor_id' sea igual a MI ID
        // Y que estén pendientes de aprobación.
        $solicitudes = Solicitud::where('supervisor_id', $user->id)
            ->where('estado', 'pendiente') // O el estado inicial que uses
            ->orderBy('created_at', 'asc')
            ->paginate(10);

        return view('supervisor.index', compact('solicitudes'));
    }

    public function aprobar(Request $request, Solicitud $solicitud)
    {
        // Validar que yo soy el supervisor asignado
        if ($solicitud->supervisor_id !== Auth::id()) {
            return back()->with('error', 'No tienes permiso para aprobar esta solicitud.');
        }

        $solicitud->update([
            'estado' => 'aprobado_supervisor', // Nuevo estado intermedio
            // O si pasa directo a compras: 'pendiente_compras'
        ]);

        return back()->with('success', 'Solicitud aprobada.');
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

        return back()->with('success', 'Solicitud rechazada.');
    }
}
