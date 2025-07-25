<?php

namespace Tests\Feature;

use App\Models\CrmUser;
use App\Models\Customer;
use App\Models\Lead;
use App\Models\Opportunity;
use App\Models\OwnerCompany;
use App\Models\Quotation;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class CrmMultiCompanyTest extends TestCase
{
    use RefreshDatabase;

    protected OwnerCompany $company1;
    protected OwnerCompany $company2;
    protected CrmUser $user1;
    protected CrmUser $user2;
    protected CrmUser $superAdmin;
    protected Lead $lead1;
    protected Lead $lead2;

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
            'view-leads',
            'create-leads',
            'edit-leads',
            'delete-leads',
            'view-opportunities',
            'create-opportunities',
            'edit-opportunities',
            'delete-opportunities',
            'view-quotations',
            'create-quotations',
            'edit-quotations',
            'delete-quotations',
            'view-customers',
            'create-customers',
            'edit-customers',
            'delete-customers'
        ]);

        $this->givePermission($this->user2, [
            'view-leads',
            'create-leads',
            'edit-leads',
            'delete-leads',
            'view-opportunities',
            'create-opportunities',
            'edit-opportunities',
            'delete-opportunities',
            'view-quotations',
            'create-quotations',
            'edit-quotations',
            'delete-quotations',
            'view-customers',
            'create-customers',
            'edit-customers',
            'delete-customers'
        ]);

        $this->givePermission($this->superAdmin, [
            'view-leads',
            'create-leads',
            'edit-leads',
            'delete-leads',
            'view-opportunities',
            'create-opportunities',
            'edit-opportunities',
            'delete-opportunities',
            'view-quotations',
            'create-quotations',
            'edit-quotations',
            'delete-quotations',
            'view-customers',
            'create-customers',
            'edit-customers',
            'delete-customers',
            'manage-companies'
        ]);

        // Create leads for company 1
        $this->lead1 = Lead::create([
            'first_name' => 'John',
            'last_name' => 'Doe',
            'email' => 'john.doe@example.com',
            'phone' => '123-456-7890',
            'company_name' => 'ABC Corp',
            'status' => 'New',
            'source' => 'Website',
            'assigned_to_user_id' => $this->user1->user_id,
            'owner_company_id' => $this->company1->owner_company_id,
        ]);

        // Create leads for company 2
        $this->lead2 = Lead::create([
            'first_name' => 'Jane',
            'last_name' => 'Smith',
            'email' => 'jane.smith@example.com',
            'phone' => '987-654-3210',
            'company_name' => 'XYZ Inc',
            'status' => 'New',
            'source' => 'Referral',
            'assigned_to_user_id' => $this->user2->user_id,
            'owner_company_id' => $this->company2->owner_company_id,
        ]);
    }

    #[Test]
    public function leads_are_isolated_between_companies()
    {
        // Verify that company 1 user can only see company 1 leads
        $this->actingAs($this->user1);
        $response = $this->get(route('leads.index'));
        $response->assertOk();
        $response->assertSee('John Doe');
        $response->assertDontSee('Jane Smith');

        // Verify that company 2 user can only see company 2 leads
        $this->actingAs($this->user2);
        $response = $this->get(route('leads.index'));
        $response->assertOk();
        $response->assertSee('Jane Smith');
        $response->assertDontSee('John Doe');

        // Verify that super admin can see both companies' leads
        $this->actingAs($this->superAdmin);
        $response = $this->get(route('leads.index'));
        $response->assertOk();
        $response->assertSee('John Doe');
        $response->assertSee('Jane Smith');
    }

    #[Test]
    public function opportunities_are_isolated_between_companies()
    {
        // Create opportunities for company 1
        $this->actingAs($this->user1);
        $opportunity1 = Opportunity::create([
            'name' => 'Company 1 Opportunity',
            'lead_id' => $this->lead1->lead_id,
            'expected_revenue' => 10000.00,
            'probability' => 70,
            'status' => 'Open',
            'expected_closing_date' => now()->addDays(30),
            'assigned_to_user_id' => $this->user1->user_id,
            'owner_company_id' => $this->company1->owner_company_id,
        ]);

        // Create opportunities for company 2
        $this->actingAs($this->user2);
        $opportunity2 = Opportunity::create([
            'name' => 'Company 2 Opportunity',
            'lead_id' => $this->lead2->lead_id,
            'expected_revenue' => 20000.00,
            'probability' => 60,
            'status' => 'Open',
            'expected_closing_date' => now()->addDays(45),
            'assigned_to_user_id' => $this->user2->user_id,
            'owner_company_id' => $this->company2->owner_company_id,
        ]);

        // Verify that company 1 user can only see company 1 opportunities
        $this->actingAs($this->user1);
        $response = $this->get(route('opportunities.index'));
        $response->assertOk();
        $response->assertSee('Company 1 Opportunity');
        $response->assertDontSee('Company 2 Opportunity');

        // Verify that company 2 user can only see company 2 opportunities
        $this->actingAs($this->user2);
        $response = $this->get(route('opportunities.index'));
        $response->assertOk();
        $response->assertSee('Company 2 Opportunity');
        $response->assertDontSee('Company 1 Opportunity');

        // Verify that super admin can see both companies' opportunities
        $this->actingAs($this->superAdmin);
        $response = $this->get(route('opportunities.index'));
        $response->assertOk();
        $response->assertSee('Company 1 Opportunity');
        $response->assertSee('Company 2 Opportunity');
    }

    #[Test]
    public function quotations_are_isolated_between_companies()
    {
        // Create opportunities for both companies
        $opportunity1 = Opportunity::create([
            'name' => 'Company 1 Opportunity',
            'lead_id' => $this->lead1->lead_id,
            'expected_revenue' => 10000.00,
            'probability' => 70,
            'status' => 'Open',
            'expected_closing_date' => now()->addDays(30),
            'assigned_to_user_id' => $this->user1->user_id,
            'owner_company_id' => $this->company1->owner_company_id,
        ]);

        $opportunity2 = Opportunity::create([
            'name' => 'Company 2 Opportunity',
            'lead_id' => $this->lead2->lead_id,
            'expected_revenue' => 20000.00,
            'probability' => 60,
            'status' => 'Open',
            'expected_closing_date' => now()->addDays(45),
            'assigned_to_user_id' => $this->user2->user_id,
            'owner_company_id' => $this->company2->owner_company_id,
        ]);

        // Create quotations for company 1
        $this->actingAs($this->user1);
        $quotation1 = Quotation::create([
            'quotation_number' => 'Q-001-C1',
            'opportunity_id' => $opportunity1->opportunity_id,
            'quotation_date' => now(),
            'valid_until' => now()->addDays(30),
            'status' => 'Draft',
            'total_amount' => 10000.00,
            'created_by_user_id' => $this->user1->user_id,
            'owner_company_id' => $this->company1->owner_company_id,
        ]);

        // Create quotations for company 2
        $this->actingAs($this->user2);
        $quotation2 = Quotation::create([
            'quotation_number' => 'Q-001-C2',
            'opportunity_id' => $opportunity2->opportunity_id,
            'quotation_date' => now(),
            'valid_until' => now()->addDays(30),
            'status' => 'Draft',
            'total_amount' => 20000.00,
            'created_by_user_id' => $this->user2->user_id,
            'owner_company_id' => $this->company2->owner_company_id,
        ]);

        // Verify that company 1 user can only see company 1 quotations
        $this->actingAs($this->user1);
        $response = $this->get(route('quotations.index'));
        $response->assertOk();
        $response->assertSee('Q-001-C1');
        $response->assertDontSee('Q-001-C2');

        // Verify that company 2 user can only see company 2 quotations
        $this->actingAs($this->user2);
        $response = $this->get(route('quotations.index'));
        $response->assertOk();
        $response->assertSee('Q-001-C2');
        $response->assertDontSee('Q-001-C1');

        // Verify that super admin can see both companies' quotations
        $this->actingAs($this->superAdmin);
        $response = $this->get(route('quotations.index'));
        $response->assertOk();
        $response->assertSee('Q-001-C1');
        $response->assertSee('Q-001-C2');
    }

    #[Test]
    public function lead_to_customer_conversion_maintains_company_isolation()
    {
        // Convert lead to customer for company 1
        $this->actingAs($this->user1);
        $response = $this->post(route('leads.convert', $this->lead1->lead_id));
        $response->assertRedirect();

        // Verify that the customer was created with the correct company
        $customer1 = Customer::where('email', $this->lead1->email)->first();
        $this->assertNotNull($customer1);
        $this->assertEquals($this->company1->owner_company_id, $customer1->owner_company_id);

        // Convert lead to customer for company 2
        $this->actingAs($this->user2);
        $response = $this->post(route('leads.convert', $this->lead2->lead_id));
        $response->assertRedirect();

        // Verify that the customer was created with the correct company
        $customer2 = Customer::where('email', $this->lead2->email)->first();
        $this->assertNotNull($customer2);
        $this->assertEquals($this->company2->owner_company_id, $customer2->owner_company_id);

        // Verify that company 1 user can only see company 1 customers
        $this->actingAs($this->user1);
        $response = $this->get(route('customers.index'));
        $response->assertOk();
        $response->assertSee($customer1->first_name . ' ' . $customer1->last_name);
        $response->assertDontSee($customer2->first_name . ' ' . $customer2->last_name);

        // Verify that company 2 user can only see company 2 customers
        $this->actingAs($this->user2);
        $response = $this->get(route('customers.index'));
        $response->assertOk();
        $response->assertSee($customer2->first_name . ' ' . $customer2->last_name);
        $response->assertDontSee($customer1->first_name . ' ' . $customer1->last_name);

        // Verify that super admin can see both companies' customers
        $this->actingAs($this->superAdmin);
        $response = $this->get(route('customers.index'));
        $response->assertOk();
        $response->assertSee($customer1->first_name . ' ' . $customer1->last_name);
        $response->assertSee($customer2->first_name . ' ' . $customer2->last_name);
    }

    #[Test]
    public function users_cannot_create_crm_entities_for_other_companies()
    {
        // Try to create a lead for company 2 as company 1 user
        $this->actingAs($this->user1);
        
        $leadData = [
            'first_name' => 'Test',
            'last_name' => 'User',
            'email' => 'test.user@example.com',
            'phone' => '555-123-4567',
            'company_name' => 'Test Company',
            'status' => 'New',
            'source' => 'Website',
            'owner_company_id' => $this->company2->owner_company_id, // Trying to set company 2
        ];
        
        $response = $this->post(route('leads.store'), $leadData);
        
        // The request should fail because the user cannot create leads for other companies
        $response->assertSessionHasErrors(['owner_company_id']);
        
        // Verify that no lead was created
        $this->assertDatabaseMissing('leads', [
            'email' => 'test.user@example.com',
            'owner_company_id' => $this->company2->owner_company_id,
        ]);
    }

    #[Test]
    public function super_admin_can_create_crm_entities_for_any_company()
    {
        // Super admin should be able to create leads for any company
        $this->actingAs($this->superAdmin);
        
        // Create lead for company 2
        $leadData = [
            'first_name' => 'Admin',
            'last_name' => 'Created',
            'email' => 'admin.created@example.com',
            'phone' => '555-987-6543',
            'company_name' => 'Admin Test Company',
            'status' => 'New',
            'source' => 'Manual',
            'owner_company_id' => $this->company2->owner_company_id,
        ];
        
        $response = $this->post(route('leads.store'), $leadData);
        $response->assertRedirect();
        
        // Verify that the lead was created
        $this->assertDatabaseHas('leads', [
            'email' => 'admin.created@example.com',
            'owner_company_id' => $this->company2->owner_company_id,
        ]);
    }
}