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
            if ($v === null)
                return null;
            $v = trim($v);
            if ($v === '')
                return null;
            $v = str_replace('.', '', $v);
            $v = str_replace(',', '.', $v);
            return is_numeric($v) ? $v : null;
        };

        // VALIDACIONES
        $validated = $request->validate([
            'matricula' => [
                'required',
                'string',
                'max:20',
                // Acepta:
                // - 1234FSW o 1234 FSW (formato moderno)
                // - M-1234          (provincial numérico)
                // - M-1234-AB       (provincial alfanumérico)
                'regex:/^(\d{4}\s?[BCDFGHJKLMNPRSTVWXYZ]{3}|[A-Z]{1,2}-\d{4}-[A-Z]{2}|[A-Z]{1,2}-\d{4})$/i',
                Rule::unique('vehiculos', 'matricula')
                    ->where(fn($q) => $q->where('id_usuario', $userId)),
            ],

            'marca' => ['required', 'string', 'max:100'],
            'modelo' => ['required', 'string', 'max:100'],

            'anio_fabricacion' => ['nullable', 'integer', 'between:1886,' . $currentYear],
            'anio_matriculacion' => ['nullable', 'integer', 'between:1886,' . $currentYear],

            'fecha_compra' => ['nullable', 'date'],
            'km' => ['nullable', 'integer', 'min:0'],
            'cv' => ['nullable', 'integer', 'min:0'],
            'combustible' => ['nullable', 'string', 'max:50'],
            'etiqueta' => ['nullable', 'string', 'max:20'],
            'precio' => ['nullable', 'string', 'max:30'],
            'precio_segunda_mano' => ['nullable', 'string', 'max:30'],
            'car_avatar' => ['nullable', 'image', 'mimes:jpeg,png,jpg,webp,avif', 'max:4096'],
        ], [
            'matricula.regex' => 'El formato de la matrícula no es válido.',
        ]);

        // REGLA LÓGICA EXTRA — fabricación <= matriculación
        if (
            !empty($validated['anio_fabricacion']) &&
            !empty($validated['anio_matriculacion']) &&
            $validated['anio_matriculacion'] < $validated['anio_fabricacion']
        ) {
            return back()->withErrors([
                'anio_matriculacion' => 'El año de matriculación no puede ser anterior al de fabricación.'
            ])->withInput();
        }

        // Normalizaciones numéricas
        foreach (['anio_fabricacion', 'anio_matriculacion', 'km', 'cv'] as $k) {
            if (isset($validated[$k])) {
                $validated[$k] = (int) preg_replace('/\D+/', '', (string) $validated[$k]);
            }
        }

        // Normalizar precios
        $validated['precio'] = $money($validated['precio'] ?? null);
        $validated['precio_segunda_mano'] = $money($validated['precio_segunda_mano'] ?? null);

        // Imagen opcional
        if ($request->hasFile('car_avatar')) {
            $path = $request->file('car_avatar')->store('cars/' . $userId, 'public');
            $validated['car_avatar'] = $path;
        }

        if (!array_key_exists('car_avatar', $validated)) {
            $validated['car_avatar'] = '';
        }

        // Asignar propietario
        $validated['id_usuario'] = $userId;

        Vehiculo::create($validated);

        return redirect()
            ->route('perfil')
            ->with('status', 'Vehículo añadido correctamente.');
    }

    /**
     * Actualizar un vehículo existente.
     */
    public function update(Request $request, Vehiculo $vehiculo)
    {
        if ((int) $vehiculo->id_usuario !== (int) Auth::id()) {
            abort(403);
        }

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
            'matricula' => [
                'required',
                'string',
                'max:20',
                // Misma lógica que en store, pero sin unique
                'regex:/^(\d{4}\s?[BCDFGHJKLMNPRSTVWXYZ]{3}|[A-Z]{1,2}-\d{4}-[A-Z]{2}|[A-Z]{1,2}-\d{4})$/i',
            ],

            'marca' => ['required', 'string', 'max:100'],
            'modelo' => ['required', 'string', 'max:100'],

            'anio_fabricacion' => ['nullable', 'integer', 'between:1886,' . $currentYear],
            'anio_matriculacion' => ['nullable', 'integer', 'between:1886,' . $currentYear],

            'fecha_compra' => ['nullable', 'date'],
            'km' => ['nullable', 'integer', 'min:0'],
            'cv' => ['nullable', 'integer', 'min:0'],
            'combustible' => ['nullable', 'string', 'max:50'],
            'etiqueta' => ['nullable', 'string', 'max:20'],
            'precio' => ['nullable', 'string', 'max:30'],
            'precio_segunda_mano' => ['nullable', 'string', 'max:30'],
            'car_avatar' => ['nullable', 'image', 'mimes:jpeg,png,jpg,webp,avif', 'max:4096'],
        ], [
            'matricula.regex' => 'El formato de la matrícula no es válido.',
        ]);

        // Regla lógica fabricación <= matriculación
        if (
            !empty($validated['anio_fabricacion']) &&
            !empty($validated['anio_matriculacion']) &&
            $validated['anio_matriculacion'] < $validated['anio_fabricacion']
        ) {
            return back()->withErrors([
                'anio_matriculacion' => 'El año de matriculación no puede ser anterior al de fabricación.'
            ])->withInput();
        }

        // Normalizar enteros
        foreach (['anio_fabricacion', 'anio_matriculacion', 'km', 'cv'] as $k) {
            if (isset($validated[$k])) {
                $validated[$k] = (int) preg_replace('/\D+/', '', (string) $validated[$k]);
            }
        }

        // Normalizar precios
        $validated['precio'] = $money($validated['precio'] ?? null);
        $validated['precio_segunda_mano'] = $money($validated['precio_segunda_mano'] ?? null);

        // Imagen
        if ($request->hasFile('car_avatar')) {
            if ($vehiculo->car_avatar && Storage::disk('public')->exists($vehiculo->car_avatar)) {
                Storage::disk('public')->delete($vehiculo->car_avatar);
            }
            $path = $request->file('car_avatar')->store('cars/' . Auth::id(), 'public');
            $validated['car_avatar'] = $path;
        }

        $vehiculo->update($validated);

        return redirect()
            ->route('perfil')
            ->with('status', 'Vehículo actualizado correctamente.');
    }

    public function selectToEdit()
    {
        $user = auth()->user();

        $vehiculos = $user->vehiculos()
            ->select(
                'id_vehiculo',
                'marca',
                'modelo',
                'matricula',
                'anio_fabricacion',
                'anio_matriculacion',
                'km',
                'cv',
                'combustible',
                'etiqueta',
                'precio',
                'precio_segunda_mano',
                'fecha_compra',
                'car_avatar'
            )
            ->orderBy('anio_matriculacion', 'desc')
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
        $currentYear = now()->year;
        return view('auth.vehiculo', compact('currentYear'));
    }

    public function destroy(Vehiculo $vehiculo)
    {
        if ((int) $vehiculo->id_usuario !== (int) Auth::id()) {
            abort(403);
        }

        if (!empty($vehiculo->car_avatar) && Storage::disk('public')->exists($vehiculo->car_avatar)) {
            Storage::disk('public')->delete($vehiculo->car_avatar);
        }

        $vehiculo->delete();

        return redirect()
            ->route('perfil')
            ->with('status', 'Vehículo eliminado correctamente.');
    }
}
