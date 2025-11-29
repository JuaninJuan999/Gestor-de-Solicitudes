<?php

namespace App\Http\Controllers;

use App\Models\Comentario;
use App\Models\Solicitud;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ComentarioController extends Controller
{
    public function store(Request $request, Solicitud $solicitud)
    {
        // Validar que el usuario tenga permiso para comentar en esta solicitud
        // Solo el dueño o un admin pueden comentar
        if ($solicitud->user_id !== Auth::id() && ! Auth::user()->esAdminCompras()) {
            abort(403, 'No tienes permiso para comentar en esta solicitud');
        }

        $request->validate([
            'comentario' => 'required|string|max:1000',
        ]);

        Comentario::create([
            'solicitud_id' => $solicitud->id,
            'user_id' => Auth::id(),
            'comentario' => $request->comentario,
        ]);

        return redirect()->back()->with('success', 'Comentario agregado exitosamente');
    }
}
