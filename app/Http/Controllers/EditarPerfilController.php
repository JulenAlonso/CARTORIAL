<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class EditarPerfilController extends Controller
{
    public function create()
    {
        $user = Auth::user();
        return view('auth.editar', compact('user'));
    }

    public function update(Request $request)
    {
        $user = Auth::user();

        $validated = $request->validate([
            'user_name'   => 'required|string|max:100',
            'nombre'      => 'nullable|string|max:100',
            'apellidos'   => 'nullable|string|max:150',
            'email'       => 'required|email|max:150|unique:usuarios,email,' . $user->id_usuario . ',id_usuario',
            'telefono'    => 'nullable|string|max:30',
            'user_avatar' => 'nullable|image|max:2048',
        ]);

        if ($request->hasFile('user_avatar')) {
            $validated['user_avatar'] = $request->file('user_avatar')->store('avatars', 'public');
        }

        $user->fill($validated)->save();

        return redirect()->route('perfil')->with('ok', 'Perfil actualizado correctamente.');
    }
}
