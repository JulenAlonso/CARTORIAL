<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Modelo Vehiculo
 *
 * Representa un vehículo registrado por un usuario. Incluye información
 * técnica (marca, modelo, CV, combustible), administrativa (matrícula,
 * años) y económica (precio y valor de segunda mano). 
 *
 * Se relaciona con:
 *  - Usuario (propietario)
 *  - RegistroKm (historial de kilometraje)
 *  - Gasto (historial de gastos)
 */
class Vehiculo extends Model
{
    /**
     * Nombre de la tabla asociada.
     */
    protected $table = 'vehiculos';

    /**
     * Clave primaria de la tabla.
     */
    protected $primaryKey = 'id_vehiculo';

    public $incrementing = true;
    protected $keyType   = 'int';

    /**
     * La tabla no usa created_at / updated_at.
     */
    public $timestamps = false;

    /**
     * Campos que pueden asignarse masivamente.
     */
    protected $fillable = [
        'id_usuario',
        'matricula',
        'marca',
        'modelo',

        'anio_fabricacion',
        'anio_matriculacion',
        'anio', // año genérico (puede ser matriculación o fabricación)

        'fecha_compra',
        'km',
        'cv',
        'combustible',
        'etiqueta',

        'precio',
        'precio_segunda_mano',

        'car_avatar', // imagen del coche
    ];

    /**
     * Relación: un vehículo pertenece a un usuario.
     *
     * FK: id_usuario
     * PK: id_usuario en usuarios
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
     * Indica a Laravel que las rutas {vehiculo} usan id_vehiculo.
     *
     * Ejemplo: /vehiculos/15 → Vehiculo::find(15)
     */
    public function getRouteKeyName()
    {
        return 'id_vehiculo';
    }

    /**
     * Accessor: devuelve la URL completa del avatar del vehículo.
     *
     * Permite hacer:
     *   $vehiculo->car_avatar_url
     */
    public function getCarAvatarUrlAttribute()
    {
        if (empty($this->car_avatar)) {
            return asset('assets/images/default-car.png');
        }

        // Si ya es URL completa
        if (preg_match('/^https?:\/\//', $this->car_avatar)) {
            return $this->car_avatar;
        }

        // Imagen en storage
        return asset('storage/' . ltrim($this->car_avatar, '/'));
    }

    /**
     * Relación: un vehículo tiene muchos registros de kilometraje.
     *
     * FK en registros_km: id_vehiculo
     */
    public function registrosKm()
    {
        return $this->hasMany(
            \App\Models\RegistroKm::class,
            'id_vehiculo',
            'id_vehiculo'
        );
    }

    /**
     * Relación: un vehículo tiene muchos gastos.
     *
     * FK en gastos: id_vehiculo
     */
    public function registrosGastos()
    {
        return $this->hasMany(
            \App\Models\Gasto::class,
            'id_vehiculo',
            'id_vehiculo'
        );
    }
}
