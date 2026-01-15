<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SolicitudHistorial extends Model
{
    use HasFactory;

    protected $table = 'solicitud_historials';

    protected $fillable = [
        'solicitud_id',
        'user_id',
        'accion',
        'estado_anterior',
        'estado_nuevo',
        'detalle',
    ];

    /**
     * Relación con Solicitud
     */
    public function solicitud()
    {
        return $this->belongsTo(Solicitud::class);
    }

    /**
     * Relación con User (quien realizó la acción)
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
