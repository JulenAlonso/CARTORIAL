<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\RegistroKm;
use App\Models\Vehiculo;
use Carbon\Carbon; // Para trabajar cómodo con fechas

class RegistroKmController extends Controller
{
    // Vista con tarjetas + gráfico + formulario
    public function index(Vehiculo $vehiculo)
    {
        // Seguridad básica: solo el dueño puede ver
        abort_unless($vehiculo->id_usuario === Auth::user()->id_usuario, 403);

        // Últimos registros para mostrar como "tarjetas"
        $registros = RegistroKm::where('id_vehiculo', $vehiculo->id_vehiculo)
            ->orderBy('fecha_registro', 'desc')
            ->limit(20)
            ->get();

        // 🟢 Si no hay registros, mostramos el km inicial desde la tabla vehiculos
        if ($registros->isEmpty()) {
            $registros->push((object) [
                'fecha_registro' => $vehiculo->fecha_compra,
                'km_actual'      => $vehiculo->km,
                'comentario'     => 'Kilometraje inicial registrado automáticamente.',
            ]);
        } else {
            // Aseguramos que el primer registro mostrado siempre incluya el valor inicial
            $primerKm = (object) [
                'fecha_registro' => $vehiculo->fecha_compra,
                'km_actual'      => $vehiculo->km,
                'comentario'     => 'Kilometraje inicial del vehículo.',
            ];
            $registros->push($primerKm);

            // Ordenamos por fecha descendente para mostrar los más recientes primero
            $registros = $registros->sortByDesc('fecha_registro')->values();
        }

        return view('km.index', compact('vehiculo', 'registros'));
    }

    // Endpoint JSON para CanvasJS (x en ms, y en km)
    public function data(Vehiculo $vehiculo)
    {
        // Seguridad básica
        abort_unless($vehiculo->id_usuario === Auth::user()->id_usuario, 403);

        // Obtenemos todos los registros de kilometraje del vehículo
        $rows = RegistroKm::where('id_vehiculo', $vehiculo->id_vehiculo)
            ->orderBy('fecha_registro', 'asc')
            ->get(['fecha_registro', 'km_actual']);

        // 🟢 Siempre añadimos el km inicial del vehículo como primer punto
        $dataPoints = collect();

        if (!empty($vehiculo->fecha_compra) && !is_null($vehiculo->km)) {
            $dataPoints->push([
                'x' => strtotime($vehiculo->fecha_compra) * 1000, // fecha en milisegundos
                'y' => (float) $vehiculo->km, // km inicial
            ]);
        }

        // 🔵 Añadimos los registros existentes de la tabla registros_km
        foreach ($rows as $r) {
            if (!empty($r->fecha_registro)) {
                $dataPoints->push([
                    // Carbon se convierte bien a string para strtotime, pero podrías usar $r->fecha_registro->timestamp
                    'x' => strtotime($r->fecha_registro) * 1000,
                    'y' => (float) $r->km_actual,
                ]);
            }
        }

        // Ordenamos los puntos por fecha ascendente
        $dataPoints = $dataPoints->sortBy('x')->values();

        return response()->json($dataPoints);
    }

    // Guardar un nuevo registro KM
    public function store(Request $request, Vehiculo $vehiculo)
    {
        // Seguridad
        abort_unless($vehiculo->id_usuario === Auth::user()->id_usuario, 403);

        // Validación (ajustada a la bbdd: km_actual = INT UNSIGNED)
        $validated = $request->validate([
            'fecha_registro' => ['required', 'date'],     // viene YYYY-MM-DD del input
            'km_actual'      => ['required', 'integer', 'min:0'],
            'comentario'     => ['nullable', 'string', 'max:255'],
        ]);

        // Montamos la fecha con hora actual
        $fechaConHora = Carbon::parse($validated['fecha_registro'])
            ->setTimeFromTimeString(now()->format('H:i:s'));

        // Registro según la tabla registros_km
        RegistroKm::create([
            'id_vehiculo'    => $vehiculo->id_vehiculo,
            'fecha_registro' => $fechaConHora,
            'km_actual'      => (int) $validated['km_actual'],
            'comentario'     => $validated['comentario'] ?? null,
        ]);

        // Actualizar los km del vehículo
        $vehiculo->km = (int) $validated['km_actual'];
        $vehiculo->save();

        // Volver al perfil con alerta
        return redirect()
            ->route('perfil')
            ->with('ok', 'Registro de KM añadido correctamente.');
    }
}
