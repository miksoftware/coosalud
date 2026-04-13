<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\ConsultaResult;
use Illuminate\Http\JsonResponse;

class ConsultaCedulaController extends Controller
{
    /**
     * Retorna la información más reciente de un afiliado por cédula.
     *
     * GET /api/consulta/cedula/{cedula}
     *
     * @param  string  $cedula  Número de documento a consultar
     */
    public function show(string $cedula): JsonResponse
    {
        $resultado = ConsultaResult::where('cedula', $cedula)
            ->where('status', 'success')
            ->latest('updated_at')
            ->first();

        if (! $resultado) {
            return response()->json([
                'success' => false,
                'message' => 'No se encontraron resultados para la cédula proporcionada.',
                'data'    => null,
            ], 404);
        }

        return response()->json([
            'success' => true,
            'message' => 'Consulta exitosa.',
            'data'    => [
                'cedula'            => $resultado->cedula,
                'tipo_documento'    => $resultado->tipo_documento,
                'primer_nombre'     => $resultado->primer_nombre,
                'segundo_nombre'    => $resultado->segundo_nombre,
                'primer_apellido'   => $resultado->primer_apellido,
                'segundo_apellido'  => $resultado->segundo_apellido,
                'nombre_completo'   => $resultado->nombre_completo,
                'departamento'      => $resultado->departamento,
                'municipio'         => $resultado->municipio,
                'direccion'         => $resultado->direccion,
                'regimen'           => $resultado->regimen,
                'estado_afiliado'   => $resultado->estado_afiliado,
                'sede'              => $resultado->sede,
                'ips'               => $resultado->ips,
                'celular'           => $resultado->celular,
                'telefono_fijo'     => $resultado->telefono_fijo,
                'correo'            => $resultado->correo,
                'poblacion_especial'=> $resultado->poblacion_especial,
                'grupo_etnico'      => $resultado->grupo_etnico,
                'consultado_en'     => $resultado->updated_at?->toIso8601String(),
            ],
        ]);
    }
}
