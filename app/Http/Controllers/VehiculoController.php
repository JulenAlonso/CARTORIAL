<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Vehiculo;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Database\QueryException;
use Throwable;

class VehiculoController extends Controller
{
    /**
     * Mostrar formulario de alta de vehículo.
     */
    public function create()
    {
        return view('auth.vehiculo');
    }

    /**
     * Guardar el vehículo en la base de datos.
     */
    public function store(Request $request)
    {
        $currentYear = now()->year;

        // --- 1) Normalización en servidor (antes de validar) ---
        $input = $request->all();

        // enteros (anio, km, cv)
        foreach (['anio','km','cv'] as $k) {
            if (isset($input[$k])) {
                $input[$k] = preg_replace('/\D+/', '', (string)$input[$k]);
            }
        }

        // dinero "1.234,56" -> "1234.56" (o null si viene vacío)
        foreach (['precio','precio_segunda_mano'] as $k) {
            if (array_key_exists($k, $input)) {
                $val = trim((string)$input[$k]);
                $input[$k] = $val === '' ? null : str_replace(['.', ','], ['', '.'], $val);
            }
        }

        // Combustible: aceptar Diesel/Diésel en distintas grafías
        if (!empty($input['combustible'])) {
            $comb = trim($input['combustible']);
            $comb = str_ireplace(['diesel','díesel','diésel'], 'Diésel', $comb);
            $comb = ucfirst($comb);
            $input['combustible'] = $comb;
        }

        // Etiqueta: normalizar espacios y mayúsculas
        if (!empty($input['etiqueta'])) {
            $etq = trim($input['etiqueta']);
            $map = [
                '0' => '0', 'eco' => 'ECO', 'c' => 'C', 'b' => 'B',
                'no tiene' => 'No tiene', 'ninguna' => 'No tiene', 'sin etiqueta' => 'No tiene'
            ];
            $key = mb_strtolower($etq, 'UTF-8');
            if (isset($map[$key])) $etq = $map[$key];
            $input['etiqueta'] = $etq;
        }

        $request->merge($input);

        // --- 2) Validación (fuera del try para ver errores de validación) ---
        $request->validate([
            'matricula' => 'required|string|max:10|unique:vehiculos,matricula',
            'marca' => 'required|string|max:50',
            'modelo' => 'required|string|max:50',
            'anio' => 'required|integer|min:1886|max:' . ($currentYear + 1),
            'fecha_compra' => 'required|date',
            'km' => 'required|integer|min:0',
            'cv' => 'required|integer|min:1',
            'combustible' => 'required|string|in:Gasolina,Diésel,Híbrido,Eléctrico',
            'etiqueta' => 'required|string|in:0,ECO,C,B,No tiene',
            'precio' => 'required|numeric|min:0',
            'precio_segunda_mano' => 'nullable|numeric|min:0',
            'car_avatar' => 'nullable|image|mimes:jpeg,jpg,png,webp|max:2048',
        ]);

        // --- 3) Operaciones susceptibles de error real (BD/FS) ---
        try {
            // Avatar
            $avatarPath = $request->hasFile('car_avatar')
                ? $request->file('car_avatar')->store('vehiculos', 'public')
                : 'vehiculos/default-car.png';

            // Inserción
            $veh = Vehiculo::create([
                'id_usuario' => Auth::user()->id_usuario,
                'matricula' => $request->matricula,
                'marca' => $request->marca,
                'modelo' => $request->modelo,
                'anio' => (int)$request->anio,
                'fecha_compra' => $request->fecha_compra,
                'km' => (int)$request->km,
                'cv' => (int)$request->cv,
                'combustible' => $request->combustible,
                'etiqueta' => $request->etiqueta,
                'precio' => (float)$request->precio,
                'precio_segunda_mano' => $request->precio_segunda_mano !== null ? (float)$request->precio_segunda_mano : 0,
                'car_avatar' => $avatarPath,
            ]);

            Log::info('Vehículo creado', ['id_vehiculo' => $veh->id_vehiculo, 'user' => Auth::id()]);

            return redirect()->route('perfil')->with('success', 'Vehículo guardado correctamente 🚗');

        } catch (QueryException $e) {
            Log::error('Error SQL al crear vehículo', [
                'sql_state' => $e->getCode(),
                'message' => $e->getMessage(),
                'bindings' => method_exists($e, 'getBindings') ? $e->getBindings() : [],
            ]);
            return back()
                ->withInput()
                ->withErrors(['db' => 'Error de base de datos al guardar el vehículo. Revisa columnas y restricciones. Detalle en logs.']);
        } catch (Throwable $e) {
            Log::error('Error inesperado al crear vehículo', [
                'type' => get_class($e),
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);
            return back()
                ->withInput()
                ->withErrors(['app' => 'Ocurrió un error inesperado creando el vehículo. Revisa logs para detalles.']);
        }
    }

    /**
     * Mostrar todos los vehículos del usuario autenticado.
     */
    public function index()
    {
        $userId = Auth::user()->id_usuario;

        $vehiculos = Vehiculo::where('id_usuario', $userId)
            ->orderByDesc('id_vehiculo')
            ->get();

        $ultimoVehiculo = $vehiculos->first();

        return view('auth.perfil', compact('vehiculos', 'ultimoVehiculo'));
    }
}
