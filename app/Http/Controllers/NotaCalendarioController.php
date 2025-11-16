<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class NotaCalendarioController extends Controller
{
    public function store(Request $request)
    {
        $user = Auth::user();

        // Algunos proyectos usan id_usuario, otros id.
        $userId = $user->id_usuario ?? $user->id;

        // Validación básica
        $data = $request->validate([
            'fecha_evento' => 'required|date',
            'hora_evento'  => 'nullable',
            'titulo'       => 'required|string|max:200',
            'descripcion'  => 'nullable|string',
            'id_vehiculo'  => 'nullable|integer',
        ]);

        DB::table('notas_calendario')->insert([
            'id_usuario'    => $userId,
            'id_vehiculo'   => $data['id_vehiculo'] ?: null,
            'titulo'        => $data['titulo'],
            'descripcion'   => $data['descripcion'] ?? null,
            'fecha_evento'  => $data['fecha_evento'],
            'hora_evento'   => $data['hora_evento'] ?: null,
            'fecha_creacion'=> now(),
        ]);

        return redirect()
            ->route('perfil')
            ->with('success', 'Nota guardada correctamente.');
    
    
    
        }
}
