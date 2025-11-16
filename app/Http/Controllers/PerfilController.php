<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;

class PerfilController extends Controller
{
    public function mostrarPerfil()
    {
        // ✅ Usuario autenticado
        $user = Auth::user();

        // ✅ Comprobación del avatar
        if (empty($user->user_avatar) || $user->user_avatar === '0') {
            $avatarPath = asset('assets/images/user.png');
        } elseif (preg_match('/^https?:\/\//', $user->user_avatar)) {
            // Si es una URL completa (http o https)
            $avatarPath = $user->user_avatar;
        } else {
            // Si es una ruta en storage, comprobamos que exista el archivo
            $relativePath = ltrim($user->user_avatar, '/');
            if (Storage::disk('public')->exists($relativePath)) {
                $avatarPath = asset('storage/' . $relativePath);
            } else {
                // Si no existe físicamente → imagen por defecto
                $avatarPath = asset('assets/images/user.png');
            }
        }

        // ✅ Vehículos del usuario + registros de km + registros de gastos
        $vehiculos = $user->vehiculos()
            ->with([
                'registrosKm' => function ($q) {
                    $q->orderBy('fecha_registro', 'desc');
                },
                'registrosGastos' => function ($q) {
                    $q->orderBy('fecha_gasto', 'desc');
                },
            ])
            ->select(
                'id_vehiculo',
                'id_usuario',
                'marca',
                'modelo',
                'anio',
                'matricula',
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

        // ✅ Cálculo de gasto total por vehículo (propiedad auxiliar)
        $vehiculos->each(function ($v) {
            $v->gastoCalc = $v->registrosGastos->sum('importe');
        });

        // ✅ Totales del perfil
        $totalVehiculos = $vehiculos->count();
        $valorTotal     = $vehiculos->sum('precio');
        $kmTotal        = $vehiculos->sum('km');

        // suma de todos los gastos de todos los vehículos
        $gastosTotales  = $vehiculos->sum(function ($v) {
            return $v->gastoCalc;
        });

        /* =======================================================
         *  CALENDARIO: km, gastos y notas_calendario por fecha
         * ======================================================= */

        $calendarEvents = collect();

        // 1) Registros de KM
        foreach ($vehiculos as $vehiculo) {
            foreach ($vehiculo->registrosKm as $rk) {
                $calendarEvents->push([
                    'fecha'   => optional($rk->fecha_registro)->toDateString(), // YYYY-MM-DD
                    'km'      => (int) ($rk->km_actual ?? 0),
                    'gastos'  => 0,
                    'nota'    => trim(($rk->comentario ?? '') . ' [' . $vehiculo->marca . ' ' . $vehiculo->modelo . ']'),
                ]);
            }
        }

        // 2) Registros de GASTOS
        foreach ($vehiculos as $vehiculo) {
            foreach ($vehiculo->registrosGastos as $g) {
                $calendarEvents->push([
                    'fecha'   => optional($g->fecha_gasto)->toDateString(),
                    'km'      => 0,
                    'gastos'  => (float) ($g->importe ?? 0),
                    'nota'    => trim(($g->descripcion ?? '') . ' [' . $vehiculo->marca . ' ' . $vehiculo->modelo . ']'),
                ]);
            }
        }

        // 3) notas_calendario → usamos fecha_evento + titulo + descripcion
        $notasCalendario = DB::table('notas_calendario')
            ->where('id_usuario', $user->id_usuario) // clave de usuario en esa tabla
            ->whereNotNull('fecha_evento')
            ->select('fecha_evento', 'titulo', 'descripcion')
            ->get();

        foreach ($notasCalendario as $n) {
            $calendarEvents->push([
                'fecha'   => (string) $n->fecha_evento, // viene formato YYYY-MM-DD del tipo DATE
                'km'      => 0,
                'gastos'  => 0,
                'nota'    => trim($n->titulo . ' — ' . ($n->descripcion ?? '')),
            ]);
        }

        // ✅ Envío a la vista
        return view('auth.perfil', compact(
            'user',
            'avatarPath',
            'vehiculos',
            'totalVehiculos',
            'valorTotal',
            'kmTotal',
            'gastosTotales',
            'calendarEvents'
        ));
    }
}
