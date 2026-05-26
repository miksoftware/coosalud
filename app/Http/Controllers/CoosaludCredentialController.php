<?php

namespace App\Http\Controllers;

use App\Services\CoosaludService;
use Illuminate\Http\Request;

class CoosaludCredentialController extends Controller
{
    public function index()
    {
        return view('coosalud.credentials', [
            'apiUrl' => session('coosalud_api_url', config('coosalud.base_url')),
        ]);
    }

    public function save(Request $request)
    {
        $request->validate([
            'api_url' => ['required', 'url', 'max:500'],
        ]);

        session(['coosalud_api_url' => rtrim($request->api_url, '/')]);

        return response()->json(['success' => true]);
    }

    public function test(Request $request)
    {
        $url = session('coosalud_api_url', config('coosalud.base_url'));

        try {
            $service = new CoosaludService();
            $result = $service->consultarAfiliado('000000000');

            // La URL responde aunque no encuentre el afiliado — eso ya confirma que está activa
            if (isset($result['success']) || isset($result['error'])) {
                return response()->json([
                    'success' => true,
                    'message' => 'La URL responde correctamente: ' . $url,
                    'url'     => $url,
                ]);
            }

            return response()->json([
                'success' => false,
                'message' => 'La URL respondió pero con formato inesperado.',
                'url'     => $url,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'No se pudo conectar: ' . $e->getMessage(),
                'url'     => $url,
            ]);
        }
    }

    public function reset(Request $request)
    {
        session()->forget('coosalud_api_url');

        return response()->json(['success' => true]);
    }
}
