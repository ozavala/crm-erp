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
        $rows[] = [
            __('iva_reports.Month'),
            __('iva_reports.VAT Paid'),
            __('iva_reports.VAT Collected'),
            __('iva_reports.Net VAT'),
            __('iva_reports.Status'),
        ];
        foreach ($this->summary as $month => $report) {
            $rows[] = [
                \Carbon\Carbon::create()->month($month+1)->format(__('iva_reports.months.' . strtolower(\Carbon\Carbon::create()->month($month+1)->format('F')))),
                $report['tax_paid']['total'] ?? 0,
                $report['tax_collected']['total'] ?? 0,
                $report['net_tax']['amount'] ?? 0,
                $report['net_tax']['status'] == 'payable' ? __('iva_reports.Payable') : __('iva_reports.Receivable'),
            ];
        }
        return $rows;
    }

    public function headings(): array
    {
        return [
            __('iva_reports.Month'),
            __('iva_reports.VAT Paid'),
            __('iva_reports.VAT Collected'),
            __('iva_reports.Net VAT'),
            __('iva_reports.Status'),
        ];
    }

    public function title(): string
    {
        return __('iva_reports.Annual VAT Report') . ' ' . $this->year;
    }
} 