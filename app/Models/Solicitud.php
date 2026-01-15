<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\SolicitudHistorial; // <-- NUEVO (para la relación)

class Solicitud extends Model
{
    use HasFactory;

    protected $table = 'solicitudes';

    protected $fillable = [
        'user_id',
        'supervisor_id',    // <--- NUEVO CAMPO AGREGADO
        'consecutivo',
        'titulo',
        'descripcion',
        'tipo_solicitud',
        'area_solicitante',
        'centro_costos',
        'presupuestado',
        'archivo',
        'estado',
        'justificacion',
        'funcion_formato',
    ];

    protected $casts = [
        'estado' => 'string',
    ];

    /**
     * Relación con el modelo User (Creador)
     * Una solicitud pertenece a un usuario
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Relación con el modelo User (Supervisor) - NUEVA
     * Una solicitud puede tener un supervisor asignado
     */
    public function supervisor()
    {
        return $this->belongsTo(User::class, 'supervisor_id');
    }

    /**
     * Relación con el modelo Item
     * Una solicitud tiene muchos items
     */
    public function items()
    {
        return $this->hasMany(Item::class);
    }

    /**
     * Relación con el modelo Comentario
     * Una solicitud tiene muchos comentarios ordenados por fecha
     */
    public function comentarios()
    {
        return $this->hasMany(Comentario::class)->orderBy('created_at', 'asc');
    }

    /**
     * NUEVO: Relación con el historial de eventos de la solicitud
     */
    public function historial()
    {
        return $this->hasMany(SolicitudHistorial::class)->orderBy('created_at', 'desc');
    }

    /**
     * Accessor para obtener el ticket_id formateado
     */
    public function getTicketIdAttribute()
    {
        return $this->consecutivo;
    }

    /**
     * Método auxiliar para obtener el color del badge según el estado
     */
    public function getEstadoColorAttribute()
    {
        return match ($this->estado) {
            'pendiente' => '#ffc107',
            'aprobado_supervisor' => '#17a2b8', // Color azulito para supervisor
            'en_proceso' => '#17a2b8',
            'finalizada' => '#28a745',
            'rechazada' => '#dc3545',
            default => '#6c757d',
        };
    }
}
