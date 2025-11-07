<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable; // 👈 para login con Auth
use Illuminate\Notifications\Notifiable;

class Usuario extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $table = 'usuarios';            // Nombre de la tabla
    protected $primaryKey = 'id_usuario';     // Clave primaria
    public $timestamps = false;               // No usa created_at / updated_at

    protected $fillable = [
        'user_name',
        'email',
        'password',
        'nombre',
        'apellidos',
        'telefono',
        'user_avatar',
    ];

    protected $hidden = [
        'password',
    ];

    /**
     * Relación: un usuario tiene muchos vehículos.
     */
    public function vehiculos()
    {
        return $this->hasMany(Vehiculo::class, 'id_usuario', 'id_usuario');
    }

    /**
     * Relación: un usuario tiene muchas notas.
     */
    public function notas()
    {
        return $this->hasMany(NotaCalendario::class, 'id_usuario', 'id_usuario');
    }

    /**
     * Getter personalizado: devuelve la URL completa del avatar.
     * Si no tiene, devuelve la imagen por defecto.
     */
    public function getAvatarUrlAttribute()
    {
        if (empty($this->user_avatar) || $this->user_avatar === '0') {
            return asset('assets/images/user.png');
        }

        // Si es URL absoluta
        if (preg_match('/^https?:\/\//', $this->user_avatar)) {
            return $this->user_avatar;
        }

        // Si está en storage
        return asset('storage/' . ltrim($this->user_avatar, '/'));
    }
}
