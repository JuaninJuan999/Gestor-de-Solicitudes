<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Solicitud extends Model
{
    use HasFactory;

    protected $table = 'solicitudes';

    protected $fillable = [
        'user_id',
        'consecutivo',
        'titulo',
        'descripcion',
        'tipo_solicitud',
        'area_solicitante', // se usa en SolicitudController@store
        'centro_costos',
        'archivo',
        'estado',
    ];

    protected $casts = [
        'estado' => 'string',
    ];

    /**
     * Relación con el modelo User
     * Una solicitud pertenece a un usuario
     */
    public function user()
    {
        return $this->belongsTo(User::class);
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
            'en_proceso' => '#17a2b8',
            'finalizada' => '#28a745',
            default => '#6c757d',
        };
    }
}
