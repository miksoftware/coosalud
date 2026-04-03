@extends('layouts.app')
@section('title', 'Procesando')

@section('content')
<div class="card">
    <div class="flex-between">
        <h3 class="card-title" style="margin:0">Procesando: {{ $consulta->filename }}</h3>
        <span class="text-sm text-muted" id="counter">0 / {{ $consulta->total_cedulas }}</span>
    </div>

    <div class="progress-bar">
        <div class="progress-bar-fill" id="progressBar"></div>
    </div>

    <div class="flex-between mt-1">
        <span class="text-sm" id="statusText">
            <span class="status-dot status-processing"></span> Iniciando consulta...
        </span>
        <span class="text-sm text-muted" id="percentage">0%</span>
    </div>
</div>

<div class="card" id="resultsCard">
    <div class="flex-between mb-1">
        <h3 class="card-title" style="margin:0">Resultados en tiempo real</h3>
        <div class="flex gap-1">
            <span class="text-sm text-accent" id="successCount">0 encontrados</span>
            <span class="text-sm" style="color: var(--danger)" id="errorCount">0 errores</span>
        </div>
    </div>

    <div class="table-wrapper">
        <table>
            <thead>
                <tr>
                    <th>Cédula</th>
                    <th>Nombre</th>
                    <th>Municipio</th>
                    <th>Estado</th>
                    <th>Régimen</th>
                    <th>Resultado</th>
                </tr>
            </thead>
            <tbody id="resultsBody">
            </tbody>
        </table>
    </div>
</div>

<div class="card" id="completedCard" style="display:none;">
    <div class="text-center">
        <p style="font-size: 1.2rem; color: var(--success);">Consulta completada</p>
        <p class="text-muted mt-1" id="summaryText"></p>
        <div class="flex gap-1 mt-2" style="justify-content: center;">
            <a href="{{ route('consultas.export', $consulta) }}" class="btn btn-primary">Exportar a Excel</a>
            <a href="{{ route('consultas.show', $consulta) }}" class="btn btn-secondary">Ver Detalle</a>
            <a href="{{ route('consultas.index') }}" class="btn btn-secondary">Volver</a>
        </div>
    </div>
</div>

<script>
(function() {
    const consultaId = {{ $consulta->id }};
    const total = {{ $consulta->total_cedulas }};
    const csrfToken = document.querySelector('meta[name="csrf-token"]').content;
    let processed = {{ $consulta->processed }};
    let successCount = 0;
    let errorCount = 0;
    let isRunning = true;

    function updateUI() {
        const pct = total > 0 ? Math.round((processed / total) * 100) : 0;
        document.getElementById('counter').textContent = processed + ' / ' + total;
        document.getElementById('progressBar').style.width = pct + '%';
        document.getElementById('percentage').textContent = pct + '%';
        document.getElementById('successCount').textContent = successCount + ' encontrados';
        document.getElementById('errorCount').textContent = errorCount + ' errores';
    }

    function addResultRow(result) {
        const tbody = document.getElementById('resultsBody');
        const tr = document.createElement('tr');
        const isSuccess = result.status === 'success';

        tr.innerHTML =
            '<td>' + result.cedula + '</td>' +
            '<td>' + (result.nombre || '-') + '</td>' +
            '<td>' + (result.municipio || '-') + '</td>' +
            '<td>' + (result.estado_afiliado || '-') + '</td>' +
            '<td>' + (result.regimen || '-') + '</td>' +
            '<td><span class="status-dot status-' + result.status + '"></span>' +
            (isSuccess ? 'OK' : (result.error || 'Error')) + '</td>';

        // Insertar al inicio
        if (tbody.firstChild) {
            tbody.insertBefore(tr, tbody.firstChild);
        } else {
            tbody.appendChild(tr);
        }
    }

    function processNext() {
        if (!isRunning) return;

        fetch('/consultas/' + consultaId + '/process-next', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': csrfToken,
                'Accept': 'application/json'
            }
        })
        .then(function(response) { return response.json(); })
        .then(function(data) {
            processed = data.processed;

            if (data.result) {
                if (data.result.status === 'success') {
                    successCount++;
                } else {
                    errorCount++;
                }
                addResultRow(data.result);
            }

            updateUI();

            if (data.completed) {
                isRunning = false;
                document.getElementById('statusText').innerHTML =
                    '<span class="status-dot status-success"></span> Consulta completada';
                document.getElementById('completedCard').style.display = 'block';
                document.getElementById('summaryText').textContent =
                    'Se procesaron ' + total + ' cédulas: ' + successCount + ' encontradas, ' + errorCount + ' con error.';
            } else {
                processNext();
            }
        })
        .catch(function(err) {
            console.error('Error:', err);
            document.getElementById('statusText').innerHTML =
                '<span class="status-dot status-error"></span> Error de conexión. Reintentando...';
            setTimeout(processNext, 3000);
        });
    }

    // Iniciar procesamiento
    processNext();
})();
</script>
@endsection
