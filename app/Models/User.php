<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use Notifiable;

    protected $table = 'usuarios';
    protected $primaryKey = 'id_usuario';
    public $timestamps = false;

    protected $fillable = [
        'user_name',
        'email',
        'password',
        'nombre',
        'apellidos',
        'user_avatar',
        'telefono',
    ];

    // 🔹 Relación con vehículos
    public function vehiculos()
    {
        return $this->hasMany(\App\Models\Vehiculo::class, 'id_usuario', 'id_usuario');
    }
}
