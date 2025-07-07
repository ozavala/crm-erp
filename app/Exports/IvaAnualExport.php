<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithTitle;

class IvaAnualExport implements FromArray, WithHeadings, WithTitle
{
    protected $summary;
    protected $year;

    public function __construct(array $summary, int $year)
    {
        $this->summary = $summary;
        $this->year = $year;
    }

    public function array(): array
    {
        $rows = [];
        $rows[] = ['Mes', 'IVA Pagado', 'IVA Cobrado', 'IVA Neto', 'Estado'];
        foreach ($this->summary as $month => $report) {
            $rows[] = [
                \Carbon\Carbon::create()->month($month+1)->format('F'),
                $report['tax_paid']['total'] ?? 0,
                $report['tax_collected']['total'] ?? 0,
                $report['net_tax']['amount'] ?? 0,
                $report['net_tax']['status'] == 'payable' ? 'A pagar' : 'A favor',
            ];
        }
        return $rows;
    }

    public function headings(): array
    {
        return ['Mes', 'IVA Pagado', 'IVA Cobrado', 'IVA Neto', 'Estado'];
    }

    public function title(): string
    {
        return 'IVA Anual ' . $this->year;
    }
} 