<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ConsultaResult extends Model
{
    protected $fillable = [
        'consulta_id',
        'cedula',
        'tipo_documento',
        'primer_nombre',
        'segundo_nombre',
        'primer_apellido',
        'segundo_apellido',
        'departamento',
        'municipio',
        'direccion',
        'regimen',
        'poblacion_especial',
        'grupo_etnico',
        'paciente_riesgo',
        'otros_riesgos',
        'celular',
        'telefono_fijo',
        'correo',
        'estado_afiliado',
        'sede',
        'ips',
        'status',
        'error_message',
        'raw_response',
    ];

    protected function casts(): array
    {
        return [
            'raw_response' => 'array',
        ];
    }

    public function consulta(): BelongsTo
    {
        return $this->belongsTo(Consulta::class);
    }

    public function getNombreCompletoAttribute(): string
    {
        return trim("{$this->primer_nombre} {$this->segundo_nombre} {$this->primer_apellido} {$this->segundo_apellido}");
    }
}
