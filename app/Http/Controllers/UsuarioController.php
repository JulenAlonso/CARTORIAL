<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Usuario;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;

class UsuarioController extends Controller
{
    // Listar todos los usuarios (vista de administración, si la usas)
    public function index()
    {
        $usuarios = Usuario::all();
        return view('usuarios.index', compact('usuarios'));
    }

    // Mostrar el perfil del usuario autenticado
    public function perfil()
    {
        $usuario = Auth::user();

        if (!$usuario) {
            abort(403);
        }

        // Si en el modelo Usuario tienes la relación vehiculos():
        // $usuario->load('vehiculos');

        return view('usuarios.perfil', compact('usuario'));
    }

    // Mostrar un usuario concreto
    public function show(string $id)
    {
        // Ojo: en el modelo Usuario deberías tener:
        // protected $primaryKey = 'id_usuario';
        $usuario = Usuario::findOrFail($id);

        return view('usuarios.show', compact('usuario'));
    }

    // Mostrar formulario para crear un nuevo usuario
    public function create()
    {
        return view('usuarios.create');
    }

    // Guardar nuevo usuario en la base de datos
    public function store(Request $request)
    {
        $request->validate([
            'nombre' => 'required|string|max:100',   // VARCHAR(100)
            'apellidos' => 'required|string|max:150',   // VARCHAR(150)
            'email' => 'required|email|max:255|unique:usuarios,email',
            'telefono' => 'nullable|string|max:50',    // VARCHAR(50)
            'user_name' => 'required|string|max:100|unique:usuarios,user_name',
            'password' => 'required|string|min:6|confirmed',
        ]);

        Usuario::create([
            'nombre' => $request->nombre,
            'apellidos' => $request->apellidos,
            'email' => $request->email,
            'telefono' => $request->telefono,
            'user_name' => $request->user_name,
            'password' => Hash::make($request->password),
            'user_avatar' => null,  // opcional, la bbdd lo pone a NULL por defecto
            'admin' => 0,     // opcional, por defecto 0 en la bbdd
        ]);

        return redirect()
            ->route('usuarios.index')
            ->with('success', 'Usuario creado correctamente.');
    }
}
