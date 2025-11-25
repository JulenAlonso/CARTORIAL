@extends('layouts.app')

@section('content')
    <link rel="stylesheet" href="{{ asset('assets/style/EditarPerfil.css') }}">

    <div class="editar-perfil-wrapper">

        <div class="editar-perfil-card">

            <h3 class="mb-3">Editar perfil</h3>

            {{-- Bloque de errores de validación (opcional) --}}
            @if ($errors->any())
                <div class="alert alert-danger">
                    <strong>Hay errores en el formulario:</strong>
                    <ul class="mb-0">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form method="POST" action="{{ route('editarPerfil.update') }}" enctype="multipart/form-data">
                @csrf
                @method('PUT')

                <div class="mb-3">
                    <label>Usuario</label>
                    <input
                        type="text"
                        name="user_name"
                        value="{{ old('user_name', $user->user_name) }}"
                        class="form-control @error('user_name') is-invalid @enderror"
                        required
                    >
                    @error('user_name')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="mb-3">
                    <label>Nombre</label>
                    <input
                        type="text"
                        name="nombre"
                        value="{{ old('nombre', $user->nombre) }}"
                        class="form-control @error('nombre') is-invalid @enderror"
                    >
                    @error('nombre')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="mb-3">
                    <label>Apellidos</label>
                    <input
                        type="text"
                        name="apellidos"
                        value="{{ old('apellidos', $user->apellidos) }}"
                        class="form-control @error('apellidos') is-invalid @enderror"
                    >
                    @error('apellidos')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="mb-3">
                    <label>Email</label>
                    <input
                        type="email"
                        name="email"
                        value="{{ old('email', $user->email) }}"
                        class="form-control @error('email') is-invalid @enderror"
                        required
                    >
                    @error('email')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="mb-3">
                    <label>Teléfono</label>
                    <input
                        type="text"
                        name="telefono"
                        value="{{ old('telefono', $user->telefono) }}"
                        class="form-control @error('telefono') is-invalid @enderror"
                    >
                    @error('telefono')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="mb-3">
                    <label>Avatar</label><br>

                    {{-- ✅ Usamos el accessor avatar_url del modelo Usuario --}}
                    <img
                        src="{{ $user->avatar_url }}"
                        alt="Avatar"
                        width="80"
                        height="80"
                        class="rounded-circle mb-2"
                    >

                    <input
                        type="file"
                        name="user_avatar"
                        class="form-control @error('user_avatar') is-invalid @enderror"
                    >
                    @error('user_avatar')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="d-flex justify-content-end gap-2">
                    <a href="{{ route('perfil') }}" class="btn btn-secondary">Cancelar</a>
                    <button type="submit" class="btn btn-primary">Guardar cambios</button>
                </div>
            </form>

        </div>

    </div>
@endsection
