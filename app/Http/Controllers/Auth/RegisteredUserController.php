<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules;
use Illuminate\View\View;

class RegisteredUserController extends Controller
{
    /**
     * Display the registration view.
     */
    public function create(): View
    {
        return view('auth.register', [
            'areas' => config('areas'),
        ]);
    }

    /**
     * Handle an incoming registration request.
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function store(Request $request): RedirectResponse
    {
        $areas = config('areas', []);

        $request->validate([
            'primer_nombre' => ['required', 'string', 'max:120'],
            'primer_apellido' => ['required', 'string', 'max:120'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
            'area' => ['required', 'string', Rule::in($areas)],
        ]);

        $nombreCompleto = trim($request->primer_nombre).' '.trim($request->primer_apellido);
        $username = User::makeUniqueUsername($request->primer_nombre, $request->primer_apellido);

        $user = User::create([
            'name' => $nombreCompleto,
            'username' => $username,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'area' => $request->area,
        ]);

        event(new Registered($user));

        Auth::login($user);

        return redirect(route('dashboard', absolute: false));
    }
}
