<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Modelo Gasto
 *
 * Representa un gasto asociado a un vehículo.
 */
class Gasto extends Model
{
    protected $table = 'gastos';
    protected $primaryKey = 'id_gasto';
    public $timestamps = false;

    protected $fillable = [
        'id_vehiculo',
        'id_usuario',
        'fecha_gasto',
        'tipo_gasto',
        'importe',
        'descripcion',
        'archivo_path',   // 🔹 nueva columna para la ruta del archivo
    ];

    // Un gasto pertenece a un vehículo
    public function vehiculo()
    {
        return $this->belongsTo(
            \App\Models\Vehiculo::class,
            'id_vehiculo',
            'id_vehiculo'
        );
    }

    // Un gasto pertenece a un usuario
    public function usuario()
    {
        return $this->belongsTo(
            \App\Models\Usuario::class,
            'id_usuario',
            'id_usuario'
        );
    }
}
