<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\AuthenticatesUsers;

class LoginController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Login Controller
    |--------------------------------------------------------------------------
    |
    | Este controlador maneja la autenticación de usuarios y la redirección
    | a la pantalla principal (/home). Utiliza un trait para facilitar
    | su funcionalidad.
    |
    */

    use AuthenticatesUsers;

    /**
     * Ruta a la que se redirige el usuario después de iniciar sesión.
     *
     * @var string
     */
    protected $redirectTo = '/perfil';

    /**
     * Crear una nueva instancia del controlador.
     *
     * @return void
     */
    public function __construct()
    {
        // Solo los invitados pueden acceder a login, excepto logout
        $this->middleware('guest')->except('logout');

        // Solo usuarios autenticados pueden hacer logout
        $this->middleware('auth')->only('logout');
    }
}
