<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RegistroKm extends Model
{
    protected $table = 'registros_km';
    protected $primaryKey = 'id_registro_km';
    public $incrementing = true;
    protected $keyType = 'int';
    public $timestamps = false; // la tabla no tiene created_at/updated_at

    protected $fillable = [
        'id_vehiculo',
        'fecha_registro',
        'km_actual',
        'comentario',
    ];

    // 🔹 Relación: muchos registros_km pertenecen a un vehículo
    public function vehiculo()
    {
        return $this->belongsTo(\App\Models\Vehiculo::class, 'id_vehiculo', 'id_vehiculo');
    }
}
