@extends('layouts.app')
@section('title', 'Archivos')

@section('content')
<div class="card">
    <h3 class="card-title">Archivos Exportables</h3>

    @if($consultas->isEmpty())
        <p class="text-muted">No hay consultas completadas para exportar.</p>
    @else
        <div class="table-wrapper">
            <table>
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Archivo Original</th>
                        <th>Usuario</th>
                        <th>Cédulas</th>
                        <th>Exitosas</th>
                        <th>Fecha</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($consultas as $consulta)
                    <tr>
                        <td>{{ $consulta->id }}</td>
                        <td>{{ $consulta->filename }}</td>
                        <td>{{ $consulta->user->name ?? '-' }}</td>
                        <td>{{ $consulta->total_cedulas }}</td>
                        <td class="text-accent">{{ $consulta->results()->where('status', 'success')->count() }}</td>
                        <td class="text-muted text-sm">{{ $consulta->created_at->format('d/m/Y H:i') }}</td>
                        <td class="flex gap-1">
                            <a href="{{ route('consultas.export', $consulta) }}" class="btn btn-primary btn-sm">Descargar Excel</a>
                            <a href="{{ route('consultas.show', $consulta) }}" class="btn btn-secondary btn-sm">Ver Detalle</a>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    @endif
</div>
@endsection
