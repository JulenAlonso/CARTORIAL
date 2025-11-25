<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class EditarPerfilController extends Controller
{
    public function create()
    {
        $user = Auth::user();

        if (!$user) {
            abort(403);
        }

        return view('auth.editar', compact('user'));
    }

    public function update(Request $request)
    {
        $user = Auth::user();

        if (!$user) {
            abort(403);
        }

        $validated = $request->validate([
            'user_name' => [
                'required',
                'string',
                'max:100', // VARCHAR(100) en la bbdd
                // UNIQUE ignorando al usuario actual
                'unique:usuarios,user_name,' . $user->id_usuario . ',id_usuario',
            ],
            'nombre' => ['nullable', 'string', 'max:100'],  // VARCHAR(100)
            'apellidos' => ['nullable', 'string', 'max:150'],  // VARCHAR(150)
            'email' => [
                'required',
                'email',
                'max:255', // VARCHAR(255) en la bbdd
                'unique:usuarios,email,' . $user->id_usuario . ',id_usuario',
            ],
            'telefono' => ['nullable', 'string', 'max:50'], // VARCHAR(50)
            'user_avatar' => ['nullable', 'image', 'mimes:jpeg,png,jpg,webp,avif', 'max:2048'],
        ]);

        if ($request->hasFile('user_avatar')) {
            $validated['user_avatar'] = $request
                ->file('user_avatar')
                ->store('avatars', 'public');
        }

        $user->fill($validated)->save();

        return redirect()
            ->route('perfil')
            ->with('ok', 'Perfil actualizado correctamente.');
    }
}
