<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

use App\Http\Controllers\HomeController;
use App\Http\Controllers\UsuarioController;
use App\Http\Controllers\VehiculoController;
use App\Http\Controllers\NotaCalendarioController;
use App\Http\Controllers\PerfilController;
use App\Http\Controllers\EditarPerfilController;
use App\Http\Controllers\RegistroKmController;

Route::get('/', function () {
    return Auth::check() ? redirect()->route('perfil') : view('inicio');
});

Auth::routes();

Route::get('/home', [HomeController::class, 'index'])->name('home');

Route::get('/cartorial', function () {
    try {
        $usuarios = DB::table('usuarios')->get();
        return $usuarios;
    } catch (\Exception $e) {
        return "Error de conexión: " . $e->getMessage();
    }
});

Route::middleware(['auth'])->group(function () {

    // ===== USUARIOS =====
    Route::get('/usuarios', [UsuarioController::class, 'index'])->name('usuarios.index');
    Route::get('/usuarios/create', [UsuarioController::class, 'create'])->name('usuarios.create');
    Route::post('/usuarios', [UsuarioController::class, 'store'])->name('usuarios.store');
    Route::get('/usuarios/{id}', [UsuarioController::class, 'show'])->name('usuarios.show')->whereNumber('id');

    // ===== PERFIL =====
    Route::get('/perfil', [PerfilController::class, 'mostrarPerfil'])->name('perfil');

    // ===== EDITAR PERFIL =====
    Route::get('/perfil/editar', [EditarPerfilController::class, 'create'])->name('editarPerfil.create');
    Route::put('/perfil', [EditarPerfilController::class, 'update'])->name('editarPerfil.update');

    // ===== VEHÍCULOS =====
    Route::get('/vehiculos', [VehiculoController::class, 'index'])->name('vehiculos.index');

    // Selector para elegir vehículo a editar (misma página con ?vehiculo=ID)
    // Debe ir ANTES de cualquier /vehiculos/{...}
    Route::get('/vehiculos/editar', [VehiculoController::class, 'selectToEdit'])->name('editarVehiculo.create');

    // Crear vehículo (singular para evitar colisiones con /vehiculos/{id})
    Route::get('/vehiculo/crear', [VehiculoController::class, 'create'])->name('vehiculo.create');
    Route::post('/vehiculo/crear', [VehiculoController::class, 'store'])->name('vehiculo.store');

    // Editar vehículo (redirige al selector con ?vehiculo=ID desde el controller)
    Route::get('/vehiculos/{vehiculo}/edit', [VehiculoController::class, 'edit'])
        ->name('vehiculos.edit')->whereNumber('vehiculo');

    Route::put('/vehiculos/{vehiculo}', [VehiculoController::class, 'update'])
        ->name('vehiculos.update')->whereNumber('vehiculo');

        // Eliminar vehiculo
        Route::delete('/vehiculos/{vehiculo}', [VehiculoController::class, 'destroy'])
    ->name('vehiculos.destroy')
    ->whereNumber('vehiculo');


    // ===== REGISTROS KM =====
    Route::get('/vehiculos/{vehiculo}/km', [RegistroKmController::class, 'index'])
        ->name('km.index')->whereNumber('vehiculo');

    Route::get('/vehiculos/{vehiculo}/km/data', [RegistroKmController::class, 'data'])
        ->name('km.data')->whereNumber('vehiculo');

    Route::post('/vehiculos/{vehiculo}/km', [RegistroKmController::class, 'store'])
        ->name('km.store')->whereNumber('vehiculo');

    // Mostrar vehículo (dejar al final y restringido a números para no chocar con /edit o /km)
    Route::get('/vehiculos/{id}', [VehiculoController::class, 'show'])
        ->name('vehiculos.show')->whereNumber('id');

    // ===== NOTAS =====
    Route::get('/notas', [NotaCalendarioController::class, 'index'])->name('notas.index');
    Route::get('/notas/{id}', [NotaCalendarioController::class, 'show'])->name('notas.show')->whereNumber('id');
});
