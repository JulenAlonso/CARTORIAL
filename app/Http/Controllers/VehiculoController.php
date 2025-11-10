<?php

namespace App\Http\Controllers;

use App\Models\Vehiculo;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class VehiculoController extends Controller
{
    /**
     * Mostrar formulario de edición de un vehículo concreto.
     * Redirigimos al selector con ?vehiculo=ID para editar en la misma página.
     */
    public function edit(Vehiculo $vehiculo)
    {
        if ((int) $vehiculo->id_usuario !== (int) Auth::id()) {
            abort(403);
        }

        // Redirige a la página de selección con el vehículo abierto
        return redirect()->route('editarVehiculo.create', ['vehiculo' => $vehiculo->id_vehiculo]);
    }

    /**
     * Actualizar un vehículo.
     */
    public function update(Request $request, Vehiculo $vehiculo)
    {
        if ((int) $vehiculo->id_usuario !== (int) Auth::id()) {
            abort(403);
        }

        // Normaliza dinero: "1.234,56" -> "1234.56"
        $money = function (?string $v) {
            if ($v === null)
                return null;
            $v = trim($v);
            if ($v === '')
                return null;
            $v = str_replace('.', '', $v);
            $v = str_replace(',', '.', $v);
            return is_numeric($v) ? $v : null;
        };

        $currentYear = now()->year;

        $validated = $request->validate([
            'matricula' => ['required', 'string', 'max:20'],
            'marca' => ['required', 'string', 'max:100'],
            'modelo' => ['required', 'string', 'max:100'],
            'anio' => ['nullable', 'integer', 'between:1900,' . $currentYear],
            'fecha_compra' => ['nullable', 'date'],
            'km' => ['nullable', 'integer', 'min:0'],
            'cv' => ['nullable', 'integer', 'min:0'],
            'combustible' => ['nullable', 'string', 'max:50'], // Gasolina, Diésel, Híbrido, Eléctrico
            'etiqueta' => ['nullable', 'string', 'max:20'], // C, B, ECO, 0
            'precio' => ['nullable', 'string', 'max:30'],
            'precio_segunda_mano' => ['nullable', 'string', 'max:30'],
            'car_avatar' => ['nullable', 'image', 'mimes:jpeg,png,jpg,webp,avif', 'max:4096'], // 4MB
        ]);

        // Convertir precio(s) a float normalizados
        $validated['precio'] = $money($validated['precio'] ?? null);
        $validated['precio_segunda_mano'] = $money($validated['precio_segunda_mano'] ?? null);

        // Subida de imagen (si llega)
        if ($request->hasFile('car_avatar')) {
            // Borrar el anterior si existía
            if ($vehiculo->car_avatar && Storage::disk('public')->exists($vehiculo->car_avatar)) {
                Storage::disk('public')->delete($vehiculo->car_avatar);
            }

            // Guarda en storage/app/public/cars/{userId}/...
            $path = $request->file('car_avatar')->store('cars/' . Auth::id(), 'public');
            $validated['car_avatar'] = $path;
        }

        $vehiculo->update($validated);

        // Volver a la página de selección con el mismo coche abierto
        return redirect()
            ->route('editarVehiculo.create', ['vehiculo' => $vehiculo->id_vehiculo])
            ->with('status', 'Vehículo actualizado correctamente.');
    }

    /**
     * Listado para seleccionar qué vehículo editar (botón del sidebar)
     * y, si viene ?vehiculo=ID, cargamos ese vehículo COMPLETO para el formulario.
     */
    public function selectToEdit()
    {
        $user = auth()->user();

        // Lista ligera para la columna izquierda
        $vehiculos = $user->vehiculos()
            ->select(
                'id_vehiculo',
                'marca',
                'modelo',
                'matricula',
                'anio',
                'km',
                'cv',
                'combustible',
                'etiqueta',
                'precio',
                'precio_segunda_mano',
                'fecha_compra',
                'car_avatar'
            )
            ->orderBy('anio', 'desc')
            ->get();

        // Vehículo seleccionado (si llega por query param) con TODAS las columnas
        $vehiculoSel = null;
        if (request()->filled('vehiculo')) {
            $vehiculoSel = $user->vehiculos()
                ->where('id_vehiculo', request('vehiculo'))
                ->first(); // todas las columnas: km, cv, combustible, etiqueta, precios, fecha_compra, etc.
        }

        // Vista correcta en /resources/views/vehiculos/editarVehiculo.blade.php
        return view('auth.editarVehiculo', compact('vehiculos', 'vehiculoSel'));
    }

    public function create()
    {
        return view('auth.vehiculo'); // usar la vista existente
    }
}
