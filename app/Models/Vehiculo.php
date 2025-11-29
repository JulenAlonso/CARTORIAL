<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;

class Vehiculo extends Model
{
    // Nombre de la tabla
    protected $table = 'vehiculos';

    // Clave primaria personalizada
    protected $primaryKey = 'id_vehiculo';
    public $incrementing = true;
    protected $keyType = 'int';

    // No tienes created_at / updated_at
    public $timestamps = false;

    // Campos que se pueden asignar masivamente
    protected $fillable = [
        'matricula',
        'marca',
        'modelo',
        'anio_fabricacion',
        'anio_matriculacion',
        'fecha_compra',
        'km',
        'cv',
        'combustible',
        'etiqueta',
        'precio',
        'precio_segunda_mano',
        'car_avatar',
        'id_usuario',
    ];

    /**
     * 🔹 Relación: un vehículo pertenece a un usuario.
     */
    public function usuario()
    {
        // Usamos el modelo Usuario (tu tabla 'usuarios')
        return $this->belongsTo(\App\Models\Usuario::class, 'id_usuario', 'id_usuario');
    }

    /**
     * 🔹 Permite a Laravel usar 'id_vehiculo' en rutas como {vehiculo}.
     */
    public function getRouteKeyName()
    {
        return 'id_vehiculo';
    }

    /**
     * 🔹 Accessor para avatar del coche.
     */
    public function getCarAvatarUrlAttribute()
    {
        if (empty($this->car_avatar)) {
            return asset('assets/images/default-car.png');
        }

        // Si es una URL absoluta (ej: https://...)
        if (preg_match('/^https?:\/\//', $this->car_avatar)) {
            return $this->car_avatar;
        }

        // Si es una ruta en storage
        return asset('storage/' . ltrim($this->car_avatar, '/'));
    }

    /**
     * 🔹 Relación: un vehículo tiene muchos registros de km.
     */
    public function registrosKm()
    {
        return $this->hasMany(\App\Models\RegistroKm::class, 'id_vehiculo', 'id_vehiculo');
    }

    /**
     * 🔹 Relación: un vehículo tiene muchos gastos.
     */
    public function registrosGastos()
    {
        return $this->hasMany(\App\Models\Gasto::class, 'id_vehiculo', 'id_vehiculo');
    }

    /* ============================================================
     *   LÓGICA DE GAMA Y DEVALUACIÓN
     * ============================================================*/

    /**
     * Devuelve la gama del vehículo según el precio nuevo.
     *
     * baja  : < 12.000 €
     * media : 12.000 – 25.000 €
     * alta  : 25.000 – 60.000 €
     * lujo  : > 60.000 €
     */
    public function getGamaAttribute(): string
    {
        $precio = (float) ($this->precio ?? 0);

        if ($precio >= 60000) {
            return 'lujo';
        } elseif ($precio >= 25000) {
            return 'alta';
        } elseif ($precio >= 12000) {
            return 'media';
        }

        return 'baja';
    }

    /**
     * Calcula años completos desde un año base o una fecha.
     */
    protected function añosDesde(?int $anioBase = null, ?string $fecha = null): int
    {
        if ($anioBase) {
            return max(0, now()->year - $anioBase);
        }

        if ($fecha) {
            try {
                return max(0, Carbon::parse($fecha)->diffInYears(now()));
            } catch (\Throwable $e) {
                return 0;
            }
        }

        return 0;
    }

