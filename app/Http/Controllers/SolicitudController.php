<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use App\Models\Solicitud;
use App\Models\CentroCosto;
use App\Models\User;
use App\Mail\NuevaSolicitudAdminMail;
use App\Mail\CambioEstadoSolicitudMail;
use App\Exports\SolicitudesExport;
use Maatwebsite\Excel\Facades\Excel;

class SolicitudController extends Controller
{
    /**
     * Muestra el listado de solicitudes del usuario autenticado.
     * Incluye filtro por estado.
     */
    public function index(Request $request)
    {
        $query = Solicitud::where('user_id', auth()->id())
            ->with(['user', 'items']);

        if ($request->filled('estado')) {
            $query->where('estado', $request->estado);
        }

        $solicitudes = $query->orderBy('created_at', 'desc')
            ->paginate(10)
            ->withQueryString();

        return view('solicitudes.index', compact('solicitudes'));
    }

    /**
     * Muestra el formulario para crear una nueva solicitud.
     * Maneja diferentes tipos de solicitud y carga centros de costos.
     */
    public function create(Request $request)
    {
        $tipo = $request->query('tipo');
        $centrosCostos = CentroCosto::orderBy('departamento')->get();

        // Si no hay tipo seleccionado, mostrar la pantalla de selección
        if (!$tipo) {
            return view('solicitudes.select-type');
        }

        if ($tipo === 'estandar') {
            return view('solicitudes.create', compact('centrosCostos'));
        } elseif ($tipo === 'pedido_mensual') {
            $areasBodega = $centrosCostos->pluck('nombre_area')->unique();
            return view('solicitudes.create-pedido-mensual', compact('centrosCostos', 'areasBodega'));
        } elseif ($tipo === 'salida_insumos') {
            $areasBodega = $centrosCostos->pluck('nombre_area')->unique();
            return view('solicitudes.create-salida-insumos', compact('centrosCostos', 'areasBodega'));
        }

        return redirect()->route('solicitudes.create');
    }

    /**
     * Guarda una nueva solicitud en la base de datos.
     * Maneja diferentes tipos de solicitud.
     * Envía correo a los admins notificando la nueva solicitud.
     */
    public function store(Request $request)
    {
        $request->validate([
            'titulo' => 'required|string|max:255',
            'tipo_solicitud' => 'required|string|in:estandar,pedido_mensual,salida_insumos',
            'area_solicitante' => 'nullable|string|max:255',
            'centro_costos' => 'nullable|string|max:255',
            'descripcion' => 'required|string',
            'archivo' => 'nullable|file|mimes:pdf,doc,docx,jpg,jpeg,png|max:2048',
            'items' => 'required|array|min:1',
        ]);

        $archivoPath = null;
        if ($request->hasFile('archivo')) {
            $archivoPath = $request->file('archivo')->store('solicitudes', 'public');
        }

        $ultimaSolicitud = Solicitud::orderBy('id', 'desc')->first();
        $numeroConsecutivo = $ultimaSolicitud ? ($ultimaSolicitud->id + 1) : 1;
        $consecutivo = 'TICKET-' . str_pad($numeroConsecutivo, 4, '0', STR_PAD_LEFT);

        $solicitud = Solicitud::create([
            'user_id' => auth()->id(),
            'consecutivo' => $consecutivo,
            'titulo' => $request->titulo,
            'tipo_solicitud' => $request->tipo_solicitud,
            'area_solicitante' => $request->area_solicitante,
            'centro_costos' => $request->centro_costos,
            'descripcion' => $request->descripcion,
            'archivo' => $archivoPath,
            'estado' => 'pendiente',
        ]);

        foreach ($request->items as $item) {
            if ($request->tipo_solicitud === 'estandar') {
                $solicitud->items()->create([
                    'referencia' => $item['referencia'] ?? null,
                    'unidad' => $item['unidad'] ?? null,
                    'descripcion' => $item['descripcion'] ?? null,
                    'cantidad' => $item['cantidad'] ?? null,
                ]);
            } elseif ($request->tipo_solicitud === 'pedido_mensual') {
                $solicitud->items()->create([
                    'codigo' => $item['codigo'] ?? null,
                    'descripcion' => $item['descripcion'] ?? null,
                    'cantidad' => $item['cantidad'] ?? null,
                    'bodega' => $item['bodega'] ?? null,
                ]);
            } elseif ($request->tipo_solicitud === 'salida_insumos') {
                $solicitud->items()->create([
                    'codigo' => $item['codigo'] ?? null,
                    'descripcion' => $item['descripcion'] ?? null,
                    'cantidad' => $item['cantidad'] ?? null,
                    'area_consumo' => $item['area_consumo'] ?? null,
                    'centro_costos_item' => $item['centro_costos_item'] ?? null,
                ]);
            }
        }

        // === ENVÍO DE CORREO A ADMINS POR NUEVA SOLICITUD ===
        $admins = User::where('is_admin', true)->get();
        foreach ($admins as $admin) {
            if ($admin->email) {
                Mail::to($admin->email)->send(new NuevaSolicitudAdminMail($solicitud));
            }
        }
        // ====================================================

        return redirect()
            ->route('solicitudes.index')
            ->with('success', 'Solicitud registrada correctamente con consecutivo: ' . $consecutivo);
    }

