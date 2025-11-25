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

        if (!$user) {
            abort(403);
        }

        // En tu bbdd el campo es id_usuario
        $userId = $user->id_usuario;

        // Validación básica
        $data = $request->validate([
            'fecha_evento' => ['required', 'date'],
            'hora_evento' => ['nullable', 'date_format:H:i'], // TIME en formato HH:MM
            'titulo' => ['required', 'string', 'max:200'],
            'descripcion' => ['nullable', 'string'],
            'id_vehiculo' => ['nullable', 'integer'],
        ]);

        DB::table('notas_calendario')->insert([
            'id_usuario' => $userId,
            'id_vehiculo' => !empty($data['id_vehiculo']) ? $data['id_vehiculo'] : null,
            'titulo' => $data['titulo'],
            'descripcion' => $data['descripcion'] ?? null,
            'fecha_evento' => $data['fecha_evento'],
            'hora_evento' => $data['hora_evento'] ?? null,
            'fecha_creacion' => now(), // tu tabla tiene DEFAULT CURRENT_TIMESTAMP, pero así queda explícito
        ]);

        return redirect()
            ->route('perfil')
            ->with('success', 'Nota guardada correctamente.');
    }
}
