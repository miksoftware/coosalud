<?php

namespace App\Imports;

use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Illuminate\Support\Collection;

class CedulasImport implements ToCollection, WithHeadingRow
{
    private Collection $cedulas;

    public function __construct()
    {
        $this->cedulas = collect();
    }

    public function collection(Collection $rows): void
    {
        foreach ($rows as $row) {
            // Busca la columna 'cedula', 'documento', 'numero_documento' o la primera columna
            $cedula = $row['cedula']
                ?? $row['documento']
                ?? $row['numero_documento']
                ?? $row['identificacion']
                ?? $row['cc']
                ?? $row->first();

            if ($cedula && is_numeric(trim($cedula))) {
                $this->cedulas->push(trim((string) $cedula));
            }
        }
    }

    public function getCedulas(): Collection
    {
        return $this->cedulas->unique()->values();
    }
}
