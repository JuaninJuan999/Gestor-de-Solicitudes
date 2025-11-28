<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Solicitud;
use App\Models\User;

class AdminComprasController extends Controller
{
    /**
     * Muestra todas las solicitudes de todos los usuarios
     * Incluye filtros por estado, área, tipo de solicitud y fechas
     */
    public function index(Request $request)
    {
        // El middleware 'is_admin' ya protege estas rutas,
        // no es necesario volver a comprobar aquí.

        // Iniciar la consulta base
        $query = Solicitud::with(['user', 'items']);

        // Filtro por estado
        if ($request->filled('estado')) {
            $query->where('estado', $request->estado);
        }

        // Filtro por área/departamento del usuario
        if ($request->filled('area')) {
            $query->whereHas('user', function ($q) use ($request) {
                $q->where('area', $request->area);
            });
        }

        // Filtro por tipo de solicitud
        if ($request->filled('tipo_solicitud')) {
            $query->where('tipo_solicitud', $request->tipo_solicitud);
        }

        // Filtros por fecha (rango sobre created_at)
        if ($request->filled('fecha_desde')) {
            $query->whereDate('created_at', '>=', $request->fecha_desde);
        }

        if ($request->filled('fecha_hasta')) {
            $query->whereDate('created_at', '<=', $request->fecha_hasta);
        }

        // Ordenar por fecha más reciente y paginar
        $solicitudes = $query->orderBy('id', 'desc')
            ->paginate(5)
            ->withQueryString(); // Mantiene los filtros en la paginación

        // Obtener todas las áreas únicas para el select del filtro
        $areas = User::select('area')
            ->whereNotNull('area')
            ->where('area', '!=', '')
            ->distinct()
            ->orderBy('area')
            ->pluck('area');

        return view('admin.solicitudes.index', compact('solicitudes', 'areas'));
    }

    /**
     * Actualiza el estado de una solicitud
     */
    public function actualizarEstado(Request $request, $id)
    {
        // El middleware 'is_admin' ya protege esta acción.

        $request->validate([
            'estado' => 'required|in:pendiente,en_proceso,finalizada,rechazada',
        ]);

        $solicitud = Solicitud::findOrFail($id);
        $solicitud->estado = $request->estado;
        $solicitud->save();

        return redirect()->back()->with('success', 'Estado actualizado correctamente');
    }
}
