<?php

namespace App\Http\Controllers;

use App\Exports\SolicitudesExport;
use Illuminate\Http\Request;
use App\Models\Solicitud;
use App\Models\User;
use Illuminate\Support\Facades\Mail; // Nuevo
use App\Mail\CambioEstadoSolicitudMail; // Nuevo

class AdminComprasController extends Controller
{
        public function index(Request $request)
    {
        $query = Solicitud::with(['user', 'items']);

        // --- Filtros (Se mantienen igual) ---
        if ($request->filled('estado')) {
            $query->where('estado', $request->estado);
        }
        if ($request->filled('area')) {
            $query->whereHas('user', function ($q) use ($request) {
                $q->where('area', $request->area);
            });
        }
        if ($request->filled('tipo_solicitud')) {
            $query->where('tipo_solicitud', $request->tipo_solicitud);
        }
        if ($request->filled('fecha_desde')) {
            $query->whereDate('created_at', '>=', $request->fecha_desde);
        }
        if ($request->filled('fecha_hasta')) {
            $query->whereDate('created_at', '<=', $request->fecha_hasta);
        }

        // --- ORDENAMIENTO PERSONALIZADO ---
        if (!$request->filled('estado')) {
            // SQL Case para definir el orden exacto que pediste
            $query->orderByRaw("
                CASE 
                    WHEN estado = 'aprobado_supervisor' THEN 1  -- Prioridad Máxima
                    WHEN estado = 'pendiente' THEN 2            -- Segundo lugar
                    WHEN estado = 'en_proceso' THEN 3           -- Tercer lugar
                    WHEN estado = 'finalizada' THEN 4           -- Al final
                    ELSE 5                                      -- Rechazadas y otros
                END ASC
            ");
        }

        // Finalmente ordenamos por fecha (dentro de cada grupo de estado)
        $solicitudes = $query->orderBy('created_at', 'desc')
            ->paginate(25)
            ->withQueryString();

        // Obtener áreas para el filtro
        $areas = User::select('area')->whereNotNull('area')->distinct()->orderBy('area')->pluck('area');

        return view('admin.solicitudes.index', compact('solicitudes', 'areas'));
    }

    public function actualizarEstado(Request $request, $id)
    {
        $request->validate([
            'estado' => 'required|in:pendiente,aprobado_supervisor,en_proceso,finalizada,rechazada',
            'comentario' => 'nullable|string|max:500' // Opcional: permitir comentario de compras
        ]);

        $solicitud = Solicitud::findOrFail($id);
        $solicitud->estado = $request->estado;
        
        // Si quieres guardar el comentario en observaciones, descomenta esto:
        // if($request->comentario) {
        //     $solicitud->observaciones .= " | Compras: " . $request->comentario;
        // }

        $solicitud->save();

        // === NUEVO: Enviar correo al usuario si el estado cambia ===
        try {
            if ($solicitud->user && $solicitud->user->email) {
                Mail::to($solicitud->user->email)->send(
                    new CambioEstadoSolicitudMail($solicitud, $request->comentario) // Pasamos el comentario si existe
                );
            }
        } catch (\Exception $e) {
            // Log::error("Error enviando correo compras: " . $e->getMessage());
        }

        return redirect()->back()->with('success', 'Estado actualizado y notificado al usuario.');
    }

    public function export(Request $request)
    {
        $export = new SolicitudesExport(
            $request->fecha_inicio,
            $request->fecha_fin,
            $request->estado,
            $request->tipo_solicitud
        );

        return $export->download('reporte-solicitudes.xlsx');
    }
}
