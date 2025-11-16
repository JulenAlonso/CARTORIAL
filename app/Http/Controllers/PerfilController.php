<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

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

        // ✅ Envío a la vista
        return view('auth.perfil', compact(
            'user',
            'avatarPath',
            'vehiculos',
            'totalVehiculos',
            'valorTotal',
            'kmTotal',
            'gastosTotales'
        ));
    }
}
