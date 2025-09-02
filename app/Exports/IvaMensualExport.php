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
        $rows[] = [__('iva_reports.VAT Paid'), $this->report['tax_paid']['total'] ?? 0];
        $rows[] = [__('iva_reports.VAT Collected'), $this->report['tax_collected']['total'] ?? 0];
        $rows[] = [__('iva_reports.Net VAT'), $this->report['net_tax']['amount'] ?? 0];
        $rows[] = [];
        $rows[] = [__('iva_reports.Breakdown VAT Paid')];
        $rows[] = [__('iva_reports.Rate'), __('iva_reports.Amount'), __('iva_reports.Operations')];
        foreach ($this->report['tax_paid']['breakdown'] ?? [] as $item) {
            $rows[] = [
                $item['tax_rate_name'] . ' (' . $item['tax_rate_percentage'] . '%)',
                $item['total_amount'],
                $item['count'],
            ];
        }
        $rows[] = [];
        $rows[] = [__('iva_reports.Breakdown VAT Collected')];
        $rows[] = [__('iva_reports.Rate'), __('iva_reports.Amount'), __('iva_reports.Operations')];
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
        return [__('iva_reports.Concept'), __('iva_reports.Value'), __('iva_reports.Extra')];
    }

    public function title(): string
    {
        return __('iva_reports.Monthly VAT Report') . ' ' . $this->year . '-' . str_pad($this->month, 2, '0', STR_PAD_LEFT);
    }
} 