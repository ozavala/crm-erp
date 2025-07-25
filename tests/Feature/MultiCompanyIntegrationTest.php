<?php

namespace Tests\Feature;

use App\Models\Appointment;
use App\Models\Bill;
use App\Models\CalendarEvent;
use App\Models\CalendarSetting;
use App\Models\CrmUser;
use App\Models\Customer;
use App\Models\Invoice;
use App\Models\OwnerCompany;
use App\Models\Product;
use App\Models\Supplier;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class MultiCompanyIntegrationTest extends TestCase
{
    use RefreshDatabase;

    protected OwnerCompany $company1;
    protected OwnerCompany $company2;
    protected CrmUser $user1;
    protected CrmUser $user2;
    protected CrmUser $superAdmin;

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
            'view-customers',
            'create-customers',
            'edit-customers',
            'delete-customers',
            'view-products',
            'create-products',
            'edit-products',
            'delete-products',
            'view-suppliers',
            'create-suppliers',
            'edit-suppliers',
            'delete-suppliers',
            'view-invoices',
            'create-invoices',
            'edit-invoices',
            'delete-invoices',
            'view-bills',
            'create-bills',
            'edit-bills',
            'delete-bills',
            'view-calendar',
            'manage-calendar',
            'view-appointments',
            'create-appointments',
            'edit-appointments',
            'delete-appointments'
        ]);

        $this->givePermission($this->user2, [
            'view-customers',
            'create-customers',
            'edit-customers',
            'delete-customers',
            'view-products',
            'create-products',
            'edit-products',
            'delete-products',
            'view-suppliers',
            'create-suppliers',
            'edit-suppliers',
            'delete-suppliers',
            'view-invoices',
            'create-invoices',
            'edit-invoices',
            'delete-invoices',
            'view-bills',
            'create-bills',
            'edit-bills',
            'delete-bills',
            'view-calendar',
            'manage-calendar',
            'view-appointments',
            'create-appointments',
            'edit-appointments',
            'delete-appointments'
        ]);

        $this->givePermission($this->superAdmin, [
            'view-customers',
            'create-customers',
            'edit-customers',
            'delete-customers',
            'view-products',
            'create-products',
            'edit-products',
            'delete-products',
            'view-suppliers',
            'create-suppliers',
            'edit-suppliers',
            'delete-suppliers',
            'view-invoices',
            'create-invoices',
            'edit-invoices',
            'delete-invoices',
            'view-bills',
            'create-bills',
            'edit-bills',
            'delete-bills',
            'view-calendar',
            'manage-calendar',
            'view-appointments',
            'create-appointments',
            'edit-appointments',
            'delete-appointments',
            'manage-companies'
        ]);
    }

    #[Test]
    public function multi_company_structure_maintains_data_isolation()
    {
        // Create data for company 1
        $this->actingAs($this->user1);
        
        // Create a customer for company 1
        $customer1 = Customer::factory()->create([
            'owner_company_id' => $this->company1->owner_company_id,
            'first_name' => 'John',
            'last_name' => 'Doe',
        ]);
        
        // Create a product for company 1
        $product1 = Product::factory()->create([
            'owner_company_id' => $this->company1->owner_company_id,
            'name' => 'Product 1',
        ]);
        
        // Create a supplier for company 1
        $supplier1 = Supplier::factory()->create([
            'owner_company_id' => $this->company1->owner_company_id,
            'name' => 'Supplier 1',
        ]);
        
        // Create an appointment for company 1
        $appointment1 = Appointment::create([
            'title' => 'Appointment 1',
            'description' => 'Description for appointment 1',
            'start_time' => now()->addDay()->setHour(10),
            'end_time' => now()->addDay()->setHour(11),
            'location' => 'Office',
            'status' => 'Scheduled',
            'created_by_user_id' => $this->user1->user_id,
            'owner_company_id' => $this->company1->owner_company_id,
        ]);
        
        // Create data for company 2
        $this->actingAs($this->user2);
        
        // Create a customer for company 2
        $customer2 = Customer::factory()->create([
            'owner_company_id' => $this->company2->owner_company_id,
            'first_name' => 'Jane',
            'last_name' => 'Smith',
        ]);
        
        // Create a product for company 2
        $product2 = Product::factory()->create([
            'owner_company_id' => $this->company2->owner_company_id,
            'name' => 'Product 2',
        ]);
        
        // Create a supplier for company 2
        $supplier2 = Supplier::factory()->create([
            'owner_company_id' => $this->company2->owner_company_id,
            'name' => 'Supplier 2',
        ]);
        
        // Create an appointment for company 2
        $appointment2 = Appointment::create([
            'title' => 'Appointment 2',
            'description' => 'Description for appointment 2',
            'start_time' => now()->addDay()->setHour(14),
            'end_time' => now()->addDay()->setHour(15),
            'location' => 'Office',
            'status' => 'Scheduled',
            'created_by_user_id' => $this->user2->user_id,
            'owner_company_id' => $this->company2->owner_company_id,
        ]);
        
        // Verify data isolation for company 1
        $this->actingAs($this->user1);
        
        // Check customers
        $response = $this->get(route('customers.index'));
        $response->assertOk();
        $response->assertSee('John Doe');
        $response->assertDontSee('Jane Smith');
        
        // Check products
        $response = $this->get(route('products.index'));
        $response->assertOk();
        $response->assertSee('Product 1');
        $response->assertDontSee('Product 2');
        
        // Check suppliers
        $response = $this->get(route('suppliers.index'));
        $response->assertOk();
        $response->assertSee('Supplier 1');
        $response->assertDontSee('Supplier 2');
        
        // Check appointments
        $response = $this->get(route('appointments.index'));
        $response->assertOk();
        $response->assertSee('Appointment 1');
        $response->assertDontSee('Appointment 2');
        
        // Verify data isolation for company 2
        $this->actingAs($this->user2);
        
        // Check customers
        $response = $this->get(route('customers.index'));
        $response->assertOk();
        $response->assertSee('Jane Smith');
        $response->assertDontSee('John Doe');
        
        // Check products
        $response = $this->get(route('products.index'));
        $response->assertOk();
        $response->assertSee('Product 2');
        $response->assertDontSee('Product 1');
        
        // Check suppliers
        $response = $this->get(route('suppliers.index'));
        $response->assertOk();
        $response->assertSee('Supplier 2');
        $response->assertDontSee('Supplier 1');
        
        // Check appointments
        $response = $this->get(route('appointments.index'));
        $response->assertOk();
        $response->assertSee('Appointment 2');
        $response->assertDontSee('Appointment 1');
        
        // Super admin should see all data
        $this->actingAs($this->superAdmin);
        
        // Check customers
        $response = $this->get(route('customers.index'));
        $response->assertOk();
        $response->assertSee('John Doe');
        $response->assertSee('Jane Smith');
        
        // Check products
        $response = $this->get(route('products.index'));
        $response->assertOk();
        $response->assertSee('Product 1');
        $response->assertSee('Product 2');
        
        // Check suppliers
        $response = $this->get(route('suppliers.index'));
        $response->assertOk();
        $response->assertSee('Supplier 1');
        $response->assertSee('Supplier 2');
        
        // Check appointments
        $response = $this->get(route('appointments.index'));
        $response->assertOk();
        $response->assertSee('Appointment 1');
        $response->assertSee('Appointment 2');
    }

    #[Test]
    public function multi_company_structure_maintains_relationship_integrity()
    {
        // Create data for company 1
        $this->actingAs($this->user1);
        
        // Create a customer for company 1
        $customer1 = Customer::factory()->create([
            'owner_company_id' => $this->company1->owner_company_id,
            'first_name' => 'John',
            'last_name' => 'Doe',
        ]);
        
        // Create a product for company 1
        $product1 = Product::factory()->create([
            'owner_company_id' => $this->company1->owner_company_id,
            'name' => 'Product 1',
            'price' => 100,
        ]);
        
        // Create an invoice for company 1 customer
        $invoiceData = [
            'customer_id' => $customer1->customer_id,
            'invoice_date' => now()->format('Y-m-d'),
            'due_date' => now()->addDays(30)->format('Y-m-d'),
            'status' => 'Draft',
            'items' => [
                [
                    'product_id' => $product1->product_id,
                    'description' => 'Product 1 description',
                    'quantity' => 1,
                    'unit_price' => $product1->price,
                    'tax_rate_id' => null,
                    'tax_amount' => 0,
                ]
            ]
        ];
        
        $response = $this->post(route('invoices.store'), $invoiceData);
        $response->assertRedirect();
        
        // Create data for company 2
        $this->actingAs($this->user2);
        
        // Create a customer for company 2
        $customer2 = Customer::factory()->create([
            'owner_company_id' => $this->company2->owner_company_id,
            'first_name' => 'Jane',
            'last_name' => 'Smith',
        ]);
        
        // Create a product for company 2
        $product2 = Product::factory()->create([
            'owner_company_id' => $this->company2->owner_company_id,
            'name' => 'Product 2',
            'price' => 200,
        ]);
        
        // Create an invoice for company 2 customer
        $invoiceData = [
            'customer_id' => $customer2->customer_id,
            'invoice_date' => now()->format('Y-m-d'),
            'due_date' => now()->addDays(30)->format('Y-m-d'),
            'status' => 'Draft',
            'items' => [
                [
                    'product_id' => $product2->product_id,
                    'description' => 'Product 2 description',
                    'quantity' => 1,
                    'unit_price' => $product2->price,
                    'tax_rate_id' => null,
                    'tax_amount' => 0,
                ]
            ]
        ];
        
        $response = $this->post(route('invoices.store'), $invoiceData);
        $response->assertRedirect();
        
        // Verify relationship integrity for company 1
        $this->actingAs($this->user1);
        
        // Get the invoice for company 1
        $invoice1 = Invoice::where('customer_id', $customer1->customer_id)->first();
        $this->assertNotNull($invoice1);
        
        // Check that the invoice is associated with the correct company
        $this->assertEquals($this->company1->owner_company_id, $invoice1->owner_company_id);
        
        // Check that the invoice is associated with the correct customer
        $this->assertEquals($customer1->customer_id, $invoice1->customer_id);
        
        // Check that the invoice items are associated with the correct product
        $this->assertEquals($product1->product_id, $invoice1->items->first()->product_id);
        
        // Verify relationship integrity for company 2
        $this->actingAs($this->user2);
        
        // Get the invoice for company 2
        $invoice2 = Invoice::where('customer_id', $customer2->customer_id)->first();
        $this->assertNotNull($invoice2);
        
        // Check that the invoice is associated with the correct company
        $this->assertEquals($this->company2->owner_company_id, $invoice2->owner_company_id);
        
        // Check that the invoice is associated with the correct customer
        $this->assertEquals($customer2->customer_id, $invoice2->customer_id);
        
        // Check that the invoice items are associated with the correct product
        $this->assertEquals($product2->product_id, $invoice2->items->first()->product_id);
        
        // Verify that company 1 user cannot access company 2 invoice
        $this->actingAs($this->user1);
        $response = $this->get(route('invoices.show', $invoice2));
        $response->assertForbidden();
        
        // Verify that company 2 user cannot access company 1 invoice
        $this->actingAs($this->user2);
        $response = $this->get(route('invoices.show', $invoice1));
        $response->assertForbidden();
        
        // Verify that super admin can access both invoices
        $this->actingAs($this->superAdmin);
        $response = $this->get(route('invoices.show', $invoice1));
        $response->assertOk();
        $response = $this->get(route('invoices.show', $invoice2));
        $response->assertOk();
    }

    #[Test]
    public function users_cannot_create_relationships_across_companies()
    {
        // Create data for both companies
        $customer1 = Customer::factory()->create([
            'owner_company_id' => $this->company1->owner_company_id,
        ]);
        
        $product2 = Product::factory()->create([
            'owner_company_id' => $this->company2->owner_company_id,
            'price' => 100,
        ]);
        
        // Try to create an invoice for company 1 customer with company 2 product
        $this->actingAs($this->user1);
        
        $invoiceData = [
            'customer_id' => $customer1->customer_id,
            'invoice_date' => now()->format('Y-m-d'),
            'due_date' => now()->addDays(30)->format('Y-m-d'),
            'status' => 'Draft',
            'items' => [
                [
                    'product_id' => $product2->product_id, // Company 2 product
                    'description' => 'Product 2 description',
                    'quantity' => 1,
                    'unit_price' => $product2->price,
                    'tax_rate_id' => null,
                    'tax_amount' => 0,
                ]
            ]
        ];
        
        $response = $this->post(route('invoices.store'), $invoiceData);
        
        // The request should fail because the product belongs to a different company
        $response->assertSessionHasErrors(['items.0.product_id']);
        
        // Verify that no invoice was created
        $this->assertDatabaseMissing('invoices', [
            'customer_id' => $customer1->customer_id,
            'owner_company_id' => $this->company1->owner_company_id,
        ]);
    }

    #[Test]
    public function super_admin_can_create_relationships_across_companies()
    {
        // Create data for both companies
        $customer1 = Customer::factory()->create([
            'owner_company_id' => $this->company1->owner_company_id,
        ]);
        
        $product2 = Product::factory()->create([
            'owner_company_id' => $this->company2->owner_company_id,
            'price' => 100,
        ]);
        
        // Super admin should be able to create an invoice for company 1 customer with company 2 product
        $this->actingAs($this->superAdmin);
        
        $invoiceData = [
            'customer_id' => $customer1->customer_id,
            'invoice_date' => now()->format('Y-m-d'),
            'due_date' => now()->addDays(30)->format('Y-m-d'),
            'status' => 'Draft',
            'owner_company_id' => $this->company1->owner_company_id,
            'items' => [
                [
                    'product_id' => $product2->product_id, // Company 2 product
                    'description' => 'Product 2 description',
                    'quantity' => 1,
                    'unit_price' => $product2->price,
                    'tax_rate_id' => null,
                    'tax_amount' => 0,
                ]
            ]
        ];
        
        $response = $this->post(route('invoices.store'), $invoiceData);
        
        // The request should succeed for super admin
        $response->assertRedirect();
        
        // Verify that the invoice was created
        $this->assertDatabaseHas('invoices', [
            'customer_id' => $customer1->customer_id,
            'owner_company_id' => $this->company1->owner_company_id,
        ]);
    }
}