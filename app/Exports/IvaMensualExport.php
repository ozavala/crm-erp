<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithTitle;

class IvaMensualExport implements FromArray, WithHeadings, WithTitle
{
    protected $report;
    protected $year;
    protected $month;

    public function __construct(array $report, int $year, int $month)
    {
        $this->report = $report;
        $this->year = $year;
        $this->month = $month;
    }

    public function array(): array
    {
        $rows = [];
        $rows[] = ['IVA Pagado', $this->report['tax_paid']['total'] ?? 0];
        $rows[] = ['IVA Cobrado', $this->report['tax_collected']['total'] ?? 0];
        $rows[] = ['IVA Neto', $this->report['net_tax']['amount'] ?? 0];
        $rows[] = [];
        $rows[] = ['Desglose IVA Pagado'];
        $rows[] = ['Tasa', 'Monto', 'Operaciones'];
        foreach ($this->report['tax_paid']['breakdown'] ?? [] as $item) {
            $rows[] = [
                $item['tax_rate_name'] . ' (' . $item['tax_rate_percentage'] . '%)',
                $item['total_amount'],
                $item['count'],
            ];
        }
        $rows[] = [];
        $rows[] = ['Desglose IVA Cobrado'];
        $rows[] = ['Tasa', 'Monto', 'Operaciones'];
        foreach ($this->report['tax_collected']['breakdown'] ?? [] as $item) {
            $rows[] = [
                $item['tax_rate_name'] . ' (' . $item['tax_rate_percentage'] . '%)',
                $item['total_amount'],
                $item['count'],
            ];
        }
        return $rows;
    }

    public function headings(): array
    {
        return ['Concepto', 'Valor', 'Extra'];
    }

    public function title(): string
    {
        return 'IVA ' . $this->year . '-' . str_pad($this->month, 2, '0', STR_PAD_LEFT);
    }
} 