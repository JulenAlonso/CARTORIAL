<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Modelo RegistroKm
 *
 * Representa un registro histórico del kilometraje de un vehículo.
 * Cada registro guarda la fecha en la que se anotó y el valor exacto
 * del cuentakilómetros en ese momento. La tabla permite mantener un
 * historial completo de la evolución del kilometraje.
 */
class RegistroKm extends Model
{
    /**
     * Nombre exacto de la tabla en la base de datos.
     */
    protected $table = 'registros_km';

    /**
     * Clave primaria de la tabla.
     */
    protected $primaryKey = 'id_registro_km';

    /**
     * La clave primaria es entera autoincremental.
     */
    public $incrementing = true;
    protected $keyType = 'int';

    /**
     * La tabla NO tiene timestamps automáticos.
     */
    public $timestamps = false;

    /**
     * Campos permitidos para asignación masiva.
     */
    protected $fillable = [
        'id_vehiculo',
        'fecha_registro',
        'km_actual',
        'comentario',
    ];

    /**
     * Relación: Cada registro pertenece a un único vehículo.
     *
     * FK: id_vehiculo
     * PK: id_vehiculo en tabla vehiculos
     */
    public function vehiculo()
    {
        return $this->belongsTo(
            \App\Models\Vehiculo::class,
            'id_vehiculo',
            'id_vehiculo'
        );
    }
}
