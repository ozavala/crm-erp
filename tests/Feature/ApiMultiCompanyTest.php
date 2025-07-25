<?php

namespace Tests\Feature;

use App\Models\ApiToken;
use App\Models\CrmUser;
use App\Models\Customer;
use App\Models\OwnerCompany;
use App\Models\Product;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class ApiMultiCompanyTest extends TestCase
{
    use RefreshDatabase;

    protected OwnerCompany $company1;
    protected OwnerCompany $company2;
    protected CrmUser $user1;
    protected CrmUser $user2;
    protected CrmUser $superAdmin;
    protected ApiToken $token1;
    protected ApiToken $token2;
    protected ApiToken $superAdminToken;
    protected Customer $customer1;
    protected Customer $customer2;
    protected Product $product1;
    protected Product $product2;

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
            'api-access',
            'view-customers',
            'create-customers',
            'edit-customers',
            'delete-customers',
            'view-products',
            'create-products',
            'edit-products',
            'delete-products'
        ]);

        $this->givePermission($this->user2, [
            'api-access',
            'view-customers',
            'create-customers',
            'edit-customers',
            'delete-customers',
            'view-products',
            'create-products',
            'edit-products',
            'delete-products'
        ]);

        $this->givePermission($this->superAdmin, [
            'api-access',
            'view-customers',
            'create-customers',
            'edit-customers',
            'delete-customers',
            'view-products',
            'create-products',
            'edit-products',
            'delete-products',
            'manage-companies'
        ]);

        // Create API tokens for each user
        $this->token1 = ApiToken::create([
            'user_id' => $this->user1->user_id,
            'name' => 'API Token for Company 1',
            'token' => 'token1_' . str_random(60),
            'owner_company_id' => $this->company1->owner_company_id,
            'expires_at' => now()->addYear(),
        ]);

        $this->token2 = ApiToken::create([
            'user_id' => $this->user2->user_id,
            'name' => 'API Token for Company 2',
            'token' => 'token2_' . str_random(60),
            'owner_company_id' => $this->company2->owner_company_id,
            'expires_at' => now()->addYear(),
        ]);

        $this->superAdminToken = ApiToken::create([
            'user_id' => $this->superAdmin->user_id,
            'name' => 'Super Admin API Token',
            'token' => 'super_' . str_random(60),
            'owner_company_id' => $this->company1->owner_company_id,
            'expires_at' => now()->addYear(),
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
    }

    #[Test]
    public function api_tokens_are_scoped_to_companies()
    {
        // Company 1 token can access company 1 customers
        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $this->token1->token,
            'Accept' => 'application/json',
        ])->get('/api/customers');

        $response->assertOk();
        $response->assertJsonCount(1, 'data');
        $response->assertJsonFragment(['email' => 'john.doe@example.com']);
        $response->assertJsonMissing(['email' => 'jane.smith@example.com']);

        // Company 2 token can access company 2 customers
        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $this->token2->token,
            'Accept' => 'application/json',
        ])->get('/api/customers');

        $response->assertOk();
        $response->assertJsonCount(1, 'data');
        $response->assertJsonFragment(['email' => 'jane.smith@example.com']);
        $response->assertJsonMissing(['email' => 'john.doe@example.com']);

        // Super admin token can access all customers
        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $this->superAdminToken->token,
            'Accept' => 'application/json',
        ])->get('/api/customers');

        $response->assertOk();
        $response->assertJsonCount(2, 'data');
        $response->assertJsonFragment(['email' => 'john.doe@example.com']);
        $response->assertJsonFragment(['email' => 'jane.smith@example.com']);
    }

    #[Test]
    public function api_tokens_cannot_access_resources_from_other_companies()
    {
        // Company 1 token cannot access company 2 customer
        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $this->token1->token,
            'Accept' => 'application/json',
        ])->get('/api/customers/' . $this->customer2->customer_id);

        $response->assertForbidden();

        // Company 2 token cannot access company 1 customer
        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $this->token2->token,
            'Accept' => 'application/json',
        ])->get('/api/customers/' . $this->customer1->customer_id);

        $response->assertForbidden();

        // Super admin token can access both customers
        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $this->superAdminToken->token,
            'Accept' => 'application/json',
        ])->get('/api/customers/' . $this->customer1->customer_id);

        $response->assertOk();
        $response->assertJsonFragment(['email' => 'john.doe@example.com']);

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $this->superAdminToken->token,
            'Accept' => 'application/json',
        ])->get('/api/customers/' . $this->customer2->customer_id);

        $response->assertOk();
        $response->assertJsonFragment(['email' => 'jane.smith@example.com']);
    }

    #[Test]
    public function api_tokens_cannot_create_resources_for_other_companies()
    {
        // Company 1 token cannot create a customer for company 2
        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $this->token1->token,
            'Accept' => 'application/json',
            'Content-Type' => 'application/json',
        ])->postJson('/api/customers', [
            'first_name' => 'Test',
            'last_name' => 'User',
            'email' => 'test.user@example.com',
            'phone' => '555-123-4567',
            'owner_company_id' => $this->company2->owner_company_id, // Trying to set company 2
        ]);

        $response->assertStatus(422); // Validation error
        $response->assertJsonValidationErrors(['owner_company_id']);

        // Verify that no customer was created
        $this->assertDatabaseMissing('customers', [
            'email' => 'test.user@example.com',
        ]);
    }

    #[Test]
    public function api_tokens_cannot_update_resources_from_other_companies()
    {
        // Company 1 token cannot update a company 2 customer
        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $this->token1->token,
            'Accept' => 'application/json',
            'Content-Type' => 'application/json',
        ])->putJson('/api/customers/' . $this->customer2->customer_id, [
            'first_name' => 'Updated',
            'last_name' => 'Name',
            'email' => 'updated@example.com',
        ]);

        $response->assertForbidden();

        // Verify that the customer was not updated
        $this->assertDatabaseHas('customers', [
            'customer_id' => $this->customer2->customer_id,
            'first_name' => 'Jane',
            'last_name' => 'Smith',
            'email' => 'jane.smith@example.com',
        ]);
    }

    #[Test]
    public function api_tokens_cannot_delete_resources_from_other_companies()
    {
        // Company 1 token cannot delete a company 2 customer
        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $this->token1->token,
            'Accept' => 'application/json',
        ])->deleteJson('/api/customers/' . $this->customer2->customer_id);

        $response->assertForbidden();

        // Verify that the customer was not deleted
        $this->assertDatabaseHas('customers', [
            'customer_id' => $this->customer2->customer_id,
        ]);
    }

    #[Test]
    public function super_admin_api_token_can_manage_resources_from_all_companies()
    {
        // Super admin can create a customer for any company
        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $this->superAdminToken->token,
            'Accept' => 'application/json',
            'Content-Type' => 'application/json',
        ])->postJson('/api/customers', [
            'first_name' => 'API',
            'last_name' => 'Created',
            'email' => 'api.created@example.com',
            'phone' => '555-987-6543',
            'owner_company_id' => $this->company2->owner_company_id, // Company 2
        ]);

        $response->assertStatus(201);
        
        // Verify that the customer was created
        $this->assertDatabaseHas('customers', [
            'first_name' => 'API',
            'last_name' => 'Created',
            'email' => 'api.created@example.com',
            'owner_company_id' => $this->company2->owner_company_id,
        ]);

        // Super admin can update a customer from any company
        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $this->superAdminToken->token,
            'Accept' => 'application/json',
            'Content-Type' => 'application/json',
        ])->putJson('/api/customers/' . $this->customer2->customer_id, [
            'first_name' => 'Updated',
            'last_name' => 'By API',
            'email' => 'updated.by.api@example.com',
        ]);

        $response->assertOk();
        
        // Verify that the customer was updated
        $this->assertDatabaseHas('customers', [
            'customer_id' => $this->customer2->customer_id,
            'first_name' => 'Updated',
            'last_name' => 'By API',
            'email' => 'updated.by.api@example.com',
        ]);

        // Super admin can delete a customer from any company
        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $this->superAdminToken->token,
            'Accept' => 'application/json',
        ])->deleteJson('/api/customers/' . $this->customer2->customer_id);

        $response->assertStatus(204);
        
        // Verify that the customer was deleted
        $this->assertDatabaseMissing('customers', [
            'customer_id' => $this->customer2->customer_id,
        ]);
    }

    #[Test]
    public function api_tokens_respect_user_permissions()
    {
        // Create a user with limited permissions
        $limitedUser = CrmUser::factory()->create([
            'owner_company_id' => $this->company1->owner_company_id,
        ]);

        // Give only view permissions
        $this->givePermission($limitedUser, [
            'api-access',
            'view-customers',
            'view-products',
        ]);

        // Create API token for the limited user
        $limitedToken = ApiToken::create([
            'user_id' => $limitedUser->user_id,
            'name' => 'Limited API Token',
            'token' => 'limited_' . str_random(60),
            'owner_company_id' => $this->company1->owner_company_id,
            'expires_at' => now()->addYear(),
        ]);

        // Limited token can view customers
        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $limitedToken->token,
            'Accept' => 'application/json',
        ])->get('/api/customers');

        $response->assertOk();

        // Limited token cannot create customers
        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $limitedToken->token,
            'Accept' => 'application/json',
            'Content-Type' => 'application/json',
        ])->postJson('/api/customers', [
            'first_name' => 'Limited',
            'last_name' => 'User',
            'email' => 'limited.user@example.com',
            'phone' => '555-123-4567',
            'owner_company_id' => $this->company1->owner_company_id,
        ]);

        $response->assertForbidden();

        // Limited token cannot update customers
        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $limitedToken->token,
            'Accept' => 'application/json',
            'Content-Type' => 'application/json',
        ])->putJson('/api/customers/' . $this->customer1->customer_id, [
            'first_name' => 'Updated',
            'last_name' => 'Name',
            'email' => 'updated@example.com',
        ]);

        $response->assertForbidden();

        // Limited token cannot delete customers
        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $limitedToken->token,
            'Accept' => 'application/json',
        ])->deleteJson('/api/customers/' . $this->customer1->customer_id);

        $response->assertForbidden();
    }

    #[Test]
    public function expired_api_tokens_are_rejected()
    {
        // Create an expired token
        $expiredToken = ApiToken::create([
            'user_id' => $this->user1->user_id,
            'name' => 'Expired API Token',
            'token' => 'expired_' . str_random(60),
            'owner_company_id' => $this->company1->owner_company_id,
            'expires_at' => now()->subDay(), // Expired
        ]);

        // Expired token should be rejected
        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $expiredToken->token,
            'Accept' => 'application/json',
        ])->get('/api/customers');

        $response->assertUnauthorized();
    }
}