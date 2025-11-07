<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;

class PerfilController extends Controller
{
    public function mostrarPerfil()
    {
        $user = Auth::user();

        // --- Avatar (tu misma lógica) ---
        if (empty($user->user_avatar) || $user->user_avatar === '0') {
            $avatarPath = asset('assets/images/user.png');
        } else {
            if (preg_match('/^https?:\/\//', $user->user_avatar)) {
                $avatarPath = $user->user_avatar;
            } else {
                $avatarPath = asset('storage/' . ltrim($user->user_avatar, '/'));
            }
        }

        // --- Vehículos del usuario ---
        // Ajusta columnas si necesitas otras
        $vehiculos = $user->vehiculos()
            ->select(
                'id_vehiculo','id_usuario','marca','modelo','anio','matricula',
                'km','cv','combustible','etiqueta','precio','precio_segunda_mano',
                'fecha_compra','car_avatar'
            )
            ->orderBy('anio','desc')
            ->get();

        // --- Totales que usas en el blade ---
        $totalVehiculos = $vehiculos->count();
        $valorTotal     = $vehiculos->sum('precio');
        $kmTotal        = $vehiculos->sum('km');
        $gastosTotales  = 0; // si luego tiras de tabla "gastos", cámbialo

        // ⚠️ Tu blade es resources/views/auth/perfil.blade.php => 'auth.perfil'
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
