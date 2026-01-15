<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use App\Models\Solicitud;
use App\Models\CentroCosto;
use App\Models\User;
use App\Models\SolicitudHistorial; // <--- IMPORTANTE: Nuevo modelo
use App\Mail\NuevaSolicitudAdminMail;
use App\Mail\CambioEstadoSolicitudMail;
use App\Exports\SolicitudesExport;
use Maatwebsite\Excel\Facades\Excel;
use Barryvdh\DomPDF\Facade\Pdf;

class SolicitudController extends Controller
{
    /**
     * Muestra el listado de solicitudes del usuario autenticado.
     * Incluye filtro por estado.
     */
    public function index(Request $request)
    {
        $query = Solicitud::where('user_id', auth()->id())
            ->with(['user', 'items', 'historial.user']); // Cargamos historial

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
     */
    public function create(Request $request)
    {
        $tipo = $request->query('tipo');
        $centrosCostos = CentroCosto::orderBy('departamento')->get();

        // Obtener lista de supervisores
        $supervisores = User::where('role', 'supervisor')->where('is_active', true)->get();

        if (!$tipo) {
            return view('solicitudes.select-type');
        }

        if ($tipo === 'estandar') {
            return view('solicitudes.create', compact('centrosCostos', 'supervisores'));
        } elseif ($tipo === 'traslado_bodegas') {
            $areasBodega = $centrosCostos->unique('nombre_area')->sortBy('nombre_area');
            return view('solicitudes.create-traslado-bodegas', compact('centrosCostos', 'areasBodega', 'supervisores'));
        } elseif ($tipo === 'solicitud_pedidos') {
            $areasBodega = $centrosCostos->unique('nombre_area')->sortBy('nombre_area');
            return view('solicitudes.create-solicitud-pedidos', compact('centrosCostos', 'areasBodega', 'supervisores'));
        } elseif ($tipo === 'solicitud_mtto') {
            return view('solicitudes.create-solicitud-mtto', compact('centrosCostos', 'supervisores'));
        }

        return redirect()->route('solicitudes.create');
    }

    public function store(Request $request)
    {
        // 1. Validaciones
        $request->validate([
            'titulo'           => 'required|string|max:255',
            'tipo_solicitud'   => 'required|string|in:estandar,traslado_bodegas,solicitud_pedidos,solicitud_mtto',
            'area_solicitante' => 'nullable|string|max:255',
            'centro_costos'    => 'nullable|string|max:255',
            'presupuestado'    => 'nullable|string',
            'descripcion'      => 'required|string',
            'archivo'          => 'nullable|file|mimes:pdf,doc,docx,jpg,jpeg,png,xlsx,xls|max:5000',
            'items'            => 'required|array|min:1',
            'supervisor_id'    => 'nullable|exists:users,id',
            'funcion_formato'  => 'required_if:tipo_solicitud,solicitud_mtto|in:insumos_activos,servicios_presupuestados',
            'justificacion'    => 'required_if:tipo_solicitud,solicitud_mtto|string|max:1000',
        ]);

        $archivoPath = null;
        if ($request->hasFile('archivo')) {
            $archivoPath = $request->file('archivo')->store('solicitudes', 'public');
        }

        $ultimaSolicitud = Solicitud::orderBy('id', 'desc')->first();
        $numeroConsecutivo = $ultimaSolicitud ? ($ultimaSolicitud->id + 1) : 1;
        $consecutivo = 'TICKET-' . str_pad($numeroConsecutivo, 4, '0', STR_PAD_LEFT);

        // === DETERMINAR ESTADO INICIAL ===
        if ($request->tipo_solicitud === 'estandar' && $request->supervisor_id) {
            $estadoInicial = 'pendiente';
        } else {
            $estadoInicial = 'pendiente';
        }

        // 2. Crear Encabezado de Solicitud
        $solicitud = Solicitud::create([
            'user_id'          => auth()->id(),
            'consecutivo'      => $consecutivo,
            'titulo'           => $request->titulo,
            'tipo_solicitud'   => $request->tipo_solicitud,
            'area_solicitante' => $request->area_solicitante,
            'centro_costos'    => $request->centro_costos,
            'presupuestado'    => $request->presupuestado ?? null,
            'descripcion'      => $request->descripcion,
            'archivo'          => $archivoPath,
            'estado'           => $estadoInicial,
            'supervisor_id'    => $request->tipo_solicitud === 'estandar' ? $request->supervisor_id : null,
            'funcion_formato'  => $request->tipo_solicitud === 'solicitud_mtto' ? $request->funcion_formato : null,
            'justificacion'    => $request->tipo_solicitud === 'solicitud_mtto' ? $request->justificacion : null,
        ]);

        // === NUEVO: REGISTRAR EN HISTORIAL ===
        SolicitudHistorial::create([
            'solicitud_id' => $solicitud->id,
            'user_id'      => auth()->id(),
            'accion'       => 'creada',
            'estado_nuevo' => $solicitud->estado,
            'detalle'      => 'Solicitud registrada en el sistema',
        ]);

        // 3. Guardar Ítems
        foreach ($request->items as $item) {
            if ($request->tipo_solicitud === 'estandar') {
                $solicitud->items()->create([
                    'codigo'             => $item['codigo'] ?? null,
                    'unidad'             => $item['unidad'] ?? null,
                    'descripcion'        => $item['descripcion'] ?? null,
                    'cantidad'           => $item['cantidad'] ?? null,
                    'centro_costos_item' => $item['centro_costos_item'] ?? null,
                ]);
            } elseif ($request->tipo_solicitud === 'traslado_bodegas') {
                $solicitud->items()->create([
                    'codigo'      => $item['codigo'] ?? null,
                    'descripcion' => $item['descripcion'] ?? null,
                    'cantidad'    => $item['cantidad'] ?? null,
                    'bodega'      => $item['bodega'] ?? null,
                ]);
            } elseif ($request->tipo_solicitud === 'solicitud_pedidos') {
                $solicitud->items()->create([
                    'codigo'             => $item['codigo'] ?? null,
                    'descripcion'        => $item['descripcion'] ?? null,
                    'cantidad'           => $item['cantidad'] ?? null,
                    'area_consumo'       => $item['area_consumo'] ?? null,
                    'centro_costos_item' => $item['centro_costos_item'] ?? null,
                ]);
            } elseif ($request->tipo_solicitud === 'solicitud_mtto') {
                $solicitud->items()->create([
                    'descripcion'      => $item['descripcion'] ?? null,
                    'especificaciones' => $item['especificaciones'] ?? null,
                    'cantidad'         => $item['cantidad'] ?? null,
                ]);
            }
        }

        // Correo a Admins
        try {
            $admins = User::where('is_admin', true)->get();
            foreach ($admins as $admin) {
                if ($admin->email) {
                    Mail::to($admin->email)->send(new NuevaSolicitudAdminMail($solicitud));
                }
            }
        } catch (\Exception $e) {
            // Log::error('Error enviando correo: ' . $e->getMessage());
        }

        return redirect()
            ->route('solicitudes.index')
            ->with('success', 'Solicitud registrada correctamente con consecutivo: ' . $consecutivo);
    }

    /**
     * Muestra los detalles de una solicitud específica.
     */
    public function show(Solicitud $solicitud)
    {
        $user = Auth::user();

        $esAdminCompras = $user->esAdminCompras();
        $esPropietario = ($solicitud->user_id === $user->id);
        $esSupervisorAsignado = ($user->esSupervisor() && $solicitud->supervisor_id === $user->id);

        if (!$esAdminCompras && !$esPropietario && !$esSupervisorAsignado) {
            abort(403, 'No tienes permiso para ver esta solicitud');
        }

        $solicitud->load(['user', 'items', 'comentarios.user', 'supervisor', 'historial.user']);

        // Cargar mapa de Centros de Costos
        $centrosMap = CentroCosto::all()->mapWithKeys(function ($centro) {
            $codigo = $centro->cc . '-' . $centro->sc;
            return [$codigo => $centro->nombre_area];
        });

        return view('solicitudes.show', compact('solicitud', 'centrosMap'));
    }

    /**
     * Exporta una solicitud a PDF.
     */
    public function exportPdfRevisados(Solicitud $solicitud)
    {
        if (!Auth::user()->esAdminCompras()) {
            abort(403, 'No tienes permiso para exportar esta solicitud a PDF');
        }

        $solicitud->load(['user', 'items']);
        $itemsRevisados = $solicitud->items->where('revisado', 1);

        $centrosMap = CentroCosto::all()->mapWithKeys(function ($centro) {
            $codigo = $centro->cc . '-' . $centro->sc;
            return [$codigo => $centro->nombre_area];
        });

        $pdf = Pdf::loadView('pdf.solicitud', [
            'solicitud'      => $solicitud,
            'itemsRevisados' => $itemsRevisados,
            'centrosMap'     => $centrosMap,
        ])->setPaper('letter', 'portrait');

        $fileName = 'solicitud_' . $solicitud->consecutivo . '_revisados.pdf';
        return $pdf->download($fileName);
    }

    /**
     * Actualiza el checklist de ítems revisados.
     */
    public function updateChecklist(Request $request, Solicitud $solicitud)
    {
        if (!Auth::user()->esAdminCompras()) {
            abort(403, 'No tienes permiso para actualizar el checklist');
        }

        $idsRevisados = $request->input('items_revisados', []);
        foreach ($solicitud->items as $item) {
            $item->revisado = in_array($item->id, $idsRevisados);
            $item->save();
        }

        return back()->with('success', 'Checklist de ítems actualizado correctamente.');
    }

    /**
     * Actualizar el estado de una solicitud (solo admin).
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

        $estadoAnterior = $solicitud->estado;

        $solicitud->update([
            'estado' => $request->estado,
        ]);

        // === NUEVO: REGISTRAR EN HISTORIAL ===
        SolicitudHistorial::create([
            'solicitud_id'    => $solicitud->id,
            'user_id'         => auth()->id(),
            'accion'          => 'cambio_estado',
            'estado_anterior' => $estadoAnterior,
            'estado_nuevo'    => $request->estado,
            'detalle'         => $request->comentario ?? 'Estado actualizado por Compras',
        ]);

        $comentario = $request->comentario ?? null;

        if ($solicitud->user && $solicitud->user->email) {
            Mail::to($solicitud->user->email)
                ->send(new CambioEstadoSolicitudMail($solicitud, $comentario));
        }

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
            'archivo' => 'nullable|file|mimes:pdf,doc,docx,jpg,jpeg,png,xlsx,xls|max:5000',
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
     * Muestra la vista de reportes.
     */
    public function reportes(Request $request)
    {
        if (!Auth::user()->esAdminCompras()) {
            abort(403, 'No tienes permiso para ver reportes');
        }

        $query = Solicitud::with('user')->orderBy('created_at', 'desc');

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

        $allSolicitudes = Solicitud::all();
        $stats = [
            'total'      => $allSolicitudes->count(),
            'pendiente'  => $allSolicitudes->where('estado', 'pendiente')->count(),
            'en_proceso' => $allSolicitudes->where('estado', 'en_proceso')->count(),
            'finalizada' => $allSolicitudes->where('estado', 'finalizada')->count(),
            'rechazada'  => $allSolicitudes->where('estado', 'rechazada')->count(),
        ];

        $statsTipos = [
            'estandar'          => $allSolicitudes->where('tipo_solicitud', 'estandar')->count(),
            'traslado_bodegas'  => $allSolicitudes->where('tipo_solicitud', 'traslado_bodegas')->count(),
            'solicitud_pedidos' => $allSolicitudes->where('tipo_solicitud', 'solicitud_pedidos')->count(),
            'solicitud_mtto'    => $allSolicitudes->where('tipo_solicitud', 'solicitud_mtto')->count(),
        ];

        $solicitudesPorMes = Solicitud::selectRaw('MONTH(created_at) as mes, COUNT(*) as total')
            ->whereYear('created_at', now()->year)
            ->groupBy('mes')
            ->orderBy('mes')
            ->get();

        $statsMeses = array_fill(1, 12, 0);
        foreach ($solicitudesPorMes as $row) {
            $statsMeses[$row->mes] = $row->total;
        }

        return view('admin.reportes', compact('solicitudes', 'stats', 'statsTipos', 'statsMeses'));
    }

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

    public function exportReportPdf(Request $request)
    {
        if (!Auth::user()->esAdminCompras()) {
            abort(403, 'No tienes permiso para exportar reportes');
        }
        $query = Solicitud::with(['user', 'items'])->orderBy('created_at', 'desc');
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
        $solicitudes = $query->get();
        $stats = [
            'total'      => $solicitudes->count(),
            'pendiente'  => $solicitudes->where('estado', 'pendiente')->count(),
            'en_proceso' => $solicitudes->where('estado', 'en_proceso')->count(),
            'finalizada' => $solicitudes->where('estado', 'finalizada')->count(),
            'rechazada'  => $solicitudes->where('estado', 'rechazada')->count(),
        ];
        $statsTipos = [
            'estandar'          => $solicitudes->where('tipo_solicitud', 'estandar')->count(),
            'traslado_bodegas'  => $solicitudes->where('tipo_solicitud', 'traslado_bodegas')->count(),
            'solicitud_pedidos' => $solicitudes->where('tipo_solicitud', 'solicitud_pedidos')->count(),
            'solicitud_mtto'    => $solicitudes->where('tipo_solicitud', 'solicitud_mtto')->count(),
        ];
        $filtros = [
            'fecha_inicio'   => $request->fecha_inicio,
            'fecha_fin'      => $request->fecha_fin,
            'estado'         => $request->estado,
            'tipo_solicitud' => $request->tipo_solicitud,
        ];
        $pdf = Pdf::loadView('pdf.reportes_resumen', [
            'solicitudes' => $solicitudes,
            'stats'       => $stats,
            'statsTipos'  => $statsTipos,
            'filtros'     => $filtros,
        ])->setPaper('letter', 'landscape');
        $fileName = 'reporte-resumen-' . now()->format('Ymd_His') . '.pdf';
        return $pdf->download($fileName);
    }
}
