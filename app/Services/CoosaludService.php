<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class CoosaludService
{
    private string $baseUrl;
    private string $endpoint;
    private string $documentType;
    private int $timeout;
    private int $retryTimes;
    private int $retrySleep;

    public function __construct()
    {
        $config = config('coosalud');
        $this->baseUrl = rtrim(session('coosalud_api_url', $config['base_url']), '/');
        $this->endpoint = $config['affiliate_endpoint'];
        $this->documentType = $config['document_type'];
        $this->timeout = $config['timeout'];
        $this->retryTimes = $config['retry_times'];
        $this->retrySleep = $config['retry_sleep'];
    }

    /**
     * Consulta un afiliado por número de cédula.
     *
     * @return array{success: bool, data: array|null, error: string|null}
     */
    public function consultarAfiliado(string $cedula): array
    {
        $url = $this->baseUrl . $this->endpoint;

        try {
            $response = Http::timeout($this->timeout)
                ->retry($this->retryTimes, $this->retrySleep)
                ->withHeaders([
                    'Content-Type' => 'application/json',
                    'Accept' => 'application/json',
                    'Origin' => 'https://coosalud.com',
                    'Referer' => 'https://coosalud.com/contacto-pqr-movil/',
                ])
                ->post($url, [
                    'documentNumber' => $cedula,
                    'documentType' => $this->documentType,
                ]);

            if ($response->successful()) {
                $data = $response->json();
                return $this->parseResponse($data, $cedula);
            }

            Log::warning("Coosalud API error para cédula {$cedula}", [
                'status' => $response->status(),
                'body' => $response->body(),
            ]);

            return [
                'success' => false,
                'data' => null,
                'error' => "HTTP {$response->status()}: {$response->body()}",
            ];
        } catch (\Exception $e) {
            Log::error("Coosalud API exception para cédula {$cedula}", [
                'message' => $e->getMessage(),
            ]);

            return [
                'success' => false,
                'data' => null,
                'error' => $e->getMessage(),
            ];
        }
    }

    private function parseResponse(array $response, string $cedula): array
    {
        // La API devuelve { success, message, data: { ... } }
        if (isset($response['success']) && $response['success'] === false) {
            return [
                'success' => false,
                'data' => null,
                'error' => $response['message'] ?? 'Afiliado no encontrado',
                'raw' => $response,
            ];
        }

        $data = $response['data'] ?? $response;

        if (empty($data) || (!isset($data['name']) && !isset($data['firstName']) && !isset($data['identityNumber']))) {
            return [
                'success' => false,
                'data' => null,
                'error' => $response['message'] ?? 'Afiliado no encontrado',
                'raw' => $response,
            ];
        }

        // Parsear nombre completo si viene en un solo campo
        $nombres = $this->parseName($data['name'] ?? '');

        return [
            'success' => true,
            'data' => [
                'cedula' => $cedula,
                'tipo_documento' => match($data['identityType'] ?? 'CC') {
                    'CC' => 'Cédula de Ciudadanía',
                    'TI' => 'Tarjeta de Identidad',
                    'CE' => 'Cédula de Extranjería',
                    'PA' => 'Pasaporte',
                    default => $data['identityType'] ?? 'Cédula de Ciudadanía',
                },
                'primer_nombre' => $data['firstName'] ?? $nombres['primer_nombre'],
                'segundo_nombre' => $data['secondName'] ?? $nombres['segundo_nombre'],
                'primer_apellido' => $data['firstLastName'] ?? $nombres['primer_apellido'],
                'segundo_apellido' => $data['secondLastName'] ?? $nombres['segundo_apellido'],
                'departamento' => $data['departmentName'] ?? $data['department'] ?? null,
                'municipio' => $data['municipalityName'] ?? $data['municipality'] ?? $data['cityCode'] ?? null,
                'direccion' => $data['address'] ?? null,
                'regimen' => $data['regimenName'] ?? $data['regimen'] ?? null,
                'poblacion_especial' => $data['specialPopulationName'] ?? $data['specialPop'] ?? null,
                'grupo_etnico' => $data['ethnicGroupName'] ?? $data['ethnicGroup'] ?? null,
                'paciente_riesgo' => $data['healthRiskPatient'] ?? null,
                'otros_riesgos' => $data['otherRisks'] ?? null,
                'celular' => $data['cellPhone'] ?? $data['phone'] ?? null,
                'telefono_fijo' => $data['landlinePhone'] ?? $data['homePhone'] ?? null,
                'correo' => $data['email'] ?? null,
                'estado_afiliado' => isset($data['enabled']) ? ($data['enabled'] ? 'Activo' : 'Inactivo') : ($data['affiliateStateName'] ?? null),
                'sede' => $data['headquartersName'] ?? $data['headquarters'] ?? null,
                'ips' => $data['ipsName'] ?? $data['ips'] ?? null,
            ],
            'raw' => $response,
            'error' => null,
        ];
    }

    /**
     * Parsea un nombre completo en sus componentes.
     * Formato esperado: "PRIMER_NOMBRE SEGUNDO_NOMBRE PRIMER_APELLIDO SEGUNDO_APELLIDO"
     */
    private function parseName(string $fullName): array
    {
        $parts = array_values(array_filter(explode(' ', trim($fullName))));
        $count = count($parts);

        return match(true) {
            $count >= 4 => [
                'primer_nombre' => $parts[0],
                'segundo_nombre' => $parts[1],
                'primer_apellido' => $parts[2],
                'segundo_apellido' => implode(' ', array_slice($parts, 3)),
            ],
            $count === 3 => [
                'primer_nombre' => $parts[0],
                'segundo_nombre' => null,
                'primer_apellido' => $parts[1],
                'segundo_apellido' => $parts[2],
            ],
            $count === 2 => [
                'primer_nombre' => $parts[0],
                'segundo_nombre' => null,
                'primer_apellido' => $parts[1],
                'segundo_apellido' => null,
            ],
            $count === 1 => [
                'primer_nombre' => $parts[0],
                'segundo_nombre' => null,
                'primer_apellido' => null,
                'segundo_apellido' => null,
            ],
            default => [
                'primer_nombre' => null,
                'segundo_nombre' => null,
                'primer_apellido' => null,
                'segundo_apellido' => null,
            ],
        };
    }
}
