<?php

namespace App\Http\Controllers\Reports;

use App\Http\Controllers\Controller;
use App\Models\Invoice;
use App\Models\Bill;
use App\Models\TaxCollection;
use App\Models\TaxPayment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class TaxBalanceController extends Controller
{
    /**
     * Display the tax balance report
     */
    public function index(Request $request)
    {
        $startDate = $request->input('start_date', now()->startOfMonth()->format('Y-m-d'));
        $endDate = $request->input('end_date', now()->endOfMonth()->format('Y-m-d'));
        
        $report = $this->generateTaxBalanceReport($startDate, $endDate);
        
        return view('reports.tax_balance.index', compact('report', 'startDate', 'endDate'));
    }

    /**
     * Generate tax balance report for a specific period
     */
    private function generateTaxBalanceReport(string $startDate, string $endDate): array
    {
        $start = Carbon::parse($startDate)->startOfDay();
        $end = Carbon::parse($endDate)->endOfDay();

        // Impuestos de venta recibidos (VAT Collected)
        $salesTaxCollected = Invoice::whereBetween('invoice_date', [$start, $end])
            ->where('status', '!=', 'Void')
            ->where('status', '!=', 'Cancelled')
            ->select(
                DB::raw('SUM(tax_amount) as total_tax_collected'),
                DB::raw('COUNT(*) as total_invoices'),
                DB::raw('SUM(subtotal) as total_taxable_amount')
            )
            ->first();

        // Impuestos de compra pagados (VAT Paid)
        $purchaseTaxPaid = Bill::whereBetween('bill_date', [$start, $end])
            ->where('status', '!=', 'Cancelled')
            ->select(
                DB::raw('SUM(tax_amount) as total_tax_paid'),
                DB::raw('COUNT(*) as total_bills'),
                DB::raw('SUM(subtotal) as total_taxable_amount')
            )
            ->first();

        // Detalle por tasa de impuesto - Ventas
        $salesTaxByRate = Invoice::whereBetween('invoices.invoice_date', [$start, $end])
            ->where('invoices.status', '!=', 'Void')
            ->where('invoices.status', '!=', 'Cancelled')
            ->whereNotNull('invoices.tax_rate_id')
            ->join('tax_rates', 'invoices.tax_rate_id', '=', 'tax_rates.tax_rate_id')
            ->select(
                'tax_rates.name as tax_rate_name',
                'tax_rates.rate as tax_rate_percentage',
                DB::raw('SUM(invoices.tax_amount) as total_tax_collected'),
                DB::raw('COUNT(*) as invoice_count'),
                DB::raw('SUM(invoices.subtotal) as total_taxable_amount')
            )
            ->groupBy('tax_rates.tax_rate_id', 'tax_rates.name', 'tax_rates.rate')
            ->orderBy('tax_rates.rate', 'desc')
            ->get();

        // Detalle por tasa de impuesto - Compras
        $purchaseTaxByRate = Bill::whereBetween('bills.bill_date', [$start, $end])
            ->where('bills.status', '!=', 'Cancelled')
            ->join('purchase_orders', 'bills.purchase_order_id', '=', 'purchase_orders.purchase_order_id')
            ->select(
                DB::raw('purchase_orders.tax_percentage as tax_rate_percentage'),
                DB::raw('CONCAT("Tax Rate ", purchase_orders.tax_percentage, "%") as tax_rate_name'),
                DB::raw('SUM(bills.tax_amount) as total_tax_paid'),
                DB::raw('COUNT(*) as bill_count'),
                DB::raw('SUM(bills.subtotal) as total_taxable_amount')
            )
            ->groupBy('purchase_orders.tax_percentage')
            ->orderBy('purchase_orders.tax_percentage', 'desc')
            ->get();

        // Top 10 clientes por impuestos pagados
        $topCustomersByTax = Invoice::whereBetween('invoices.invoice_date', [$start, $end])
            ->where('invoices.status', '!=', 'Void')
            ->where('invoices.status', '!=', 'Cancelled')
            ->join('customers', 'invoices.customer_id', '=', 'customers.customer_id')
            ->select(
                'customers.company_name as customer_name',
                'customers.first_name',
                'customers.last_name',
                DB::raw('SUM(invoices.tax_amount) as total_tax_collected'),
                DB::raw('COUNT(*) as invoice_count')
            )
            ->groupBy('customers.customer_id', 'customers.company_name', 'customers.first_name', 'customers.last_name')
            ->orderByDesc('total_tax_collected')
            ->limit(10)
            ->get();

        // Top 10 proveedores por impuestos pagados
        $topSuppliersByTax = Bill::whereBetween('bills.bill_date', [$start, $end])
            ->where('bills.status', '!=', 'Cancelled')
            ->join('suppliers', 'bills.supplier_id', '=', 'suppliers.supplier_id')
            ->select(
                'suppliers.name as supplier_name',
                'suppliers.contact_person',
                DB::raw('SUM(bills.tax_amount) as total_tax_paid'),
                DB::raw('COUNT(*) as bill_count')
            )
            ->groupBy('suppliers.supplier_id', 'suppliers.name', 'suppliers.contact_person')
            ->orderByDesc('total_tax_paid')
            ->limit(10)
            ->get();

        // Cálculo del balance neto
        $totalTaxCollected = $salesTaxCollected->total_tax_collected ?? 0;
        $totalTaxPaid = $purchaseTaxPaid->total_tax_paid ?? 0;
        $netTaxBalance = $totalTaxCollected - $totalTaxPaid;

        return [
            'period' => [
                'start_date' => $start->format('Y-m-d'),
                'end_date' => $end->format('Y-m-d'),
                'start_formatted' => $start->format('d/m/Y'),
                'end_formatted' => $end->format('d/m/Y'),
            ],
            'summary' => [
                'total_tax_collected' => $totalTaxCollected,
                'total_tax_paid' => $totalTaxPaid,
                'net_tax_balance' => $netTaxBalance,
                'balance_status' => $netTaxBalance >= 0 ? 'payable' : 'refundable',
                'total_invoices' => $salesTaxCollected->total_invoices ?? 0,
                'total_bills' => $purchaseTaxPaid->total_bills ?? 0,
            ],
            'sales_tax_by_rate' => $salesTaxByRate,
            'purchase_tax_by_rate' => $purchaseTaxByRate,
            'top_customers_by_tax' => $topCustomersByTax,
            'top_suppliers_by_tax' => $topSuppliersByTax,
        ];
    }

    /**
     * Export tax balance report to PDF
     */
    public function exportPdf(Request $request)
    {
        $startDate = $request->input('start_date', now()->startOfMonth()->format('Y-m-d'));
        $endDate = $request->input('end_date', now()->endOfMonth()->format('Y-m-d'));
        
        $report = $this->generateTaxBalanceReport($startDate, $endDate);
        
        $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('reports.tax_balance.pdf', compact('report'));
        
        return $pdf->stream('tax_balance_report_' . $startDate . '_to_' . $endDate . '.pdf');
    }

    /**
     * Export tax balance report to Excel
     */
    public function exportExcel(Request $request)
    {
        $startDate = $request->input('start_date', now()->startOfMonth()->format('Y-m-d'));
        $endDate = $request->input('end_date', now()->endOfMonth()->format('Y-m-d'));
        
        $report = $this->generateTaxBalanceReport($startDate, $endDate);
        
        // Create a new spreadsheet
        $spreadsheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        
        // Set the title
        $sheet->setCellValue('A1', 'Tax Balance Report');
        $sheet->setCellValue('A2', 'Period: ' . $report['period']['start_formatted'] . ' to ' . $report['period']['end_formatted']);
        
        // Set headers for summary
        $sheet->setCellValue('A4', 'Summary');
        $sheet->setCellValue('A5', 'Total Tax Collected');
        $sheet->setCellValue('B5', $report['summary']['total_tax_collected']);
        $sheet->setCellValue('A6', 'Total Tax Paid');
        $sheet->setCellValue('B6', $report['summary']['total_tax_paid']);
        $sheet->setCellValue('A7', 'Net Tax Balance');
        $sheet->setCellValue('B7', $report['summary']['net_tax_balance']);
        $sheet->setCellValue('A8', 'Balance Status');
        $sheet->setCellValue('B8', $report['summary']['balance_status']);
        
        // Set headers for sales tax by rate
        $sheet->setCellValue('A10', 'Sales Tax by Rate');
        $sheet->setCellValue('A11', 'Tax Rate');
        $sheet->setCellValue('B11', 'Percentage');
        $sheet->setCellValue('C11', 'Total Amount');
        $sheet->setCellValue('D11', 'Count');
        
        // Add sales tax by rate data
        $row = 12;
        foreach ($report['sales_tax_by_rate'] as $item) {
            $sheet->setCellValue('A' . $row, $item['tax_rate_name']);
            $sheet->setCellValue('B' . $row, $item['tax_rate_percentage'] . '%');
            $sheet->setCellValue('C' . $row, $item['total_tax_collected']);
            $sheet->setCellValue('D' . $row, $item['invoice_count']);
            $row++;
        }
        
        // Set headers for purchase tax by rate
        $sheet->setCellValue('A' . ($row + 1), 'Purchase Tax by Rate');
        $sheet->setCellValue('A' . ($row + 2), 'Tax Rate');
        $sheet->setCellValue('B' . ($row + 2), 'Percentage');
        $sheet->setCellValue('C' . ($row + 2), 'Total Amount');
        $sheet->setCellValue('D' . ($row + 2), 'Count');
        
        // Add purchase tax by rate data
        $row += 3;
        foreach ($report['purchase_tax_by_rate'] as $item) {
            $sheet->setCellValue('A' . $row, $item['tax_rate_name']);
            $sheet->setCellValue('B' . $row, $item['tax_rate_percentage'] . '%');
            $sheet->setCellValue('C' . $row, $item['total_tax_paid']);
            $sheet->setCellValue('D' . $row, $item['bill_count']);
            $row++;
        }
        
        // Create Excel file
        $writer = new \PhpOffice\PhpSpreadsheet\Writer\Xlsx($spreadsheet);
        
        // Set the filename
        $filename = 'tax_balance_report_' . $startDate . '_to_' . $endDate . '.xlsx';
        
        // Create a temporary file
        $tempFile = tempnam(sys_get_temp_dir(), 'tax_balance_report');
        $writer->save($tempFile);
        
        // Return the file as a download
        return response()->download($tempFile, $filename, [
            'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
        ])->deleteFileAfterSend(true);
    }
} 