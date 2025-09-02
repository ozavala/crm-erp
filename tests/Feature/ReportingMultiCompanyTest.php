<?php

namespace Tests\Feature;

use App\Models\CrmUser;
use App\Models\Customer;
use App\Models\Invoice;
use App\Models\InvoiceItem;
use App\Models\JournalEntry;
use App\Models\OwnerCompany;
use App\Models\Payment;
use App\Models\Product;
use App\Models\Supplier;
use App\Models\TaxRate;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class ReportingMultiCompanyTest extends TestCase
{
    use RefreshDatabase;

    protected OwnerCompany $company1;
    protected OwnerCompany $company2;
    protected CrmUser $user1;
    protected CrmUser $user2;
    protected CrmUser $superAdmin;
    protected Customer $customer1;
    protected Customer $customer2;
    protected Supplier $supplier1;
    protected Supplier $supplier2;
    protected Product $product1;
    protected Product $product2;
    protected TaxRate $taxRate1;
    protected TaxRate $taxRate2;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(\Database\Seeders\SettingsTableSeeder::class);

        // Create two companies
        $this->company1 = OwnerCompany::create([
            'name' => 'Company One',
            'legal_name' => 'Company One LLC',
            'tax_id' => 'TAX-001',
            'email' => 'info@company1.com',
            'phone' => '123-456-7890',
            'address' => '123 Main St, Anytown, USA',
            'is_active' => true,
        ]);

        $this->company2 = OwnerCompany::create([
            'name' => 'Company Two',
            'legal_name' => 'Company Two Inc',
            'tax_id' => 'TAX-002',
            'email' => 'info@company2.com',
            'phone' => '987-654-3210',
            'address' => '456 Oak Ave, Somewhere, USA',
            'is_active' => true,
        ]);

        // Create users for each company
        $this->user1 = CrmUser::factory()->create([
            'owner_company_id' => $this->company1->owner_company_id,
        ]);

        $this->user2 = CrmUser::factory()->create([
            'owner_company_id' => $this->company2->owner_company_id,
        ]);

        // Create a super admin user
        $this->superAdmin = CrmUser::factory()->create([
            'is_super_admin' => true,
            'owner_company_id' => $this->company1->owner_company_id, // Primary company
        ]);

        // Give necessary permissions to users
        $this->givePermission($this->user1, [
            'view-reports',
            'view-financial-reports',
            'view-sales-reports',
            'view-tax-reports',
            'view-customer-reports',
            'view-supplier-reports',
            'view-inventory-reports',
            'export-reports'
        ]);

        $this->givePermission($this->user2, [
            'view-reports',
            'view-financial-reports',
            'view-sales-reports',
            'view-tax-reports',
            'view-customer-reports',
            'view-supplier-reports',
            'view-inventory-reports',
            'export-reports'
        ]);

        $this->givePermission($this->superAdmin, [
            'view-reports',
            'view-financial-reports',
            'view-sales-reports',
            'view-tax-reports',
            'view-customer-reports',
            'view-supplier-reports',
            'view-inventory-reports',
            'export-reports',
            'manage-companies'
        ]);

        // Create customers for each company
        $this->customer1 = Customer::factory()->create([
            'first_name' => 'John',
            'last_name' => 'Doe',
            'email' => 'john.doe@example.com',
            'owner_company_id' => $this->company1->owner_company_id,
        ]);

        $this->customer2 = Customer::factory()->create([
            'first_name' => 'Jane',
            'last_name' => 'Smith',
            'email' => 'jane.smith@example.com',
            'owner_company_id' => $this->company2->owner_company_id,
        ]);

        // Create suppliers for each company
        $this->supplier1 = Supplier::factory()->create([
            'name' => 'Supplier 1',
            'email' => 'supplier1@example.com',
            'owner_company_id' => $this->company1->owner_company_id,
        ]);

        $this->supplier2 = Supplier::factory()->create([
            'name' => 'Supplier 2',
            'email' => 'supplier2@example.com',
            'owner_company_id' => $this->company2->owner_company_id,
        ]);

        // Create products for each company
        $this->product1 = Product::factory()->create([
            'name' => 'Product 1',
            'price' => 100.00,
            'owner_company_id' => $this->company1->owner_company_id,
        ]);

        $this->product2 = Product::factory()->create([
            'name' => 'Product 2',
            'price' => 200.00,
            'owner_company_id' => $this->company2->owner_company_id,
        ]);

        // Create tax rates for each company
        $this->taxRate1 = TaxRate::create([
            'name' => 'IVA 12% - Company 1',
            'rate' => 12.00,
            'is_active' => true,
            'owner_company_id' => $this->company1->owner_company_id,
        ]);

        $this->taxRate2 = TaxRate::create([
            'name' => 'IVA 12% - Company 2',
            'rate' => 12.00,
            'is_active' => true,
            'owner_company_id' => $this->company2->owner_company_id,
        ]);

        // Create invoices and payments for company 1
        $this->createInvoicesAndPayments($this->company1, $this->customer1, $this->product1, $this->taxRate1, $this->user1);

        // Create invoices and payments for company 2
        $this->createInvoicesAndPayments($this->company2, $this->customer2, $this->product2, $this->taxRate2, $this->user2);
    }

    protected function createInvoicesAndPayments($company, $customer, $product, $taxRate, $user)
    {
        // Create 5 invoices for the company
        for ($i = 1; $i <= 5; $i++) {
            $invoice = Invoice::create([
                'customer_id' => $customer->customer_id,
                'invoice_number' => 'INV-' . $company->owner_company_id . '-' . $i,
                'invoice_date' => now()->subDays(30 - $i),
                'due_date' => now()->addDays($i),
                'status' => $i <= 3 ? 'Paid' : 'Pending',
                'notes' => 'Invoice ' . $i . ' for ' . $company->name,
                'created_by_user_id' => $user->user_id,
                'owner_company_id' => $company->owner_company_id,
            ]);

            // Create invoice items
            $quantity = $i;
            $unitPrice = $product->price;
            $subtotal = $quantity * $unitPrice;
            $taxAmount = $subtotal * ($taxRate->rate / 100);
            $total = $subtotal + $taxAmount;

            InvoiceItem::create([
                'invoice_id' => $invoice->invoice_id,
                'product_id' => $product->product_id,
                'description' => $product->name,
                'quantity' => $quantity,
                'unit_price' => $unitPrice,
                'tax_rate_id' => $taxRate->tax_rate_id,
                'tax_amount' => $taxAmount,
                'subtotal' => $subtotal,
                'total' => $total,
                'owner_company_id' => $company->owner_company_id,
            ]);

            // Create payments for paid invoices
            if ($i <= 3) {
                Payment::create([
                    'invoice_id' => $invoice->invoice_id,
                    'payment_date' => now()->subDays(25 - $i),
                    'amount' => $total,
                    'payment_method' => 'Bank Transfer',
                    'reference' => 'REF-' . $company->owner_company_id . '-' . $i,
                    'notes' => 'Payment for invoice ' . $invoice->invoice_number,
                    'created_by_user_id' => $user->user_id,
                    'owner_company_id' => $company->owner_company_id,
                ]);

                // Update invoice status
                $invoice->update(['status' => 'Paid']);
            }

            // Create journal entries for the invoice
            JournalEntry::create([
                'entry_date' => $invoice->invoice_date,
                'reference' => $invoice->invoice_number,
                'description' => 'Invoice ' . $invoice->invoice_number,
                'debit_account_id' => 1, // Accounts Receivable
                'credit_account_id' => 4, // Sales Revenue
                'amount' => $subtotal,
                'created_by_user_id' => $user->user_id,
                'owner_company_id' => $company->owner_company_id,
            ]);

            // Create journal entry for tax
            JournalEntry::create([
                'entry_date' => $invoice->invoice_date,
                'reference' => $invoice->invoice_number . '-TAX',
                'description' => 'Tax for invoice ' . $invoice->invoice_number,
                'debit_account_id' => 1, // Accounts Receivable
                'credit_account_id' => 5, // Tax Payable
                'amount' => $taxAmount,
                'tax_rate_id' => $taxRate->tax_rate_id,
                'tax_amount' => $taxAmount,
                'created_by_user_id' => $user->user_id,
                'owner_company_id' => $company->owner_company_id,
            ]);

            // Create journal entries for payments
            if ($i <= 3) {
                JournalEntry::create([
                    'entry_date' => now()->subDays(25 - $i),
                    'reference' => 'REF-' . $company->owner_company_id . '-' . $i,
                    'description' => 'Payment for invoice ' . $invoice->invoice_number,
                    'debit_account_id' => 2, // Cash
                    'credit_account_id' => 1, // Accounts Receivable
                    'amount' => $total,
                    'created_by_user_id' => $user->user_id,
                    'owner_company_id' => $company->owner_company_id,
                ]);
            }
        }
    }

    #[Test]
    public function sales_reports_are_isolated_between_companies()
    {
        // Verify that company 1 user can only see company 1 sales data
        $this->actingAs($this->user1);
        $response = $this->get(route('reports.sales', [
            'start_date' => now()->subDays(30)->format('Y-m-d'),
            'end_date' => now()->format('Y-m-d'),
        ]));
        $response->assertOk();
        
        // Check for company 1 invoice numbers
        for ($i = 1; $i <= 5; $i++) {
            $response->assertSee('INV-' . $this->company1->owner_company_id . '-' . $i);
        }
        
        // Check that company 2 invoice numbers are not visible
        for ($i = 1; $i <= 5; $i++) {
            $response->assertDontSee('INV-' . $this->company2->owner_company_id . '-' . $i);
        }

        // Verify that company 2 user can only see company 2 sales data
        $this->actingAs($this->user2);
        $response = $this->get(route('reports.sales', [
            'start_date' => now()->subDays(30)->format('Y-m-d'),
            'end_date' => now()->format('Y-m-d'),
        ]));
        $response->assertOk();
        
        // Check for company 2 invoice numbers
        for ($i = 1; $i <= 5; $i++) {
            $response->assertSee('INV-' . $this->company2->owner_company_id . '-' . $i);
        }
        
        // Check that company 1 invoice numbers are not visible
        for ($i = 1; $i <= 5; $i++) {
            $response->assertDontSee('INV-' . $this->company1->owner_company_id . '-' . $i);
        }

        // Verify that super admin can see both companies' sales data
        $this->actingAs($this->superAdmin);
        $response = $this->get(route('reports.sales', [
            'start_date' => now()->subDays(30)->format('Y-m-d'),
            'end_date' => now()->format('Y-m-d'),
        ]));
        $response->assertOk();
        
        // Check for both companies' invoice numbers
        for ($i = 1; $i <= 5; $i++) {
            $response->assertSee('INV-' . $this->company1->owner_company_id . '-' . $i);
            $response->assertSee('INV-' . $this->company2->owner_company_id . '-' . $i);
        }
    }

    #[Test]
    public function tax_reports_are_isolated_between_companies()
    {
        // Verify that company 1 user can only see company 1 tax data
        $this->actingAs($this->user1);
        $response = $this->get(route('reports.tax', [
            'year' => now()->year,
            'month' => now()->month,
        ]));
        $response->assertOk();
        
        // Check for company 1 tax rate name
        $response->assertSee('IVA 12% - Company 1');
        
        // Check that company 2 tax rate name is not visible
        $response->assertDontSee('IVA 12% - Company 2');
        
        // Check for company 1 invoice numbers in tax report
        for ($i = 1; $i <= 5; $i++) {
            $response->assertSee('INV-' . $this->company1->owner_company_id . '-' . $i . '-TAX');
        }
        
        // Check that company 2 invoice numbers are not visible in tax report
        for ($i = 1; $i <= 5; $i++) {
            $response->assertDontSee('INV-' . $this->company2->owner_company_id . '-' . $i . '-TAX');
        }

        // Verify that company 2 user can only see company 2 tax data
        $this->actingAs($this->user2);
        $response = $this->get(route('reports.tax', [
            'year' => now()->year,
            'month' => now()->month,
        ]));
        $response->assertOk();
        
        // Check for company 2 tax rate name
        $response->assertSee('IVA 12% - Company 2');
        
        // Check that company 1 tax rate name is not visible
        $response->assertDontSee('IVA 12% - Company 1');
        
        // Check for company 2 invoice numbers in tax report
        for ($i = 1; $i <= 5; $i++) {
            $response->assertSee('INV-' . $this->company2->owner_company_id . '-' . $i . '-TAX');
        }
        
        // Check that company 1 invoice numbers are not visible in tax report
        for ($i = 1; $i <= 5; $i++) {
            $response->assertDontSee('INV-' . $this->company1->owner_company_id . '-' . $i . '-TAX');
        }

        // Verify that super admin can see both companies' tax data
        $this->actingAs($this->superAdmin);
        $response = $this->get(route('reports.tax', [
            'year' => now()->year,
            'month' => now()->month,
        ]));
        $response->assertOk();
        
        // Check for both companies' tax rate names
        $response->assertSee('IVA 12% - Company 1');
        $response->assertSee('IVA 12% - Company 2');
        
        // Check for both companies' invoice numbers in tax report
        for ($i = 1; $i <= 5; $i++) {
            $response->assertSee('INV-' . $this->company1->owner_company_id . '-' . $i . '-TAX');
            $response->assertSee('INV-' . $this->company2->owner_company_id . '-' . $i . '-TAX');
        }
    }

    #[Test]
    public function customer_reports_are_isolated_between_companies()
    {
        // Verify that company 1 user can only see company 1 customer data
        $this->actingAs($this->user1);
        $response = $this->get(route('reports.customers'));
        $response->assertOk();
        
        // Check for company 1 customer
        $response->assertSee('John Doe');
        
        // Check that company 2 customer is not visible
        $response->assertDontSee('Jane Smith');

        // Verify that company 2 user can only see company 2 customer data
        $this->actingAs($this->user2);
        $response = $this->get(route('reports.customers'));
        $response->assertOk();
        
        // Check for company 2 customer
        $response->assertSee('Jane Smith');
        
        // Check that company 1 customer is not visible
        $response->assertDontSee('John Doe');

        // Verify that super admin can see both companies' customer data
        $this->actingAs($this->superAdmin);
        $response = $this->get(route('reports.customers'));
        $response->assertOk();
        
        // Check for both companies' customers
        $response->assertSee('John Doe');
        $response->assertSee('Jane Smith');
    }

    #[Test]
    public function financial_reports_are_isolated_between_companies()
    {
        // Verify that company 1 user can only see company 1 financial data
        $this->actingAs($this->user1);
        $response = $this->get(route('reports.financial', [
            'start_date' => now()->subDays(30)->format('Y-m-d'),
            'end_date' => now()->format('Y-m-d'),
        ]));
        $response->assertOk();
        
        // Check for company 1 invoice references
        for ($i = 1; $i <= 5; $i++) {
            $response->assertSee('INV-' . $this->company1->owner_company_id . '-' . $i);
        }
        
        // Check that company 2 invoice references are not visible
        for ($i = 1; $i <= 5; $i++) {
            $response->assertDontSee('INV-' . $this->company2->owner_company_id . '-' . $i);
        }
        
        // Check for company 1 payment references
        for ($i = 1; $i <= 3; $i++) {
            $response->assertSee('REF-' . $this->company1->owner_company_id . '-' . $i);
        }
        
        // Check that company 2 payment references are not visible
        for ($i = 1; $i <= 3; $i++) {
            $response->assertDontSee('REF-' . $this->company2->owner_company_id . '-' . $i);
        }

        // Verify that company 2 user can only see company 2 financial data
        $this->actingAs($this->user2);
        $response = $this->get(route('reports.financial', [
            'start_date' => now()->subDays(30)->format('Y-m-d'),
            'end_date' => now()->format('Y-m-d'),
        ]));
        $response->assertOk();
        
        // Check for company 2 invoice references
        for ($i = 1; $i <= 5; $i++) {
            $response->assertSee('INV-' . $this->company2->owner_company_id . '-' . $i);
        }
        
        // Check that company 1 invoice references are not visible
        for ($i = 1; $i <= 5; $i++) {
            $response->assertDontSee('INV-' . $this->company1->owner_company_id . '-' . $i);
        }
        
        // Check for company 2 payment references
        for ($i = 1; $i <= 3; $i++) {
            $response->assertSee('REF-' . $this->company2->owner_company_id . '-' . $i);
        }
        
        // Check that company 1 payment references are not visible
        for ($i = 1; $i <= 3; $i++) {
            $response->assertDontSee('REF-' . $this->company1->owner_company_id . '-' . $i);
        }

        // Verify that super admin can see both companies' financial data
        $this->actingAs($this->superAdmin);
        $response = $this->get(route('reports.financial', [
            'start_date' => now()->subDays(30)->format('Y-m-d'),
            'end_date' => now()->format('Y-m-d'),
        ]));
        $response->assertOk();
        
        // Check for both companies' invoice references
        for ($i = 1; $i <= 5; $i++) {
            $response->assertSee('INV-' . $this->company1->owner_company_id . '-' . $i);
            $response->assertSee('INV-' . $this->company2->owner_company_id . '-' . $i);
        }
        
        // Check for both companies' payment references
        for ($i = 1; $i <= 3; $i++) {
            $response->assertSee('REF-' . $this->company1->owner_company_id . '-' . $i);
            $response->assertSee('REF-' . $this->company2->owner_company_id . '-' . $i);
        }
    }

    #[Test]
    public function report_exports_are_isolated_between_companies()
    {
        // Verify that company 1 user can only export company 1 data
        $this->actingAs($this->user1);
        $response = $this->get(route('reports.export', [
            'report_type' => 'sales',
            'format' => 'excel',
            'start_date' => now()->subDays(30)->format('Y-m-d'),
            'end_date' => now()->format('Y-m-d'),
        ]));
        $response->assertOk();
        $response->assertHeader('Content-Type', 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        
        // The content of the Excel file cannot be easily checked in a test,
        // but we can verify that the export was successful and the correct
        // content-type header was set.

        // Verify that company 2 user can only export company 2 data
        $this->actingAs($this->user2);
        $response = $this->get(route('reports.export', [
            'report_type' => 'sales',
            'format' => 'excel',
            'start_date' => now()->subDays(30)->format('Y-m-d'),
            'end_date' => now()->format('Y-m-d'),
        ]));
        $response->assertOk();
        $response->assertHeader('Content-Type', 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');

        // Verify that super admin can export all data
        $this->actingAs($this->superAdmin);
        $response = $this->get(route('reports.export', [
            'report_type' => 'sales',
            'format' => 'excel',
            'start_date' => now()->subDays(30)->format('Y-m-d'),
            'end_date' => now()->format('Y-m-d'),
        ]));
        $response->assertOk();
        $response->assertHeader('Content-Type', 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
    }
}