<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Gasto extends Model
{
    protected $table = 'gastos';
    protected $primaryKey = 'id_gasto';
    public $timestamps = false; // si tu tabla no tiene created_at / updated_at

    protected $fillable = [
        'id_vehiculo',
        'id_usuario',
        'fecha_gasto',
        'tipo_gasto',
        'importe',
        'descripcion',
    ];

    // 🔗 Relación con Vehiculo
    public function vehiculo()
    {
        return $this->belongsTo(\App\Models\Vehiculo::class, 'id_vehiculo', 'id_vehiculo');
    }

    // 🔗 Relación con User (si la necesitas)
    public function usuario()
    {
        return $this->belongsTo(\App\Models\User::class, 'id_usuario', 'id');
    }
}
