@extends('layouts.app')
@section('title', 'Procesando')

@section('content')
<div class="card">
    <div class="flex-between">
        <h3 class="card-title" style="margin:0">Procesando: {{ $consulta->filename }}</h3>
        <span class="text-sm text-muted" id="counter">{{ $consulta->processed }} / {{ $consulta->total_cedulas }}</span>
    </div>

    <div class="progress-bar">
        <div class="progress-bar-fill" id="progressBar"></div>
    </div>

    <div class="flex-between mt-1">
        <span class="text-sm" id="statusText">
            <span class="status-dot status-processing"></span> Reanudando consulta...
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
    var consultaId = {{ $consulta->id }};
    var total = {{ $consulta->total_cedulas }};
    var csrfToken = document.querySelector('meta[name="csrf-token"]').content;
    var processed = {{ $consulta->processed }};
    var successCount = 0;
    var errorCount = 0;
    var isRunning = true;

    // Cargar resultados ya procesados
    var previousResults = @json($processed);

    previousResults.forEach(function(r) {
        if (r.status === 'success') successCount++;
        else errorCount++;
        addResultRow(r);
    });

    updateUI();

    if (processed >= total) {
        markCompleted();
    } else {
        document.getElementById('statusText').innerHTML =
            '<span class="status-dot status-processing"></span> Procesando cédulas...';
        processNext();
    }

    function updateUI() {
        var pct = total > 0 ? Math.round((processed / total) * 100) : 0;
        document.getElementById('counter').textContent = processed + ' / ' + total;
        document.getElementById('progressBar').style.width = pct + '%';
        document.getElementById('percentage').textContent = pct + '%';
        document.getElementById('successCount').textContent = successCount + ' encontrados';
        document.getElementById('errorCount').textContent = errorCount + ' errores';
    }

    function addResultRow(result) {
        var tbody = document.getElementById('resultsBody');
        var tr = document.createElement('tr');
        var isSuccess = result.status === 'success';

        tr.innerHTML =
            '<td>' + result.cedula + '</td>' +
            '<td>' + (result.nombre || '-') + '</td>' +
            '<td>' + (result.municipio || '-') + '</td>' +
            '<td>' + (result.estado_afiliado || '-') + '</td>' +
            '<td>' + (result.regimen || '-') + '</td>' +
            '<td><span class="status-dot status-' + result.status + '"></span>' +
            (isSuccess ? 'OK' : (result.error || 'Error')) + '</td>';

        if (tbody.firstChild) {
            tbody.insertBefore(tr, tbody.firstChild);
        } else {
            tbody.appendChild(tr);
        }
    }

    function markCompleted() {
        isRunning = false;
        document.getElementById('statusText').innerHTML =
            '<span class="status-dot status-success"></span> Consulta completada';
        document.getElementById('completedCard').style.display = 'block';
        document.getElementById('summaryText').textContent =
            'Se procesaron ' + total + ' cédulas: ' + successCount + ' encontradas, ' + errorCount + ' con error.';
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
                if (data.result.status === 'success') successCount++;
                else errorCount++;
                addResultRow(data.result);
            }

            updateUI();

            if (data.completed) {
                markCompleted();
            } else {
                processNext();
            }
        })
        .catch(function(err) {
            console.error('Error:', err);
            document.getElementById('statusText').innerHTML =
                '<span class="status-dot status-error"></span> Error de conexión. Reintentando en 3s...';
            setTimeout(processNext, 3000);
        });
    }
})();
</script>
@endsection
