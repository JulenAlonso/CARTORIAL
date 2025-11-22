<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Log;
use App\Models\User;
use App\Models\Vehiculo;
use App\Models\Gasto;

class AdminController extends Controller
{
    /* ============================================================
     *   DASHBOARD PRINCIPAL (ADMIN ZONE)
     * ============================================================*/
    public function dashboard()
    {
        $user = Auth::user();

        // Totales básicos
        $totalUsers = User::count();
        $totalVehiculos = Vehiculo::count();
        $gastoTotal = (float) Gasto::sum('importe');

        // Vehículos que tienen gastos
        $vehiculosConGasto = Vehiculo::whereHas('registrosGastos')->count();

        // Promedio
        $gastoMedioVehiculo = $vehiculosConGasto > 0
            ? round($gastoTotal / $vehiculosConGasto, 2)
            : 0.0;

        /* ============================================================
         *   GASTOS POR CATEGORÍA PARA LA GRÁFICA
         * ============================================================*/
        $categoriaDataPoints = [];

        if (Schema::hasColumn('gastos', 'tipo_gasto')) {
            $rawCategorias = Gasto::selectRaw('LOWER(tipo_gasto) as tipo_gasto, SUM(importe) as total')
                ->groupBy('tipo_gasto')
                ->pluck('total', 'tipo_gasto');

            // Mapeo flexible
            $mapCategorias = [
                'mantenimiento' => 'Mantenimiento',
                'mant' => 'Mantenimiento',
                'reparacion' => 'Mantenimiento',
                'reparación' => 'Mantenimiento',

                'combustible' => 'Combustible',
                'gasolina' => 'Combustible',
                'diesel' => 'Combustible',

                'seguro' => 'Seguro',

                'impuesto' => 'Impuestos',
                'impuestos' => 'Impuestos',
                'tasa' => 'Impuestos',

                'peaje' => 'Peajes',
                'peajes' => 'Peajes',
            ];

            $acumuladoPorCategoria = [];

            foreach ($rawCategorias as $tipoRaw => $total) {
                $clave = trim(mb_strtolower($tipoRaw ?? ''));
                $label = $mapCategorias[$clave] ?? 'Otros';

                if (!isset($acumuladoPorCategoria[$label])) {
                    $acumuladoPorCategoria[$label] = 0.0;
                }

                $acumuladoPorCategoria[$label] += (float) $total;
            }

            foreach ($acumuladoPorCategoria as $label => $total) {
                $categoriaDataPoints[] = [
                    'label' => $label,
                    'y' => round($total, 2),
                ];
            }

            usort($categoriaDataPoints, fn($a, $b) => $b['y'] <=> $a['y']);
        } else {
            $categoriaDataPoints[] = [
                'label' => 'Total',
                'y' => $gastoTotal,
            ];
        }

        /* ==============================
         *  Últimos usuarios + vehículos
         * ==============================*/
        $latestUsers = User::withCount('vehiculos')
            ->with('vehiculos')
            ->orderByDesc('id_usuario')
            ->take(5)
            ->get();

        // Enviar a la vista
        return view('admin.adminzone', compact(
            'user',
            'totalUsers',
            'totalVehiculos',
            'gastoTotal',
            'gastoMedioVehiculo',
            'vehiculosConGasto',
            'categoriaDataPoints',
            'latestUsers'
        ));
    }

    /* ============================================================
     *   MODIFICAR USUARIO
     * ============================================================*/
    public function updateUser(Request $request, $id)
    {
        $user = User::findOrFail($id);

        $data = $request->validate([
            'nombre' => ['nullable', 'string', 'max:255'],
            'apellidos' => ['nullable', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255'],
            'telefono' => ['nullable', 'string', 'max:50'],
            'user_name' => ['required', 'string', 'max:255'],
        ]);

        // Checkbox rol admin
        $data['admin'] = $request->has('admin') ? 1 : 0;

        $user->update($data);

        return redirect()
            ->route('admin.dashboard')
            ->with('success', 'Usuario actualizado correctamente.');
    }

    /* ============================================================
     *   ELIMINAR USUARIO
     * ============================================================*/
    public function deleteUser($id)
    {
        $user = User::findOrFail($id);

        if (auth()->id() === $user->id_usuario) {
            return redirect()
                ->route('admin.dashboard')
                ->with('error', 'No puedes eliminar tu propio usuario.');
        }

        $user->delete();

        return redirect()
            ->route('admin.dashboard')
            ->with('success', 'Usuario eliminado correctamente.');
    }

    /* ============================================================
     *   MODIFICAR VEHÍCULO
     * ============================================================*/
    public function updateVehiculo(Request $request, $id)
    {
        // Buscamos el vehículo
        $vehiculo = Vehiculo::findOrFail($id);

        // Validamos solo lo que editas en el adminzone
        $data = $request->validate([
            'matricula' => ['nullable', 'string', 'max:20'],
            'marca' => ['nullable', 'string', 'max:100'],
            'modelo' => ['nullable', 'string', 'max:100'],
            'anio_matriculacion' => ['nullable', 'integer', 'min:1900', 'max:' . date('Y') + 1],
            'anio_fabricacion' => ['nullable', 'integer', 'min:1900', 'max:' . date('Y') + 1],
            'km' => ['nullable', 'integer', 'min:0'],
            'combustible' => ['nullable', 'string', 'max:50'],
            'etiqueta' => ['nullable', 'string', 'max:10'],
            'precio' => ['nullable', 'numeric', 'min:0'],
            'precio_segunda_mano' => ['nullable', 'numeric', 'min:0'],
        ]);

        // Actualizamos el vehículo con los datos validados
        $vehiculo->update($data);

        return back()->with('success', 'Vehículo actualizado correctamente.');
    }

    /* ============================================================
     *   ELIMINAR VEHÍCULO (ADMIN ZONE)
     * ============================================================*/
    public function deleteVehiculo($id)
    {
        try {
            $vehiculo = Vehiculo::findOrFail($id);

            $vehiculo->delete();

            return back()->with('success', 'Vehículo eliminado correctamente.');
        } catch (\Throwable $e) {
            Log::error('Error al eliminar vehículo en AdminZone', [
                'vehiculo_id' => $id,
                'error' => $e->getMessage(),
            ]);

            return back()->with('error', 'No se ha podido eliminar el vehículo. Revisa el log para más detalles.');
        }
    }
}
