<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Vehiculo extends Model
{
    // Nombre de la tabla
    protected $table = 'vehiculos';

    // Clave primaria personalizada
    protected $primaryKey = 'id_vehiculo';

    public $incrementing = true;
    protected $keyType = 'int';

    // No tienes created_at / updated_at
    public $timestamps = false;

    // Campos que se pueden asignar masivamente
    protected $fillable = [
        'matricula',
        'marca',
        'modelo',
        'anio',          // en la BBDD se llama anio
        'fecha_compra',
        'km',
        'cv',
        'combustible',
        'etiqueta',
        'precio',
        'id_usuario',
        // Si más adelante añades columnas nuevas (car_avatar, precio_segunda_mano, etc.),
        // las puedes añadir aquí.
    ];

    /**
     * 🔹 Relación: un vehículo pertenece a un usuario.
     */
    public function usuario()
    {
        // Ojo: aquí debe ir tu modelo de usuario real (Usuario o User)
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
     * 🔹 Accessor opcional para avatar (si luego añades la columna car_avatar).
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

    /**
     * 🔹 Relación: un vehículo tiene muchos registros de km.
     */
    public function registrosKm()
    {
        return $this->hasMany(\App\Models\RegistroKm::class, 'id_vehiculo', 'id_vehiculo');
    }

    /**
     * 🔹 Relación: un vehículo tiene muchos gastos.
     */
    public function registrosGastos()
    {
        return $this->hasMany(\App\Models\Gasto::class, 'id_vehiculo', 'id_vehiculo');
    }
}
