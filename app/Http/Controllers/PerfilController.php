<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;

class PerfilController extends Controller
{
    public function mostrarPerfil()
    {
        // ============================
        // 1) USUARIO + AVATAR
        // ============================
        $user = Auth::user();

        if (empty($user->user_avatar) || $user->user_avatar === '0') {
            // Avatar por defecto
            $avatarPath = asset('assets/images/user.png');
        } elseif (preg_match('/^https?:\/\//', $user->user_avatar)) {
            // Si es una URL absoluta (ej: https://...)
            $avatarPath = $user->user_avatar;
        } else {
            // Ruta relativa guardada en user_avatar (storage)
            $relativePath = ltrim($user->user_avatar, '/');
            $avatarPath = Storage::disk('public')->exists($relativePath)
                ? asset('storage/' . $relativePath)
                : asset('assets/images/user.png');
        }

        // ============================
        // 2) VEHÍCULOS + KM + GASTOS
        // ============================
        $vehiculos = $user->vehiculos()
            ->with([
                'registrosKm' => fn($q) => $q->orderBy('fecha_registro', 'desc'),
                'registrosGastos' => fn($q) => $q->orderBy('fecha_gasto', 'desc'),
            ])
            ->orderBy('anio_matriculacion', 'desc')
            ->get();

        // Calcular gasto total por vehículo
        $vehiculos->each(function ($v) {
            $v->gastoCalc = $v->registrosGastos->sum('importe');
        });

        // Totales para las tarjetas del perfil
        $totalVehiculos = $vehiculos->count();
        $valorTotal     = $vehiculos->sum('precio');
        $kmTotal        = $vehiculos->sum('km');
        $gastosTotales  = $vehiculos->sum(fn($v) => $v->gastoCalc);

        /* =======================================================
         * 3) CALENDARIO (KM + GASTOS + NOTAS CON HORA)
         * ======================================================= */
        $calendarEvents = collect();

        // --- 3.1) Registros de KM ---
        foreach ($vehiculos as $vehiculo) {
            foreach ($vehiculo->registrosKm as $rk) {
                $calendarEvents->push([
                    'fecha'       => optional($rk->fecha_registro)->toDateString(),
                    'km'          => (int) ($rk->km_actual ?? 0),
                    'gastos'      => 0,
                    'nota'        => trim(($rk->comentario ?? '') . ' [' . $vehiculo->marca . ' ' . $vehiculo->modelo . ']'),
                    'hora_evento' => null,
                ]);
            }
        }

        // --- 3.2) Gastos ---
        foreach ($vehiculos as $vehiculo) {
            foreach ($vehiculo->registrosGastos as $g) {
                $calendarEvents->push([
                    'fecha'       => optional($g->fecha_gasto)->toDateString(),
                    'km'          => 0,
                    'gastos'      => (float) ($g->importe ?? 0),
                    'nota'        => trim(($g->descripcion ?? '') . ' [' . $vehiculo->marca . ' ' . $vehiculo->modelo . ']'),
                    'hora_evento' => null,
                ]);
            }
        }

        // --- 3.3) Notas del calendario ---
        $userId = $user->id_usuario ?? $user->id;

        $notas = DB::table('notas_calendario')
            ->where('id_usuario', $userId)
            ->whereNotNull('fecha_evento')
            ->select('fecha_evento', 'hora_evento', 'titulo', 'descripcion')
            ->get();

        foreach ($notas as $n) {
            $calendarEvents->push([
                'fecha'       => $n->fecha_evento,
                'km'          => 0,
                'gastos'      => 0,
                'nota'        => trim($n->titulo . ' — ' . ($n->descripcion ?? '')),
                'hora_evento' => $n->hora_evento,
            ]);
        }

        // (Opcional) podrías ordenar los eventos por fecha/hora aquí
        // $calendarEvents = $calendarEvents->sortBy(['fecha', 'hora_evento'])->values();

        // ============================
        // 4) DEVOLVER VISTA
        // ============================
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
