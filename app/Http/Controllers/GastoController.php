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
        // 1) Vehículo del usuario
        $vehiculo = Vehiculo::where('id_vehiculo', $idVehiculo)
            ->where('id_usuario', Auth::id())
            ->firstOrFail();

        // 2) Validación (archivo opcional)
        $validated = $request->validate([
            'fecha_gasto' => 'required|date',
            'tipo_gasto' => 'required|string|max:50',
            'importe' => 'required|numeric|min:0',
            'descripcion' => 'nullable|string',
            'archivo' => 'nullable|file|max:20480', // 20 MB
        ]);

        // 3) Crear gasto
        $gasto = new Gasto();
        $gasto->id_vehiculo = $vehiculo->id_vehiculo;
        $gasto->id_usuario = Auth::id();
        $gasto->fecha_gasto = $validated['fecha_gasto'];
        $gasto->tipo_gasto = $validated['tipo_gasto'];
        $gasto->importe = $validated['importe'];
        $gasto->descripcion = $validated['descripcion'] ?? null;

        // 4) Guardar archivo si existe
        if ($request->hasFile('archivo')) {
            $file = $request->file('archivo');

            // Se guarda en storage/app/public/gastos
            $path = $file->store('gastos', 'public'); // "gastos/xxxx.ext"

            // Ruta relativa que luego usaremos con asset('storage/' . ...)
            $gasto->archivo_path = $path;

            // Si tienes estas columnas en la tabla, puedes usarlas también:
            // $gasto->archivo_nombre = $file->getClientOriginalName();
            // $gasto->archivo_mime   = $file->getClientMimeType();
            // $gasto->archivo_size   = $file->getSize();
        }

        // 5) Guardar gasto en BD
        $gasto->save();

        // 6) Volver con mensaje
        return back()->with('success', 'Gasto registrado correctamente.');
    }
}
