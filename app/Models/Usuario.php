<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable; // Para trabajar con Auth
use Illuminate\Notifications\Notifiable;

/**
 * Modelo Usuario
 *
 * Este modelo representa a los usuarios de la aplicación y se utiliza
 * como modelo de autenticación (login, Auth::user(), etc).
 *
 * Está asociado a la tabla `usuarios`, no a la típica `users` de Laravel.
 */
class Usuario extends Authenticatable
{
    use HasFactory, Notifiable;

    /**
     * Nombre de la tabla en la base de datos.
     */
    protected $table = 'usuarios';

    /**
     * Clave primaria de la tabla.
     *
     * En tu esquema es `id_usuario` en vez de `id`.
     */
    protected $primaryKey = 'id_usuario';

    /**
     * La tabla `usuarios` no usa created_at / updated_at.
     */
    public $timestamps = false;

    /**
     * Campos que se pueden asignar masivamente (mass assignment),
     * por ejemplo con Usuario::create([...]) o $usuario->update([...]).
     */
    protected $fillable = [
        'user_name',
        'email',
        'password',
        'nombre',
        'apellidos',
        'telefono',
        'user_avatar',
        'admin',
    ];

    /**
     * Campos que NO se deben exponer cuando el modelo se convierte a
     * array o JSON (por ejemplo en APIs o dumps debug).
     */
    protected $hidden = [
        'password',
        // 'remember_token', // por si en el futuro lo añades
    ];

    /**
     * Conversión automática de tipos para ciertos atributos.
     *
     * - `admin` se trata como boolean para poder usarlo como true/false.
     */
    protected $casts = [
        'admin' => 'boolean',
    ];

    /**
     * Relación: un usuario tiene muchos vehículos.
     *
     * FK en `vehiculos`:  id_usuario
     * PK en `usuarios`:   id_usuario
     *
     * Uso:
     *   $usuario->vehiculos;
     *   $usuario->vehiculos()->where('marca', 'Volkswagen')->get();
     */
    public function vehiculos()
    {
        return $this->hasMany(
            Vehiculo::class,
            'id_usuario',
            'id_usuario'
        );
    }

    /**
     * Relación: un usuario tiene muchas notas de calendario.
     *
     * FK en `notas_calendario`: id_usuario
     * PK en `usuarios`:         id_usuario
     *
     * Uso:
     *   $usuario->notas;
     *   $usuario->notas()->orderBy('fecha_evento')->get();
     */
    public function notas()
    {
        return $this->hasMany(
            NotaCalendario::class,
            'id_usuario',
            'id_usuario'
        );
    }

    /**
     * Accessor: devuelve la URL completa del avatar del usuario.
     *
     * Permite hacer:
     *   $usuario->avatar_url
     *
     * Lógica:
     * - Si no tiene avatar, devuelve una imagen por defecto.
     * - Si el campo es una URL completa (http/https), se devuelve tal cual.
     * - En otro caso, se asume que es una ruta en storage/app/public
     *   y se construye con asset('storage/...').
     */
    public function getAvatarUrlAttribute()
    {
        // Sin avatar: imagen por defecto
        if (empty($this->user_avatar) || $this->user_avatar === '0') {
            return asset('assets/images/user.png');
        }

        // Si ya es una URL absoluta (por ejemplo avatar de Google, etc.)
        if (preg_match('/^https?:\/\//', $this->user_avatar)) {
            return $this->user_avatar;
        }

        // Si es una ruta relativa dentro de storage
        return asset('storage/' . ltrim($this->user_avatar, '/'));
    }

    /**
     * Helper para saber si el usuario es administrador.
     *
     * Uso:
     *   if ($usuario->isAdmin()) { ... }
     *   @if(auth()->user()->isAdmin()) ... @endif
     */
    public function isAdmin(): bool
    {
        return (bool) $this->admin;
    }

    /**
     * (Opcional) Helper para obtener el nombre completo.
     *
     * Uso:
     *   $usuario->nombre_completo
     */
    public function getNombreCompletoAttribute(): string
    {
        return trim(($this->nombre ?? '') . ' ' . ($this->apellidos ?? ''));
    }
}
