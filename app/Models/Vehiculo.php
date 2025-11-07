<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Vehiculo extends Model
{
    protected $table = 'vehiculos';
    protected $primaryKey = 'id_vehiculo';
    public $timestamps = false;

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

    // Relación inversa (opcional)
    public function usuario()
    {
        return $this->belongsTo(\App\Models\User::class, 'id_usuario', 'id_usuario');
    }
}
