<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="shortcut icon" href="{{ asset('assets/images/CARTORIAL2.png') }}" type="image/x-icon">
    <title>Perfil de Usuario — AutoControl</title>

    <link rel="stylesheet" href="{{ asset('assets/style/EditarPerfil.css') }}">
</head>

<body>

@extends('layouts.app')

@section('content')
    <div class="container py-4">
        <div class="card p-4" style="max-width: 720px; margin: 0 auto;">
            <h3 class="mb-3">Editar perfil</h3>

            <form method="POST" action="{{ route('editarPerfil.update') }}" enctype="multipart/form-data">
                @csrf
                @method('PUT')

                <div class="mb-3">
                    <label>Usuario</label>
                    <input type="text" name="user_name" value="{{ old('user_name', $user->user_name) }}"
                        class="form-control" required>
                </div>

                <div class="mb-3">
                    <label>Nombre</label>
                    <input type="text" name="nombre" value="{{ old('nombre', $user->nombre) }}" class="form-control">
                </div>

                <div class="mb-3">
                    <label>Apellidos</label>
                    <input type="text" name="apellidos" value="{{ old('apellidos', $user->apellidos) }}"
                        class="form-control">
                </div>

                <div class="mb-3">
                    <label>Email</label>
                    <input type="email" name="email" value="{{ old('email', $user->email) }}" class="form-control"
                        required>
                </div>

                <div class="mb-3">
                    <label>Teléfono</label>
                    <input type="text" name="telefono" value="{{ old('telefono', $user->telefono) }}"
                        class="form-control">
                </div>

                <div class="mb-3">
                    <label>Avatar</label><br>
                    @if ($user->user_avatar)
                        <img src="{{ asset('storage/' . $user->user_avatar) }}" alt="Avatar" width="80" height="80"
                            class="rounded-circle mb-2">
                    @endif
                    <input type="file" name="user_avatar" class="form-control">
                </div>

                <div class="d-flex justify-content-end gap-2">
                    <a href="{{ route('perfil') }}" class="btn btn-secondary">Cancelar</a>
                    <button type="submit" class="btn btn-primary">Guardar cambios</button>
                </div>
            </form>
        </div>
    </div>
@endsection
