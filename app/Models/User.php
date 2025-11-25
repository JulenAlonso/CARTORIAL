<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

/**
 * Modelo User
 *
 * Este modelo representa a los usuarios de la aplicación y está
 * vinculado directamente con la tabla `usuarios` de la base de datos.
 *
 * Extiende de `Authenticatable` porque Laravel lo utiliza como
 * modelo base para la autenticación (`Auth::user()`, login, etc.).
 */
class User extends Authenticatable
{
    use Notifiable;

    /**
     * Nombre de la tabla asociada en la base de datos.
     *
     * En tu caso no es `users`, sino `usuarios`.
     */
    protected $table = 'usuarios';

    /**
     * Clave primaria de la tabla.
     *
     * Tu PK es `id_usuario` en lugar de `id`.
     */
    protected $primaryKey = 'id_usuario';

    /**
     * La tabla `usuarios` NO tiene columnas created_at / updated_at.
     */
    public $timestamps = false;

    /**
     * Campos que se pueden rellenar mediante asignación masiva
     * (por ejemplo con `User::create([...])` o `$user->update([...])`).
     */
    protected $fillable = [
        'user_name',
        'email',
        'password',
        'nombre',
        'apellidos',
        'user_avatar',
        'telefono',
        'admin',
    ];

    /**
     * Campos que NO se deben exponer cuando se convierte el modelo
     * a array o JSON (por ejemplo en APIs o debug).
     */
    protected $hidden = [
        'password',
        // Si algún día añades remember_token:
        // 'remember_token',
    ];

    /**
     * Casts de tipos para ciertos campos.
     *
     * - `admin` se fuerza a boolean para poder usar $user->admin como true/false.
     */
    protected $casts = [
        'admin' => 'boolean',
    ];

    /**
     * Relación: un usuario puede tener muchos vehículos.
     *
     * FK en vehiculos: id_usuario
     * PK en usuarios: id_usuario
     *
     * Permite hacer:
     *   $user->vehiculos
     *   $user->vehiculos()->where(...)->get();
     */
    public function vehiculos()
    {
        return $this->hasMany(
            \App\Models\Vehiculo::class,
            'id_usuario',
            'id_usuario'
        );
    }

    /**
     * Helper cómodo para saber si el usuario es administrador.
     *
     * Uso:
     *   if ($user->isAdmin()) { ... }
     */
    public function isAdmin(): bool
    {
        return (bool) $this->admin;
    }
}
