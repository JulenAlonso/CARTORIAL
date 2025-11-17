<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Gasto extends Model
{
    protected $table = 'gastos';
    protected $primaryKey = 'id_gasto';
    public $timestamps = false; // tu tabla no usa created_at / updated_at

    protected $fillable = [
        'id_vehiculo',
        'id_usuario',
        'fecha_gasto',
        'tipo_gasto',
        'importe',
        'descripcion',

        // 📎 NUEVOS CAMPOS PARA ARCHIVO
        'archivo_path',
        'archivo_nombre',
        'archivo_mime',
        'archivo_size',
    ];

    // 🔗 Relación con Vehiculo
    public function vehiculo()
    {
        return $this->belongsTo(\App\Models\Vehiculo::class, 'id_vehiculo', 'id_vehiculo');
    }

    // 🔗 Relación con User
    public function usuario()
    {
        return $this->belongsTo(\App\Models\User::class, 'id_usuario', 'id');
    }
}
