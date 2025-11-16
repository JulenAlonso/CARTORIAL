<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Vehiculo;
use App\Models\Gasto;
use Illuminate\Support\Facades\Auth;

class GastoController extends Controller
{
    public function store(Request $request, $idVehiculo)
    {
        // 1) Buscar el vehículo y asegurar que pertenece al usuario autenticado (si aplica)
        $vehiculo = Vehiculo::where('id_vehiculo', $idVehiculo)
            ->where('id_usuario', Auth::id()) // si tu tabla Vehiculo tiene id_usuario
            ->firstOrFail();

        // 2) Validar datos
        $validated = $request->validate([
            'fecha_gasto' => ['required', 'date'],
            'tipo_gasto' => ['required', 'string', 'max:50'],
            'importe' => ['required', 'numeric', 'min:0'],
            'descripcion' => ['nullable', 'string'],
        ]);

        // 3) Crear gasto
        Gasto::create([
            'id_vehiculo' => $vehiculo->id_vehiculo,
            'id_usuario' => Auth::id(),
            'fecha_gasto' => $validated['fecha_gasto'],
            'tipo_gasto' => $validated['tipo_gasto'],
            'importe' => $validated['importe'],
            'descripcion' => $validated['descripcion'] ?? null,
        ]);

        // 4) Redirigir de vuelta a la vista del vehículo con mensaje
        return back()->with('success', 'Gasto registrado correctamente.');
    }
}
