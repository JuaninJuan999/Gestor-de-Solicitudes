<?php

namespace App\Http\Controllers;

use App\Models\Comentario;
use App\Models\Solicitud;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use App\Mail\NuevoComentarioSolicitudMail;

class ComentarioController extends Controller
{
    public function store(Request $request, Solicitud $solicitud)
    {
        // Solo el dueño o un admin pueden comentar
        if ($solicitud->user_id !== Auth::id() && ! Auth::user()->esAdminCompras()) {
            abort(403, 'No tienes permiso para comentar en esta solicitud');
        }

        $request->validate([
            'comentario' => 'required|string|max:1000',
            'archivo'    => 'nullable|file|mimes:pdf,doc,docx,xls,xlsx,jpg,jpeg,png|max:4096',
        ]);

        // Guardar archivo si viene
        $archivoPath = null;
        if ($request->hasFile('archivo')) {
            $archivoPath = $request->file('archivo')->store('comentarios', 'public');
        }

        // Crear comentario
        $comentario = Comentario::create([
            'solicitud_id' => $solicitud->id,
            'user_id'      => Auth::id(),
            'comentario'   => $request->comentario,
            'archivo'      => $archivoPath,
        ]);

        // Notificar por correo al dueño de la solicitud
        if ($solicitud->user && $solicitud->user->email) {
            Mail::to($solicitud->user->email)
                ->send(new NuevoComentarioSolicitudMail($solicitud, $comentario));
        }

        return redirect()->back()->with('success', 'Comentario agregado exitosamente');
    }
}
