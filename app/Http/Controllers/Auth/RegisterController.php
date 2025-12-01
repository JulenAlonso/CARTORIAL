<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Providers\RouteServiceProvider;
use App\Models\Usuario; 
use Illuminate\Foundation\Auth\RegistersUsers;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class RegisterController extends Controller
{
    use RegistersUsers;

    protected $redirectTo = '/perfil';

    public function __construct()
    {
        $this->middleware('guest');
    }

    protected function validator(array $data)
    {
        return Validator::make($data, [
            'user_name'  => ['required', 'string'],
            'email'      => ['required', 'string', 'email'],
            'password'   => ['required', 'string'],
            'nombre'     => ['required', 'string'],
            'apellidos'  => ['required', 'string'],
            'telefono'   => ['nullable', 'string'],
        ]);
    }

    protected function create(array $data)
    {
        // Guardar avatar
        if (request()->hasFile('user_avatar')) {
            $avatarPath = request()->file('user_avatar')->store('avatars', 'public');
        } else {
            $avatarPath = 'assets/images/user.png';
        }

        // ✔ Guarda el teléfono si viene, o null si no
        return Usuario::create([
            'user_name'   => $data['user_name'],
            'email'       => $data['email'],
            'password'    => Hash::make($data['password']),
            'nombre'      => $data['nombre'],
            'apellidos'   => $data['apellidos'],
            'telefono'    => $data['telefono'] ?? null, 
            'user_avatar' => $avatarPath,
        ]);
    }
}
