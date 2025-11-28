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
        $request->validate([
            'comentario' => 'required|string|max:1000'
        ]);

        Comentario::create([
            'solicitud_id' => $solicitud->id,
            'user_id' => Auth::id(),
            'comentario' => $request->comentario
        ]);

        return redirect()->back()->with('success', 'Comentario agregado exitosamente');
    }
}
