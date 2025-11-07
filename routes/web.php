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
        // OJO: si tu tabla es "usuarios" en minúscula, usa 'usuarios'
        $usuarios = DB::table('usuarios')->get();
        return $usuarios;
    } catch (\Exception $e) {
        return "Error de conexión: " . $e->getMessage();
    }
});

Route::middleware(['auth'])->group(function () {
    // ===== Usuarios =====
    Route::get('/usuarios', [UsuarioController::class, 'index'])->name('usuarios.index');
    Route::get('/usuarios/create', [UsuarioController::class, 'create'])->name('usuarios.create');
    Route::post('/usuarios', [UsuarioController::class, 'store'])->name('usuarios.store');
    Route::get('/usuarios/{id}', [UsuarioController::class, 'show'])->name('usuarios.show');

    // ===== Perfil (¡solo una ruta!) =====
    Route::get('/perfil', [PerfilController::class, 'mostrarPerfil'])->name('perfil');

    // ===== Vehículos =====
    Route::get('/vehiculos', [VehiculoController::class, 'index'])->name('vehiculos.index');
    Route::get('/vehiculos/{id}', [VehiculoController::class, 'show'])->name('vehiculos.show');
    Route::get('/vehiculo/crear', [VehiculoController::class, 'create'])->name('vehiculo.create');
    Route::post('/vehiculo/crear', [VehiculoController::class, 'store'])->name('vehiculo.store');

    // ===== Notas =====
    Route::get('/notas', [NotaCalendarioController::class, 'index'])->name('notas.index');
    Route::get('/notas/{id}', [NotaCalendarioController::class, 'show'])->name('notas.show');

    // ===== Editar Perfil =====
    Route::get('/perfil/editar', [EditarPerfilController::class, 'create'])->name('editarPerfil.create');
    Route::put('/perfil', [EditarPerfilController::class, 'update'])->name('editarPerfil.update');

    // ===== Registros KM =====
    Route::get('/vehiculos/{vehiculo}/km', [RegistroKmController::class, 'index'])->name('km.index');
    Route::get('/vehiculos/{vehiculo}/km/data', [RegistroKmController::class, 'data'])->name('km.data');
    Route::post('/vehiculos/{vehiculo}/km', [RegistroKmController::class, 'store'])->name('km.store');
});
