<?php

namespace Tests\Feature;

use App\Models\CrmUser;
use App\Models\Customer;
use App\Models\OwnerCompany;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class MultiCompanyTest extends TestCase
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
            'delete-customers'
        ]);

        $this->givePermission($this->user2, [
            'view-customers',
            'create-customers',
            'edit-customers',
            'delete-customers'
        ]);

        $this->givePermission($this->superAdmin, [
            'view-customers',
            'create-customers',
            'edit-customers',
            'delete-customers',
            'manage-companies'
        ]);
    }

    #[Test]
    public function users_can_only_see_customers_from_their_company()
    {
        // Create customers for company 1
        $company1Customers = Customer::factory()->count(3)->create([
            'owner_company_id' => $this->company1->owner_company_id,
        ]);

        // Create customers for company 2
        $company2Customers = Customer::factory()->count(2)->create([
            'owner_company_id' => $this->company2->owner_company_id,
        ]);

        // User 1 should only see company 1 customers
        $this->actingAs($this->user1);
        $response = $this->get(route('customers.index'));
        $response->assertOk();
        
        foreach ($company1Customers as $customer) {
            $response->assertSee($customer->first_name);
        }
        
        foreach ($company2Customers as $customer) {
            $response->assertDontSee($customer->first_name);
        }

        // User 2 should only see company 2 customers
        $this->actingAs($this->user2);
        $response = $this->get(route('customers.index'));
        $response->assertOk();
        
        foreach ($company1Customers as $customer) {
            $response->assertDontSee($customer->first_name);
        }
        
        foreach ($company2Customers as $customer) {
            $response->assertSee($customer->first_name);
        }

        // Super admin should see all customers
        $this->actingAs($this->superAdmin);
        $response = $this->get(route('customers.index'));
        $response->assertOk();
        
        foreach ($company1Customers as $customer) {
            $response->assertSee($customer->first_name);
        }
        
        foreach ($company2Customers as $customer) {
            $response->assertSee($customer->first_name);
        }
    }

    #[Test]
    public function users_cannot_access_customers_from_other_companies()
    {
        // Create a customer for company 1
        $company1Customer = Customer::factory()->create([
            'owner_company_id' => $this->company1->owner_company_id,
        ]);

        // Create a customer for company 2
        $company2Customer = Customer::factory()->create([
            'owner_company_id' => $this->company2->owner_company_id,
        ]);

        // User 1 should be able to access company 1 customer
        $this->actingAs($this->user1);
        $response = $this->get(route('customers.show', $company1Customer));
        $response->assertOk();

        // User 1 should not be able to access company 2 customer
        $response = $this->get(route('customers.show', $company2Customer));
        $response->assertForbidden();

        // User 2 should be able to access company 2 customer
        $this->actingAs($this->user2);
        $response = $this->get(route('customers.show', $company2Customer));
        $response->assertOk();

        // User 2 should not be able to access company 1 customer
        $response = $this->get(route('customers.show', $company1Customer));
        $response->assertForbidden();

        // Super admin should be able to access both customers
        $this->actingAs($this->superAdmin);
        $response = $this->get(route('customers.show', $company1Customer));
        $response->assertOk();
        $response = $this->get(route('customers.show', $company2Customer));
        $response->assertOk();
    }

    #[Test]
    public function users_cannot_create_customers_for_other_companies()
    {
        $this->actingAs($this->user1);

        $customerData = [
            'type' => 'Person',
            'first_name' => 'John',
            'last_name' => 'Doe',
            'legal_id' => 'PERSON-001',
            'email' => 'john.doe@example.com',
            'phone_number' => '123-456-7890',
            'status' => 'Active',
            'owner_company_id' => $this->company2->owner_company_id, // Trying to create for company 2
        ];

        $response = $this->post(route('customers.store'), $customerData);
        
        // The request should succeed because the controller should override the owner_company_id
        $response->assertRedirect(route('customers.index'));
        
        // But the customer should be created for company 1, not company 2
        $this->assertDatabaseHas('customers', [
            'first_name' => 'John',
            'last_name' => 'Doe',
            'email' => 'john.doe@example.com',
            'owner_company_id' => $this->company1->owner_company_id,
        ]);
        
        $this->assertDatabaseMissing('customers', [
            'first_name' => 'John',
            'last_name' => 'Doe',
            'email' => 'john.doe@example.com',
            'owner_company_id' => $this->company2->owner_company_id,
        ]);
    }

    #[Test]
    public function super_admin_can_create_customers_for_any_company()
    {
        $this->actingAs($this->superAdmin);

        // Create customer for company 1
        $customerData1 = [
            'type' => 'Person',
            'first_name' => 'John',
            'last_name' => 'Doe',
            'legal_id' => 'PERSON-001',
            'email' => 'john.doe@example.com',
            'phone_number' => '123-456-7890',
            'status' => 'Active',
            'owner_company_id' => $this->company1->owner_company_id,
        ];

        $response = $this->post(route('customers.store'), $customerData1);
        $response->assertRedirect(route('customers.index'));
        
        $this->assertDatabaseHas('customers', [
            'first_name' => 'John',
            'last_name' => 'Doe',
            'email' => 'john.doe@example.com',
            'owner_company_id' => $this->company1->owner_company_id,
        ]);

        // Create customer for company 2
        $customerData2 = [
            'type' => 'Person',
            'first_name' => 'Jane',
            'last_name' => 'Smith',
            'legal_id' => 'PERSON-002',
            'email' => 'jane.smith@example.com',
            'phone_number' => '987-654-3210',
            'status' => 'Active',
            'owner_company_id' => $this->company2->owner_company_id,
        ];

        $response = $this->post(route('customers.store'), $customerData2);
        $response->assertRedirect(route('customers.index'));
        
        $this->assertDatabaseHas('customers', [
            'first_name' => 'Jane',
            'last_name' => 'Smith',
            'email' => 'jane.smith@example.com',
            'owner_company_id' => $this->company2->owner_company_id,
        ]);
    }

    #[Test]
    public function users_cannot_update_customers_from_other_companies()
    {
        // Create a customer for company 2
        $company2Customer = Customer::factory()->create([
            'owner_company_id' => $this->company2->owner_company_id,
            'first_name' => 'Original',
            'last_name' => 'Name',
        ]);

        // User 1 should not be able to update company 2 customer
        $this->actingAs($this->user1);
        
        $updateData = [
            'type' => 'Person',
            'first_name' => 'Updated',
            'last_name' => 'Name',
            'legal_id' => 'PERSON-002',
            'email' => 'updated@example.com',
            'status' => 'Active',
        ];

        $response = $this->put(route('customers.update', $company2Customer), $updateData);
        $response->assertForbidden();
        
        // Verify the customer was not updated
        $this->assertDatabaseHas('customers', [
            'customer_id' => $company2Customer->customer_id,
            'first_name' => 'Original',
            'last_name' => 'Name',
        ]);
    }

    #[Test]
    public function users_cannot_delete_customers_from_other_companies()
    {
        // Create a customer for company 2
        $company2Customer = Customer::factory()->create([
            'owner_company_id' => $this->company2->owner_company_id,
        ]);

        // User 1 should not be able to delete company 2 customer
        $this->actingAs($this->user1);
        
        $response = $this->delete(route('customers.destroy', $company2Customer));
        $response->assertForbidden();
        
        // Verify the customer was not deleted
        $this->assertDatabaseHas('customers', [
            'customer_id' => $company2Customer->customer_id,
            'deleted_at' => null,
        ]);
    }
}