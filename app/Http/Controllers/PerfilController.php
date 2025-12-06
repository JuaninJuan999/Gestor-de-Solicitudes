<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class PerfilController extends Controller
{
    /**
     * Muestra el formulario de editar perfil.
     */
    public function edit()
    {
        return view('perfil.edit', [
            'user' => Auth::user()
        ]);
    }

    /**
     * Actualiza la información básica (Nombre).
     */
    public function update(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            // Agrega 'email' aquí si quieres permitir cambiar correo
        ]);

        $user = Auth::user();
        $user->name = $request->name;
        $user->save();

        return back()->with('success', 'Perfil actualizado correctamente.');
    }

    /**
     * Actualiza la contraseña.
     */
    public function updatePassword(Request $request)
    {
        $request->validate([
            'current_password' => 'required',
            'password' => 'required|string|min:8|confirmed',
        ]);

        $user = Auth::user();

        // Verificar que la contraseña actual sea correcta
        if (!Hash::check($request->current_password, $user->password)) {
            return back()->withErrors(['current_password' => 'La contraseña actual no es correcta.']);
        }

        // Actualizar contraseña
        $user->password = Hash::make($request->password);
        $user->save();

        return back()->with('success', 'Contraseña cambiada exitosamente.');
    }
}
