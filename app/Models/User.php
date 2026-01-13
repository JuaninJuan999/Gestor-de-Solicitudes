<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'area',
        'role',      // Usamos 'role' (admin, supervisor, user)
        'rol',       // Mantenemos 'rol' por compatibilidad
        'is_active', // Estado activo/inactivo
        'is_admin',  // Mantenemos por compatibilidad
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'is_admin' => 'boolean',
            'is_active' => 'boolean',
        ];
    }

    /**
     * Verifica si el usuario es administrador
     */
    public function esAdminCompras(): bool
    {
        // 1. Prioridad: Nueva columna 'role'
        if ($this->role === 'admin') {
            return true;
        }

        // 2. Compatibilidad: Columna 'is_admin'
        if (! is_null($this->is_admin) && $this->is_admin) {
            return true;
        }

        // 3. Compatibilidad: Columna antigua 'rol'
        return $this->rol === 'admin_compras';
    }

    /**
     * Verifica si el usuario es Supervisor
     * (IMPORTANTE: Esto es nuevo para tu lógica)
     */
    public function esSupervisor(): bool
    {
        return $this->role === 'supervisor';
    }

    /**
     * Verifica si el usuario es normal
     */
    public function esUsuario(): bool
    {
        return !$this->esAdminCompras() && !$this->esSupervisor();
    }

    /**
     * Relación con solicitudes
     * Un usuario puede tener muchas solicitudes
     */
    public function solicitudes()
    {
        return $this->hasMany(Solicitud::class);
    }
}
