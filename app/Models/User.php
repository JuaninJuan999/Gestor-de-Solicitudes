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
        'rol',
        'is_admin', // nuevo: para marcar si es administrador de compras
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
            'is_admin' => 'boolean', // casteo a boolean
        ];
    }

    /**
     * Verifica si el usuario es administrador de compras
     * Mantiene compatibilidad con código antiguo basado en "rol",
     * pero ahora prioriza la columna is_admin.
     */
    public function esAdminCompras(): bool
    {
        if (! is_null($this->is_admin)) {
            return (bool) $this->is_admin;
        }

        return $this->rol === 'admin_compras';
    }

    /**
     * Verifica si el usuario es normal
     */
    public function esUsuario(): bool
    {
        if (! is_null($this->is_admin)) {
            return ! (bool) $this->is_admin;
        }

        return $this->rol === 'usuario';
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