    /**
     * Aplica la tabla de devaluación por gama.
     *
     *  - 0-10 años:      r1 por año
     *  - 10-20 años:     r2 por año (sobre el valor a los 10 años)
     *  - 20+ años:       r3 por año (sobre el valor a los 20 años)
     */
    protected function calcularDevaluacion(float $precioBase, int $años, string $gama): float
    {
        if ($precioBase <= 0 || $años <= 0) {
            return max(0, $precioBase);
        }

        // Tabla de porcentajes por gama
        $tabla = [
            'lujo' => [
                'r1' => 0.02,
                'r2' => 0.03,
                'r3' => 0.035,
            ],
            'alta' => [
                'r1' => 0.03,
                'r2' => 0.04,
                'r3' => 0.05,
            ],
            'media' => [
                'r1' => 0.04,
                'r2' => 0.05,
                'r3' => 0.06,
            ],
            'baja' => [
                'r1' => 0.05,
                'r2' => 0.06,
                'r3' => 0.07,
            ],
        ];

        $g = $tabla[$gama] ?? $tabla['media'];

        $valor = $precioBase;
        $restantes = $años;

        // Primeros 10 años
        $añosTramo = min($restantes, 10);
        if ($añosTramo > 0) {
            $valor *= pow(1 - $g['r1'], $añosTramo);
            $restantes -= $añosTramo;
        }

        // Años 10-20
        $añosTramo = min($restantes, 10);
        if ($añosTramo > 0) {
            $valor *= pow(1 - $g['r2'], $añosTramo);
            $restantes -= $añosTramo;
        }

        // Años 20+
        if ($restantes > 0) {
            $valor *= pow(1 - $g['r3'], $restantes);
        }

        return max(0, $valor);
    }

    /**
     * Valor estimado actual del vehículo si fuera “precio nuevo”.
     *
     * Usa:
     *  - año de matriculación (prioridad)
     *  - o año de fabricación
     *  - o año de la fecha de compra
     */
    public function getValorNuevoActualAttribute(): ?float
    {
        if (empty($this->precio) || $this->precio <= 0) {
            return null;
        }

        $anioBase = $this->anio_matriculacion
            ?? $this->anio_fabricacion
            ?? ($this->fecha_compra ? Carbon::parse($this->fecha_compra)->year : null);

        $años = $this->añosDesde($anioBase, $this->fecha_compra);

        return $this->calcularDevaluacion((float) $this->precio, $años, $this->gama);
    }

    /**
     * Valor estimado actual a partir del precio de 2ª mano.
     *
     * Se considera que el “t=0” es la fecha de compra (fecha_compra)
     * con el precio_segunda_mano como valor inicial.
     */
    public function getValorSegundaManoActualAttribute(): ?float
    {
        if (empty($this->precio_segunda_mano) || $this->precio_segunda_mano <= 0) {
            return null;
        }

        if (empty($this->fecha_compra)) {
            // Sin fecha de compra, devolvemos el precio tal cual
            return (float) $this->precio_segunda_mano;
        }

        $años = $this->añosDesde(null, $this->fecha_compra);

        return $this->calcularDevaluacion((float) $this->precio_segunda_mano, $años, $this->gama);
    }

    /* ============================================================
     *   DEVALUACIÓN EN € Y %
     *   (para mostrar en la vista)
     * ============================================================*/

    /**
     * Devaluación del valor nuevo:
     *  - euros perdidos
     *  - porcentaje perdido
     *
     * Se usa como: $vehiculo->devaluacion_nuevo['euros'], ['porcentaje']
     */
    public function getDevaluacionNuevoAttribute(): ?array
    {
        $precioOriginal = (float) ($this->precio ?? 0);
        $valorActual = (float) ($this->valor_nuevo_actual ?? 0);

        if ($precioOriginal <= 0 || $valorActual <= 0) {
            return null;
        }

        $dif = $precioOriginal - $valorActual;
        $porc = ($dif / $precioOriginal) * 100;

        return [
            'euros' => $dif,
            'porcentaje' => $porc,
        ];
    }

    /**
     * Devaluación del valor de 2ª mano:
     *  - euros perdidos
     *  - porcentaje perdido
     *
     * Se usa como: $vehiculo->devaluacion_segunda_mano['euros'], ['porcentaje']
     */
    public function getDevaluacionSegundaManoAttribute(): ?array
    {
        $precioOriginal = (float) ($this->precio_segunda_mano ?? 0);
        $valorActual = (float) ($this->valor_segunda_mano_actual ?? 0);

        if ($precioOriginal <= 0 || $valorActual <= 0) {
            return null;
        }

        $dif = $precioOriginal - $valorActual;
        $porc = ($dif / $precioOriginal) * 100;

        return [
            'euros' => $dif,
            'porcentaje' => $porc,
        ];
    }
}
