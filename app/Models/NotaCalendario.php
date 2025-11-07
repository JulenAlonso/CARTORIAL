<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class NotaCalendario extends Model
{
    use HasFactory;

    protected $table = 'Notas_calendario';
    protected $primaryKey = 'id_nota';
    protected $fillable = [
        'titulo',
        'descripcion',
        'fecha_evento',
        'hora_evento',
        'id_usuario',
        'id_vehiculo',
    ];

    // Relación: una nota pertenece a un usuario
    public function usuario()
    {
        return $this->belongsTo(Usuario::class, 'id_usuario', 'id_usuario');
    }

    // Relación: una nota pertenece a un vehículo (opcional)
    public function vehiculo()
    {
        return $this->belongsTo(Vehiculo::class, 'id_vehiculo', 'id_vehiculo');
    }
}
