<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\OwnerCompany;
use App\Models\CrmUser;
use App\Models\Customer;
use App\Models\Supplier;
use App\Models\Product;
use App\Models\Order;
use App\Models\Invoice;
use App\Models\Payment;
use App\Models\Contact;
use Illuminate\Support\Str;

class MultiTenancyDemoSeeder extends Seeder
{
    /**
     * Run the database seeds to demonstrate multi-tenancy functionality.
     * This seeder creates multiple owner companies with their own sets of data.
     */
    public function run(): void
    {
        $this->command->info('Creating Multi-Tenancy Demo Data...');

        // Create 3 distinct owner companies
        $companies = [
            [
                'name' => 'Alpha Corporation',
                'legal_id' => 'ALPHA-001',
                'industry' => 'Technology',
            ],
            [
                'name' => 'Beta Enterprises',
                'legal_id' => 'BETA-002',
                'industry' => 'Manufacturing',
            ],
            [
                'name' => 'Gamma Services',
                'legal_id' => 'GAMMA-003',
                'industry' => 'Consulting',
            ],
        ];

        foreach ($companies as $index => $companyData) {
            // Create owner company
            $company = OwnerCompany::create($companyData);
            $this->command->info("Created Owner Company: {$company->name}");

            // Create users for this company
            $admin = CrmUser::create([
                'username' => 'admin_' . strtolower(explode(' ', $company->name)[0]),
                'full_name' => 'Admin ' . $company->name,
                'email' => 'admin@' . strtolower(explode(' ', $company->name)[0]) . '.com',
                'password' => bcrypt('password'),
                'email_verified_at' => now(),
                'owner_company_id' => $company->id,
            ]);

            $user = CrmUser::create([
                'username' => 'user_' . strtolower(explode(' ', $company->name)[0]),
                'full_name' => 'User ' . $company->name,
                'email' => 'user@' . strtolower(explode(' ', $company->name)[0]) . '.com',
                'password' => bcrypt('password'),
                'email_verified_at' => now(),
                'owner_company_id' => $company->id,
            ]);

            // Create customers for this company
            for ($i = 1; $i <= 3; $i++) {
                $customer = Customer::create([
                    'first_name' => "Customer{$i}",
                    'last_name' => $company->name,
                    'email' => "customer{$i}@" . strtolower(explode(' ', $company->name)[0]) . '.com',
                    'phone_number' => '123-456-' . rand(1000, 9999),
                    'company_name' => "Client {$i} of {$company->name}",
                    'legal_id' => "CUST-{$company->legal_id}-{$i}",
                    'status' => 'active',
                    'created_by_user_id' => $admin->user_id,
                    'owner_company_id' => $company->id,
                ]);

                // Add contact for customer
                $contact = Contact::create([
                    'contactable_type' => Customer::class,
                    'contactable_id' => $customer->customer_id,
                    'first_name' => "Contact{$i}",
                    'last_name' => $company->name,
                    'email' => "contact{$i}@" . strtolower(explode(' ', $company->name)[0]) . '.com',
                    'phone' => '987-654-' . rand(1000, 9999),
                    'position' => 'Manager',
                    'is_primary' => true,
                ]);

                // Create products for this company
                $product = Product::create([
                    'name' => "Product {$i} - {$company->name}",
                    'description' => "A product specific to {$company->name}",
                    'sku' => "SKU-{$company->legal_id}-{$i}",
                    'price' => rand(100, 500),
                    'quantity_on_hand' => rand(10, 100),
                    'is_service' => false,
                    'is_active' => true,
                    'created_by_user_id' => $admin->user_id,
                    'product_category_id' => 1, // Assuming category ID 1 exists
                    'owner_company_id' => $company->id,
                ]);

                // Create supplier for this company
                $supplier = Supplier::create([
                    'name' => "Supplier {$i} - {$company->name}",
                    'legal_id' => "SUP-{$company->legal_id}-{$i}",
                    'contact_person' => "Supplier Contact {$i}",
                    'email' => "supplier{$i}@" . strtolower(explode(' ', $company->name)[0]) . '.com',
                    'phone_number' => '555-123-' . rand(1000, 9999),
                    'notes' => "Supplier for {$company->name}",
                    'owner_company_id' => $company->id,
                ]);

                // Create order for this customer
                $order = Order::create([
                    'customer_id' => $customer->customer_id,
                    'order_number' => "ORD-{$company->legal_id}-{$i}",
                    'order_date' => now()->subDays(rand(1, 30)),
                    'status' => 'Processing',
                    'subtotal' => 100 * $i,
                    'tax_percentage' => 10,
                    'tax_amount' => 10 * $i,
                    'total_amount' => 110 * $i,
                    'amount_paid' => 0,
                    'notes' => "Order for {$customer->full_name}",
                    'created_by_user_id' => $user->user_id,
                    'owner_company_id' => $company->id,
                ]);

                // Create invoice for this order
                $invoice = Invoice::create([
                    'order_id' => $order->order_id,
                    'customer_id' => $customer->customer_id,
                    'invoice_number' => "INV-{$company->legal_id}-{$i}",
                    'invoice_date' => now()->subDays(rand(1, 15)),
                    'due_date' => now()->addDays(15),
                    'status' => 'Sent',
                    'subtotal' => 100 * $i,
                    'tax_percentage' => 10,
                    'tax_amount' => 10 * $i,
                    'total_amount' => 110 * $i,
                    'amount_paid' => 0,
                    'terms_and_conditions' => 'Standard payment terms apply.',
                    'notes' => "Invoice for Order {$order->order_number}",
                    'created_by_user_id' => $user->user_id,
                    'owner_company_id' => $company->id,
                ]);

                // Create payment for this invoice
                if ($i % 2 == 0) { // Only create payment for some invoices
                    $payment = Payment::create([
                        'payable_type' => Invoice::class,
                        'payable_id' => $invoice->invoice_id,
                        'payment_date' => now()->subDays(rand(1, 10)),
                        'amount' => 110 * $i,
                        'payment_method' => 'Credit Card',
                        'reference_number' => "PAY-{$company->legal_id}-{$i}",
                        'notes' => "Payment for Invoice {$invoice->invoice_number}",
                        'created_by_user_id' => $user->user_id,
                        'owner_company_id' => $company->id,
                    ]);

                    // Update invoice status
                    $invoice->amount_paid = 110 * $i;
                    $invoice->status = 'Paid';
                    $invoice->save();
                }
            }

            $this->command->info("Created demo data for {$company->name}");
            $this->command->info("---");
        }

        $this->command->info('Multi-Tenancy Demo Data created successfully!');
        $this->command->info('You can now test the multi-tenancy functionality by switching between different owner companies.');
    }
}