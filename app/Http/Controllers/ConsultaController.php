<?php

namespace App\Http\Controllers;

use App\Exports\ResultsExport;
use App\Imports\CedulasImport;
use App\Models\Consulta;
use App\Models\ConsultaResult;
use App\Services\CoosaludService;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;

class ConsultaController extends Controller
{
    public function __construct(private CoosaludService $coosaludService) {}

    public function index()
    {
        if (auth()->user()->isAdmin()) {
            $consultas = Consulta::where('user_id', auth()->id())
                ->orderByDesc('created_at')
                ->get();
        } else {
            $consultas = Consulta::where('status', 'completed')
                ->orderByDesc('created_at')
                ->get();
        }

        return view('consultas.index', compact('consultas'));
    }

    /**
     * Sube el archivo y crea el lote de consulta.
     */
    public function upload(Request $request)
    {
        $request->validate([
            'file' => 'required|file|max:5120',
        ]);

        $ext = strtolower($request->file('file')->getClientOriginalExtension());
        if (!in_array($ext, ['xlsx', 'xls', 'csv'])) {
            return back()->with('error', 'El archivo debe ser de tipo: xlsx, xls o csv.');
        }

        $file = $request->file('file');
        $import = new CedulasImport();
        Excel::import($import, $file);

        $cedulas = $import->getCedulas();

        if ($cedulas->isEmpty()) {
            return back()->with('error', 'No se encontraron cédulas válidas en el archivo.');
        }

        $consulta = Consulta::create([
            'user_id' => auth()->id(),
            'filename' => $file->getClientOriginalName(),
            'total_cedulas' => $cedulas->count(),
            'status' => 'pending',
        ]);

        // Crear registros pendientes para cada cédula
        foreach ($cedulas as $cedula) {
            ConsultaResult::create([
                'consulta_id' => $consulta->id,
                'cedula' => $cedula,
                'status' => 'pending',
            ]);
        }

        return redirect()->route('consultas.process', $consulta)
            ->with('success', "Se encontraron {$cedulas->count()} cédulas. Iniciando consulta...");
    }

    /**
     * Vista de procesamiento en tiempo real.
     */
    public function process(Consulta $consulta)
    {
        $this->authorizeConsulta($consulta);
        return view('consultas.process', compact('consulta'));
    }

    /**
     * Procesa una cédula individual (llamado via AJAX).
     */
    public function processNext(Consulta $consulta)
    {
        $this->authorizeConsulta($consulta);

        $pending = $consulta->results()->where('status', 'pending')->first();

        if (!$pending) {
            $consulta->update(['status' => 'completed']);
            return response()->json([
                'completed' => true,
                'processed' => $consulta->processed,
                'total' => $consulta->total_cedulas,
            ]);
        }

        if ($consulta->status === 'pending') {
            $consulta->update(['status' => 'processing']);
        }

        // Consultar la API de Coosalud
        $result = $this->coosaludService->consultarAfiliado($pending->cedula);

        if ($result['success']) {
            $pending->update(array_merge($result['data'], [
                'status' => 'success',
                'raw_response' => $result['raw'] ?? null,
            ]));
        } else {
            $pending->update([
                'status' => 'error',
                'error_message' => $result['error'],
                'raw_response' => $result['raw'] ?? null,
            ]);
        }

        $consulta->increment('processed');

        // Delay configurable entre peticiones
        usleep(config('coosalud.delay', 500) * 1000);

        return response()->json([
            'completed' => false,
            'processed' => $consulta->fresh()->processed,
            'total' => $consulta->total_cedulas,
            'result' => [
                'cedula' => $pending->cedula,
                'status' => $pending->fresh()->status,
                'nombre' => $pending->fresh()->nombre_completo,
                'error' => $pending->fresh()->error_message,
                'primer_nombre' => $pending->fresh()->primer_nombre,
                'primer_apellido' => $pending->fresh()->primer_apellido,
                'municipio' => $pending->fresh()->municipio,
                'estado_afiliado' => $pending->fresh()->estado_afiliado,
                'regimen' => $pending->fresh()->regimen,
            ],
        ]);
    }

    /**
     * Exportar resultados a Excel.
     */
    public function export(Consulta $consulta)
    {
        $this->authorizeConsulta($consulta);

        $filename = "coosalud_resultados_{$consulta->id}_" . now()->format('Ymd_His') . '.xlsx';

        return Excel::download(new ResultsExport($consulta), $filename);
    }

    /**
     * Buscar por cédula (ambos roles).
     */
    public function search(Request $request)
    {
        $results = null;

        if ($request->filled('cedula')) {
            $cedula = trim($request->cedula);

            $results = ConsultaResult::where('cedula', $cedula)
                ->where('status', 'success')
                ->orderByDesc('created_at')
                ->get();
        }

        return view('consultas.search', compact('results'));
    }

    /**
     * Listar archivos/consultas exportables.
     */
    public function files()
    {
        $consultas = Consulta::with('user')
            ->where('status', 'completed')
            ->orderByDesc('created_at')
            ->get();

        return view('consultas.files', compact('consultas'));
    }

    /**
     * Ver detalle de una consulta.
     */
    public function show(Consulta $consulta)
    {
        $this->authorizeConsulta($consulta);
        $consulta->load('results');

        return view('consultas.show', compact('consulta'));
    }

    private function authorizeConsulta(Consulta $consulta): void
    {
        if (!auth()->user()->isAdmin() && $consulta->user_id !== auth()->id()) {
            abort(403);
        }
    }
}
