<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    // Listar usuarios
    public function index(Request $request)
    {
        $query = User::query();

        // Buscador
        if ($request->has('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('name', 'LIKE', "%{$search}%")
                  ->orWhere('email', 'LIKE', "%{$search}%");
            });
        }

        // Ordenar y paginar
        $users = $query->orderBy('is_active', 'desc')
                       ->orderBy('name')
                       ->paginate(10);

        return view('admin.users.index', compact('users'));
    }

    // Actualizar Rol
    public function updateRole(Request $request, User $user)
    {
        $request->validate(['role' => 'required|string']);
        $user->update(['role' => $request->role]);
        return back()->with('success', 'Rol actualizado.');
    }

    // Activar/Inactivar
    public function toggleStatus(User $user)
    {
        $user->update(['is_active' => !$user->is_active]);
        return back()->with('success', $user->is_active ? 'Usuario activado.' : 'Usuario inactivado.');
    }

    // Reset Password
    public function resetPassword(User $user)
    {
        $user->update(['password' => Hash::make('Password123')]);
        return back()->with('success', 'Contraseña restablecida a: Password123');
    }
}
