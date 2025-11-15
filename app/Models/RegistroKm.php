<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RegistroKm extends Model
{
    protected $table = 'registros_km';
    protected $primaryKey = 'id_registro_km';
    public $timestamps = false; // la tabla no tiene created_at/updated_at

    protected $fillable = [
        'id_usuario',
        'nombre_usuario',
        'email_usuario',
        'id_vehiculo',
        'matricula',
        'modelo',
        'km_vehiculo',
        'fecha_registro',
        'km_actual',
        'comentario',
    ];

    // Relaciones (opcional)
    public function vehiculo()
    {
        return $this->belongsTo(\App\Models\Vehiculo::class, 'id_vehiculo', 'id_vehiculo');
    }

    public function usuario()
    {
        return $this->belongsTo(\App\Models\User::class, 'id_usuario', 'id_usuario');
    }


}
