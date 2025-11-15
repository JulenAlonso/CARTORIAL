<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Vehiculo extends Model
{
    // Nombre de la tabla
    protected $table = 'vehiculos';

    // Clave primaria personalizada
    protected $primaryKey = 'id_vehiculo';

    // Si tu ID es autoincremental
    public $incrementing = true;

    // Tipo de clave primaria
    protected $keyType = 'int';

    // Desactivar timestamps si no usas created_at / updated_at
    public $timestamps = false;

    // Campos que se pueden asignar masivamente
    protected $fillable = [
        'id_usuario',
        'marca',
        'modelo',
        'anio',
        'matricula',
        'km',
        'cv',
        'combustible',
        'etiqueta',
        'precio',
        'precio_segunda_mano',
        'fecha_compra',
        'car_avatar'
    ];

    /**
     * 🔹 Relación: un vehículo pertenece a un usuario.
     */
    public function usuario()
    {
        return $this->belongsTo(\App\Models\User::class, 'id_usuario', 'id_usuario');
    }

    /**
     * 🔹 Permite a Laravel usar 'id_vehiculo' en rutas como {vehiculo}.
     */
    public function getRouteKeyName()
    {
        return 'id_vehiculo';
    }

    /**
     * 🔹 Accessor para obtener la URL pública del avatar del coche.
     * Permite usar {{ $vehiculo->car_avatar_url }} directamente en las vistas.
     */
    public function getCarAvatarUrlAttribute()
    {
        if (empty($this->car_avatar)) {
            return asset('assets/images/default-car.png');
        }

        if (preg_match('/^https?:\/\//', $this->car_avatar)) {
            return $this->car_avatar;
        }

        return asset('storage/' . ltrim($this->car_avatar, '/'));


    }
    public function registrosKm()
    {
        return $this->hasMany(\App\Models\RegistroKm::class, 'id_vehiculo', 'id_vehiculo');
    }
}
