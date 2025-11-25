<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Vehiculo;
use App\Models\Gasto;
use Illuminate\Support\Facades\Auth;

class GastoController extends Controller
{
    public function store(Request $request, int $idVehiculo)
    {
        // Usuario autenticado (tu PK es id_usuario)
        $user = Auth::user();
        if (!$user) {
            abort(403);
        }

        // Vehículo del usuario
        $vehiculo = Vehiculo::where('id_vehiculo', $idVehiculo)
            ->where('id_usuario', $user->id_usuario)
            ->firstOrFail();

        // Validación
        $validated = $request->validate([
            'fecha_gasto' => ['required', 'date'],
            'tipo_gasto'  => ['required', 'string', 'max:50'],
            'importe'     => ['required', 'numeric', 'min:0'],
            'descripcion' => ['nullable', 'string'],
            'archivo'     => ['nullable', 'file', 'max:20480'], // 20 MB
        ]);

        // Crear gasto
        $gasto = new Gasto();
        $gasto->id_vehiculo  = $vehiculo->id_vehiculo;
        $gasto->id_usuario   = $user->id_usuario;
        $gasto->fecha_gasto  = $validated['fecha_gasto'];
        $gasto->tipo_gasto   = $validated['tipo_gasto'];
        $gasto->importe      = (float) $validated['importe'];
        $gasto->descripcion  = $validated['descripcion'] ?? null;

        // Guardar archivo si se ha subido
        if ($request->hasFile('archivo')) {
            $file = $request->file('archivo');

            // Se guarda en storage/app/public/gastos
            $path = $file->store('gastos', 'public'); // ⬅️ devuelve "gastos/loquesea.pdf"

            // Guardamos solo la ruta relativa
            $gasto->archivo_path = $path;
        }

        $gasto->save();

        return back()->with('success', 'Gasto registrado correctamente.');
    }
}
