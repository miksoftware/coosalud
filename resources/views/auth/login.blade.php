@extends('layouts.app')
@section('title', 'Iniciar Sesión')

@section('content')
<div style="max-width: 400px; margin: 4rem auto;">
    <div class="card">
        <div class="text-center mb-2">
            <h2 style="color: var(--accent); font-size: 1.5rem;">Coosalud</h2>
            <p class="text-muted text-sm mt-1">Consulta de afiliados</p>
        </div>

        <form method="POST" action="{{ route('login') }}">
            @csrf
            <div class="form-group">
                <label for="email">Correo electrónico</label>
                <input type="email" id="email" name="email" class="form-control"
                       value="{{ old('email') }}" required autofocus placeholder="correo@ejemplo.com">
            </div>

            <div class="form-group">
                <label for="password">Contraseña</label>
                <input type="password" id="password" name="password" class="form-control"
                       required placeholder="••••••">
            </div>

            <div class="form-group" style="display: flex; align-items: center; gap: 8px;">
                <input type="checkbox" id="remember" name="remember" style="accent-color: var(--accent);">
                <label for="remember" style="margin: 0; cursor: pointer;">Recordarme</label>
            </div>

            <button type="submit" class="btn btn-primary" style="width: 100%; justify-content: center;">
                Iniciar Sesión
            </button>
        </form>
    </div>
</div>
@endsection
