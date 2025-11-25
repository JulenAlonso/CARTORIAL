<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * Modelo NotaCalendario
 *
 * Representa una nota o recordatorio vinculado a un usuario y,
 * opcionalmente, a uno de sus vehículos. La tabla almacena la fecha
 * del evento, título, descripción y la hora (nullable).
 */
class NotaCalendario extends Model
{
    use HasFactory;

    /**
     * Nombre exacto de la tabla en MySQL.
     */
    protected $table = 'notas_calendario';

    /**
     * Clave primaria de la tabla.
     */
    protected $primaryKey = 'id_nota';

    /**
     * La tabla NO usa created_at / updated_at.
     */
    public $timestamps = false;

    /**
     * Campos que se pueden establecer mediante asignación masiva.
     */
    protected $fillable = [
        'titulo',
        'descripcion',
        'fecha_evento',
        'hora_evento',
        'id_usuario',
        'id_vehiculo',
    ];

    /**
     * Relación: una nota pertenece a un usuario.
     *
     * FK: id_usuario
     * PK: id_usuario en tabla usuarios
     */
    public function usuario()
    {
        return $this->belongsTo(
            \App\Models\Usuario::class,
            'id_usuario',
            'id_usuario'
        );
    }

    /**
     * Relación: una nota puede pertenecer a un vehículo (opcional).
     *
     * FK: id_vehiculo
     * PK: id_vehiculo
     *
     * ON DELETE SET NULL en la base de datos.
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
