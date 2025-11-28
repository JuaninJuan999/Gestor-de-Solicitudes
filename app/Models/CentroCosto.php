<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CentroCosto extends Model
{
    // Especifica el nombre correcto de la tabla
    protected $table = 'centros_costos';

    // Si quieres habilitar asignación masiva (opcional)
    protected $guarded = [];
}
