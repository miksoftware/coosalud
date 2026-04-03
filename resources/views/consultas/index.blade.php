@extends('layouts.app')
@section('title', 'Consultas')

@section('content')
@if(auth()->user()->isAdmin())
<div class="card">
    <h3 class="card-title">Subir archivo de cédulas</h3>
    <form method="POST" action="{{ route('consultas.upload') }}" enctype="multipart/form-data">
        @csrf
        <div class="form-group">
            <label>Archivo Excel/CSV (columna: cedula, documento, cc o primera columna)</label>
            <div class="file-input-wrapper">
                <input type="file" name="file" class="form-control" accept=".xlsx,.xls,.csv" required>
            </div>
        </div>
        <button type="submit" class="btn btn-primary">Subir y Procesar</button>
    </form>
</div>
@endif

<div class="card">
    <h3 class="card-title">Historial de Consultas</h3>
    @if($consultas->isEmpty())
        <p class="text-muted">No hay consultas registradas.</p>
    @else
        <div class="table-wrapper">
            <table>
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Archivo</th>
                        <th>Cédulas</th>
                        <th>Procesadas</th>
                        <th>Estado</th>
                        <th>Fecha</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($consultas as $consulta)
                    <tr>
                        <td>{{ $consulta->id }}</td>
                        <td>{{ $consulta->filename }}</td>
                        <td>{{ $consulta->total_cedulas }}</td>
                        <td>{{ $consulta->processed }}</td>
                        <td>
                            <span class="status-dot status-{{ $consulta->status }}"></span>
                            {{ ucfirst($consulta->status) }}
                        </td>
                        <td class="text-muted text-sm">{{ $consulta->created_at->format('d/m/Y H:i') }}</td>
                        <td class="flex gap-1">
                            <a href="{{ route('consultas.show', $consulta) }}" class="btn btn-secondary btn-sm">Ver</a>
                            @if($consulta->status === 'completed')
                                <a href="{{ route('consultas.export', $consulta) }}" class="btn btn-primary btn-sm">Exportar</a>
                            @elseif($consulta->status !== 'processing' && auth()->user()->isAdmin())
                                <a href="{{ route('consultas.process', $consulta) }}" class="btn btn-primary btn-sm">Procesar</a>
                            @endif
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    @endif
</div>
@endsection
