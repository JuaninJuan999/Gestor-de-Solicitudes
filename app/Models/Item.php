<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Item extends Model
{
    use HasFactory;

    protected $fillable = [
        'solicitud_id',
        'referencia',
        'codigo',      // ← LÍNEA AGREGADA
        'unidad',
        'descripcion',
        'especificaciones', 
        'justificacion',
        'cantidad',
        'bodega',       // ← LÍNEA AGREGADA
        'area_consumo',        // ← agregar
        'centro_costos_item',  // ← agregar
    ];

    public function solicitud()
    {
        return $this->belongsTo(Solicitud::class);
    }
}
