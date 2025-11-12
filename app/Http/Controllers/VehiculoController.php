<?php

namespace App\Http\Controllers;

use App\Models\Vehiculo;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;

class VehiculoController extends Controller
{
    /**
     * CREAR un vehículo (alta).
     */
    public function store(Request $request)
    {
        $userId = (int) Auth::id();
        $currentYear = now()->year;

        // Normaliza dinero: "1.234,56" -> "1234.56"
        $money = function (?string $v) {
            if ($v === null) return null;
            $v = trim($v);
            if ($v === '') return null;
            $v = str_replace('.', '', $v);
            $v = str_replace(',', '.', $v);
            return is_numeric($v) ? $v : null;
        };

        // Validación (matrícula única por usuario)
        $validated = $request->validate([
            'matricula' => [
                'required', 'string', 'max:20',
                Rule::unique('vehiculos', 'matricula')->where(fn ($q) => $q->where('id_usuario', $userId)),
            ],
            'marca'               => ['required', 'string', 'max:100'],
            'modelo'              => ['required', 'string', 'max:100'],
            'anio'                => ['nullable', 'integer', 'between:1900,' . $currentYear],
            'fecha_compra'        => ['nullable', 'date'],
            'km'                  => ['nullable', 'integer', 'min:0'],
            'cv'                  => ['nullable', 'integer', 'min:0'],
            'combustible'         => ['nullable', 'string', 'max:50'],
            'etiqueta'            => ['nullable', 'string', 'max:20'],
            'precio'              => ['nullable', 'string', 'max:30'],
            'precio_segunda_mano' => ['nullable', 'string', 'max:30'],
            'car_avatar'          => ['nullable', 'image', 'mimes:jpeg,png,jpg,webp,avif', 'max:4096'],
        ]);

        // Normalizaciones numéricas
        foreach (['anio', 'km', 'cv'] as $k) {
            if (isset($validated[$k])) {
                $validated[$k] = (int) preg_replace('/\D+/', '', (string) $validated[$k]);
            }
        }

        // Normalizar precios
        $validated['precio'] = $money($validated['precio'] ?? null);
        $validated['precio_segunda_mano'] = $money($validated['precio_segunda_mano'] ?? null);

        // Subida de imagen (opcional)
        if ($request->hasFile('car_avatar')) {
            $path = $request->file('car_avatar')->store('cars/' . $userId, 'public');
            $validated['car_avatar'] = $path;
        }

        // ⚠️ Si no hay imagen, usar cadena vacía (evita error NOT NULL)
        if (!array_key_exists('car_avatar', $validated)) {
            $validated['car_avatar'] = '';
        }

        // Asignar propietario
        $validated['id_usuario'] = $userId;

        // Crear vehículo
        Vehiculo::create($validated);

        // ✅ Redirigir al perfil
        return redirect()
            ->route('perfil')
            ->with('status', 'Vehículo añadido correctamente.');
    }

    /**
     * Actualizar un vehículo.
     */
    public function update(Request $request, Vehiculo $vehiculo)
    {
        if ((int) $vehiculo->id_usuario !== (int) Auth::id()) {
            abort(403);
        }

        $money = function (?string $v) {
            if ($v === null) return null;
            $v = trim($v);
            if ($v === '') return null;
            $v = str_replace('.', '', $v);
            $v = str_replace(',', '.', $v);
            return is_numeric($v) ? $v : null;
        };

        $currentYear = now()->year;

        $validated = $request->validate([
            'matricula'           => ['required', 'string', 'max:20'],
            'marca'               => ['required', 'string', 'max:100'],
            'modelo'              => ['required', 'string', 'max:100'],
            'anio'                => ['nullable', 'integer', 'between:1900,' . $currentYear],
            'fecha_compra'        => ['nullable', 'date'],
            'km'                  => ['nullable', 'integer', 'min:0'],
            'cv'                  => ['nullable', 'integer', 'min:0'],
            'combustible'         => ['nullable', 'string', 'max:50'],
            'etiqueta'            => ['nullable', 'string', 'max:20'],
            'precio'              => ['nullable', 'string', 'max:30'],
            'precio_segunda_mano' => ['nullable', 'string', 'max:30'],
            'car_avatar'          => ['nullable', 'image', 'mimes:jpeg,png,jpg,webp,avif', 'max:4096'],
        ]);

        $validated['precio'] = $money($validated['precio'] ?? null);
        $validated['precio_segunda_mano'] = $money($validated['precio_segunda_mano'] ?? null);

        if ($request->hasFile('car_avatar')) {
            if ($vehiculo->car_avatar && Storage::disk('public')->exists($vehiculo->car_avatar)) {
                Storage::disk('public')->delete($vehiculo->car_avatar);
            }
            $path = $request->file('car_avatar')->store('cars/' . Auth::id(), 'public');
            $validated['car_avatar'] = $path;
        }

        $vehiculo->update($validated);

        // ✅ Redirigir al perfil también al editar
        return redirect()
            ->route('perfil')
            ->with('status', 'Vehículo actualizado correctamente.');
    }

    /**
     * Mostrar lista de vehículos para editar (no se usa para redirección).
     */
    public function selectToEdit()
    {
        $user = auth()->user();

        $vehiculos = $user->vehiculos()
            ->select(
                'id_vehiculo','marca','modelo','matricula','anio','km','cv',
                'combustible','etiqueta','precio','precio_segunda_mano',
                'fecha_compra','car_avatar'
            )
            ->orderBy('anio', 'desc')
            ->get();

        $vehiculoSel = null;
        if (request()->filled('vehiculo')) {
            $vehiculoSel = $user->vehiculos()
                ->where('id_vehiculo', request('vehiculo'))
                ->first();
        }

        return view('auth.editarVehiculo', compact('vehiculos', 'vehiculoSel'));
    }

    public function create()
    {
        return view('auth.vehiculo');
    }

    public function destroy(Vehiculo $vehiculo)
{
    // Solo el dueño puede borrar
    if ((int) $vehiculo->id_usuario !== (int) Auth::id()) {
        abort(403);
    }

    // Borra la imagen si existe en storage
    if (!empty($vehiculo->car_avatar) && Storage::disk('public')->exists($vehiculo->car_avatar)) {
        Storage::disk('public')->delete($vehiculo->car_avatar);
    }

    // Si tienes registros relacionados (km, gastos, notas) y no tienes ON DELETE CASCADE,
    // bórralos aquí antes del vehículo.

    $vehiculo->delete();

    return redirect()
        ->route('perfil')
        ->with('status', 'Vehículo eliminado correctamente.');
}

}
