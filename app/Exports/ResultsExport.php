<?php

namespace App\Exports;

use App\Models\Consulta;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class ResultsExport implements FromCollection, WithHeadings, WithStyles, WithColumnWidths
{
    public function __construct(private Consulta $consulta) {}

    public function collection()
    {
        return $this->consulta->results()
            ->where('status', 'success')
            ->get()
            ->map(fn ($r) => [
                $r->cedula,
                $r->tipo_documento,
                $r->primer_nombre,
                $r->segundo_nombre,
                $r->primer_apellido,
                $r->segundo_apellido,
                $r->departamento,
                $r->municipio,
                $r->direccion,
                $r->regimen,
                $r->poblacion_especial,
                $r->grupo_etnico,
                $r->paciente_riesgo,
                $r->otros_riesgos,
                $r->celular,
                $r->telefono_fijo,
                $r->correo,
                $r->estado_afiliado,
                $r->sede,
                $r->ips,
            ]);
    }

    public function headings(): array
    {
        return [
            'Cédula', 'Tipo Documento', 'Primer Nombre', 'Segundo Nombre',
            'Primer Apellido', 'Segundo Apellido', 'Departamento', 'Municipio',
            'Dirección', 'Régimen', 'Población Especial', 'Grupo Étnico',
            'Paciente Riesgo', 'Otros Riesgos', 'Celular', 'Teléfono Fijo',
            'Correo', 'Estado Afiliado', 'Sede', 'IPS',
        ];
    }

    public function styles(Worksheet $sheet): array
    {
        return [
            1 => [
                'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
                'fill' => [
                    'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                    'startColor' => ['rgb' => '00897B'],
                ],
            ],
        ];
    }

    public function columnWidths(): array
    {
        return [
            'A' => 15, 'B' => 20, 'C' => 15, 'D' => 15,
            'E' => 15, 'F' => 15, 'G' => 18, 'H' => 18,
            'I' => 30, 'J' => 15, 'K' => 20, 'L' => 15,
            'M' => 15, 'N' => 15, 'O' => 15, 'P' => 15,
            'Q' => 25, 'R' => 15, 'S' => 20, 'T' => 20,
        ];
    }
}