    /**
     * Muestra los detalles de una solicitud específica con comentarios.
     */
    public function show(Solicitud $solicitud)
    {
        // Admin de compras puede ver todas; usuario normal solo sus propias solicitudes
        if (!Auth::user()->esAdminCompras() && $solicitud->user_id !== Auth::id()) {
            abort(403, 'No tienes permiso para ver esta solicitud');
        }

        $solicitud->load(['user', 'items', 'comentarios.user']);

        return view('solicitudes.show', compact('solicitud'));
    }

    /**
     * Actualizar el estado de una solicitud (solo admin).
     * Envía correo al usuario cuando cambia el estado.
     */
    public function updateStatus(Request $request, Solicitud $solicitud)
    {
        if (!Auth::user()->esAdminCompras()) {
            abort(403, 'No tienes permiso para cambiar el estado');
        }

        $request->validate([
            'estado' => 'required|in:pendiente,en_proceso,finalizada,rechazada',
            'comentario' => 'nullable|string',
        ]);

        $solicitud->update([
            'estado' => $request->estado,
        ]);

        // === ENVÍO DE CORREO AL USUARIO POR CAMBIO DE ESTADO ===
        $comentario = $request->comentario ?? null;

        if ($solicitud->user && $solicitud->user->email) {
            Mail::to($solicitud->user->email)
                ->send(new CambioEstadoSolicitudMail($solicitud, $comentario));
        }
        // =======================================================

        return redirect()->back()->with('success', 'Estado actualizado exitosamente');
    }

    /**
     * Muestra el formulario para editar una solicitud.
     */
    public function edit(string $id)
    {
        $solicitud = Solicitud::where('id', $id)
            ->where('user_id', auth()->id())
            ->firstOrFail();

        return view('solicitudes.edit', compact('solicitud'));
    }

    /**
     * Actualiza una solicitud existente.
     */
    public function update(Request $request, string $id)
    {
        $solicitud = Solicitud::where('id', $id)
            ->where('user_id', auth()->id())
            ->firstOrFail();

        $request->validate([
            'titulo' => 'required|string|max:255',
            'descripcion' => 'required|string',
            'archivo' => 'nullable|file|mimes:pdf,doc,docx,jpg,jpeg,png|max:2048',
        ]);

        $solicitud->titulo = $request->titulo;
        $solicitud->descripcion = $request->descripcion;

        if ($request->hasFile('archivo')) {
            $archivoPath = $request->file('archivo')->store('solicitudes', 'public');
            $solicitud->archivo = $archivoPath;
        }

        $solicitud->save();

        return redirect()->route('solicitudes.index')->with('success', 'Solicitud actualizada correctamente');
    }

    /**
     * Elimina una solicitud.
     */
    public function destroy(string $id)
    {
        $solicitud = Solicitud::where('id', $id)
            ->where('user_id', auth()->id())
            ->firstOrFail();

        $solicitud->delete();

        return redirect()->route('solicitudes.index')->with('success', 'Solicitud eliminada correctamente');
    }

    /**
     * Muestra la vista de reportes con filtros (solo admin).
     */
    public function reportes(Request $request)
    {
        if (!Auth::user()->esAdminCompras()) {
            abort(403, 'No tienes permiso para ver reportes');
        }

        $query = Solicitud::with('user')->orderBy('created_at', 'desc');

        // Filtros
        if ($request->filled('fecha_inicio')) {
            $query->whereDate('created_at', '>=', $request->fecha_inicio);
        }
        if ($request->filled('fecha_fin')) {
            $query->whereDate('created_at', '<=', $request->fecha_fin);
        }
        if ($request->filled('estado')) {
            $query->where('estado', $request->estado);
        }
        if ($request->filled('tipo_solicitud')) {
            $query->where('tipo_solicitud', $request->tipo_solicitud);
        }

        $solicitudes = $query->paginate(20)->withQueryString();

        // Estadísticas
        $allSolicitudes = Solicitud::all();
        $stats = [
            'total' => $allSolicitudes->count(),
            'pendiente' => $allSolicitudes->where('estado', 'pendiente')->count(),
            'en_proceso' => $allSolicitudes->where('estado', 'en_proceso')->count(),
            'finalizada' => $allSolicitudes->where('estado', 'finalizada')->count(),
            'rechazada' => $allSolicitudes->where('estado', 'rechazada')->count(),
        ];

        return view('admin.reportes', compact('solicitudes', 'stats'));
    }

    /**
     * Exporta el reporte a Excel (solo admin).
     */
    public function exportReport(Request $request)
{
    if (!Auth::user()->esAdminCompras()) {
        abort(403, 'No tienes permiso para exportar reportes');
    }

    $export = new \App\Exports\SolicitudesExport(
        $request->fecha_inicio,
        $request->fecha_fin,
        $request->estado,
        $request->tipo_solicitud
    );

    return $export->download('reporte-solicitudes-' . now()->format('Y-m-d') . '.xlsx');
}
}