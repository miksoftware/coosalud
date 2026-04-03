@extends('layouts.app')
@section('title', 'Detalle Consulta')

@section('content')
<div class="flex-between mb-2">
    <h2 class="card-title" style="margin:0">Consulta #{{ $consulta->id }} — {{ $consulta->filename }}</h2>
    <div class="flex gap-1">
        @if($consulta->status === 'completed')
            <a href="{{ route('consultas.export', $consulta) }}" class="btn btn-primary btn-sm">Exportar Excel</a>
        @endif
        <a href="{{ route('consultas.index') }}" class="btn btn-secondary btn-sm">Volver</a>
    </div>
</div>

<div class="grid-3 mb-2">
    <div class="card text-center">
        <span class="text-muted text-sm">Total Cédulas</span>
        <p style="font-size: 1.5rem; font-weight: 700;">{{ $consulta->total_cedulas }}</p>
    </div>
    <div class="card text-center">
        <span class="text-muted text-sm">Exitosas</span>
        <p style="font-size: 1.5rem; font-weight: 700; color: var(--success);">
            {{ $consulta->results->where('status', 'success')->count() }}
        </p>
    </div>
    <div class="card text-center">
        <span class="text-muted text-sm">Errores</span>
        <p style="font-size: 1.5rem; font-weight: 700; color: var(--danger);">
            {{ $consulta->results->where('status', 'error')->count() }}
        </p>
    </div>
</div>

<div class="card">
    <h3 class="card-title">Resultados</h3>
    <div class="table-wrapper">
        <table>
            <thead>
                <tr>
                    <th>Cédula</th>
                    <th>Nombre Completo</th>
                    <th>Departamento</th>
                    <th>Municipio</th>
                    <th>Dirección</th>
                    <th>Régimen</th>
                    <th>Estado</th>
                    <th>Celular</th>
                    <th>Correo</th>
                    <th>Resultado</th>
                </tr>
            </thead>
            <tbody>
                @foreach($consulta->results as $r)
                <tr>
                    <td>{{ $r->cedula }}</td>
                    <td>{{ $r->nombre_completo ?: '-' }}</td>
                    <td>{{ $r->departamento ?? '-' }}</td>
                    <td>{{ $r->municipio ?? '-' }}</td>
                    <td>{{ $r->direccion ?? '-' }}</td>
                    <td>{{ $r->regimen ?? '-' }}</td>
                    <td>{{ $r->estado_afiliado ?? '-' }}</td>
                    <td>{{ $r->celular ?? '-' }}</td>
                    <td>{{ $r->correo ?? '-' }}</td>
                    <td>
                        <span class="status-dot status-{{ $r->status }}"></span>
                        @if($r->status === 'error')
                            <span title="{{ $r->error_message }}" style="color: var(--danger); cursor: help;">Error</span>
                        @else
                            OK
                        @endif
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
@endsection
