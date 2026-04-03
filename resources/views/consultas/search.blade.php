@extends('layouts.app')
@section('title', 'Buscar')

@section('content')
<div class="card">
    <h3 class="card-title">Buscar Afiliado por Cédula</h3>

    <form method="GET" action="{{ route('consultas.search') }}">
        <div class="form-group">
            <label>Buscar en consultas anteriores</label>
            <div class="flex gap-1">
                <input type="text" name="cedula" class="form-control" placeholder="Número de cédula"
                       value="{{ request('cedula') }}" pattern="[0-9]+" inputmode="numeric">
                <button type="submit" class="btn btn-primary">Buscar</button>
            </div>
        </div>
    </form>
</div>

@if(isset($results))
<div class="card">
    <h3 class="card-title">Resultados encontrados ({{ $results->count() }})</h3>
    @if($results->isEmpty())
        <p class="text-muted">No se encontraron resultados para esta cédula en consultas anteriores.</p>
    @else
        <div class="table-wrapper">
            <table>
                <thead>
                    <tr>
                        <th>Cédula</th>
                        <th>Nombre</th>
                        <th>Municipio</th>
                        <th>Dirección</th>
                        <th>Régimen</th>
                        <th>Estado</th>
                        <th>Celular</th>
                        <th>Fecha Consulta</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($results as $r)
                    <tr>
                        <td>{{ $r->cedula }}</td>
                        <td>{{ $r->nombre_completo }}</td>
                        <td>{{ $r->municipio ?? '-' }}</td>
                        <td>{{ $r->direccion ?? '-' }}</td>
                        <td>{{ $r->regimen ?? '-' }}</td>
                        <td>{{ $r->estado_afiliado ?? '-' }}</td>
                        <td>{{ $r->celular ?? '-' }}</td>
                        <td class="text-muted text-sm">{{ $r->created_at->format('d/m/Y H:i') }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    @endif
</div>
@endif
@endsection
