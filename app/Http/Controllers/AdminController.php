<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;              // 👈 FALTABA ESTO
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Schema;
use App\Models\User;
use App\Models\Vehiculo;
use App\Models\Gasto; // tu modelo de gastos

class AdminController extends Controller
{
    public function dashboard()
    {
        $user = Auth::user();

        $totalUsers     = User::count();
        $totalVehiculos = Vehiculo::count();

        // Suma total de importes de la tabla gastos
        $gastoTotal = (float) Gasto::sum('importe');

        // Vehículos que tienen al menos un gasto asociado
        $vehiculosConGasto = Vehiculo::whereHas('registrosGastos')->count();

        $gastoMedioVehiculo = $vehiculosConGasto > 0
            ? round($gastoTotal / $vehiculosConGasto, 2)
            : 0.0;

        // ===========================
        // 3) Gasto por categoría (tipo_gasto)
        // ===========================
        $categoriaDataPoints = [];

        if (Schema::hasColumn('gastos', 'tipo_gasto')) {
            $rawCategorias = Gasto::selectRaw('LOWER(tipo_gasto) as tipo_gasto, SUM(importe) as total')
                ->groupBy('tipo_gasto')
                ->pluck('total', 'tipo_gasto'); // ['mantenimiento' => 123, ...]

            $mapCategorias = [
                'mantenimiento' => 'Mantenimiento',
                'mant'          => 'Mantenimiento',
                'reparacion'    => 'Mantenimiento',
                'reparación'    => 'Mantenimiento',

                'combustible'   => 'Combustible',
                'gasolina'      => 'Combustible',
                'diesel'        => 'Combustible',

                'seguro'        => 'Seguro',

                'impuesto'      => 'Impuestos',
                'impuestos'     => 'Impuestos',
                'tasa'          => 'Impuestos',

                'peaje'         => 'Peajes',
                'peajes'        => 'Peajes',
            ];

            $acumuladoPorCategoria = [];

            foreach ($rawCategorias as $tipoRaw => $total) {
                $tipoRaw = $tipoRaw ?? '';
                $clave   = trim(mb_strtolower($tipoRaw));
                $label   = $mapCategorias[$clave] ?? 'Otros';

                if (!isset($acumuladoPorCategoria[$label])) {
                    $acumuladoPorCategoria[$label] = 0.0;
                }
                $acumuladoPorCategoria[$label] += (float) $total;
            }

            foreach ($acumuladoPorCategoria as $label => $total) {
                $categoriaDataPoints[] = [
                    'label' => $label,
                    'y'     => round($total, 2),
                ];
            }

            usort($categoriaDataPoints, fn($a, $b) => $b['y'] <=> $a['y']);
        } else {
            // Fallback: solo una barra total
            $categoriaDataPoints[] = [
                'label' => 'Total',
                'y'     => round($gastoTotal, 2),
            ];
        }

        // Últimos usuarios con número de vehículos y la relación cargada
        $latestUsers = User::withCount('vehiculos')
            ->with('vehiculos')                    // 👈 necesario para mostrar los coches en la vista
            ->orderByDesc('id_usuario')
            ->take(5)
            ->get();

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

    public function updateUser(Request $request, $id)
    {
        $user = User::findOrFail($id);

        $data = $request->validate([
            'nombre'     => ['nullable', 'string', 'max:255'],
            'apellidos'  => ['nullable', 'string', 'max:255'],
            'email'      => ['required', 'email', 'max:255'],
            'telefono'   => ['nullable', 'string', 'max:50'],
            'user_name'  => ['required', 'string', 'max:255'],
            // no validamos password aquí porque no lo estamos cambiando
        ]);

        // checkbox admin
        $data['admin'] = $request->has('admin') ? 1 : 0;

        $user->update($data);

        return redirect()
            ->route('admin.dashboard')
            ->with('success', 'Usuario actualizado correctamente.');
    }

    public function deleteUser($id)
    {
        $user = User::findOrFail($id);

        // Evitar que el admin actual se borre a sí mismo
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
}
