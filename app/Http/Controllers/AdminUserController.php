<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class AdminUserController extends Controller
{
    // 1. Mostrar la lista de usuarios
    public function index(Request $request)
    {
        // Iniciamos la consulta
        $query = User::query();

        // Si hay búsqueda en el backend
        if ($request->has('search')) {
            $search = $request->get('search');
            $query->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%");
        }

        // --- CAMBIO CLAVE AQUÍ ---
        // 1. 'is_active' desc: Pone a los activos (1) arriba y bloqueados (0) abajo.
        // 2. 'created_at' desc: Dentro de cada grupo, ordena por los más nuevos.
        $users = $query->orderBy('is_active', 'desc') 
                       ->orderBy('created_at', 'desc')
                       ->paginate(10);

        return view('admin.users.index', compact('users'));
    }

    // 2. Cambiar Rol
    public function updateRole(Request $request, User $user)
    {
        $request->validate([
            'role' => 'required|in:admin,user,supervisor',
        ]);

        if ($user->id === auth()->id()) {
            return back()->with('error', '¡Seguridad: No puedes cambiar tu propio rol!');
        }

        $user->role = $request->role;
        $user->save();

        return back()->with('success', "Rol de {$user->name} actualizado correctamente.");
    }

    // 3. Bloquear / Activar
    public function toggleStatus(User $user)
    {
        if ($user->id === auth()->id()) {
            return back()->with('error', '¡Seguridad: No puedes bloquear tu propia cuenta!');
        }

        $user->is_active = !$user->is_active;
        $user->save();

        $estado = $user->is_active ? 'ACTIVADO' : 'BLOQUEADO';
        
        return back()->with('success', "El usuario {$user->name} ha sido {$estado}.");
    }

    // 4. Resetear Contraseña
    public function resetPassword(User $user)
    {
        $tempPassword = 'Colbeef2026*';
        
        $user->password = Hash::make($tempPassword);
        $user->save();

        return back()->with('success', "Contraseña restablecida. La nueva clave temporal es: {$tempPassword}");
    }
}
